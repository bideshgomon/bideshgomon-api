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

class User extends Authenticatable implements MustVerifyEmail
{
    use HasApiTokens, HasFactory, Notifiable, SoftDeletes;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role_id',
        'is_active',
        'email_verified_at', // Added for seeder
    ];

    protected $hidden = [ 'password', 'remember_token' ];

    protected function casts(): array {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
        ];
    }

    // --- Core Role & Profile ---
    public function role(): BelongsTo { return $this->belongsTo(Role::class); }
    public function profile(): HasOne { return $this->hasOne(UserProfile::class); }
    public function hasRole(string $roleName): bool { return $this->role?->name === $roleName; }

    // --- Agency & Consultant Relationships ---
    public function ownedAgency(): HasOne { return $this->hasOne(Agency::class, 'owner_id'); }
    public function agenciesAsConsultant(): BelongsToMany { return $this->belongsToMany(Agency::class, 'agency_consultant', 'consultant_id', 'agency_id'); }
    public function agenciesAsClient(): BelongsToMany { return $this->belongsToMany(Agency::class, 'agency_user', 'user_id', 'agency_id'); }
    public function consultantProfile(): HasOne { return $this->hasOne(ConsultantProfile::class); }

    // --- User Profile Data Sections ---
    public function educations(): HasMany { return $this->hasMany(UserEducation::class); }
    public function experiences(): HasMany { return $this->hasMany(UserExperience::class); }
    public function skillSet(): BelongsToMany { return $this->belongsToMany(Skill::class, 'user_skill'); }
    public function documents(): HasMany { return $this->hasMany(UserDocument::class); }
    public function portfolios(): HasMany { return $this->hasMany(UserPortfolio::class); }
    public function licenses(): HasMany { return $this->hasMany(UserLicense::class); }
    public function languages(): HasMany { return $this->hasMany(UserLanguage::class); }
    public function technicalEducations(): HasMany { return $this->hasMany(UserTechnicalEducation::class); }
    public function memberships(): HasMany { return $this->hasMany(UserMembership::class); }

    // --- Service Relationships ---
    public function appointmentsAsClient(): HasMany { return $this->hasMany(Appointment::class, 'user_id'); }
    public function appointmentsAsConsultant(): HasMany { return $this->hasMany(Appointment::class, 'consultant_id'); }
    public function flightBookings(): HasMany { return $this->hasMany(FlightBooking::class); }
    public function touristVisas(): HasMany { return $this->hasMany(TouristVisa::class); }
    public function travelInsurances(): HasMany { return $this->hasMany(TravelInsurance::class); }
}