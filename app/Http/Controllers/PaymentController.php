<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Library\SslCommerz\SslCommerzNotification;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\DB;
use App\Models\TravelInsurance;
use App\Models\Country;
use App\Models\User;
use App\Jobs\IssueTravelInsurancePolicy;

class PaymentController extends Controller
{
    /**
     * Initiate Payment for Travel Insurance.
     */
    public function initiateTravelInsurancePayment(Request $request)
    {
        $validated = $request->validate([
            'quote_reference' => 'required|string',
            'package_name' => 'required|string',
            'destination_country_id' => 'required|exists:countries,id',
            'start_date' => 'required|date',
            'end_date' => 'required|date',
            'num_travelers' => 'required|integer|min:1',
            'total_premium' => 'required|numeric|min:1',
            'currency' => 'required|string|in:BDT',
            'policyholder_name' => 'required|string|max:255',
            'policyholder_email' => 'required|email|max:255',
            'policyholder_phone' => 'required|string|max:20',
            'policyholder_address' => 'required|string|max:255',
            'travelers' => 'required|array',
        ]);

        $user = auth()->user();
        $country = Country::find($validated['destination_country_id']);

        $transactionId = 'TI-' . strtoupper(Str::random(10));

        $post_data = [];
        $post_data['total_amount'] = $validated['total_premium'];
        $post_data['currency'] = $validated['currency'];
        $post_data['tran_id'] = $transactionId;

        $post_data['success_url'] = route('payment.success');
        $post_data['fail_url'] = route('payment.fail');
        $post_data['cancel_url'] = route('payment.cancel');
        $post_data['ipn_url'] = route('payment.ipn');

        $post_data['cus_name'] = $validated['policyholder_name'];
        $post_data['cus_email'] = $validated['policyholder_email'];
        $post_data['cus_add1'] = $validated['policyholder_address'];
        $post_data['cus_city'] = 'Dhaka';
        $post_data['cus_postcode'] = '1200';
        $post_data['cus_country'] = 'Bangladesh';
        $post_data['cus_phone'] = $validated['policyholder_phone'];
        $post_data['shipping_method'] = "NO";

        $post_data['product_name'] = "Travel Insurance: " . $validated['package_name'];
        $post_data['product_category'] = "Insurance";
        $post_data['product_profile'] = "non-physical-goods";

        try {
            $duration = (new \DateTime($validated['end_date']))->diff(new \DateTime($validated['start_date']))->days + 1;

            $pendingInsurance = TravelInsurance::create([
                'user_id' => $user->id,
                'bimafy_policy_reference' => $transactionId,
                'package_name' => $validated['package_name'],
                'destination_country_id' => $validated['destination_country_id'],
                'start_date' => $validated['start_date'],
                'end_date' => $validated['end_date'],
                'duration_days' => $duration,
                'premium_paid' => $validated['total_premium'],
                'currency' => $validated['currency'],
                'status' => 'pending_payment',
                'coverage_details' => ['travelers' => $validated['travelers']],
            ]);

            $post_data['value_a'] = $user->id;
            $post_data['value_b'] = $validated['quote_reference'];
            $post_data['value_c'] = 'travel_insurance';
            $post_data['value_d'] = $pendingInsurance->id;

            $sslc = new SslCommerzNotification();
            $payment_options_json = $sslc->makePayment($post_data, 'checkout', 'json');
            $payment_options = json_decode($payment_options_json, true);

            if (isset($payment_options['status']) && in_array(strtoupper($payment_options['status']), ['SUCCESS', 'VALID'])) {
                return response()->json(['redirectUrl' => $payment_options['data']], 200);
            } else {
                Log::error('SSLCommerz initiation failed.', $payment_options ?? ['error' => 'Unknown']);
                return response()->json(['message' => $payment_options['message'] ?? 'Payment initiation failed.'], 400);
            }
        } catch (\Exception $e) {
            Log::error('SSLCommerz Exception during initiation.', ['error' => $e->getMessage()]);
            return response()->json(['message' => 'Could not initiate payment. Please try again later.'], 500);
        }
    }

    // --- Callback Handlers ---

