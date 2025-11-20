<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use App\Models\UserProfile;
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
     * Display the user's profile.
     */
    public function show(Request $request): Response
    {
        $user = $request->user();
        $user->load([
            'role',
            'profile',
            'familyMembers',
            'languages',
            'securityInformation',
            'educations' => function ($query) {
                $query->orderBy('start_date', 'desc');
            },
            'workExperiences' => function ($query) {
                $query->orderBy('start_date', 'desc');
            },
            'skills',
            'travelHistory' => function ($query) {
                $query->orderBy('entry_date', 'desc');
            },
            'phoneNumbers' => function ($query) {
                $query->orderBy('is_primary', 'desc');
            }
        ]);

        // Create profile if doesn't exist
        if (!$user->profile) {
            $user->profile()->create([]);
        }

        return Inertia::render('Profile/Show', [
            'user' => $user,
            'userProfile' => $user->profile,
            'familyMembers' => $user->familyMembers,
            'languages' => $user->languages,
            'securityInformation' => $user->securityInformation,
            'educations' => $user->educations,
            'workExperiences' => $user->workExperiences,
            'skills' => $user->skills,
            'travelHistory' => $user->travelHistory,
            'phoneNumbers' => $user->phoneNumbers,
            'profileCompletion' => $user->calculateProfileCompletion(),
        ]);
    }    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): Response
    {
        $user = $request->user();
        $user->load([
            'role',
            'profile',
            'familyMembers',
            'languages',
            'securityInformation',
            'educations' => function ($query) {
                $query->orderBy('start_date', 'desc');
            },
            'workExperiences' => function ($query) {
                $query->orderBy('start_date', 'desc');
            },
            'skills',
            'travelHistory' => function ($query) {
                $query->orderBy('entry_date', 'desc');
            },
            'phoneNumbers' => function ($query) {
                $query->orderBy('is_primary', 'desc');
            }
        ]);

        // Create profile if doesn't exist
        if (!$user->profile) {
            $user->profile()->create([]);
            $user->load('profile'); // Reload the profile relationship
        }
        
        // Ensure we have fresh profile data and log for debugging
        if ($user->profile) {
            $user->profile->refresh();
            \Log::info('Profile edit returning', [
                'user_id' => $user->id,
                'profile_names' => $user->profile->only(['first_name','middle_name','last_name','name_as_per_passport'])
            ]);
        }

        return Inertia::render('Profile/Edit', [
            'mustVerifyEmail' => $request->user() instanceof MustVerifyEmail,
            'status' => session('status'),
            'user' => $user,
            'userProfile' => $user->profile,
            'familyMembers' => $user->familyMembers,
            'userLanguages' => $user->languages()->with(['language', 'languageTest'])->get(),
            'languages' => \App\Models\Language::where('is_active', true)->orderBy('name')->get(['id', 'name']),
            'languageTests' => \App\Models\LanguageTest::where('is_active', true)->orderBy('name')->get(['id', 'name', 'language_id']),
            'securityInformation' => $user->securityInformation,
            'educations' => $user->educations,
            'workExperiences' => $user->workExperiences,
            'skills' => $user->skills,
            'travelHistory' => $user->travelHistory,
            'phoneNumbers' => $user->phoneNumbers,
            'divisions' => get_bd_divisions(),
            'profileCompletion' => $user->getProfileCompletionDetails(),
            'section' => $request->query('section'), // Pass section from query parameter
        ]);
    }    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        \Log::info('Profile update started', [
            'user_id' => $request->user()->id,
            'input' => $request->all()
        ]);

        // Update user email if changed
        $request->user()->fill($request->only('email'));

        if ($request->user()->isDirty('email')) {
            $request->user()->email_verified_at = null;
        }

        $request->user()->save();

        // Update user profile with passport-standard name fields
        // Ensure profile exists
        if (!$request->user()->profile) {
            $request->user()->profile()->create([]);
            $request->user()->load('profile');
        }
        
        $profile = $request->user()->profile;
        
        \Log::info('Profile before update', ['profile' => $profile->toArray()]);
        
        $profile->update([
            'first_name' => $request->input('first_name'),
            'middle_name' => $request->input('middle_name'),
            'last_name' => $request->input('last_name'),
            'name_as_per_passport' => $request->input('name_as_per_passport'),
        ]);

        \Log::info('Profile after update', ['profile' => $profile->fresh()->toArray()]);

        // Also update the user's name field for backward compatibility
        $fullName = trim(implode(' ', array_filter([
            $request->input('first_name'),
            $request->input('middle_name'),
            $request->input('last_name'),
        ])));
        
        if ($fullName) {
            $request->user()->update(['name' => $fullName]);
        }

        \Log::info('Profile update completed successfully');

        return Redirect::route('profile.edit', ['section' => 'basic'])
            ->with('status', 'profile-updated');
    }

    /**
     * Update the user's profile details.
     */
    public function updateDetails(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'bio' => ['nullable', 'string', 'max:500'],
            'phone' => ['nullable', 'string', 'max:20'],
            'dob' => ['nullable', 'date', 'before:today'],
            'gender' => ['nullable', 'in:male,female,other'],
            'nationality' => ['nullable', 'string', 'max:100'],
            'present_address_line' => ['nullable', 'string', 'max:255'],
            'present_division' => ['nullable', 'string', 'max:100'],
            'present_district' => ['nullable', 'string', 'max:100'],
            'permanent_address_line' => ['nullable', 'string', 'max:255'],
            'permanent_division' => ['nullable', 'string', 'max:100'],
            'permanent_district' => ['nullable', 'string', 'max:100'],
            'nid' => ['nullable', 'string', 'max:20'],
            'passport_number' => ['nullable', 'string', 'max:20'],
            'passport_issue_date' => ['nullable', 'date'],
            'passport_expiry_date' => ['nullable', 'date', 'after:passport_issue_date'],
            // Financial fields (33 fields)
            'employer_name' => ['nullable', 'string', 'max:255'],
            'employer_address' => ['nullable', 'string', 'max:255'],
            'employment_start_date' => ['nullable', 'date'],
            'monthly_income_bdt' => ['nullable', 'numeric', 'min:0'],
            'annual_income_bdt' => ['nullable', 'numeric', 'min:0'],
            'bank_name' => ['nullable', 'string', 'max:255'],
            'bank_branch' => ['nullable', 'string', 'max:255'],
            'bank_account_number' => ['nullable', 'string', 'max:50'],
            'bank_account_type' => ['nullable', 'in:savings,current,salary,fixed_deposit'],
            'bank_balance_bdt' => ['nullable', 'numeric', 'min:0'],
            'bank_statement_path' => ['nullable', 'string', 'max:255'],
            'owns_property' => ['nullable', 'boolean'],
            'property_type' => ['nullable', 'string', 'max:100'],
            'property_address' => ['nullable', 'string', 'max:255'],
            'property_value_bdt' => ['nullable', 'numeric', 'min:0'],
            'property_documents_path' => ['nullable', 'string', 'max:255'],
            'owns_vehicle' => ['nullable', 'boolean'],
            'vehicle_type' => ['nullable', 'string', 'max:100'],
            'vehicle_make_model' => ['nullable', 'string', 'max:255'],
            'vehicle_year' => ['nullable', 'integer', 'min:1900', 'max:' . (date('Y') + 1)],
            'vehicle_value_bdt' => ['nullable', 'numeric', 'min:0'],
            'has_investments' => ['nullable', 'boolean'],
            'investment_types' => ['nullable', 'string', 'max:500'],
            'investment_value_bdt' => ['nullable', 'numeric', 'min:0'],
            'has_liabilities' => ['nullable', 'boolean'],
            'liability_types' => ['nullable', 'string', 'max:500'],
            'liabilities_amount_bdt' => ['nullable', 'numeric', 'min:0'],
            'total_assets_bdt' => ['nullable', 'numeric', 'min:0'],
            'net_worth_bdt' => ['nullable', 'numeric', 'min:0'],
            'tax_return_path' => ['nullable', 'string', 'max:255'],
            'salary_certificate_path' => ['nullable', 'string', 'max:255'],
            'financial_sponsor_info' => ['nullable', 'string', 'max:1000'],
        ]);

        $profile = $request->user()->profile()->firstOrCreate([]);
        $profile->update($validated);

        return Redirect::route('profile.edit')->with('success', 'Profile updated successfully!');
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
