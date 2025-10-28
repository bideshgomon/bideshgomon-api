<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;
use Inertia\Response;
use App\Models\UserProfile; // <-- 1. IMPORT UserProfile

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): Response
    {
        // 2. EAGER LOAD THE USER'S PROFILE RELATION
        // This makes 'userProfile' available in the Vue page
        $user = $request->user()->load('userProfile');

        return Inertia::render('Profile/Edit', [
            'mustVerifyEmail' => $user instanceof MustVerifyEmail,
            'status' => session('status'),
            'userProfile' => $user->userProfile, // <-- 3. PASS THE PROFILE DATA
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        // 4. UPDATE THE USER MODEL (Name, Email)
        $request->user()->fill($request->validated());

        if ($request->user()->isDirty('email')) {
            $request->user()->email_verified_at = null;
        }

        $request->user()->save();

        // 5. UPDATE OR CREATE THE USERPROFILE MODEL (All CV data)
        // We use updateOrCreate to handle new users who don't have a profile row yet.
        $userProfileData = $request->safe()->except(['name', 'email']);
        
        UserProfile::updateOrCreate(
            ['user_id' => $request->user()->id],
            $userProfileData
        );

        return Redirect::route('profile.edit')->with('success', 'Profile updated successfully.');
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