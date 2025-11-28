<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Default Payment Gateway
    |--------------------------------------------------------------------------
    |
    | This option defines the default payment gateway that will be used
    | by the application. You can set this to 'sslcommerz' or 'bkash'.
    |
    */

    'default' => env('PAYMENT_GATEWAY', 'sslcommerz'),

    /*
    |--------------------------------------------------------------------------
    | Payment Currency
    |--------------------------------------------------------------------------
    |
    | The default currency for payments. Set to BDT for Bangladesh.
    |
    */

    'currency' => env('PAYMENT_CURRENCY', 'BDT'),

    /*
    |--------------------------------------------------------------------------
    | SSLCommerz Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration for SSLCommerz payment gateway
    |
    */

    'sslcommerz' => [
        'store_id' => env('SSLCOMMERZ_STORE_ID'),
        'store_password' => env('SSLCOMMERZ_STORE_PASSWORD'),
        'api_url' => env('SSLCOMMERZ_SANDBOX', true) 
            ? 'https://sandbox.sslcommerz.com' 
            : 'https://securepay.sslcommerz.com',
        'validation_url' => env('SSLCOMMERZ_SANDBOX', true)
            ? 'https://sandbox.sslcommerz.com/validator/api/validationserverAPI.php'
            : 'https://securepay.sslcommerz.com/validator/api/validationserverAPI.php',
        'success_url' => env('APP_URL') . '/payment/success',
        'fail_url' => env('APP_URL') . '/payment/fail',
        'cancel_url' => env('APP_URL') . '/payment/cancel',
        'ipn_url' => env('APP_URL') . '/payment/ipn',
        'sandbox' => env('SSLCOMMERZ_SANDBOX', true),
    ],

    /*
    |--------------------------------------------------------------------------
    | bKash Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration for bKash payment gateway
    |
    */

    'bkash' => [
        'app_key' => env('BKASH_APP_KEY'),
        'app_secret' => env('BKASH_APP_SECRET'),
        'username' => env('BKASH_USERNAME'),
        'password' => env('BKASH_PASSWORD'),
        'base_url' => env('BKASH_SANDBOX', true)
            ? 'https://tokenized.sandbox.bka.sh/v1.2.0-beta'
            : 'https://tokenized.pay.bka.sh/v1.2.0-beta',
        'sandbox' => env('BKASH_SANDBOX', true),
        'callback_url' => env('APP_URL') . '/payment/bkash/callback',
        'webhook_url' => env('APP_URL') . '/payment/bkash/webhook',
    ],

    /*
    |--------------------------------------------------------------------------
    | Nagad Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration for Nagad payment gateway
    |
    */

    'nagad' => [
        'merchant_id' => env('NAGAD_MERCHANT_ID'),
        'merchant_number' => env('NAGAD_MERCHANT_NUMBER'),
        'public_key' => env('NAGAD_PUBLIC_KEY'),
        'private_key' => env('NAGAD_PRIVATE_KEY'),
        'base_url' => env('NAGAD_SANDBOX', true)
            ? 'http://sandbox.mynagad.com:10080/remote-payment-gateway-1.0'
            : 'https://api.mynagad.com/api/dfs',
        'sandbox' => env('NAGAD_SANDBOX', true),
        'callback_url' => env('APP_URL') . '/payment/nagad/callback',
        'webhook_url' => env('APP_URL') . '/payment/nagad/webhook',
    ],

    /*
    |--------------------------------------------------------------------------
    | Payment Settings
    |--------------------------------------------------------------------------
    |
    | General payment settings
    |
    */

    'settings' => [
        // Minimum payment amount in BDT
        'min_amount' => env('PAYMENT_MIN_AMOUNT', 10),

        // Maximum payment amount in BDT
        'max_amount' => env('PAYMENT_MAX_AMOUNT', 500000),

        // Payment timeout in minutes
        'timeout' => env('PAYMENT_TIMEOUT', 30),

        // Auto refund enabled
        'auto_refund' => env('PAYMENT_AUTO_REFUND', false),

        // Transaction fee percentage
        'transaction_fee' => env('PAYMENT_TRANSACTION_FEE', 0),

        // Enable wallet payments
        'wallet_enabled' => env('PAYMENT_WALLET_ENABLED', true),

        // Minimum wallet balance
        'wallet_min_balance' => env('PAYMENT_WALLET_MIN_BALANCE', 0),
    ],

    /*
    |--------------------------------------------------------------------------
    | Payment Methods
    |--------------------------------------------------------------------------
    |
    | Available payment methods
    |
    */

    'methods' => [
        'sslcommerz' => [
            'name' => 'SSLCommerz',
            'description' => 'Pay with Visa, MasterCard, AMEX, Mobile Banking, Internet Banking',
            'icon' => 'credit-card',
            'enabled' => env('SSLCOMMERZ_ENABLED', true),
        ],
        'bkash' => [
            'name' => 'bKash',
            'description' => 'Pay with bKash mobile wallet',
            'icon' => 'mobile',
            'enabled' => env('BKASH_ENABLED', true),
        ],
        'nagad' => [
            'name' => 'Nagad',
            'description' => 'Pay with Nagad mobile wallet',
            'icon' => 'mobile',
            'enabled' => env('NAGAD_ENABLED', true),
        ],
        'wallet' => [
            'name' => 'Wallet',
            'description' => 'Pay from your wallet balance',
            'icon' => 'wallet',
            'enabled' => env('WALLET_ENABLED', true),
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Payment Statuses
    |--------------------------------------------------------------------------
    |
    | Available payment statuses
    |
    */

    'statuses' => [
        'pending' => 'Pending',
        'processing' => 'Processing',
        'completed' => 'Completed',
        'failed' => 'Failed',
        'cancelled' => 'Cancelled',
        'refunded' => 'Refunded',
        'partially_refunded' => 'Partially Refunded',
    ],

];
