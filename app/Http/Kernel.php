<?php

namespace App\Http;

use Illuminate\Foundation\Http\Kernel as HttpKernel;

class Kernel extends HttpKernel
{
    /**
     * The application's global HTTP middleware stack.
     *
     * These middleware run during every request to your application.
     *
     * @var array<int, class-string|string>
     */
    protected $middleware = [
        // \App\Http\Middleware\TrustHosts::class,
        \App\Http\Middleware\TrustProxies::class,
        \Illuminate\Http\Middleware\HandleCors::class, // Handles Cross-Origin Resource Sharing
        \App\Http\Middleware\PreventRequestsDuringMaintenance::class, // Shows maintenance page if down
        \Illuminate\Foundation\Http\Middleware\ValidatePostSize::class, // Checks max post size
        \App\Http\Middleware\TrimStrings::class, // Trims whitespace from input
        \Illuminate\Foundation\Http\Middleware\ConvertEmptyStringsToNull::class, // Converts empty strings to null
    ];

    /**
     * The application's route middleware groups.
     *
     * @var array<string, array<int, class-string|string>>
     */
    protected $middlewareGroups = [
        'web' => [
            \App\Http\Middleware\EncryptCookies::class, // Encrypts/decrypts cookies
            \Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse::class, // Adds queued cookies to response
            \Illuminate\Session\Middleware\StartSession::class, // Starts session handling
            \Illuminate\View\Middleware\ShareErrorsFromSession::class, // Shares session errors with views
            \App\Http\Middleware\VerifyCsrfToken::class, // Protects against CSRF attacks
            \Illuminate\Routing\Middleware\SubstituteBindings::class, // Handles route model binding

            // Inertia middleware (Added in bootstrap/app.php now, but keeping here doesn't hurt)
            \App\Http\Middleware\HandleInertiaRequests::class,

            // Preload assets for better performance (Added in bootstrap/app.php now)
            \Illuminate\Http\Middleware\AddLinkHeadersForPreloadedAssets::class,
        ],

        'api' => [
            // For Laravel Sanctum SPA/stateful authentication
            \Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful::class,
            'throttle:api', // Rate limiting for API routes
            \Illuminate\Routing\Middleware\SubstituteBindings::class, // Route model binding
        ],
    ];

    /**
     * The application's route middleware aliases.
     *
     * Aliases can be used instead of full class names in routes.
     *
     * @var array<string, class-string|string>
     */
    protected $middlewareAliases = [
        'auth' => \App\Http\Middleware\Authenticate::class, // Ensures user is authenticated
        'auth.basic' => \Illuminate\Auth\Middleware\AuthenticateWithBasicAuth::class, // HTTP Basic Auth
        'auth.session' => \Illuminate\Session\Middleware\AuthenticateSession::class, // Session-based auth state persistence
        'cache.headers' => \Illuminate\Http\Middleware\SetCacheHeaders::class, // Sets cache headers
        'can' => \Illuminate\Auth\Middleware\Authorize::class, // Authorization based on abilities/policies
        'guest' => \App\Http\Middleware\RedirectIfAuthenticated::class, // Redirects logged-in users from guest routes
        'precognitive' => \Illuminate\Foundation\Http\Middleware\HandlePrecognitiveRequests::class, // Handles frontend validation requests
        'signed' => \Illuminate\Routing\Middleware\ValidateSignature::class, // Validates signed URLs
        'throttle' => \Illuminate\Routing\Middleware\ThrottleRequests::class, // Rate limiting
        'verified' => \Illuminate\Auth\Middleware\EnsureEmailIsVerified::class, // Ensures user email is verified

        // --- [PATCH START] ---
        // Custom middleware alias for Role-Based Access Control
        // 'role' => \App\Http\Middleware\CheckRole::class, // <-- BUG: This is for WEB routes
        'role' => \App\Http\Middleware\EnsureUserHasRole::class, // <-- FIX: Use API-safe middleware
        // --- [PATCH END] ---
    ];
}