<?php

namespace App\Http\Controllers;

use App\Services\PaymentGatewayService;
use App\Models\PaymentTransaction;
use Illuminate\Http\Request;
use Inertia\Inertia;

class PaymentController extends Controller
{
    protected PaymentGatewayService $paymentGateway;

    public function __construct(PaymentGatewayService $paymentGateway)
    {
        $this->paymentGateway = $paymentGateway;
    }

    /**
     * Initiate SSLCommerz payment.
     */
    public function initiateSSLCommerz(Request $request)
    {
        $validated = $request->validate([
            'amount' => 'required|numeric|min:100|max:100000',
            'customer_name' => 'required|string|max:255',
            'customer_email' => 'required|email',
            'customer_phone' => 'required|string|max:20',
            'address' => 'nullable|string',
            'city' => 'nullable|string',
            'country' => 'nullable|string',
        ]);

        $result = $this->paymentGateway->initiate('sslcommerz', array_merge($validated, [
            'user_id' => $request->user()->id,
            'currency' => 'BDT',
            'gateway_fee' => $validated['amount'] * 0.015, // 1.5% fee
        ]));

        if (!$result['success']) {
            return back()->with('error', $result['message']);
        }

        return redirect($result['data']['gateway_url']);
    }

    /**
     * Handle SSLCommerz success callback.
     */
    public function sslcommerzSuccess(Request $request)
    {
        $result = $this->paymentGateway->processCallback('sslcommerz', $request->all());

        if ($result['success']) {
            return redirect()->route('wallet.index')->with('success', 'Payment successful! Your wallet has been credited.');
        }

        return redirect()->route('wallet.index')->with('error', $result['message'] ?? 'Payment failed');
    }

    /**
     * Handle SSLCommerz failure callback.
     */
    public function sslcommerzFail(Request $request)
    {
        $transactionId = $request->input('tran_id');
        
        if ($transactionId) {
            $transaction = PaymentTransaction::where('transaction_id', $transactionId)->first();
            
            if ($transaction) {
                $transaction->markAsFailed(
                    'USER_CANCELLED',
                    'Payment failed or was cancelled by user'
                );
            }
        }

        return redirect()->route('wallet.index')->with('error', 'Payment failed. Please try again.');
    }

    /**
     * Handle SSLCommerz cancel callback.
     */
    public function sslcommerzCancel(Request $request)
    {
        $transactionId = $request->input('tran_id');
        
        if ($transactionId) {
            $transaction = PaymentTransaction::where('transaction_id', $transactionId)->first();
            
            if ($transaction) {
                $transaction->markAsCancelled('User cancelled the payment');
            }
        }

        return redirect()->route('wallet.index')->with('info', 'Payment was cancelled.');
    }

    /**
     * Handle SSLCommerz IPN (webhook).
     */
    public function sslcommerzIPN(Request $request)
    {
        $result = $this->paymentGateway->processWebhook('sslcommerz', $request->all());

        return response()->json([
            'status' => $result['success'] ? 'success' : 'failed',
            'message' => $result['message'] ?? '',
        ]);
    }

    /**
     * Initiate bKash payment.
     */
    public function initiateBKash(Request $request)
    {
        $validated = $request->validate([
            'amount' => 'required|numeric|min:100|max:100000',
            'customer_name' => 'required|string|max:255',
            'customer_email' => 'required|email',
            'customer_phone' => 'required|string|max:20',
        ]);

        $result = $this->paymentGateway->initiate('bkash', array_merge($validated, [
            'user_id' => $request->user()->id,
            'currency' => 'BDT',
            'gateway_fee' => $validated['amount'] * 0.018, // 1.8% fee
        ]));

        if (!$result['success']) {
            return back()->with('error', $result['message']);
        }

        return redirect($result['data']['bkash_url']);
    }

    /**
     * Handle bKash callback.
     */
    public function bkashCallback(Request $request)
    {
        $result = $this->paymentGateway->processCallback('bkash', $request->all());

        if ($result['success']) {
            return redirect()->route('wallet.index')->with('success', 'Payment successful! Your wallet has been credited.');
        }

        return redirect()->route('wallet.index')->with('error', $result['message'] ?? 'Payment failed');
    }

    /**
     * Handle bKash webhook.
     */
    public function bkashWebhook(Request $request)
    {
        $result = $this->paymentGateway->processWebhook('bkash', $request->all());

        return response()->json([
            'status' => $result['success'] ? 'success' : 'failed',
            'message' => $result['message'] ?? '',
        ]);
    }

    /**
     * Initiate Nagad payment.
     */
    public function initiateNagad(Request $request)
    {
        $validated = $request->validate([
            'amount' => 'required|numeric|min:100|max:100000',
            'customer_name' => 'required|string|max:255',
            'customer_email' => 'required|email',
            'customer_phone' => 'required|string|max:20',
        ]);

        $result = $this->paymentGateway->initiate('nagad', array_merge($validated, [
            'user_id' => $request->user()->id,
            'currency' => 'BDT',
            'gateway_fee' => $validated['amount'] * 0.016, // 1.6% fee
        ]));

        if (!$result['success']) {
            return back()->with('error', $result['message']);
        }

        return redirect($result['data']['callback_url']);
    }

    /**
     * Handle Nagad callback.
     */
    public function nagadCallback(Request $request)
    {
        $result = $this->paymentGateway->processCallback('nagad', $request->all());

        if ($result['success']) {
            return redirect()->route('wallet.index')->with('success', 'Payment successful! Your wallet has been credited.');
        }

        return redirect()->route('wallet.index')->with('error', $result['message'] ?? 'Payment failed');
    }

    /**
     * Handle Nagad webhook.
     */
    public function nagadWebhook(Request $request)
    {
        $result = $this->paymentGateway->processWebhook('nagad', $request->all());

        return response()->json([
            'status' => $result['success'] ? 'success' : 'failed',
            'message' => $result['message'] ?? '',
        ]);
    }

    /**
     * Show payment history.
     */
    public function index(Request $request)
    {
        $transactions = PaymentTransaction::where('user_id', $request->user()->id)
            ->with('wallet')
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return Inertia::render('Payment/Index', [
            'transactions' => $transactions,
        ]);
    }

    /**
     * Show payment details.
     */
    public function show(Request $request, PaymentTransaction $transaction)
    {
        // Ensure user can only view their own transactions
        if ($transaction->user_id !== $request->user()->id) {
            abort(403);
        }

        return Inertia::render('Payment/Show', [
            'transaction' => $transaction->load('wallet'),
        ]);
    }
}
