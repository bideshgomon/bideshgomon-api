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
        // --- ADD SKILLS HERE IF IT'S NOT ALREADY ---
        'skills',
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
            // --- THIS IS THE FIX ---
            'skills' => 'array',
        ];
    }

    /**
     * Get the role associated with the user.
     */
    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class);
    }

    /**
     * Check if the user has a specific role.
     */
    public function hasRole(string $roleName): bool
    {
        return $this->role?->name === $roleName;
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
        return $this->hasMany(UserExperience::class);
    }

    /**
     * The skills that belong to the user.
     * --- THIS RELATIONSHIP MUST HAVE A DIFFERENT NAME ---
     * --- BECAUSE 'skills' IS ALREADY A COLUMN. ---
     * --- We will rename it to 'skillSet' ---
     */
    public function skillSet(): BelongsToMany
    {
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

    /**
     * Get the consultant profile associated with the user (if they are a consultant).
     */
    public function consultantProfile(): HasOne
    {
        return $this->hasOne(ConsultantProfile::class);
    }

    /**
     * Get the appointments this user has booked (as a client).
     */
    public function appointmentsAsClient(): HasMany
    {
        return $this->hasMany(Appointment::class, 'user_id');
    }

    /**
     * Get the appointments this user is assigned to (as a consultant).
     */
    public function appointmentsAsConsultant(): HasMany
    {
        return $this->hasMany(Appointment::class, 'consultant_id');
    }
}