<?php

namespace App\Models;

// *** Use statements from Original File ***
use Illuminate\Contracts\Auth\MustVerifyEmail; // Added MustVerifyEmail based on previous User model merges
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes; // Added SoftDeletes based on previous merges
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

// *** Model Imports (Combined & Alphabetized) ***
use App\Models\Agency;
use App\Models\Appointment; // Added based on previous merges
use App\Models\ConsultantProfile; // Added based on previous merges
use App\Models\FlightBooking; // Added based on previous merges
use App\Models\Role;
use App\Models\Skill;
use App\Models\StudentVisaApplication; // <-- ADDED for new relationship
use App\Models\TouristVisa; // Added based on previous merges
use App\Models\TravelInsurance; // Added based on previous merges
use App\Models\UserDocument;
use App\Models\UserEducation;
use App\Models\UserExperience;
use App\Models\UserPortfolio;
use App\Models\UserProfile;


// Note: Assuming MustVerifyEmail and SoftDeletes were added in previous merges based on context.
class User extends Authenticatable implements MustVerifyEmail
{
    // Use traits consistent with previous merges
    use HasApiTokens, HasFactory, Notifiable, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     * (Reflects removal of phone, avatar, skills JSON based on previous merges)
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role_id',
        'is_active',
    ];

    /**
     * The attributes that should be hidden for serialization.
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     * (Reflects removal of skills JSON based on previous merges)
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

    // --- Core Relationships ---

    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class);
    }

     public function hasRole(string $roleName): bool // Added helper based on previous merges
     {
         return $this->role?->name === $roleName;
     }

    public function profile(): HasOne
    {
        return $this->hasOne(UserProfile::class);
    }

    // --- Agency/Consultant/Client Relationships ---

     public function ownedAgency(): HasOne // Kept from original file
     {
         return $this->hasOne(Agency::class, 'owner_id');
     }

     public function agenciesAsConsultant(): BelongsToMany // Kept from original file
     {
         return $this->belongsToMany(Agency::class, 'agency_consultant', 'consultant_id', 'agency_id');
     }

     // Added based on previous merges, assuming agency_user table exists
     public function agenciesAsClient(): BelongsToMany
     {
         return $this->belongsToMany(Agency::class, 'agency_user', 'user_id', 'agency_id');
     }

     public function clients(): BelongsToMany // Kept from original file
     {
         return $this->belongsToMany(User::class, 'consultant_user', 'consultant_id', 'user_id');
     }

     public function consultant(): BelongsToMany // Kept from original file
     {
         return $this->belongsToMany(User::class, 'consultant_user', 'user_id', 'consultant_id');
     }

     public function consultantProfile(): HasOne // Added based on previous merges
     {
         return $this->hasOne(ConsultantProfile::class);
     }


    // --- User Profile Data Relationships ---

    public function educations(): HasMany
    {
        return $this->hasMany(UserEducation::class);
    }

    public function experiences(): HasMany
    {
        return $this->hasMany(UserExperience::class);
    }

    public function portfolios(): HasMany
    {
        return $this->hasMany(UserPortfolio::class);
    }

    public function documents(): HasMany
    {
        return $this->hasMany(UserDocument::class);
    }

    public function skillSet(): BelongsToMany
    {
        return $this->belongsToMany(Skill::class, 'user_skill');
    }

    // --- Service Relationships ---

    public function appointmentsAsClient(): HasMany // Added based on previous merges
    {
        return $this->hasMany(Appointment::class, 'user_id');
    }

    public function appointmentsAsConsultant(): HasMany // Added based on previous merges
    {
        return $this->hasMany(Appointment::class, 'consultant_id');
    }

    public function flightBookings(): HasMany // Added based on previous merges
    {
        return $this->hasMany(FlightBooking::class);
    }

    public function touristVisas(): HasMany // Added based on previous merges
    {
        return $this->hasMany(TouristVisa::class);
    }

    public function travelInsurances(): HasMany // Added based on previous merges
    {
        return $this->hasMany(TravelInsurance::class);
    }

    /**
     * Get the student visa applications for the user.
     * (MERGED from new submission)
     */
    public function studentVisaApplications(): HasMany
    {
        return $this->hasMany(StudentVisaApplication::class);
    }
}