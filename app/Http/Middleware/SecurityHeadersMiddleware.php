<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SecurityHeadersMiddleware
{
    /**
     * Handle an incoming request and add security headers
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // Determine if we're in development mode
        $isDevelopment = config('app.debug');

        // In development, use a very permissive CSP
        if ($isDevelopment) {
            // Permissive CSP for development (Vite hot reload, etc.)
            $response->headers->set('Content-Security-Policy', 
                "default-src * 'unsafe-inline' 'unsafe-eval' data: blob:; " .
                "script-src * 'unsafe-inline' 'unsafe-eval'; " .
                "style-src * 'unsafe-inline'; " .
                "img-src * data: blob: 'unsafe-inline'; " .
                "font-src * data:; " .
                "connect-src * ws: wss:; " .
                "frame-ancestors 'none';"
            );
        } else {
            // Strict CSP for production
            $response->headers->set('Content-Security-Policy', 
                "default-src 'self'; " .
                "script-src 'self' 'unsafe-inline' 'unsafe-eval' https://cdn.jsdelivr.net; " .
                "style-src 'self' 'unsafe-inline' https://fonts.googleapis.com https://fonts.bunny.net; " .
                "img-src 'self' data: https: blob:; " .
                "font-src 'self' data: https://fonts.gstatic.com https://fonts.bunny.net; " .
                "connect-src 'self'; " .
                "frame-ancestors 'none'; " .
                "base-uri 'self';"
            );
            
            // Strict Transport Security (HSTS)
            $response->headers->set('Strict-Transport-Security', 'max-age=31536000; includeSubDomains');
        }

        // Prevent clickjacking
        $response->headers->set('X-Frame-Options', 'DENY');

        // Prevent MIME sniffing
        $response->headers->set('X-Content-Type-Options', 'nosniff');

        // XSS Protection
        $response->headers->set('X-XSS-Protection', '1; mode=block');

        // Referrer Policy
        $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');

        // Permissions Policy
        $response->headers->set('Permissions-Policy', 
            'geolocation=(), ' .
            'microphone=(), ' .
            'camera=()'
        );

        return $response;
    }
}
