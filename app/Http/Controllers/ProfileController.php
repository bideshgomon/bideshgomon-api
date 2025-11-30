<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use App\Models\UserProfile;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
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
            Log::info('Profile edit returning', [
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
            'countries' => \App\Models\Country::where('is_active', true)->orderBy('name')->get(['id', 'name', 'nationality']),
            'degrees' => \App\Models\Degree::where('is_active', true)->orderBy('name')->get(['id', 'name']),
            'serviceCategories' => \App\Models\ServiceCategory::where('is_active', true)->orderBy('name')->get(['id', 'name', 'icon']),
            'currencies' => \App\Models\Currency::where('is_active', true)->orderBy('code')->get(['id', 'code', 'name', 'symbol']),
            'profileCompletion' => $user->getProfileCompletionDetails(),
            'section' => $request->query('section'), // Pass section from query parameter
        ]);
    }    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        Log::info('Profile update started', [
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
        
        Log::info('Profile before update', ['profile' => $profile->toArray()]);
        
        $profile->update([
            'first_name' => $request->input('first_name'),
            'middle_name' => $request->input('middle_name'),
            'last_name' => $request->input('last_name'),
            'name_as_per_passport' => $request->input('name_as_per_passport'),
        ]);

        Log::info('Profile after update', ['profile' => $profile->fresh()->toArray()]);

        // Also update the user's name field for backward compatibility
        $fullName = trim(implode(' ', array_filter([
            $request->input('first_name'),
            $request->input('middle_name'),
            $request->input('last_name'),
        ])));
        
        if ($fullName) {
            $request->user()->update(['name' => $fullName]);
        }

        Log::info('Profile update completed successfully');

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
            'dob' => ['nullable', 'date', 'before:today'],
            'gender' => ['nullable', 'in:male,female,other'],
            'nationality' => ['nullable', 'string', 'max:100'],
            'present_address_line' => ['nullable', 'string', 'max:255'],
            'present_country' => ['nullable', 'string', 'max:100'],
            'present_division' => ['nullable', 'string', 'max:100'],
            'present_district' => ['nullable', 'string', 'max:100'],
            'present_city' => ['nullable', 'string', 'max:100'],
            'present_postal_code' => ['nullable', 'string', 'max:20'],
            'permanent_address_line' => ['nullable', 'string', 'max:255'],
            'permanent_country' => ['nullable', 'string', 'max:100'],
            'permanent_division' => ['nullable', 'string', 'max:100'],
            'permanent_district' => ['nullable', 'string', 'max:100'],
            'permanent_city' => ['nullable', 'string', 'max:100'],
            'permanent_postal_code' => ['nullable', 'string', 'max:20'],
            'nid' => ['nullable', 'string', 'max:20'],
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
     * Update the user's social links.
     */
    public function updateSocialLinks(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'social_links' => ['nullable', 'array'],
            'social_links.linkedin' => ['nullable', 'string', 'max:255', 'url'],
            'social_links.github' => ['nullable', 'string', 'max:255', 'url'],
            'social_links.facebook' => ['nullable', 'string', 'max:255'],
            'social_links.twitter' => ['nullable', 'string', 'max:255', 'url'],
            'social_links.instagram' => ['nullable', 'string', 'max:255'],
            'social_links.youtube' => ['nullable', 'string', 'max:255', 'url'],
            'social_links.tiktok' => ['nullable', 'string', 'max:255'],
            'social_links.whatsapp' => ['nullable', 'string', 'max:20'],
            'social_links.telegram' => ['nullable', 'string', 'max:100'],
            'social_links.wechat' => ['nullable', 'string', 'max:100'],
            'social_links.skype' => ['nullable', 'string', 'max:100'],
            'social_links.discord' => ['nullable', 'string', 'max:100'],
            'social_links.medium' => ['nullable', 'string', 'max:255', 'url'],
            'social_links.behance' => ['nullable', 'string', 'max:255', 'url'],
            'social_links.dribbble' => ['nullable', 'string', 'max:255', 'url'],
            'social_links.website' => ['nullable', 'string', 'max:255', 'url'],
        ]);

        $profile = $request->user()->profile()->firstOrCreate([]);
        
        // Filter out empty values
        $socialLinks = array_filter($validated['social_links'] ?? [], function($value) {
            return !empty(trim($value));
        });
        
        $profile->update(['social_links' => $socialLinks]);

        return Redirect::back()->with('success', 'Social links updated successfully!');
    }

    /**
     * Update emergency contact information.
     */
    public function updateEmergencyContact(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'emergency_contact_name' => ['required', 'string', 'max:255'],
            'emergency_contact_relationship' => ['required', 'string', 'max:50'],
            'emergency_contact_phone' => ['required', 'string', 'max:20'],
            'emergency_contact_email' => ['nullable', 'email', 'max:255'],
            'emergency_contact_address' => ['nullable', 'string', 'max:500'],
        ]);

        $profile = $request->user()->profile()->firstOrCreate([]);
        $profile->update($validated);

        return Redirect::back()->with('success', 'Emergency contact updated successfully!');
    }

    /**
     * Update medical information.
     */
    public function updateMedicalInfo(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'blood_group' => ['required', 'string', 'max:10'],
            'allergies' => ['nullable', 'string'],
            'medical_conditions' => ['nullable', 'string'],
            'vaccinations' => ['nullable', 'array'],
            'vaccinations.*.id' => ['required', 'string'],
            'vaccinations.*.name' => ['required', 'string'],
            'vaccinations.*.date' => ['required', 'date'],
            'health_insurance_provider' => ['nullable', 'string', 'max:255'],
            'health_insurance_policy_number' => ['nullable', 'string', 'max:255'],
            'health_insurance_expiry_date' => ['nullable', 'date'],
        ]);

        $profile = $request->user()->profile()->firstOrCreate([]);
        $profile->update($validated);

        return Redirect::back()->with('success', 'Medical information updated successfully!');
    }

    /**
     * Update references.
     */
    public function updateReferences(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'references' => ['required', 'array'],
            'references.*.id' => ['required'],
            'references.*.type' => ['required', 'string', 'in:professional,academic,personal'],
            'references.*.name' => ['required', 'string', 'max:255'],
            'references.*.relationship' => ['required', 'string', 'max:100'],
            'references.*.organization' => ['required', 'string', 'max:255'],
            'references.*.position' => ['required', 'string', 'max:255'],
            'references.*.email' => ['required', 'email', 'max:255'],
            'references.*.phone' => ['required', 'string', 'max:20'],
            'references.*.address' => ['nullable', 'string', 'max:500'],
            'references.*.years_known' => ['nullable', 'integer', 'min:0', 'max:50'],
            'references.*.can_contact' => ['boolean'],
        ]);

        $profile = $request->user()->profile()->firstOrCreate([]);
        $profile->update($validated);

        return Redirect::back()->with('success', 'References updated successfully!');
    }

    /**
     * Update certifications.
     */
    public function updateCertifications(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'certifications' => ['required', 'array'],
            'certifications.*.id' => ['required'],
            'certifications.*.type' => ['required', 'string', 'in:professional,trade,driving,technical,language,other'],
            'certifications.*.name' => ['required', 'string', 'max:255'],
            'certifications.*.issuing_organization' => ['required', 'string', 'max:255'],
            'certifications.*.issue_date' => ['required', 'date'],
            'certifications.*.expiry_date' => ['nullable', 'date', 'after:issue_date'],
            'certifications.*.credential_id' => ['nullable', 'string', 'max:100'],
            'certifications.*.credential_url' => ['nullable', 'string', 'url', 'max:500'],
            'certifications.*.never_expires' => ['boolean'],
        ]);

        $profile = $request->user()->profile()->firstOrCreate([]);
        $profile->update($validated);

        return Redirect::back()->with('success', 'Certifications updated successfully!');
    }

    /**
     * Update privacy settings.
     */
    public function updatePrivacySettings(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'privacy_settings' => ['required', 'array'],
            'privacy_settings.profile_visibility' => ['required', 'string', 'in:public,private,connections'],
            'privacy_settings.show_email' => ['boolean'],
            'privacy_settings.show_phone' => ['boolean'],
            'privacy_settings.show_address' => ['boolean'],
            'privacy_settings.show_dob' => ['boolean'],
            'privacy_settings.show_social_links' => ['boolean'],
            'privacy_settings.allow_search_engines' => ['boolean'],
            'privacy_settings.show_in_directory' => ['boolean'],
        ]);

        $profile = $request->user()->profile()->firstOrCreate([]);
        $profile->update($validated);

        return Redirect::back()->with('success', 'Privacy settings updated successfully!');
    }

    /**
     * Download user data (GDPR).
     */
    public function downloadData(Request $request)
    {
        $user = $request->user();
        $profile = $user->profile;

        $data = [
            'user' => $user->toArray(),
            'profile' => $profile ? $profile->toArray() : null,
            'family_members' => $user->familyMembers->toArray(),
            'educations' => $user->educations->toArray(),
            'work_experiences' => $user->workExperiences->toArray(),
            'skills' => $user->skills->toArray(),
            'languages' => $user->languages->toArray(),
            'travel_history' => $user->travelHistory->toArray(),
            'phone_numbers' => $user->phoneNumbers->toArray(),
            'exported_at' => now()->toISOString(),
        ];

        // Update download timestamp
        if ($profile) {
            $profile->update(['data_downloaded_at' => now()]);
        }

        $filename = 'user-data-' . $user->id . '-' . now()->format('Y-m-d') . '.json';

        return response()->json($data)
            ->header('Content-Type', 'application/json')
            ->header('Content-Disposition', 'attachment; filename="' . $filename . '"');
    }

    /**
     * Update user preferences.
     */
    public function updatePreferences(Request $request)
    {
        $validated = $request->validate([
            'preferences.preferred_destinations' => 'nullable|array',
            'preferences.service_interests' => 'nullable|array',
            'preferences.communication_preferences' => 'nullable|array',
            'preferences.language' => 'nullable|string|max:10',
            'preferences.timezone' => 'nullable|string|max:50',
            'preferences.currency' => 'nullable|string|max:10',
            'preferences.notifications' => 'nullable|array',
            'preferences.notifications.email' => 'nullable|boolean',
            'preferences.notifications.sms' => 'nullable|boolean',
            'preferences.notifications.push' => 'nullable|boolean',
            'preferences.notifications.whatsapp' => 'nullable|boolean',
            'preferences.theme' => 'nullable|string|in:light,dark,system',
            'preferences.font_size' => 'nullable|string|in:small,medium,large',
        ]);

        $request->user()->profile->update([
            'preferences' => $validated['preferences'] ?? []
        ]);

        return back()->with('success', 'Preferences updated successfully!');
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
