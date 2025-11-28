<?php

namespace App\Services;

use Exception;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class NagadService
{
    protected $merchantId;
    protected $merchantNumber;
    protected $publicKey;
    protected $privateKey;
    protected $baseUrl;
    protected $sandbox;

    public function __construct()
    {
        $this->merchantId = config('payment.nagad.merchant_id');
        $this->merchantNumber = config('payment.nagad.merchant_number');
        $this->publicKey = config('payment.nagad.public_key');
        $this->privateKey = config('payment.nagad.private_key');
        $this->baseUrl = config('payment.nagad.base_url');
        $this->sandbox = config('payment.nagad.sandbox', true);
    }

    /**
     * Generate signature
     */
    protected function generateSignature(string $data): string
    {
        $private_key = "-----BEGIN RSA PRIVATE KEY-----\n" . $this->privateKey . "\n-----END RSA PRIVATE KEY-----";
        
        openssl_sign($data, $signature, $private_key, OPENSSL_ALGO_SHA256);
        
        return base64_encode($signature);
    }

    /**
     * Encrypt data with public key
     */
    protected function encryptData(string $data): string
    {
        $public_key = "-----BEGIN PUBLIC KEY-----\n" . $this->publicKey . "\n-----END PUBLIC KEY-----";
        
        openssl_public_encrypt($data, $encrypted, $public_key);
        
        return base64_encode($encrypted);
    }

    /**
     * Create payment
     */
    public function createPayment(array $data): array
    {
        try {
            $orderId = $data['transaction_id'];
            $amount = (string) $data['amount'];
            $timestamp = now()->format('YmdHis');

            // Sensitive data
            $sensitiveData = [
                'merchantId' => $this->merchantId,
                'datetime' => $timestamp,
                'orderId' => $orderId,
                'challenge' => $this->generateRandomString(40),
            ];

            $postData = [
                'accountNumber' => $this->merchantNumber,
                'dateTime' => $timestamp,
                'sensitiveData' => $this->encryptData(json_encode($sensitiveData)),
                'signature' => $this->generateSignature(json_encode($sensitiveData)),
            ];

            if (config('payment.logging.enabled')) {
                Log::channel(config('payment.logging.channel'))
                    ->info('Nagad: Creating payment', [
                        'transaction_id' => $orderId,
                        'amount' => $amount,
                    ]);
            }

            // Initialize payment
            $initUrl = $this->baseUrl . '/api/dfs/check-out/initialize/' . $this->merchantId . '/' . $orderId;
            $response = Http::withHeaders([
                'X-KM-IP-V4' => request()->ip(),
                'X-KM-Client-Type' => 'PC_WEB',
            ])->post($initUrl, $postData);

            if ($response->failed()) {
                throw new Exception('Nagad API request failed');
            }

            $result = $response->json();

            if (!isset($result['sensitiveData']) || !isset($result['signature'])) {
                throw new Exception('Invalid response from Nagad');
            }

            // Complete payment request
            $paymentData = [
                'merchantId' => $this->merchantId,
                'orderId' => $orderId,
                'currencyCode' => '050', // BDT
                'amount' => $amount,
                'challenge' => $result['challenge'] ?? $this->generateRandomString(40),
            ];

            $completeData = [
                'paymentReferenceId' => $result['paymentReferenceId'],
                'callbackUrl' => config('payment.nagad.callback_url'),
                'sensitiveData' => $this->encryptData(json_encode($paymentData)),
                'signature' => $this->generateSignature(json_encode($paymentData)),
            ];

            $completeUrl = $this->baseUrl . '/api/dfs/check-out/complete/' . $result['paymentReferenceId'];
            $completeResponse = Http::withHeaders([
                'X-KM-IP-V4' => request()->ip(),
                'X-KM-Client-Type' => 'PC_WEB',
            ])->post($completeUrl, $completeData);

            if ($completeResponse->failed()) {
                throw new Exception('Payment completion request failed');
            }

            $completeResult = $completeResponse->json();

            if (!isset($completeResult['callBackUrl'])) {
                throw new Exception('Payment URL not received');
            }

            return [
                'success' => true,
                'payment_reference_id' => $result['paymentReferenceId'],
                'callback_url' => $completeResult['callBackUrl'],
                'data' => $completeResult,
            ];
        } catch (Exception $e) {
            if (config('payment.logging.enabled')) {
                Log::channel(config('payment.logging.channel'))
                    ->error('Nagad: Payment creation failed', [
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
     * Verify payment
     */
    public function verifyPayment(string $paymentReferenceId): array
    {
        try {
            if (config('payment.logging.enabled')) {
                Log::channel(config('payment.logging.channel'))
                    ->info('Nagad: Verifying payment', ['payment_reference_id' => $paymentReferenceId]);
            }

            $url = $this->baseUrl . '/api/dfs/verify/payment/' . $paymentReferenceId;
            
            $response = Http::withHeaders([
                'X-KM-IP-V4' => request()->ip(),
                'X-KM-Client-Type' => 'PC_WEB',
            ])->get($url);

            if ($response->failed()) {
                throw new Exception('Verification request failed');
            }

            $result = $response->json();

            if (!isset($result['status'])) {
                throw new Exception('Invalid verification response');
            }

            return [
                'success' => $result['status'] === 'Success',
                'status' => $result['status'],
                'data' => $result,
            ];
        } catch (Exception $e) {
            if (config('payment.logging.enabled')) {
                Log::channel(config('payment.logging.channel'))
                    ->error('Nagad: Verification failed', [
                        'error' => $e->getMessage(),
                        'payment_reference_id' => $paymentReferenceId,
                    ]);
            }

            return [
                'success' => false,
                'message' => $e->getMessage(),
            ];
        }
    }

    /**
     * Generate random string
     */
    protected function generateRandomString(int $length = 40): string
    {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        
        return $randomString;
    }
}
