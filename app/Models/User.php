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
use Laravel\Sanctum\HasApiTokens;

// Model Imports
use App\Models\Role;
use App\Models\UserProfile;
use App\Models\UserEducation;
use App\Models\UserExperience;
use App\Models\Skill;
use App\Models\UserDocument;
use App\Models\UserPortfolio;
use App\Models\ConsultantProfile;
use App\Models\Appointment;
use App\Models\FlightBooking;
use App\Models\TouristVisa;
use App\Models\TravelInsurance; // <-- ADDED for new relationship

class User extends Authenticatable implements MustVerifyEmail
{
    use HasApiTokens, HasFactory, Notifiable, SoftDeletes;

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
        'skills', // JSON column
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
            'skills' => 'array', // Cast JSON column to array
        ];
    }

    // --- Relationships ---

    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class);
    }

    public function hasRole(string $roleName): bool
    {
        return $this->role?->name === $roleName;
    }

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
        return $this->hasMany(UserExperience::class);
    }

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

    public function consultantProfile(): HasOne
    {
        return $this->hasOne(ConsultantProfile::class);
    }

    public function appointmentsAsClient(): HasMany
    {
        return $this->hasMany(Appointment::class, 'user_id');
    }

    public function appointmentsAsConsultant(): HasMany
    {
        return $this->hasMany(Appointment::class, 'consultant_id');
    }

    /**
     * Get the flight bookings associated with the user.
     */
    public function flightBookings(): HasMany
    {
        return $this->hasMany(FlightBooking::class);
    }

    /**
     * Get the tourist visa applications for the user.
     */
    public function touristVisas(): HasMany
    {
        return $this->hasMany(TouristVisa::class);
    }

    /**
     * Get the travel insurance policies for the user.
     * (MERGED from new submission)
     */
    public function travelInsurances(): HasMany
    {
        return $this->hasMany(TravelInsurance::class);
    }
}