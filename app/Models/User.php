<?php

namespace App\Models;

// --- Use Statements (Combined & Alphabetized) ---
use App\Models\Agency;
use App\Models\Appointment;
use App\Models\ConsultantProfile;
use App\Models\Course; // Needed if adding Course relationships
use App\Models\FlightBooking; // Assuming this model exists
use App\Models\Payment; // Added based on Payment integration
use App\Models\Role;
use App\Models\Skill;
use App\Models\StudentVisaApplication;
use App\Models\TouristVisa;
use App\Models\TravelInsurance;
use App\Models\University; // Needed if adding University relationships
use App\Models\UserDocument;
use App\Models\UserEducation;
// use App\Models\UserExperience; // Removed - Redundant (Bug 9)
use App\Models\UserLanguage; // Added for relationship
use App\Models\UserLicense; // Added for relationship
use App\Models\UserMembership; // Added for relationship
use App\Models\UserPortfolio;
use App\Models\UserProfile;
use App\Models\UserTechnicalEducation; // Added for relationship
use App\Models\UserWorkExperience; // Correct model for experience
use App\Models\WorkVisaApplication;
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
        'is_active',
        'phone',    // Added from Bug 7 fix
        'avatar',   // Added from Bug 7 fix
        // 'skills', // <-- REMOVED (Bug 8)
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
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
            // 'skills' => 'array', // <-- REMOVED (Bug 8)
        ];
    }

    // --- Core Relationships ---

    /**
     * Get the role associated with the user.
     */
    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class);
    }

    /**
     * Check if the user has a specific role.
     * Uses slug for reliable checking.
     */
     public function hasRole(string $roleSlug): bool
     {
         return $this->role?->slug === $roleSlug;
     }

    /**
     * Get the user profile associated with the user.
     * (Renamed from userProfile during debugging)
     */
    public function profile(): HasOne
    {
        return $this->hasOne(UserProfile::class);
    }


    // --- Agency/Consultant/Client Relationships ---

    /**
     * Get the agency this user owns. (Only for 'agency' role)
     */
     public function ownedAgency(): HasOne
     {
         return $this->hasOne(Agency::class, 'owner_id');
     }

    /**
     * The agencies this user works for as a consultant. (Only for 'consultant' role)
     */
     public function agenciesAsConsultant(): BelongsToMany
     {
         return $this->belongsToMany(Agency::class, 'agency_consultant', 'consultant_id', 'agency_id');
     }

    /**
     * The agencies this user is associated with as a client/user.
     */
     public function agenciesAsClient(): BelongsToMany
     {
         return $this->belongsToMany(Agency::class, 'agency_user', 'user_id', 'agency_id');
     }

    /**
     * The clients (users) this consultant manages. (Only for 'consultant' role)
     */
     public function clients(): BelongsToMany
     {
         // Links a consultant (this user) to their clients (other users)
         return $this->belongsToMany(User::class, 'consultant_user', 'consultant_id', 'user_id');
     }

    /**
     * The consultant managing this user. (Only for 'user' role)
     */
     public function consultant(): BelongsToMany
     {
         // Links a user (this user) to their consultant (another user)
         return $this->belongsToMany(User::class, 'consultant_user', 'user_id', 'consultant_id');
     }

    /**
     * Get the consultant profile if this user is a consultant.
     */
     public function consultantProfile(): HasOne
     {
         // Assumes ConsultantProfile model exists and migration was created
         return $this->hasOne(ConsultantProfile::class);
     }


    // --- User Profile Data Relationships ---

    /**
     * Get the education history for the user.
     */
    public function educations(): HasMany
    {
        return $this->hasMany(UserEducation::class);
    }

    /**
     * Get the work experience for the user.
     * (Renamed from experiences & points to correct model - Bug 3 Fix)
     */
    public function workExperiences(): HasMany
    {
        return $this->hasMany(UserWorkExperience::class);
    }

    /**
     * Get the portfolio items for the user.
     */
    public function portfolios(): HasMany
    {
        return $this->hasMany(UserPortfolio::class);
    }

    /**
     * Get the uploaded documents for the user.
     */
    public function documents(): HasMany
    {
        return $this->hasMany(UserDocument::class);
    }

    /**
     * Get the skills for the user (uses pivot table).
     * (Renamed from skillSet during debugging & corrected for Bug 8)
     */
    public function skills(): BelongsToMany
    {
        return $this->belongsToMany(Skill::class, 'user_skill');
    }

    /**
     * Get the languages known by the user.
     */
    public function languages(): HasMany
    {
        // Assumes UserLanguage model exists
        return $this->hasMany(UserLanguage::class);
    }

    /**
     * Get the licenses held by the user.
     */
    public function licenses(): HasMany
    {
        // Assumes UserLicense model exists
        return $this->hasMany(UserLicense::class);
    }

    /**
     * Get the memberships held by the user.
     */
    public function memberships(): HasMany
    {
        // Assumes UserMembership model exists
        return $this->hasMany(UserMembership::class);
    }

    /**
     * Get the technical education records for the user.
     */
    public function technicalEducations(): HasMany
    {
        // Assumes UserTechnicalEducation model exists
        return $this->hasMany(UserTechnicalEducation::class);
    }


    // --- Service Relationships ---

    /**
     * Get the appointments booked by the user (as client).
     */
    public function appointmentsAsClient(): HasMany
    {
        // Assumes Appointment model exists
        return $this->hasMany(Appointment::class, 'user_id');
    }

    /**
     * Get the appointments managed by the user (as consultant).
     */
    public function appointmentsAsConsultant(): HasMany
    {
        // Assumes Appointment model exists
        return $this->hasMany(Appointment::class, 'consultant_id');
    }

    /**
     * Get the flight bookings made by the user.
     */
    public function flightBookings(): HasMany
    {
        // Assumes FlightBooking model exists
        return $this->hasMany(FlightBooking::class);
    }

    /**
     * Get the tourist visa applications submitted by the user.
     */
    public function touristVisas(): HasMany
    {
        // Assumes TouristVisa model exists
        return $this->hasMany(TouristVisa::class);
    }

    /**
     * Get the travel insurances purchased by the user.
     */
    public function travelInsurances(): HasMany
    {
        // Assumes TravelInsurance model exists
        return $this->hasMany(TravelInsurance::class);
    }

    /**
     * Get the student visa applications submitted by the user.
     */
    public function studentVisaApplications(): HasMany
    {
        return $this->hasMany(StudentVisaApplication::class);
    }

    /**
     * Get the work visa applications submitted by the user.
     */
    public function workVisaApplications(): HasMany
    {
         return $this->hasMany(WorkVisaApplication::class);
    }

    /**
     * Get the payments made by the user.
     */
    public function payments(): HasMany
    {
        // Assumes Payment model exists
        return $this->hasMany(Payment::class);
    }

}