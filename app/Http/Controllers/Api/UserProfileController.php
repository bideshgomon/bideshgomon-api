<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\UserProfile; // Import the UserProfile model
use Illuminate\Support\Facades\Auth; // To get the authenticated user
use App\Http\Requests\UserProfileUpdateRequest; // We'll create this next

class UserProfileController extends Controller
{
    /**
     * Update the authenticated user's profile details.
     */
    public function update(UserProfileUpdateRequest $request)
    {
        $user = Auth::user();

        // Use updateOrCreate to handle cases where the profile might not exist yet
        $profile = UserProfile::updateOrCreate(
            ['user_id' => $user->id],
            $request->validated() // Use validated data from the request
        );

        return response()->json([
            'message' => 'Profile details updated successfully.',
            'profile' => $profile // Return the updated profile data
        ], 200);
    }

    // Add other methods like show() if needed later
    // public function show() { ... }
}