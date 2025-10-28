<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;
use App\Models\DataAccessRequest;
use App\Models\User;

class CheckDataAccess
{
    /**
     * Handle an incoming request.
     * Checks if a consultant has approved access to a user's data.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        /** @var \App\Models\User $requester */
        $requester = $request->user();

        // --- Determine the Target User ---
        // Option 1: Route parameter (e.g., /api/profile/{user}/education) - Less common for profile routes accessed by others
        // $targetUser = $request->route('user');

        // Option 2: Data owner (e.g., fetching education owned by a user)
        // This requires the controller method to identify the owner ID.
        // We'll assume for now the route structure is like /api/profile/education and implicitly targets the logged-in user,
        // OR the controller needs modification.

        // --- Simpler Check (for routes like /api/profile/education where user GETs their OWN data) ---
        // If the requester is the owner of the data (or an admin), allow access immediately.
        // For simplicity now, let's assume profile routes are for the user themselves *unless* we add specific consultant routes later.
        // A more robust check might involve inspecting the resource being accessed.

        // --- Logic if a Consultant tries to access another User's data ---
        // This logic needs to be refined based on *how* a consultant accesses a specific user's profile data via API.
        // For example, if there was an endpoint like GET /api/consultant/view-profile/{user}

        /*
        // --- EXAMPLE: If the route was GET /api/consultant/view-profile/{user} ---
        if ($requester->hasRole('consultant')) {
            $targetUser = $request->route('user'); // Get the User model instance from the route

            // Ensure $targetUser is valid and is a 'user'
            if (!$targetUser || !$targetUser->hasRole('user')) {
                 return response()->json(['message' => 'Invalid target user.'], 404);
            }

            // Allow if consultant is accessing their own profile (unlikely scenario for this route)
            if ($requester->id === $targetUser->id) {
                return $next($request);
            }

            // Check for an approved request
            $hasApprovedAccess = DataAccessRequest::where('consultant_id', $requester->id)
                                                  ->where('user_id', $targetUser->id)
                                                  ->where('status', 'approved')
                                                  ->exists();

            if (!$hasApprovedAccess) {
                 // Also allow Admin access
                 if (!$requester->hasRole('admin')) {
                    return response()->json(['message' => 'Access Denied. Request access from the user.'], 403);
                 }
            }
        }
        // --- END EXAMPLE ---
        */

        // For now, as profile routes are user-specific, let the request proceed.
        // We will apply this middleware more specifically later when building consultant-facing profile view endpoints.
        return $next($request);
    }
}