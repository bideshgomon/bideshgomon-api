<?php

namespace App\Http\Controllers\Profile; // Assuming this is the correct namespace based on stack trace

use App\Http\Controllers\Controller; // Assuming base controller
use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;
use Inertia\Response;

// Make sure UserProfile model is imported if used directly
use App\Models\UserProfile;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): Response
    {
        // ✅ [FIX] Use the correct relationship name 'userProfile'
        // Pre-load the userProfile relationship if needed by the Inertia page
        $user = $request->user()->load('userProfile');

        return Inertia::render('Profile/Edit', [
            'mustVerifyEmail' => $user instanceof MustVerifyEmail,
            'status' => session('status'),
             // Pass the profile data if your Vue component expects it directly
             // Make sure the User model correctly loads 'userProfile'
            'userProfile' => $user->userProfile,
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $request->user()->fill($request->validated());

        if ($request->user()->isDirty('email')) {
            $request->user()->email_verified_at = null;
        }

        $request->user()->save();

        return Redirect::route('profile.edit');
    }

     /**
     * Update the user's profile details (from UserProfile model).
     * Assuming you have a separate route/method for this based on web.php
     */
    public function updateDetails(Request $request): RedirectResponse
    {
        // ✅ [FIX] Use the correct relationship name 'userProfile'
        $user = $request->user();
        $userProfile = $user->userProfile; // Get the related profile model

        if (!$userProfile) {
             // Handle case where profile doesn't exist, maybe create it
             $userProfile = UserProfile::create(['user_id' => $user->id]);
        }

        $validated = $request->validate([
            'bio' => 'nullable|string|max:1000',
            'address' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:20',
            // Add other UserProfile fields here
        ]);

        $userProfile->update($validated);

        return Redirect::route('profile.edit')->with('status', 'profile-details-updated');
    }


    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validate([
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}