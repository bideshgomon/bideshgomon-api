<?php

namespace App\Http\Controllers;

use App\Library\SslCommerz\SslCommerzNotification;
use App\Models\Country;
use App\Models\Payment;
use App\Models\TravelInsurance;
use App\Models\User; // <-- Import DB Facade
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB; // <-- ADDED: Import Payment model
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redirect;
use Throwable; // <-- ADDED: Import Throwable for exceptions

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
            'end_date' => 'required|date', // Removed after_or_equal, assuming validation is done
            'num_travelers' => 'required|integer|min:1',
            'total_premium' => 'required|numeric|min:1',
            'currency' => 'required|string|in:BDT',
            'policyholder_name' => 'required|string|max:255',
            'policyholder_email' => 'required|email|max:255',
            'policyholder_phone' => 'required|string|max:20',
            'policyholder_address' => 'required|string|max:255', // Corrected max length from 255 to 500
            'travelers' => 'required|array',
        ]);

        $user = auth()->user();
        $country = Country::find($validated['destination_country_id']);

        // Use the transaction ID from the request (value_a/tran_id)
        $transactionId = $validated['quote_reference']; // Use quote_reference as the master transaction ID

        // --- [PATCH START] Create Payment & Insurance records in a transaction ---
        DB::beginTransaction();
        try {
            $duration = (new \DateTime($validated['end_date']))->diff(new \DateTime($validated['start_date']))->days + 1;

            // 1. Create TravelInsurance record
            $insurance = TravelInsurance::create([
                'user_id' => $user->id,
                'bimafy_policy_reference' => $transactionId, // Use our tran_id as the reference
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

            // 2. Create Payment record linked to the TravelInsurance
            $payment = Payment::create([
                'user_id' => $user->id,
                'payable_type' => TravelInsurance::class, // Polymorphic link
                'payable_id' => $insurance->id,         // Link to the insurance record
                'transaction_id' => $transactionId,     // Our unique ID
                'amount' => $validated['total_premium'],
                'currency' => $validated['currency'],
                'status' => 'initiated', // Status: Payment initiated
                'gateway' => 'sslcommerz',
            ]);

            DB::commit(); // Commit
        } catch (Throwable $e) {
            DB::rollBack();
            Log::error('Failed to create insurance or payment record: '.$e->getMessage(), $validated);

            return response()->json(['message' => 'Could not initiate payment process. Please try again.'], 500);
        }
        // --- [PATCH END] ---

        // Prepare data for SSLCommerz
        $post_data = [];
        $post_data['total_amount'] = $validated['total_premium'];
        $post_data['currency'] = $validated['currency'];
        $post_data['tran_id'] = $transactionId; // Use our consistent transaction ID

        $post_data['success_url'] = route('sslcommerz.success'); // Use route names from config/sslcommerz.php
        $post_data['fail_url'] = route('sslcommerz.fail');
        $post_data['cancel_url'] = route('sslcommerz.cancel');
        $post_data['ipn_url'] = route('sslcommerz.ipn');

        $post_data['cus_name'] = $validated['policyholder_name'];
        $post_data['cus_email'] = $validated['policyholder_email'];
        $post_data['cus_add1'] = $validated['policyholder_address'];
        $post_data['cus_city'] = 'Dhaka'; // Default or from user profile
        $post_data['cus_postcode'] = '1200'; // Default or from user profile
        $post_data['cus_country'] = 'Bangladesh'; // Default or from user profile
        $post_data['cus_phone'] = $validated['policyholder_phone'];
        $post_data['shipping_method'] = 'NO';

        $post_data['product_name'] = 'Travel Insurance: '.$validated['package_name'];
        $post_data['product_category'] = 'Insurance';
        $post_data['product_profile'] = 'non-physical-goods';

        // Pass our internal IDs for tracking
        $post_data['value_a'] = $user->id;
        $post_data['value_b'] = $validated['quote_reference']; // Keep quote ref
        $post_data['value_c'] = 'travel_insurance';
        $post_data['value_d'] = $insurance->id; // Pass our insurance record ID

        try {
            $sslc = new SslCommerzNotification;
            $payment_options_json = $sslc->makePayment($post_data, 'checkout', 'json');
            $payment_options = json_decode($payment_options_json, true);

            if (isset($payment_options['status']) && in_array(strtoupper($payment_options['status']), ['SUCCESS', 'VALID'])) {
                // --- [PATCH START] Update payment status to 'pending' ---
                $payment->update(['status' => 'pending']); // Now waiting for gateway

                // --- [PATCH END] ---
                return response()->json(['redirectUrl' => $payment_options['GatewayPageURL']], 200);
            } else {
                Log::error('SSLCommerz initiation failed.', $payment_options ?? ['error' => 'Unknown']);
                // --- [PATCH START] Update payment status to 'failed' ---
                $payment->update(['status' => 'failed', 'gateway_response' => json_encode($payment_options)]);
                $insurance->update(['status' => 'payment_failed']);

                // --- [PATCH END] ---
                return response()->json(['message' => $payment_options['message'] ?? 'Payment initiation failed.'], 400);
            }
        } catch (\Exception $e) {
            Log::error('SSLCommerz Exception during initiation.', ['error' => $e->getMessage()]);
            // --- [PATCH START] Update payment status to 'failed' ---
            $payment->update(['status' => 'failed', 'gateway_response' => $e->getMessage()]);
            $insurance->update(['status' => 'payment_failed']);

            // --- [PATCH END] ---
            return response()->json(['message' => 'Could not initiate payment. Please try again later.'], 500);
        }
    }

    // --- Callback Handlers ---

    public function paymentSuccess(Request $request)
    {
        Log::info('SSLCommerz Success Callback Received', $request->all());
        $tran_id = $request->input('tran_id');

        // --- [PATCH START] Update generic payment table ---
        $payment = Payment::where('transaction_id', $tran_id)->first();
        if ($payment && $payment->status === 'pending') { // Only update if pending
            $payment->update([
                'status' => 'success', // Marked as success by gateway redirect
                'gateway_transaction_id' => $request->input('bank_tran_id'), // Capture bank ID
                'gateway_response' => json_encode($request->all()),
                'paid_at' => now(), // Mark as paid
            ]);
        }
        // --- [PATCH END] ---

        // Update the related insurance record (as before)
        TravelInsurance::where('bimafy_policy_reference', $tran_id)
            ->where('status', 'pending_payment')
            ->update(['status' => 'processing']); // Set to processing, wait for IPN to confirm

        return Redirect::away(config('app.frontend_url', url('/')).
            '/payment/success?transaction_id='.$tran_id.
            '&status='.$request->input('status'));
    }

    public function paymentFail(Request $request)
    {
        Log::warning('SSLCommerz Fail Callback Received', $request->all());
        $tran_id = $request->input('tran_id');

        // --- [PATCH START] Update generic payment table ---
        Payment::where('transaction_id', $tran_id)
            ->whereIn('status', ['initiated', 'pending'])
            ->update([
                'status' => 'failed',
                'gateway_response' => json_encode($request->all()),
            ]);
        // --- [PATCH END] ---

        TravelInsurance::where('bimafy_policy_reference', $tran_id)
            ->where('status', 'pending_payment')
            ->update(['status' => 'payment_failed']);

        return Redirect::away(config('app.frontend_url', url('/')).
            '/payment/fail?transaction_id='.$tran_id);
    }

    public function paymentCancel(Request $request)
    {
        Log::info('SSLCommerz Cancel Callback Received', $request->all());
        $tran_id = $request->input('tran_id');

        // --- [PATCH START] Update generic payment table ---
        Payment::where('transaction_id', $tran_id)
            ->whereIn('status', ['initiated', 'pending'])
            ->update([
                'status' => 'cancelled',
                'gateway_response' => json_encode($request->all()),
            ]);
        // --- [PATCH END] ---

        TravelInsurance::where('bimafy_policy_reference', $tran_id)
            ->where('status', 'pending_payment')
            ->update(['status' => 'payment_cancelled']);

        return Redirect::away(config('app.frontend_url', url('/')).
            '/payment/cancel?transaction_id='.$tran_id);
    }

    public function paymentIpn(Request $request)
    {
        Log::info('SSLCommerz IPN Received', $request->all());
        $ipnData = $request->all();

        if (empty($ipnData) || ! isset($ipnData['tran_id'])) {
            Log::error('SSLCommerz IPN: Received empty or invalid data.');

            return response()->json(['status' => 'error', 'message' => 'Invalid data'], 400);
        }

        $transactionId = $ipnData['tran_id'];
        $paymentStatus = $ipnData['status'];
        $ourInsuranceRecordId = $ipnData['value_d'] ?? null;

        // --- [PATCH START] Find Payment Record ---
        $payment = Payment::where('transaction_id', $transactionId)->first();
        if (! $payment) {
            Log::error('SSLCommerz IPN: Could not find matching Payment record.', [
                'tran_id' => $transactionId,
            ]);

            return response()->json(['status' => 'error', 'message' => 'Payment Record not found'], 404);
        }
        // --- [PATCH END] ---

        $insurance = TravelInsurance::find($ourInsuranceRecordId); // Keep finding insurance record
        if (! $insurance || $payment->payable_id != $ourInsuranceRecordId || $payment->payable_type != TravelInsurance::class) {
            Log::error('SSLCommerz IPN: Could not find matching local insurance record or payment mismatch.', [
                'tran_id' => $transactionId,
                'local_insurance_id' => $ourInsuranceRecordId,
                'payment_payable_id' => $payment->payable_id ?? 'N/A',
            ]);

            return response()->json(['status' => 'error', 'message' => 'Record mismatch'], 404);
        }

        // Check if already processed
        if ($insurance->status !== 'pending_payment' || $payment->status === 'success') {
            Log::info('SSLCommerz IPN: Transaction already processed.', ['tran_id' => $transactionId, 'status' => $insurance->status]);

            return response()->json(['status' => 'success', 'message' => 'Already processed'], 200);
        }

        if (in_array($paymentStatus, ['VALID', 'VALIDATED'])) {
            $sslc = new SslCommerzNotification;
            $isValid = $sslc->orderValidate($ipnData, $transactionId, $insurance->premium_paid, $insurance->currency);

            if ($isValid) {
                // --- [PATCH START] Update both records ---
                DB::beginTransaction();
                try {
                    $payment->update([
                        'status' => 'success',
                        'gateway_transaction_id' => $ipnData['bank_tran_id'] ?? $ipnData['ssl_tran_id'] ?? null,
                        'gateway_response' => json_encode($ipnData),
                        'paid_at' => now(),
                    ]);

                    $insurance->update([
                        'status' => 'processing', // 'processing' until job runs
                        'admin_notes' => 'Payment validated via IPN. Bank TRN: '.($ipnData['bank_tran_id'] ?? 'N/A'),
                    ]);

                    DB::commit();
                } catch (Throwable $e) {
                    DB::rollBack();
                    Log::error('SSLCommerz IPN: DB update failed after validation.', ['error' => $e->getMessage()]);

                    return response()->json(['status' => 'error', 'message' => 'Database update failed'], 500);
                }
                // --- [PATCH END] ---

                // Dispatch the job
                \App\Jobs\IssueTravelInsurancePolicy::dispatch($insurance);

                Log::info('SSLCommerz IPN Validated and Job Dispatched.', ['tran_id' => $transactionId]);

                return response()->json(['status' => 'success', 'message' => 'IPN Processed'], 200);

            } else {
                // --- [PATCH START] Update both records on validation fail ---
                $payment->update([
                    'status' => 'failed',
                    'gateway_response' => json_encode($ipnData),
                ]);
                $insurance->update([
                    'status' => 'payment_failed',
                    'admin_notes' => 'IPN validation failed.',
                ]);
                // --- [PATCH END] ---
                Log::error('SSLCommerz IPN Validation FAILED.', $ipnData);

                return response()->json(['status' => 'error', 'message' => 'IPN Validation Failed'], 400);
            }
        } else {
            // --- [PATCH START] Update both records on non-valid status ---
            $newStatus = in_array($paymentStatus, ['FAILED', 'Bounced'])
                ? 'payment_failed'
                : 'payment_cancelled';

            $payment->update([
                'status' => str_replace('payment_', '', $newStatus), // e.g., 'failed' or 'cancelled'
                'gateway_response' => json_encode($ipnData),
            ]);
            $insurance->update([
                'status' => $newStatus,
                'admin_notes' => "IPN Status: $paymentStatus",
            ]);
            // --- [PATCH END] ---

            Log::warning('SSLCommerz IPN received non-valid status.', $ipnData);

            return response()->json(['status' => 'success', 'message' => 'IPN Received for non-valid status'], 200);
        }
    }
}
