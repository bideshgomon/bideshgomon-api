<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Models\UserPassport;
use App\Models\UserEducation;
use App\Models\UserWorkExperience;
use App\Models\UserLanguage;
use App\Models\UserVisaHistory;
use App\Models\UserTravelHistory;
use App\Models\UserFamilyMember;
use App\Models\UserFinancialInformation;
use App\Models\UserSecurityInformation;
use App\Models\UserCv;
use App\Models\JobApplication;
use App\Models\Country;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'phone',
        'role_id',
        'referral_code',
        'referred_by',
        'google_id',
        'google_token',
        'google_refresh_token',
        'public_profile_slug',
        'profile_is_public',
        'profile_visibility_settings',
        'profile_bio',
        'profile_headline',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'profile_is_public' => 'boolean',
            'profile_visibility_settings' => 'array',
        ];
    }

    /**
     * Get the role that the user belongs to.
     */
    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class);
    }

    /**
     * Get the user's profile.
     */
    public function profile(): HasOne
    {
        return $this->hasOne(UserProfile::class);
    }

    /**
     * Alias for profile relationship (used in admin panel).
     */
    public function userProfile(): HasOne
    {
        return $this->hasOne(UserProfile::class);
    }

    /**
     * Get the user's wallet.
     */
    public function wallet(): HasOne
    {
        return $this->hasOne(Wallet::class);
    }

    /**
     * Get the user's flight requests.
     */
    public function flightRequests(): HasMany
    {
        return $this->hasMany(FlightRequest::class);
    }

    /**
     * Get the user's country.
     */
    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class);
    }

    /**
     * Get all referrals made by this user.
     */
    public function referrals(): HasMany
    {
        return $this->hasMany(Referral::class, 'referrer_id');
    }

    /**
     * Get the user who referred this user.
     */
    public function referredBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'referred_by');
    }

    /**
     * Get all rewards for this user.
     */
    public function rewards(): HasMany
    {
        return $this->hasMany(Reward::class);
    }

    /**
     * Get the user's passports.
     */
    public function passports(): HasMany
    {
        return $this->hasMany(UserPassport::class);
    }

    /**
     * Backward compatibility alias used by ProfileAssessmentService & older tests.
     */
    public function userPassports(): HasMany
    {
        return $this->hasMany(UserPassport::class);
    }

    /**
     * Get the user's current passport.
     */
    public function currentPassport(): HasOne
    {
        return $this->hasOne(UserPassport::class)->where('is_current_passport', true);
    }

    /**
     * Get the user's visa history.
     */
    public function visaHistory(): HasMany
    {
        return $this->hasMany(UserVisaHistory::class);
    }

    /**
     * Get the user's travel history.
     */
    public function travelHistory(): HasMany
    {
        return $this->hasMany(UserTravelHistory::class);
    }

    /**
     * Get the user's family members.
     */
    public function familyMembers(): HasMany
    {
        return $this->hasMany(UserFamilyMember::class);
    }

    /**
     * Get the user's financial information.
     */
    public function financialInformation(): HasOne
    {
        return $this->hasOne(UserFinancialInformation::class);
    }

    /**
     * Get the user's security information.
     */
    public function securityInformation(): HasOne
    {
        return $this->hasOne(UserSecurityInformation::class);
    }

    /**
     * Get the user's education history.
     */
    public function educations(): HasMany
    {
        return $this->hasMany(UserEducation::class);
    }

    /**
     * Get the user's work experience history.
     */
    public function workExperiences(): HasMany
    {
        return $this->hasMany(UserWorkExperience::class);
    }

    /**
     * Get the user's language proficiencies.
     */
    public function languages(): HasMany
    {
        return $this->hasMany(UserLanguage::class);
    }

    /**
     * Get the user's skills.
     */
    public function skills(): BelongsToMany
    {
        return $this->belongsToMany(Skill::class, 'user_skill')
            ->withPivot('proficiency_level', 'years_of_experience')
            ->withTimestamps();
    }

    /**
     * Get the user's CVs.
     */
    public function cvs(): HasMany
    {
        return $this->hasMany(UserCv::class);
    }

    /**
     * Get the user's job applications.
     */
    public function jobApplications(): HasMany
    {
        return $this->hasMany(JobApplication::class);
    }

    /**
     * Get the user's phone numbers.
     */
    public function phoneNumbers(): HasMany
    {
        return $this->hasMany(UserPhoneNumber::class);
    }

    /**
     * Get the user's primary phone number.
     */
    public function primaryPhoneNumber(): HasOne
    {
        return $this->hasOne(UserPhoneNumber::class)->where('is_primary', true);
    }

    /**
     * Get the user's visa applications.
     */
    public function visaApplications(): HasMany
    {
        return $this->hasMany(VisaApplication::class);
    }

    /**
     * Get the user's translation requests.
     */
    public function translationRequests(): HasMany
    {
        return $this->hasMany(TranslationRequest::class);
    }

    /**
     * Get the user's travel insurance bookings.
     */
    public function insuranceBookings(): HasMany
    {
        return $this->hasMany(TravelInsuranceBooking::class);
    }

    /**
     * Get the user's flight bookings.
     */
    public function flightBookings(): HasMany
    {
        return $this->hasMany(FlightBooking::class);
    }

    /**
     * Get the user's hotel bookings.
     */
    public function hotelBookings(): HasMany
    {
        return $this->hasMany(HotelBooking::class);
    }

    /**
     * Check if user has a specific role.
     */
    public function hasRole(string $roleSlug): bool
    {
        return $this->role && $this->role->slug === $roleSlug;
    }

    /**
     * Check if user is an admin.
     */
    public function isAdmin(): bool
    {
        return $this->hasRole('admin');
    }

    /**
     * Check if user is a regular user.
     */
    public function isUser(): bool
    {
        return $this->hasRole('user');
    }

    /**
     * Check if user is an agency.
     */
    public function isAgency(): bool
    {
        return $this->hasRole('agency');
    }

    /**
     * Check if user is a consultant.
     */
    public function isConsultant(): bool
    {
        return $this->hasRole('consultant');
    }

    /**
     * Calculate profile completion percentage (all 12 sections).
     */
    public function calculateProfileCompletion(): int
    {
        $completion = 0;
        $totalSections = 12;

        // 1. Basic Information (10 points)
        if ($this->name && $this->email) {
            $completion += 10;
        }

        // 2. Profile Details (10 points)
        if ($this->profile) {
            $profileFields = [
                $this->profile->dob,
                $this->profile->gender,
                $this->profile->nationality,
                $this->profile->nid,
                $this->profile->present_address_line,
            ];
            $profileFilled = count(array_filter($profileFields, fn($v) => !empty($v)));
            $completion += (int) (($profileFilled / 5) * 10);
        }

        // 3. Education & Qualifications (10 points)
        if ($this->educations()->count() > 0) {
            $completion += 10;
        }

        // 4. Work Experience (10 points)
        if ($this->workExperiences()->count() > 0) {
            $completion += 10;
        }

        // 5. Skills & Expertise (10 points)
        if ($this->skills()->count() > 0) {
            $completion += 10;
        }

        // 6. Travel History (5 points)
        if ($this->travelHistory()->count() > 0) {
            $completion += 5;
        }

        // 7. Family Information (5 points)
        if ($this->familyMembers()->count() > 0) {
            $completion += 5;
        }

        // 8. Financial Information (10 points)
        if ($this->profile) {
            $financialFields = [
                $this->profile->monthly_income_bdt,
                $this->profile->employer_name,
                $this->profile->bank_balance_bdt,
            ];
            $financialFilled = count(array_filter($financialFields, fn($v) => !empty($v)));
            $completion += (int) (($financialFilled / 3) * 10);
        }

        // 9. Language Proficiency (10 points)
        if ($this->languages()->count() > 0) {
            $completion += 10;
        }

        // 10. Background & Security (5 points)
        if ($this->securityInformation) {
            $completion += 5;
        }

        // 11. Phone Numbers (5 points)
        if ($this->phoneNumbers()->count() > 0) {
            $completion += 5;
        }

        // 12. Passport Information (10 points)
        if ($this->profile && $this->profile->passport_number) {
            $completion += 10;
        }

        return min(100, $completion);
    }

    /**
     * Get detailed completion breakdown by section.
     */
    public function getProfileCompletionDetails(): array
    {
        return [
            'overall' => $this->calculateProfileCompletion(),
            'sections' => [
                'basic' => [
                    'name' => 'Basic Information',
                    'completed' => $this->name && $this->email,
                    'weight' => 10,
                    'items' => [
                        'name' => !empty($this->name),
                        'email' => !empty($this->email),
                        'phone' => $this->phoneNumbers()->count() > 0,
                    ]
                ],
                'profile' => [
                    'name' => 'Profile Details',
                    'completed' => $this->profile && $this->profile->dob && $this->profile->gender,
                    'weight' => 10,
                    'items' => [
                        'dob' => $this->profile?->dob ? true : false,
                        'gender' => !empty($this->profile?->gender),
                        'nationality' => !empty($this->profile?->nationality),
                        'nid' => !empty($this->profile?->nid),
                        'address' => !empty($this->profile?->present_address_line),
                    ]
                ],
                'education' => [
                    'name' => 'Education & Qualifications',
                    'completed' => $this->educations()->count() > 0,
                    'weight' => 10,
                    'count' => $this->educations()->count(),
                ],
                'experience' => [
                    'name' => 'Work Experience',
                    'completed' => $this->workExperiences()->count() > 0,
                    'weight' => 10,
                    'count' => $this->workExperiences()->count(),
                ],
                'skills' => [
                    'name' => 'Skills & Expertise',
                    'completed' => $this->skills()->count() > 0,
                    'weight' => 10,
                    'count' => $this->skills()->count(),
                ],
                'travel' => [
                    'name' => 'Travel History',
                    'completed' => $this->travelHistory()->count() > 0,
                    'weight' => 5,
                    'count' => $this->travelHistory()->count(),
                ],
                'family' => [
                    'name' => 'Family Information',
                    'completed' => $this->familyMembers()->count() > 0,
                    'weight' => 5,
                    'count' => $this->familyMembers()->count(),
                ],
                'financial' => [
                    'name' => 'Financial Information',
                    'completed' => $this->profile && $this->profile->monthly_income_bdt,
                    'weight' => 10,
                    'items' => [
                        'income' => !empty($this->profile?->monthly_income_bdt),
                        'employer' => !empty($this->profile?->employer_name),
                        'bank' => !empty($this->profile?->bank_balance_bdt),
                    ]
                ],
                'languages' => [
                    'name' => 'Language Proficiency',
                    'completed' => $this->languages()->count() > 0,
                    'weight' => 10,
                    'count' => $this->languages()->count(),
                ],
                'security' => [
                    'name' => 'Background & Security',
                    'completed' => $this->securityInformation ? true : false,
                    'weight' => 5,
                ],
                'passport' => [
                    'name' => 'Passport Information',
                    'completed' => $this->profile && $this->profile->passport_number,
                    'weight' => 10,
                    'items' => [
                        'passport_number' => !empty($this->profile?->passport_number),
                        'issue_date' => !empty($this->profile?->passport_issue_date),
                        'expiry_date' => !empty($this->profile?->passport_expiry_date),
                    ]
                ],
                'phone' => [
                    'name' => 'Phone Numbers',
                    'completed' => $this->phoneNumbers()->count() > 0,
                    'weight' => 5,
                    'count' => $this->phoneNumbers()->count(),
                ],
            ],
            'categories' => [
                'essential' => [
                    'basic' => $this->name && $this->email,
                    'profile' => $this->profile && $this->profile->dob && $this->profile->gender,
                ],
                'professional' => [
                    'education' => $this->educations()->count() > 0,
                    'experience' => $this->workExperiences()->count() > 0,
                    'skills' => $this->skills()->count() > 0,
                    'languages' => $this->languages()->count() > 0,
                ],
                'additional' => [
                    'travel' => $this->travelHistory()->count() > 0,
                    'family' => $this->familyMembers()->count() > 0,
                    'financial' => $this->profile && $this->profile->monthly_income_bdt,
                    'security' => $this->securityInformation ? true : false,
                ],
            ]
        ];
    }

    /**
     * Get profile views for this user.
     */
    public function profileViews(): HasMany
    {
        return $this->hasMany(ProfileView::class);
    }

    /**
     * Get the public profile URL.
     */
    public function getPublicProfileUrlAttribute(): ?string
    {
        if (!$this->public_profile_slug) {
            return null;
        }

        return route('profile.public.show', $this->public_profile_slug);
    }

    /**
     * Check if a section is visible on public profile.
     */
    public function isSectionVisible(string $section): bool
    {
        if (!$this->profile_is_public) {
            return false;
        }

        $settings = $this->profile_visibility_settings ?? [];
        
        return $settings[$section] ?? true; // Default to visible
    }
    /**
     * Smart suggestions generated for the user.
     */
    public function smartSuggestions(): HasMany
    {
        return $this->hasMany(SmartSuggestion::class);
    }

    public function documents(): HasMany
    {
        return $this->hasMany(UserDocument::class);
    }

    public function notifications(): HasMany
    {
        return $this->hasMany(UserNotification::class);
    }

    /**
     * AI profile assessments history for the user.
     */
    public function profileAssessments(): HasMany
    {
        return $this->hasMany(ProfileAssessment::class);
    }
}




