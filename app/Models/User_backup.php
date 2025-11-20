<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
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
        'role_id',
        'referral_code',
        'referred_by',
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
     * Get the user's wallet.
     */
    public function wallet(): HasOne
    {
        return $this->hasOne(Wallet::class);
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
     * Get the user's education records.
     */
    public function educations(): HasMany
    {
        return $this->hasMany(UserEducation::class);
    }

    /**
     * Get the user's work experience records.
     */
    public function workExperiences(): HasMany
    {
        return $this->hasMany(UserWorkExperience::class);
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
     * Calculate profile completion percentage.
     */
    public function calculateProfileCompletion(): int
    {
        if (!$this->profile) {
            return 0;
        }

        $fields = [
            $this->profile->bio,
            $this->profile->phone,
            $this->profile->dob,
            $this->profile->gender,
            $this->profile->nationality,
            $this->profile->present_address_line,
            $this->profile->present_city,
            $this->profile->present_division,
            $this->profile->nid,
            $this->profile->passport_number,
        ];

        $filled = count(array_filter($fields, fn($value) => !empty($value)));
        $total = count($fields);

        return (int) round(($filled / $total) * 100);
    }
}




