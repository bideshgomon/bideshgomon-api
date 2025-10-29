<?php

namespace App\Http;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as Middleware;

class VerifyCsrfToken extends Middleware
{
    /**
     * The URIs that should be excluded from CSRF verification.
     *
     * @var array<int, string>
     */
    protected $except = [
        // Add SSLCommerz callback routes here to prevent CSRF errors
        '/sslcommerz/success',
        '/sslcommerz/fail',
        '/sslcommerz/cancel',
        '/sslcommerz/ipn',
    ];
}