<?php

namespace App\Services;

use App\Models\PaymentTransaction;
use App\Models\Wallet;
use App\Models\WalletTransaction;
use App\Services\SSLCommerzService;
use App\Services\BKashService;
use App\Services\NagadService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class PaymentGatewayService
{
    protected SSLCommerzService $sslcommerz;
    protected BKashService $bkash;
    protected NagadService $nagad;

    public function __construct(
        SSLCommerzService $sslcommerz,
        BKashService $bkash,
        NagadService $nagad
    ) {
        $this->sslcommerz = $sslcommerz;
        $this->bkash = $bkash;
        $this->nagad = $nagad;
    }

    /**
     * Initiate a payment through the specified gateway.
     */
    public function initiate(string $gateway, array $data): array
    {
        try {
            // Validate gateway
            if (!in_array($gateway, ['sslcommerz', 'bkash', 'nagad'])) {
                return [
                    'success' => false,
                    'message' => 'Invalid payment gateway',
                ];
            }

            // Create transaction record
            $transaction = PaymentTransaction::create([
                'user_id' => $data['user_id'],
                'wallet_id' => $data['wallet_id'] ?? null,
                'transaction_id' => 'TXN-' . strtoupper(Str::random(16)),
                'gateway' => $gateway,
                'amount' => $data['amount'],
                'currency' => $data['currency'] ?? 'BDT',
                'gateway_fee' => $data['gateway_fee'] ?? 0,
                'net_amount' => $data['amount'] - ($data['gateway_fee'] ?? 0),
                'status' => 'pending',
                'payment_method' => $gateway,
                'customer_name' => $data['customer_name'],
                'customer_email' => $data['customer_email'],
                'customer_phone' => $data['customer_phone'],
                'product_name' => $data['product_name'] ?? 'Wallet Top-up',
                'description' => $data['description'] ?? 'Add funds to wallet',
                'metadata' => $data['metadata'] ?? [],
            ]);

            Log::info("Payment initiated", [
                'transaction_id' => $transaction->transaction_id,
                'gateway' => $gateway,
                'amount' => $data['amount'],
            ]);

            // Initiate payment with gateway
            $result = match($gateway) {
                'sslcommerz' => $this->initiateSSLCommerz($transaction, $data),
                'bkash' => $this->initiateBKash($transaction, $data),
                'nagad' => $this->initiateNagad($transaction, $data),
            };

            if (!$result['success']) {
                $transaction->markAsFailed(
                    $result['error_code'] ?? 'INITIATION_FAILED',
                    $result['message'] ?? 'Failed to initiate payment'
                );
            }

            return array_merge($result, [
                'transaction_id' => $transaction->transaction_id,
            ]);

        } catch (\Exception $e) {
            Log::error("Payment initiation failed", [
                'gateway' => $gateway,
                'error' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'message' => 'Payment initiation failed: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Process callback from payment gateway.
     */
    public function processCallback(string $gateway, array $data): array
    {
        try {
            $result = match($gateway) {
                'sslcommerz' => $this->processSSLCommerzCallback($data),
                'bkash' => $this->processBKashCallback($data),
                'nagad' => $this->processNagadCallback($data),
                default => ['success' => false, 'message' => 'Invalid gateway'],
            };

            if ($result['success'] && isset($result['transaction'])) {
                $this->creditWallet($result['transaction']);
            }

            return $result;

        } catch (\Exception $e) {
            Log::error("Callback processing failed", [
                'gateway' => $gateway,
                'error' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'message' => 'Callback processing failed: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Process webhook from payment gateway.
     */
    public function processWebhook(string $gateway, array $data): array
    {
        try {
            $result = match($gateway) {
                'sslcommerz' => $this->processSSLCommerzWebhook($data),
                'bkash' => $this->processBKashWebhook($data),
                'nagad' => $this->processNagadWebhook($data),
                default => ['success' => false, 'message' => 'Invalid gateway'],
            };

            if ($result['success'] && isset($result['transaction'])) {
                $this->creditWallet($result['transaction']);
            }

            return $result;

        } catch (\Exception $e) {
            Log::error("Webhook processing failed", [
                'gateway' => $gateway,
                'error' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'message' => 'Webhook processing failed: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Process refund for a transaction.
     */
    public function refund(PaymentTransaction $transaction, float $amount, string $reason = ''): array
    {
        try {
            if (!$transaction->isSuccessful()) {
                return [
                    'success' => false,
                    'message' => 'Can only refund successful transactions',
                ];
            }

            if ($amount > $transaction->amount) {
                return [
                    'success' => false,
                    'message' => 'Refund amount cannot exceed transaction amount',
                ];
            }

            $result = match($transaction->gateway) {
                'sslcommerz' => $this->sslcommerz->refund(
                    $transaction->gateway_transaction_id,
                    $amount,
                    $reason
                ),
                'bkash' => $this->bkash->refund(
                    $transaction->gateway_transaction_id,
                    $amount,
                    $reason
                ),
                default => ['success' => false, 'message' => 'Refund not supported for this gateway'],
            };

            if ($result['success']) {
                $transaction->markAsRefunded(
                    $amount,
                    $result['data']['refund_reference'] ?? null,
                    $result['data'] ?? []
                );

                // Debit wallet
                $this->debitWallet($transaction, $amount);
            }

            return $result;

        } catch (\Exception $e) {
            Log::error("Refund processing failed", [
                'transaction_id' => $transaction->transaction_id,
                'error' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'message' => 'Refund processing failed: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Initiate SSLCommerz payment.
     */
    protected function initiateSSLCommerz(PaymentTransaction $transaction, array $data): array
    {
        $result = $this->sslcommerz->initiate([
            'total_amount' => $transaction->amount,
            'currency' => $transaction->currency,
            'tran_id' => $transaction->transaction_id,
            'product_name' => $transaction->product_name,
            'product_category' => 'wallet',
            'cus_name' => $transaction->customer_name,
            'cus_email' => $transaction->customer_email,
            'cus_phone' => $transaction->customer_phone,
            'cus_add1' => $data['address'] ?? 'N/A',
            'cus_city' => $data['city'] ?? 'Dhaka',
            'cus_country' => $data['country'] ?? 'Bangladesh',
            'value_a' => $transaction->user_id,
            'value_b' => $transaction->wallet_id,
            'value_c' => 'wallet_topup',
            'value_d' => $transaction->transaction_id,
        ]);

        if ($result['success']) {
            $transaction->update([
                'callback_url' => $result['data']['gateway_url'] ?? null,
                'gateway_response' => $result['data'] ?? [],
            ]);
        }

        return $result;
    }

    /**
     * Initiate bKash payment.
     */
    protected function initiateBKash(PaymentTransaction $transaction, array $data): array
    {
        $result = $this->bkash->createPayment([
            'amount' => $transaction->amount,
            'invoice_number' => $transaction->transaction_id,
            'intent' => 'sale',
        ]);

        if ($result['success']) {
            $transaction->update([
                'payment_reference_id' => $result['data']['payment_id'] ?? null,
                'callback_url' => $result['data']['bkash_url'] ?? null,
                'gateway_response' => $result['data'] ?? [],
            ]);
        }

        return $result;
    }

    /**
     * Initiate Nagad payment.
     */
    protected function initiateNagad(PaymentTransaction $transaction, array $data): array
    {
        $result = $this->nagad->createPayment([
            'amount' => $transaction->amount,
            'invoice_number' => $transaction->transaction_id,
            'merchant_additional_info' => json_encode([
                'user_id' => $transaction->user_id,
                'wallet_id' => $transaction->wallet_id,
            ]),
        ]);

        if ($result['success']) {
            $transaction->update([
                'payment_reference_id' => $result['data']['payment_reference_id'] ?? null,
                'callback_url' => $result['data']['callback_url'] ?? null,
                'gateway_response' => $result['data'] ?? [],
            ]);
        }

        return $result;
    }

    /**
     * Process SSLCommerz callback.
     */
    protected function processSSLCommerzCallback(array $data): array
    {
        $transactionId = $data['tran_id'] ?? null;
        
        if (!$transactionId) {
            return ['success' => false, 'message' => 'Transaction ID not found'];
        }

        $transaction = PaymentTransaction::where('transaction_id', $transactionId)->first();
        
        if (!$transaction) {
            return ['success' => false, 'message' => 'Transaction not found'];
        }

        // Validate with SSLCommerz
        $result = $this->sslcommerz->validate(
            $transactionId,
            $transaction->amount,
            $transaction->currency
        );

        if ($result['success']) {
            $transaction->markAsPaid(
                $data['bank_tran_id'] ?? null,
                $result['data'] ?? []
            );

            return [
                'success' => true,
                'message' => 'Payment successful',
                'transaction' => $transaction,
            ];
        }

        $transaction->markAsFailed(
            $result['error_code'] ?? 'VALIDATION_FAILED',
            $result['message'] ?? 'Payment validation failed',
            $result['data'] ?? []
        );

        return $result;
    }

    /**
     * Process bKash callback.
     */
    protected function processBKashCallback(array $data): array
    {
        $paymentId = $data['paymentID'] ?? null;
        
        if (!$paymentId) {
            return ['success' => false, 'message' => 'Payment ID not found'];
        }

        $transaction = PaymentTransaction::where('payment_reference_id', $paymentId)->first();
        
        if (!$transaction) {
            return ['success' => false, 'message' => 'Transaction not found'];
        }

        // Execute payment
        $result = $this->bkash->executePayment($paymentId);

        if ($result['success']) {
            $transaction->markAsPaid(
                $result['data']['trxID'] ?? null,
                $result['data'] ?? []
            );

            return [
                'success' => true,
                'message' => 'Payment successful',
                'transaction' => $transaction,
            ];
        }

        $transaction->markAsFailed(
            $result['error_code'] ?? 'EXECUTION_FAILED',
            $result['message'] ?? 'Payment execution failed',
            $result['data'] ?? []
        );

        return $result;
    }

    /**
     * Process Nagad callback.
     */
    protected function processNagadCallback(array $data): array
    {
        $paymentReferenceId = $data['payment_ref_id'] ?? null;
        
        if (!$paymentReferenceId) {
            return ['success' => false, 'message' => 'Payment reference ID not found'];
        }

        $transaction = PaymentTransaction::where('payment_reference_id', $paymentReferenceId)->first();
        
        if (!$transaction) {
            return ['success' => false, 'message' => 'Transaction not found'];
        }

        // Verify payment
        $result = $this->nagad->verifyPayment($paymentReferenceId);

        if ($result['success']) {
            $transaction->markAsPaid(
                $result['data']['issuerPaymentRefNo'] ?? null,
                $result['data'] ?? []
            );

            return [
                'success' => true,
                'message' => 'Payment successful',
                'transaction' => $transaction,
            ];
        }

        $transaction->markAsFailed(
            $result['error_code'] ?? 'VERIFICATION_FAILED',
            $result['message'] ?? 'Payment verification failed',
            $result['data'] ?? []
        );

        return $result;
    }

    /**
     * Process SSLCommerz webhook (IPN).
     */
    protected function processSSLCommerzWebhook(array $data): array
    {
        // IPN uses same logic as callback
        return $this->processSSLCommerzCallback($data);
    }

    /**
     * Process bKash webhook.
     */
    protected function processBKashWebhook(array $data): array
    {
        // Webhook uses same logic as callback
        return $this->processBKashCallback($data);
    }

    /**
     * Process Nagad webhook.
     */
    protected function processNagadWebhook(array $data): array
    {
        // Webhook uses same logic as callback
        return $this->processNagadCallback($data);
    }

    /**
     * Credit wallet after successful payment.
     */
    protected function creditWallet(PaymentTransaction $transaction): void
    {
        DB::transaction(function () use ($transaction) {
            $wallet = Wallet::where('user_id', $transaction->user_id)->first();
            
            if (!$wallet) {
                $wallet = Wallet::create([
                    'user_id' => $transaction->user_id,
                    'balance' => 0,
                    'currency' => $transaction->currency,
                ]);
            }

            // Credit wallet
            $wallet->increment('balance', $transaction->net_amount);

            // Create wallet transaction
            WalletTransaction::create([
                'wallet_id' => $wallet->id,
                'type' => 'credit',
                'amount' => $transaction->net_amount,
                'description' => "Payment via {$transaction->gateway}",
                'reference_type' => PaymentTransaction::class,
                'reference_id' => $transaction->id,
                'balance_after' => $wallet->balance,
                'status' => 'completed',
            ]);

            // Update payment transaction with wallet_id
            $transaction->update(['wallet_id' => $wallet->id]);

            Log::info("Wallet credited", [
                'transaction_id' => $transaction->transaction_id,
                'amount' => $transaction->net_amount,
                'wallet_balance' => $wallet->balance,
            ]);
        });
    }

    /**
     * Debit wallet for refund.
     */
    protected function debitWallet(PaymentTransaction $transaction, float $amount): void
    {
        DB::transaction(function () use ($transaction, $amount) {
            $wallet = $transaction->wallet;
            
            if (!$wallet) {
                Log::error("Wallet not found for refund", [
                    'transaction_id' => $transaction->transaction_id,
                ]);
                return;
            }

            // Debit wallet
            $wallet->decrement('balance', $amount);

            // Create wallet transaction
            WalletTransaction::create([
                'wallet_id' => $wallet->id,
                'type' => 'debit',
                'amount' => $amount,
                'description' => "Refund for {$transaction->gateway} payment",
                'reference_type' => PaymentTransaction::class,
                'reference_id' => $transaction->id,
                'balance_after' => $wallet->balance,
                'status' => 'completed',
            ]);

            Log::info("Wallet debited for refund", [
                'transaction_id' => $transaction->transaction_id,
                'amount' => $amount,
                'wallet_balance' => $wallet->balance,
            ]);
        });
    }
}
