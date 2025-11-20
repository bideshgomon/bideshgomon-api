<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserHasRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @param  string  ...$roles  (e.g., 'admin', 'agency')
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        if (! Auth::check()) {
            return $request->expectsJson()
                        ? response()->json(['message' => 'Unauthenticated.'], 401)
                        : redirect()->route('login');
        }

        $user = Auth::user();
        $user->loadMissing('role');

        if (! $user->role) {
            abort(403, 'Forbidden. No role assigned to this user.');
        }

        $roleSlug = strtolower($user->role->slug ?? '');

        foreach ($roles as $role) {
            $requestedRole = strtolower($role);

            // Direct role match
            if ($roleSlug === $requestedRole) {
                return $next($request);
            }

            // Allow admins to access user routes (for testing and profile management)
            if ($requestedRole === 'user' && $roleSlug === 'admin') {
                return $next($request);
            }
        }

        // If no role matched, abort with 403
        abort(403, 'Forbidden. You do not have the required permissions.');
    }
}
