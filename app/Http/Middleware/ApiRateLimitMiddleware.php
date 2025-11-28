<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Symfony\Component\HttpFoundation\Response;

class ApiRateLimitMiddleware
{
    /**
     * Handle an incoming request with role-based rate limiting
     */
    public function handle(Request $request, Closure $next, string $limit = 'default'): Response
    {
        $key = $this->resolveRequestSignature($request);
        
        // Get rate limit based on user role or default
        $maxAttempts = $this->getMaxAttempts($request, $limit);
        $decayMinutes = 1;

        if (RateLimiter::tooManyAttempts($key, $maxAttempts)) {
            $retryAfter = RateLimiter::availableIn($key);
            
            return response()->json([
                'success' => false,
                'message' => 'Too many requests. Please try again later.',
                'retry_after' => $retryAfter
            ], 429)->header('Retry-After', $retryAfter);
        }

        RateLimiter::hit($key, $decayMinutes * 60);

        $response = $next($request);

        return $response->withHeaders([
            'X-RateLimit-Limit' => $maxAttempts,
            'X-RateLimit-Remaining' => RateLimiter::remaining($key, $maxAttempts),
        ]);
    }

    /**
     * Resolve request signature for rate limiting
     */
    protected function resolveRequestSignature(Request $request): string
    {
        if ($user = $request->user()) {
            return 'api:' . $user->id;
        }

        return 'api:' . $request->ip();
    }

    /**
     * Get max attempts based on user role
     */
    protected function getMaxAttempts(Request $request, string $limit): int
    {
        // Custom limits
        if ($limit === 'strict') {
            return 10; // For sensitive operations
        }

        if ($limit === 'relaxed') {
            return 200; // For general API calls
        }

        // Role-based limits
        $user = $request->user();
        
        if ($user && $user->role) {
            $roleName = strtolower($user->role->name);
            
            return match($roleName) {
                'admin', 'super_admin' => 1000,  // High limit for admins
                'agent', 'service_provider' => 500,  // Medium for agents
                default => 100  // Standard for users
            };
        }

        return 60; // Default for unauthenticated requests
    }
}
