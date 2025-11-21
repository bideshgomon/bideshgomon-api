<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\ProfileView;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Inertia\Inertia;

class PublicProfileController extends Controller
{
    /**
     * Show public profile by slug.
     */
    public function show(Request $request, string $slug)
    {
        $user = User::where('public_profile_slug', $slug)
            ->where('profile_is_public', true)
            ->with([
                'userProfile',
                'educations' => function($q) {
                    $q->orderBy('start_date', 'desc');
                },
                'workExperiences' => function($q) {
                    $q->orderBy('start_date', 'desc');
                },
                'languages',
                'userPassports',
                'travelHistory' => function($q) {
                    $q->orderBy('entry_date', 'desc')->limit(10);
                },
            ])
            ->firstOrFail();

        // Track view
        $this->trackView($request, $user);

        // Build profile data based on visibility settings
        $profileData = $this->buildPublicProfileData($user);

        return Inertia::render('PublicProfile/Show', [
            'profile' => $profileData,
            'totalViews' => $user->profileViews()->count(),
        ]);
    }

    /**
     * Show user's own public profile settings.
     */
    public function settings()
    {
        $user = auth()->user();

        return Inertia::render('PublicProfile/Settings', [
            'user' => [
                'public_profile_slug' => $user->public_profile_slug,
                'profile_is_public' => $user->profile_is_public,
                'profile_visibility_settings' => $user->profile_visibility_settings ?? $this->getDefaultVisibilitySettings(),
                'profile_bio' => $user->profile_bio,
                'profile_headline' => $user->profile_headline,
                'public_profile_url' => $user->public_profile_url,
            ],
            'totalViews' => $user->profileViews()->count(),
            'recentViews' => $user->profileViews()
                ->orderBy('viewed_at', 'desc')
                ->limit(10)
                ->get()
                ->map(fn($view) => [
                    'ip_address' => $this->maskIp($view->ip_address),
                    'country' => $view->country,
                    'city' => $view->city,
                    'viewed_at' => format_bd_date($view->viewed_at),
                ]),
        ]);
    }

    /**
     * Update public profile settings.
     */
    public function updateSettings(Request $request)
    {
        $user = auth()->user();

        $validated = $request->validate([
            'public_profile_slug' => [
                'nullable',
                'string',
                'max:255',
                'regex:/^[a-z0-9\-]+$/',
                'unique:users,public_profile_slug,' . $user->id,
            ],
            'profile_is_public' => 'required|boolean',
            'profile_visibility_settings' => 'nullable|array',
            'profile_bio' => 'nullable|string|max:1000',
            'profile_headline' => 'nullable|string|max:255',
        ]);

        // Generate slug if making profile public and no slug exists
        if ($validated['profile_is_public'] && empty($validated['public_profile_slug'])) {
            $validated['public_profile_slug'] = $this->generateUniqueSlug($user);
        }

        $user->update($validated);

        return redirect()->route('profile.public.settings')
            ->with('success', 'Public profile settings updated successfully!');
    }

    /**
     * Generate QR code for public profile.
     */
    public function generateQrCode()
    {
        $user = auth()->user();

        if (!$user->public_profile_slug || !$user->profile_is_public) {
            return response()->json([
                'error' => 'Public profile not enabled',
            ], 400);
        }

        $url = $user->public_profile_url;

        // Use QR code library or service
        // For now, return URL for frontend to generate QR
        return response()->json([
            'url' => $url,
            'qr_data' => $url,
        ]);
    }

    /**
     * Track profile view.
     */
    protected function trackView(Request $request, User $user)
    {
        // Don't track if viewing own profile
        if (auth()->check() && auth()->id() === $user->id) {
            return;
        }

        ProfileView::create([
            'user_id' => $user->id,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'referrer' => $request->header('referer'),
            'viewed_at' => now(),
        ]);
    }

    /**
     * Build public profile data based on visibility settings.
     */
    protected function buildPublicProfileData(User $user): array
    {
        $data = [
            'name' => $user->name,
            'headline' => $user->profile_headline,
            'bio' => $user->profile_bio,
            'slug' => $user->public_profile_slug,
        ];

        // Basic info
        if ($user->isSectionVisible('basic_info') && $user->userProfile) {
            $profile = $user->userProfile;
            $data['basic_info'] = [
                'full_name' => $profile->full_name,
                'email' => $user->isSectionVisible('contact') ? $user->email : null,
                'phone' => $user->isSectionVisible('contact') ? format_bd_phone($profile->phone) : null,
                'nationality' => $profile->nationality,
                'city' => $profile->city,
                'country' => 'Bangladesh',
            ];
        }

        // Education
        if ($user->isSectionVisible('education')) {
            $data['education'] = $user->educations->map(fn($edu) => [
                'degree_level' => $edu->degree_level,
                'institution_name' => $edu->institution_name,
                'field_of_study' => $edu->field_of_study,
                'start_date' => $edu->start_date?->format('Y'),
                'end_date' => $edu->end_date?->format('Y'),
                'gpa' => $edu->gpa,
                'country' => $edu->country,
            ])->toArray();
        }

        // Work Experience
        if ($user->isSectionVisible('work_experience')) {
            $data['work_experience'] = $user->workExperiences->map(fn($work) => [
                'job_title' => $work->job_title,
                'company_name' => $work->company_name,
                'start_date' => $work->start_date?->format('M Y'),
                'end_date' => $work->currently_working ? 'Present' : $work->end_date?->format('M Y'),
                'job_description' => $work->job_description,
                'country' => $work->country,
                'city' => $work->city,
            ])->toArray();
        }

        // Languages
        if ($user->isSectionVisible('languages')) {
            $data['languages'] = $user->languages->map(fn($lang) => [
                'language_name' => $lang->language_name,
                'proficiency_level' => $lang->proficiency_level,
                'ielts_score' => $lang->ielts_score,
                'toefl_score' => $lang->toefl_score,
            ])->toArray();
        }

        // Travel History (limited)
        if ($user->isSectionVisible('travel_history')) {
            $data['travel_history'] = $user->travelHistory->map(fn($travel) => [
                'country' => $travel->country,
                'purpose' => $travel->purpose,
                'entry_date' => $travel->entry_date?->format('Y'),
            ])->toArray();
        }

        // Skills (if available)
        if ($user->isSectionVisible('skills')) {
            $data['skills'] = $user->skills->pluck('skill_name')->toArray();
        }

        return $data;
    }

    /**
     * Generate unique slug for user.
     */
    protected function generateUniqueSlug(User $user): string
    {
        $baseSlug = Str::slug($user->name);
        $slug = $baseSlug;
        $counter = 1;

        while (User::where('public_profile_slug', $slug)->exists()) {
            $slug = $baseSlug . '-' . $counter;
            $counter++;
        }

        return $slug;
    }

    /**
     * Get default visibility settings.
     */
    protected function getDefaultVisibilitySettings(): array
    {
        return [
            'basic_info' => true,
            'contact' => false,
            'education' => true,
            'work_experience' => true,
            'languages' => true,
            'skills' => true,
            'travel_history' => false,
            'documents' => false,
        ];
    }

    /**
     * Mask IP address for privacy.
     */
    protected function maskIp(?string $ip): ?string
    {
        if (!$ip) {
            return null;
        }

        $parts = explode('.', $ip);
        if (count($parts) === 4) {
            return $parts[0] . '.' . $parts[1] . '.xxx.xxx';
        }

        return 'xxx.xxx.xxx.xxx';
    }
}
