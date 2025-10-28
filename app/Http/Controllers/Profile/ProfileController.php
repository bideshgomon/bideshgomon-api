<?php

// --- CORRECT NAMESPACE ---
namespace App\Http\Controllers\Profile;
// --- END CORRECTION ---

use App\Http\Controllers\Controller; // Import the base Controller
use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;
use Inertia\Response;

// Import models if needed (optional here, but good practice if used more)
use App\Models\UserProfile;
use App\Models\UserEducation;
use App\Models\UserWorkExperience;
use App\Models\UserDocument;
use App\Models\Skill;

class ProfileController extends Controller // Extend the base Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): Response
    {
        // Eager load the 'profile' relationship
        $user = $request->user()->load([
            'profile',
            // Add other relationships needed by Edit.vue if not handled by partials
        ]);

        return Inertia::render('Profile/Edit', [
            'mustVerifyEmail' => $request->user() instanceof MustVerifyEmail,
            'status' => session('status'),
            // 'userData' => $user, // Pass if needed
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        // Update User model fields
        $request->user()->fill($request->validated());

        if ($request->user()->isDirty('email')) {
            $request->user()->email_verified_at = null;
        }

        $request->user()->save();

        // Update UserProfile model fields
        $profile = $request->user()->profile()->firstOrCreate(['user_id' => $request->user()->id]);

        // Filter only the fields that exist in the request AND are fillable in UserProfile
        $profileFillable = (new \App\Models\UserProfile)->getFillable(); // Get fillable fields
        $profileDataToUpdate = array_filter(
            $request->only($profileFillable), // Get only fillable fields from request
            fn($key) => $request->has($key), // Ensure the key exists in the request
            ARRAY_FILTER_USE_KEY
        );

        if (!empty($profileDataToUpdate)) {
            $profile->update($profileDataToUpdate);
        }


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

    // --- CV Builder Methods (If they belong here) ---
    // Note: These might belong in a separate CvBuilderController

    /**
     * Display the CV builder page.
     */
    // public function showCvBuilder(Request $request): Response
    // {
    //     $user = $request->user()->load(['profile', 'educations', 'workExperiences', 'skills', 'languages']);
    //     return Inertia::render('Profile/CvBuilder', ['userData' => $user]);
    // }

    /**
     * Handle downloading the CV.
     */
    // public function downloadCv(Request $request)
    // {
    //     $user = $request->user()->load(['profile', 'educations', 'workExperiences', 'skills', 'languages']);
    //     // PDF Generation Logic...
    //     return response()->json(['message' => 'CV Download functionality not yet implemented.', 'userData' => $user]);
    // }
    // --- End CV Builder Methods ---
}