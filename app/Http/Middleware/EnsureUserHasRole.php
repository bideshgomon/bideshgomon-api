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
            // If not authenticated, redirect to login for web, or return 401 for API
            return $request->expectsJson()
                        ? response()->json(['message' => 'Unauthenticated.'], 401)
                        : redirect()->route('login');
        }

        $user = Auth::user();
        $user->loadMissing('role');

        foreach ($roles as $role) {
            if ($user->role && strtolower($user->role->name) === strtolower($role)) {
                return $next($request);
            }
        }

        // If no role matched, abort with 403
        // This works for both web (shows 403 page) and API (returns JSON 403)
        abort(403, 'Forbidden. You do not have the required permissions.');
    }
}
