<?php

namespace App\Models;

// --- Use Statements ---
use App\Models\Agency;
use App\Models\Appointment;
use App\Models\ConsultantProfile;
use App\Models\Course;
use App\Models\FlightBooking;
use App\Models\Payment;
use App\Models\Role;
use App\Models\Skill;
use App\Models\StudentVisaApplication;
use App\Models\TouristVisa;
use App\Models\TravelInsurance;
use App\Models\University;
use App\Models\UserDocument;
use App\Models\UserEducation;
use App\Models\UserLanguage;
use App\Models\UserLicense;
use App\Models\UserMembership;
use App\Models\UserPortfolio;
use App\Models\UserProfile;
use App\Models\UserTechnicalEducation;
use App\Models\UserWorkExperience; // Correct model
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
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role_id',
        'is_active',
        'phone',
        'avatar',
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
     * The attributes that should be cast.
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'is_active' => 'boolean',
    ];

    /*
    |--------------------------------------------------------------------------
    | Core Relationships
    |--------------------------------------------------------------------------
    */
    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class);
    }

    public function profile(): HasOne
    {
        return $this->hasOne(UserProfile::class);
    }

    /*
    |--------------------------------------------------------------------------
    | CV Builder Relationships
    |--------------------------------------------------------------------------
    */
    public function educations(): HasMany
    {
        return $this->hasMany(UserEducation::class);
    }

    /**
     * Get the work experience for the user. (CORRECT)
     */
    public function workExperiences(): HasMany
    {
        return $this->hasMany(UserWorkExperience::class);
    }

    // --- Obsolete experiences() relationship REMOVED ---

    public function portfolios(): HasMany
    {
        return $this->hasMany(UserPortfolio::class);
    }

    public function documents(): HasMany
    {
        return $this->hasMany(UserDocument::class);
    }

    public function skills(): BelongsToMany
    {
        return $this->belongsToMany(Skill::class, 'user_skill');
    }

    public function languages(): HasMany
    {
        return $this->hasMany(UserLanguage::class);
    }

    public function licenses(): HasMany
    {
        return $this->hasMany(UserLicense::class);
    }

    public function memberships(): HasMany
    {
        return $this->hasMany(UserMembership::class);
    }

    public function technicalEducations(): HasMany
    {
        return $this->hasMany(UserTechnicalEducation::class);
    }


    /*
    |--------------------------------------------------------------------------
    | Agency & Consultant Relationships
    |--------------------------------------------------------------------------
    */
    public function ownedAgency(): HasOne
    {
        return $this->hasOne(Agency::class, 'owner_id');
    }

    public function agenciesAsConsultant(): BelongsToMany
    {
        return $this->belongsToMany(Agency::class, 'agency_consultant', 'consultant_id', 'agency_id');
    }

    public function agenciesAsClient(): BelongsToMany
    {
        return $this->belongsToMany(Agency::class, 'agency_user', 'user_id', 'agency_id');
    }

    public function clients(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'consultant_user', 'consultant_id', 'user_id');
    }

    public function consultant(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'consultant_user', 'user_id', 'consultant_id');
    }

    public function consultantProfile(): HasOne
    {
        return $this->hasOne(ConsultantProfile::class, 'consultant_id');
    }


    /*
    |--------------------------------------------------------------------------
    | Service Relationships
    |--------------------------------------------------------------------------
    */
    public function appointmentsAsClient(): HasMany
    {
        return $this->hasMany(Appointment::class, 'client_id');
    }

    public function appointmentsAsConsultant(): HasMany
    {
        return $this->hasMany(Appointment::class, 'consultant_id');
    }

    public function flightBookings(): HasMany
    {
        return $this->hasMany(FlightBooking::class);
    }

    public function touristVisas(): HasMany
    {
        return $this->hasMany(TouristVisa::class);
    }

    public function travelInsurances(): HasMany
    {
        return $this->hasMany(TravelInsurance::class);
    }

    public function studentVisaApplications(): HasMany
    {
        return $this->hasMany(StudentVisaApplication::class);
    }

    public function workVisaApplications(): HasMany
    {
         return $this->hasMany(WorkVisaApplication::class);
    }

    // --- THIS IS THE FIX ---
    // The stray "Signature: ..." line has been removed from before this method.
    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }
}