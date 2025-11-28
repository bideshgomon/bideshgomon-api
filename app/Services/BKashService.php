<?php

namespace App\Services;

use Exception;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class BKashService
{
    protected $appKey;
    protected $appSecret;
    protected $username;
    protected $password;
    protected $baseUrl;
    protected $sandbox;

    public function __construct()
    {
        $this->appKey = config('payment.bkash.app_key');
        $this->appSecret = config('payment.bkash.app_secret');
        $this->username = config('payment.bkash.username');
        $this->password = config('payment.bkash.password');
        $this->baseUrl = config('payment.bkash.base_url');
        $this->sandbox = config('payment.bkash.sandbox', true);
    }

    /**
     * Get access token (grant token)
     */
    protected function getToken(): ?string
    {
        $cacheKey = 'bkash_token';

        // Check if token exists in cache
        if (Cache::has($cacheKey)) {
            return Cache::get($cacheKey);
        }

        try {
            $response = Http::withHeaders([
                'username' => $this->username,
                'password' => $this->password,
            ])->post($this->baseUrl . '/checkout/token/grant', [
                'app_key' => $this->appKey,
                'app_secret' => $this->appSecret,
            ]);

            if ($response->failed()) {
                throw new Exception('Failed to get bKash token');
            }

            $result = $response->json();

            if (!isset($result['id_token'])) {
                throw new Exception('Invalid token response from bKash');
            }

            // Cache token for 50 minutes (tokens expire in 1 hour)
            Cache::put($cacheKey, $result['id_token'], now()->addMinutes(50));

            return $result['id_token'];
        } catch (Exception $e) {
            if (config('payment.logging.enabled')) {
                Log::channel(config('payment.logging.channel'))
                    ->error('bKash: Token generation failed', ['error' => $e->getMessage()]);
            }
            return null;
        }
    }

    /**
     * Create payment
     */
    public function createPayment(array $data): array
    {
        try {
            $token = $this->getToken();
            if (!$token) {
                throw new Exception('Failed to get authentication token');
            }

            $postData = [
                'mode' => '0011', // Tokenized checkout
                'payerReference' => $data['customer_phone'],
                'callbackURL' => config('payment.bkash.callback_url'),
                'amount' => (string) $data['amount'],
                'currency' => 'BDT',
                'intent' => 'sale',
                'merchantInvoiceNumber' => $data['transaction_id'],
            ];

            if (config('payment.logging.enabled')) {
                Log::channel(config('payment.logging.channel'))
                    ->info('bKash: Creating payment', [
                        'transaction_id' => $data['transaction_id'],
                        'amount' => $data['amount'],
                    ]);
            }

            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $token,
                'X-APP-Key' => $this->appKey,
            ])->post($this->baseUrl . '/checkout/payment/create', $postData);

            if ($response->failed()) {
                throw new Exception('bKash API request failed');
            }

            $result = $response->json();

            if (!isset($result['paymentID'])) {
                throw new Exception($result['errorMessage'] ?? 'Payment creation failed');
            }

            return [
                'success' => true,
                'payment_id' => $result['paymentID'],
                'bkash_url' => $result['bkashURL'] ?? null,
                'callback_url' => $result['callbackURL'] ?? null,
                'data' => $result,
            ];
        } catch (Exception $e) {
            if (config('payment.logging.enabled')) {
                Log::channel(config('payment.logging.channel'))
                    ->error('bKash: Payment creation failed', [
                        'error' => $e->getMessage(),
                        'transaction_id' => $data['transaction_id'] ?? null,
                    ]);
            }

            return [
                'success' => false,
                'message' => $e->getMessage(),
            ];
        }
    }

    /**
     * Execute payment
     */
    public function executePayment(string $paymentId): array
    {
        try {
            $token = $this->getToken();
            if (!$token) {
                throw new Exception('Failed to get authentication token');
            }

            if (config('payment.logging.enabled')) {
                Log::channel(config('payment.logging.channel'))
                    ->info('bKash: Executing payment', ['payment_id' => $paymentId]);
            }

            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $token,
                'X-APP-Key' => $this->appKey,
            ])->post($this->baseUrl . '/checkout/payment/execute', [
                'paymentID' => $paymentId,
            ]);

            if ($response->failed()) {
                throw new Exception('Payment execution request failed');
            }

            $result = $response->json();

            if (!isset($result['transactionStatus']) || $result['transactionStatus'] !== 'Completed') {
                throw new Exception($result['errorMessage'] ?? 'Payment execution failed');
            }

            return [
                'success' => true,
                'transaction_id' => $result['trxID'],
                'data' => $result,
            ];
        } catch (Exception $e) {
            if (config('payment.logging.enabled')) {
                Log::channel(config('payment.logging.channel'))
                    ->error('bKash: Payment execution failed', [
                        'error' => $e->getMessage(),
                        'payment_id' => $paymentId,
                    ]);
            }

            return [
                'success' => false,
                'message' => $e->getMessage(),
            ];
        }
    }

    /**
     * Query payment status
     */
    public function queryPayment(string $paymentId): array
    {
        try {
            $token = $this->getToken();
            if (!$token) {
                throw new Exception('Failed to get authentication token');
            }

            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $token,
                'X-APP-Key' => $this->appKey,
            ])->post($this->baseUrl . '/checkout/payment/query', [
                'paymentID' => $paymentId,
            ]);

            if ($response->failed()) {
                throw new Exception('Query request failed');
            }

            $result = $response->json();

            return [
                'success' => true,
                'status' => $result['transactionStatus'] ?? 'unknown',
                'data' => $result,
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => $e->getMessage(),
            ];
        }
    }

    /**
     * Refund transaction
     */
    public function refund(string $transactionId, float $amount, string $reason = ''): array
    {
        try {
            $token = $this->getToken();
            if (!$token) {
                throw new Exception('Failed to get authentication token');
            }

            if (config('payment.logging.enabled')) {
                Log::channel(config('payment.logging.channel'))
                    ->info('bKash: Initiating refund', [
                        'transaction_id' => $transactionId,
                        'amount' => $amount,
                    ]);
            }

            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $token,
                'X-APP-Key' => $this->appKey,
            ])->post($this->baseUrl . '/checkout/payment/refund', [
                'paymentID' => $transactionId,
                'amount' => (string) $amount,
                'trxID' => $transactionId,
                'sku' => 'refund',
                'reason' => $reason ?: 'Customer requested refund',
            ]);

            if ($response->failed()) {
                throw new Exception('Refund request failed');
            }

            $result = $response->json();

            return [
                'success' => isset($result['transactionStatus']) && $result['transactionStatus'] === 'Completed',
                'refund_id' => $result['refundTrxID'] ?? null,
                'data' => $result,
            ];
        } catch (Exception $e) {
            if (config('payment.logging.enabled')) {
                Log::channel(config('payment.logging.channel'))
                    ->error('bKash: Refund failed', [
                        'error' => $e->getMessage(),
                        'transaction_id' => $transactionId,
                    ]);
            }

            return [
                'success' => false,
                'message' => $e->getMessage(),
            ];
        }
    }
}
