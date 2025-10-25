<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserHasRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string $role): Response
    {
        // Check if user is authenticated and has a role
        if (!$request->user() || !$request->user()->role) {
            abort(403);
        }

        // ✅ [BUG FIX] Check role by 'name' instead of 'slug'
        // The RoleSeeder populates 'name' (e.g., 'admin'), not 'slug'.
        if ($request->user()->role->name !== $role) {
            abort(403);
        }

        return $next($request);
    }
}