<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasFactory, Notifiable, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role_id',
        'phone',
        'avatar',
        'is_active',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
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
            'is_active' => 'boolean',
        ];
    }

    // --- Relationships ---

    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class);
    }

    public function profile(): HasOne
    {
        return $this->hasOne(UserProfile::class);
    }

    public function educations(): HasMany
    {
        return $this->hasMany(UserEducation::class);
    }

    /**
     * Get the experiences for the user.
     */
    public function experiences(): HasMany
    {
        // --- Corrected ---
        return $this->hasMany(UserWorkExperience::class);
        // --- End Correction ---
    }

    public function skills(): BelongsToMany
    {
        return $this->belongsToMany(Skill::class, 'user_skill');
    }

    public function documents(): HasMany
    {
        return $this->hasMany(UserDocument::class);
    }

    public function portfolios(): HasMany
    {
        return $this->hasMany(UserPortfolio::class);
    }

    public function travelHistories(): HasMany
    {
        return $this->hasMany(UserTravelHistory::class)->orderBy('entry_date', 'desc');
    }

    // --- NEW RELATIONSHIPS WE ADDED ---

    public function languages(): HasMany
    {
        return $this->hasMany(UserLanguage::class);
    }

    public function licenses(): HasMany
    {
        return $this->hasMany(UserLicense::class)->orderBy('issue_date', 'desc');
    }

    public function technicalEducations(): HasMany
    {
        return $this->hasMany(UserTechnicalEducation::class)->orderBy('start_date', 'desc');
    }

    public function memberships(): HasMany
    {
        return $this->hasMany(UserMembership::class)->orderBy('start_date', 'desc');
    }

    // --- Helper Methods ---

    public function hasRole(string $roleName): bool
    {
        return $this->role?->name === $roleName;
    }
}
