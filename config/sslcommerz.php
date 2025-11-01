<?php

// config for SslCommerz
$apiDomain = env('SSLCZ_TESTMODE', true) // Use SSLCZ_TESTMODE from env_example
    ? 'https://sandbox.sslcommerz.com'
    : 'https://securepay.sslcommerz.com';

return [
    'apiCredentials' => [
        'store_id' => env('SSLCZ_STORE_ID'),
        'store_password' => env('SSLCZ_STORE_PASSWORD'),
    ],
    'apiUrl' => [
        'make_payment' => '/gwprocess/v4/api.php',
        'transaction_status' => '/validator/api/merchantTransIDvalidationAPI.php',
        'order_validate' => '/validator/api/validationserverAPI.php',
        'refund_payment' => '/validator/api/merchantTransIDvalidationAPI.php',
        'refund_status' => '/validator/api/merchantTransIDvalidationAPI.php',
    ],
    'apiDomain' => $apiDomain,
    'connect_from_localhost' => env('SSLCZ_TESTMODE', true), // Use SSLCZ_TESTMODE

    // Define the callback URLs (we will create these routes)
    'success_url' => '/sslcommerz/success',
    'fail_url' => '/sslcommerz/fail',
    'cancel_url' => '/sslcommerz/cancel',
    'ipn_url' => '/sslcommerz/ipn',
];
