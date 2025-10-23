<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class EnsureUserHasRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @param  string  ...$roles (e.g., 'admin', 'agency')
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        if (!Auth::check()) {
            // Not authenticated
            return response()->json(['message' => 'Unauthenticated.'], 401);
        }

        $user = Auth::user();
        
        // Eager load the role to avoid extra queries
        $user->loadMissing('role'); 

        foreach ($roles as $role) {
            // Check if the user's role name matches one of the required roles
            if ($user->role && $user->role->name === $role) {
                return $next($request);
            }
        }

        // If no role matched
        return response()->json(['message' => 'Forbidden. You do not have the required permissions.'], 403);
    }
}