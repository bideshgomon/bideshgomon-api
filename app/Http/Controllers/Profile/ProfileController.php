<?php

namespace App\Http\Controllers\Profile;

use App\Http\Controllers\Controller;
use App\Http\Requests\ProfileUpdateRequest;
use App\Http\Requests\UpdateUserProfileDetailsRequest;
use App\Models\DocumentType;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;
use Inertia\Response;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): Response
    {
        // This is our custom logic
        $user = $request->user()->load([
            'profile',
            'educations' => fn ($query) => $query->orderBy('start_date', 'desc'),
            'experiences' => fn ($query) => $query->orderBy('is_current', 'desc')->orderBy('start_date', 'desc'),
            // --- UPDATE THIS LINE TO USE skillSet ---
            'skillSet' => fn ($query) => $query->orderBy('name'),
            'portfolios' => fn ($query) => $query->orderBy('created_at', 'desc'),
            'documents.documentType'
        ]);

        return Inertia::render('Profile/Edit', [
            'mustVerifyEmail' => $request->user() instanceof MustVerifyEmail,
            'status' => session('status'),
            // We pass all our custom data to the page
            'user_profile_data' => $user,
            'document_types' => DocumentType::orderBy('name')->get(),
        ]);
    }

    // ... (rest of the file is the same)

    /**
     * Update the user's profile information (name, email).
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
     * Update the user's custom profile details (bio, phone, address).
     */
    public function updateDetails(UpdateUserProfileDetailsRequest $request): RedirectResponse
    {
        $request->user()->profile()->updateOrCreate(
            ['user_id' => $request->user()->id],
            $request->validated()
        );

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