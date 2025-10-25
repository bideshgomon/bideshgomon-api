<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes; // Keep SoftDeletes if used
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens; // Keep HasApiTokens for API authentication

class User extends Authenticatable implements MustVerifyEmail
{
    // Combine traits from both versions, ensuring Sanctum is included
    use HasApiTokens, HasFactory, Notifiable, SoftDeletes;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role_id',
        'phone', // From second version
        'avatar', // From second version
        'is_active',
        'skills', // JSON column from second version
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
            'skills' => 'array', // Cast JSON column to array from second version
        ];
    }

    // --- Relationships ---

    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class);
    }

    public function hasRole(string $roleName): bool
    {
        // Using nullsafe operator from second version is good practice
        return $this->role?->name === $roleName;
    }

    // Renamed from userProfile() in first version to profile() in second
    public function profile(): HasOne
    {
        return $this->hasOne(UserProfile::class);
    }

    public function educations(): HasMany
    {
        return $this->hasMany(UserEducation::class);
    }

    public function experiences(): HasMany
    {
        // Ensure this points to the correct Experience model if you consolidated
        return $this->hasMany(UserExperience::class);
    }

    // Relationship for Many-to-Many skills (from second version)
    public function skillSet(): BelongsToMany
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

    // Consultant specific relationship (from second version)
    public function consultantProfile(): HasOne
    {
        return $this->hasOne(ConsultantProfile::class);
    }

    // Appointment relationships (from second version)
    public function appointmentsAsClient(): HasMany
    {
        return $this->hasMany(Appointment::class, 'user_id');
    }

    public function appointmentsAsConsultant(): HasMany
    {
        return $this->hasMany(Appointment::class, 'consultant_id');
    }

    // --- ADDED Relationship from first code block ---
    /**
     * Get the flight bookings associated with the user.
     */
    public function flightBookings(): HasMany
    {
        return $this->hasMany(FlightBooking::class);
    }
    // --- END ADDED Relationship ---

    // --- Note: Removed relationships from first code block that were ---
    // --- differently named or structured in the second, preferred version ---
    // public function ownedAgency(): HasOne { ... } // Replaced by role logic?
    // public function agenciesAsConsultant(): BelongsToMany { ... } // Replaced by role logic?
    // public function clients(): BelongsToMany { ... } // Replaced by role logic/appointments?
    // public function consultant(): BelongsToMany { ... } // Replaced by role logic/appointments?
}