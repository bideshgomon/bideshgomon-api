<?php

namespace App\Services;

use Exception;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SSLCommerzService
{
    protected $storeId;
    protected $storePassword;
    protected $apiUrl;
    protected $validationUrl;
    protected $sandbox;

    public function __construct()
    {
        $this->storeId = config('payment.sslcommerz.store_id');
        $this->storePassword = config('payment.sslcommerz.store_password');
        $this->sandbox = config('payment.sslcommerz.sandbox', true);
        $this->apiUrl = config('payment.sslcommerz.api_url');
        $this->validationUrl = config('payment.sslcommerz.validation_url');
    }

    /**
     * Initialize payment session
     */
    public function initiate(array $data): array
    {
        try {
            $postData = [
                'store_id' => $this->storeId,
                'store_passwd' => $this->storePassword,
                'total_amount' => $data['amount'],
                'currency' => $data['currency'] ?? 'BDT',
                'tran_id' => $data['transaction_id'],
                'success_url' => config('payment.sslcommerz.success_url'),
                'fail_url' => config('payment.sslcommerz.fail_url'),
                'cancel_url' => config('payment.sslcommerz.cancel_url'),
                'ipn_url' => config('payment.sslcommerz.ipn_url'),
                
                // Customer Information
                'cus_name' => $data['customer_name'],
                'cus_email' => $data['customer_email'],
                'cus_phone' => $data['customer_phone'],
                'cus_add1' => $data['customer_address'] ?? 'N/A',
                'cus_city' => $data['customer_city'] ?? 'Dhaka',
                'cus_country' => $data['customer_country'] ?? 'Bangladesh',
                'cus_postcode' => $data['customer_postcode'] ?? '1000',
                
                // Product Information
                'product_name' => $data['product_name'] ?? 'Wallet Recharge',
                'product_category' => $data['product_category'] ?? 'Service',
                'product_profile' => $data['product_profile'] ?? 'general',
                
                // Shipment Information (Required)
                'shipping_method' => 'NO',
                'num_of_item' => 1,
                
                // Additional Information
                'value_a' => $data['metadata']['user_id'] ?? null,
                'value_b' => $data['metadata']['wallet_id'] ?? null,
                'value_c' => $data['metadata']['type'] ?? null,
                'value_d' => $data['metadata']['reference_id'] ?? null,
            ];

            if (config('payment.logging.enabled')) {
                Log::channel(config('payment.logging.channel'))
                    ->info('SSLCommerz: Initiating payment', [
                        'transaction_id' => $data['transaction_id'],
                        'amount' => $data['amount'],
                    ]);
            }

            $response = Http::asForm()->post($this->apiUrl . '/gwprocess/v4/api.php', $postData);

            if ($response->failed()) {
                throw new Exception('SSLCommerz API request failed');
            }

            $result = $response->json();

            if (!isset($result['status']) || $result['status'] !== 'SUCCESS') {
                throw new Exception($result['failedreason'] ?? 'Payment initiation failed');
            }

            return [
                'success' => true,
                'gateway_url' => $result['GatewayPageURL'],
                'session_key' => $result['sessionkey'],
                'data' => $result,
            ];
        } catch (Exception $e) {
            if (config('payment.logging.enabled')) {
                Log::channel(config('payment.logging.channel'))
                    ->error('SSLCommerz: Payment initiation failed', [
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
     * Validate payment transaction
     */
    public function validate(string $transactionId, float $amount, string $currency = 'BDT'): array
    {
        try {
            $postData = [
                'val_id' => $transactionId,
                'store_id' => $this->storeId,
                'store_passwd' => $this->storePassword,
                'format' => 'json',
            ];

            if (config('payment.logging.enabled')) {
                Log::channel(config('payment.logging.channel'))
                    ->info('SSLCommerz: Validating transaction', [
                        'transaction_id' => $transactionId,
                    ]);
            }

            $response = Http::asForm()->get($this->validationUrl, $postData);

            if ($response->failed()) {
                throw new Exception('Validation API request failed');
            }

            $result = $response->json();

            if (!isset($result['status']) || $result['status'] !== 'VALID') {
                throw new Exception('Transaction validation failed');
            }

            // Verify amount and currency
            if ($result['currency_amount'] != $amount) {
                throw new Exception('Amount mismatch');
            }

            if ($result['currency_type'] !== $currency) {
                throw new Exception('Currency mismatch');
            }

            return [
                'success' => true,
                'data' => $result,
            ];
        } catch (Exception $e) {
            if (config('payment.logging.enabled')) {
                Log::channel(config('payment.logging.channel'))
                    ->error('SSLCommerz: Validation failed', [
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

    /**
     * Refund transaction
     */
    public function refund(string $bankTransactionId, float $refundAmount, string $reason = ''): array
    {
        try {
            $postData = [
                'bank_tran_id' => $bankTransactionId,
                'store_id' => $this->storeId,
                'store_passwd' => $this->storePassword,
                'refund_amount' => $refundAmount,
                'refund_remarks' => $reason,
                'format' => 'json',
            ];

            if (config('payment.logging.enabled')) {
                Log::channel(config('payment.logging.channel'))
                    ->info('SSLCommerz: Initiating refund', [
                        'bank_transaction_id' => $bankTransactionId,
                        'amount' => $refundAmount,
                    ]);
            }

            $response = Http::asForm()->post($this->apiUrl . '/validator/api/merchantTransIDvalidationAPI.php', $postData);

            if ($response->failed()) {
                throw new Exception('Refund API request failed');
            }

            $result = $response->json();

            return [
                'success' => isset($result['status']) && $result['status'] === 'success',
                'data' => $result,
                'message' => $result['errorReason'] ?? 'Refund processed',
            ];
        } catch (Exception $e) {
            if (config('payment.logging.enabled')) {
                Log::channel(config('payment.logging.channel'))
                    ->error('SSLCommerz: Refund failed', [
                        'error' => $e->getMessage(),
                        'bank_transaction_id' => $bankTransactionId,
                    ]);
            }

            return [
                'success' => false,
                'message' => $e->getMessage(),
            ];
        }
    }

    /**
     * Check transaction status
     */
    public function checkStatus(string $transactionId): array
    {
        try {
            $queryData = [
                'sessionkey' => $transactionId,
                'store_id' => $this->storeId,
                'store_passwd' => $this->storePassword,
            ];

            $response = Http::get($this->apiUrl . '/validator/api/merchantTransIDvalidationAPI.php', $queryData);

            if ($response->failed()) {
                throw new Exception('Status check API request failed');
            }

            $result = $response->json();

            return [
                'success' => true,
                'status' => $result['status'] ?? 'unknown',
                'data' => $result,
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => $e->getMessage(),
            ];
        }
    }
}
