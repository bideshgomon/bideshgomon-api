<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @param  string  $role  The role to check for (e.g., 'admin')
     */
    public function handle(Request $request, Closure $next, string $role): Response
    {
        // 1. Check if the user is authenticated
        if (!Auth::check()) {
            return redirect('login');
        }

        // 2. Get the authenticated user
        $user = $request->user();

        // 3. Eager load the 'role' relationship if it's not already loaded
        if (!$user->relationLoaded('role')) {
            $user->load('role');
        }

        // 4. Check if the user's role name matches the required role
        //    We use strtolower() to prevent any case-sensitivity issues
        if ($user->role && strtolower($user->role->name) === strtolower($role)) {
            // 5. User has the role, allow the request
            return $next($request);
        }

        // 6. User does not have the role, forbid the request
        return abort(403, 'Forbidden. You do not have the required permissions.');
    }
}