    public function paymentSuccess(Request $request)
    {
        Log::info('SSLCommerz Success Callback Received', $request->all());
        return Redirect::away(config('app.frontend_url', url('/')) .
            '/payment/success?transaction_id=' . $request->input('tran_id') .
            '&status=' . $request->input('status'));
    }

    public function paymentFail(Request $request)
    {
        Log::warning('SSLCommerz Fail Callback Received', $request->all());
        $tran_id = $request->input('tran_id');

        TravelInsurance::where('bimafy_policy_reference', $tran_id)
            ->where('status', 'pending_payment')
            ->update(['status' => 'payment_failed']);

        return Redirect::away(config('app.frontend_url', url('/')) .
            '/payment/fail?transaction_id=' . $tran_id);
    }

    public function paymentCancel(Request $request)
    {
        Log::info('SSLCommerz Cancel Callback Received', $request->all());
        $tran_id = $request->input('tran_id');

        TravelInsurance::where('bimafy_policy_reference', $tran_id)
            ->where('status', 'pending_payment')
            ->update(['status' => 'payment_cancelled']);

        return Redirect::away(config('app.frontend_url', url('/')) .
            '/payment/cancel?transaction_id=' . $tran_id);
    }

    public function paymentIpn(Request $request)
    {
        Log::info('SSLCommerz IPN Received', $request->all());
        $ipnData = $request->all();

        if (empty($ipnData) || !isset($ipnData['tran_id'])) {
            Log::error('SSLCommerz IPN: Received empty or invalid data.');
            return response()->json(['status' => 'error', 'message' => 'Invalid data'], 400);
        }

        $transactionId = $ipnData['tran_id'];
        $paymentStatus = $ipnData['status'];
        $ourInsuranceRecordId = $ipnData['value_d'] ?? null;

        $insurance = TravelInsurance::find($ourInsuranceRecordId);
        if (!$insurance) {
            Log::error('SSLCommerz IPN: Could not find matching local record.', [
                'tran_id' => $transactionId,
                'local_id' => $ourInsuranceRecordId
            ]);
            return response()->json(['status' => 'error', 'message' => 'Record not found'], 404);
        }

        if ($insurance->status !== 'pending_payment') {
            Log::info('SSLCommerz IPN: Transaction already processed.', [
                'tran_id' => $transactionId,
                'status' => $insurance->status
            ]);
            return response()->json(['status' => 'success', 'message' => 'Already processed'], 200);
        }

        if (in_array($paymentStatus, ['VALID', 'VALIDATED'])) {
            $sslc = new SslCommerzNotification();
            $isValid = $sslc->orderValidate($ipnData, $transactionId, $insurance->premium_paid, $insurance->currency);

            if ($isValid) {
                $insurance->update([
                    'status' => 'processing',
                    'admin_notes' => 'Payment validated via IPN. Bank TRN: ' . ($ipnData['bank_tran_id'] ?? 'N/A')
                ]);

                // ✅ DISPATCH THE JOB (FINAL MERGED VERSION)
                \App\Jobs\IssueTravelInsurancePolicy::dispatch($insurance);

                Log::info('SSLCommerz IPN Validated and Job Dispatched.', ['tran_id' => $transactionId]);
                return response()->json(['status' => 'success', 'message' => 'IPN Processed'], 200);
            } else {
                $insurance->update([
                    'status' => 'payment_failed',
                    'admin_notes' => 'IPN validation failed.'
                ]);
                Log::error('SSLCommerz IPN Validation FAILED.', $ipnData);
                return response()->json(['status' => 'error', 'message' => 'IPN Validation Failed'], 400);
            }
        } else {
            $newStatus = in_array($paymentStatus, ['FAILED', 'Bounced'])
                ? 'payment_failed'
                : 'payment_cancelled';

            $insurance->update([
                'status' => $newStatus,
                'admin_notes' => "IPN Status: $paymentStatus"
            ]);

            Log::warning('SSLCommerz IPN received non-valid status.', $ipnData);
            return response()->json(['status' => 'success', 'message' => 'IPN Received for non-valid status'], 200);
        }
    }
}
