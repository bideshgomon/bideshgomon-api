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

// Traits are correctly included
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
        // 'skills' is correctly NOT cast here, aligning with the relationship approach
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
        ];
    }

    // --- Relationships ---

    /**
     * Get the role associated with the user.
     */
    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class);
    }

    /**
     * Get the profile associated with the user.
     */
    public function profile(): HasOne
    {
        return $this->hasOne(UserProfile::class);
    }

    /**
     * Get the educations for the user.
     */
    public function educations(): HasMany
    {
        return $this->hasMany(UserEducation::class);
    }

    /**
     * Get the experiences for the user.
     */
    public function experiences(): HasMany
    {
        // Assuming UserExperience is the intended model
        return $this->hasMany(UserExperience::class);
    }

    /**
     * The skills that belong to the user (Many-to-Many).
     */
    public function skills(): BelongsToMany
    {
        // Correctly defined BelongsToMany relationship
        return $this->belongsToMany(Skill::class, 'user_skill');
    }

    /**
     * Get the documents for the user.
     */
    public function documents(): HasMany
    {
        return $this->hasMany(UserDocument::class);
    }

    /**
     * Get the portfolio items for the user.
     */
    public function portfolios(): HasMany
    {
        return $this->hasMany(UserPortfolio::class);
    }

    // --- Helper Methods ---

    /**
     * Check if the user has a specific role.
     */
    public function hasRole(string $roleName): bool
    {
        // Correctly uses the nullsafe operator and checks the name property
        return $this->role?->name === $roleName;
    }

    // ---
    // Confirmed: There is NO 'getSkillsAttribute' method present.
    // ---
}