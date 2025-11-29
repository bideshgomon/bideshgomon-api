<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Agency extends Model
{
    protected $fillable = [
        'user_id',
        'name',
        'company_name',
        'registration_number',
        'phone',
        'whatsapp',
        'email',
        'address',
        'city',
        'country',
        'postal_code',
        'logo_path',
        'description',
        'meta_description',
        'website',
        'facebook_url',
        'linkedin_url',
        'twitter_url',
        'instagram_url',
        'business_type',
        'agency_type_id',
        'established_year',
        'license_number',
        'license_expiry',
        'services_offered',
        'countries_expertise',
        'languages_spoken',
        'slug',
        'certifications',
        'awards',
        'commission_rate',
        'total_clients',
        'successful_applications',
        'success_rate',
        'average_rating',
        'total_reviews',
        'team_size',
        'office_hours',
        'office_images',
        'verification_documents',
        'verification_notes',
        'profile_completed_at',
        'is_verified',
        'is_active',
        'is_featured',
        'is_premium',
        'verified_at',
        'featured_until',
        'premium_until',
    ];

    protected $casts = [
        'commission_rate' => 'decimal:2',
        'success_rate' => 'decimal:2',
        'average_rating' => 'decimal:2',
        'services_offered' => 'array',
        'countries_expertise' => 'array',
        'languages_spoken' => 'array',
        'office_images' => 'array',
        'certifications' => 'array',
        'awards' => 'array',
        'verification_documents' => 'array',
        'is_verified' => 'boolean',
        'is_active' => 'boolean',
        'is_featured' => 'boolean',
        'is_premium' => 'boolean',
        'verified_at' => 'datetime',
        'featured_until' => 'datetime',
        'premium_until' => 'datetime',
        'profile_completed_at' => 'datetime',
        'license_expiry' => 'date',
        'established_year' => 'integer',
        'total_clients' => 'integer',
        'successful_applications' => 'integer',
        'total_reviews' => 'integer',
        'team_size' => 'integer',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($agency) {
            if (empty($agency->slug)) {
                $agency->slug = Str::slug($agency->name);
            }
        });

        static::updating(function ($agency) {
            if ($agency->isDirty('name') && empty($agency->slug)) {
                $agency->slug = Str::slug($agency->name);
            }
        });
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function agencyType(): BelongsTo
    {
        return $this->belongsTo(AgencyType::class);
    }

    public function serviceApplications(): HasMany
    {
        return $this->hasMany(ServiceApplication::class);
    }

    public function quotes(): HasMany
    {
        return $this->hasMany(ServiceQuote::class);
    }

    public function countryAssignments(): HasMany
    {
        return $this->hasMany(AgencyCountryAssignment::class);
    }

    public function teamMembers(): HasMany
    {
        return $this->hasMany(AgencyTeamMember::class);
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(AgencyReview::class);
    }

    public function verificationDocuments(): HasMany
    {
        return $this->hasMany(AgencyVerificationDocument::class);
    }

    public function verificationRequests(): HasMany
    {
        return $this->hasMany(AgencyVerificationRequest::class);
    }

    // Scopes
    public function scopeVerified($query)
    {
        return $query->where('is_verified', true);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true)
            ->where(function ($q) {
                $q->whereNull('featured_until')
                  ->orWhere('featured_until', '>', now());
            });
    }

    public function scopePremium($query)
    {
        return $query->where('is_premium', true)
            ->where(function ($q) {
                $q->whereNull('premium_until')
                  ->orWhere('premium_until', '>', now());
            });
    }

    public function scopeWithRating($query, $minRating = 0)
    {
        return $query->where('average_rating', '>=', $minRating);
    }

    // Helpers
    public function getRouteKeyName()
    {
        return 'slug';
    }

    public function isFeaturedActive()
    {
        return $this->is_featured && 
               (!$this->featured_until || $this->featured_until->isFuture());
    }

    public function isPremiumActive()
    {
        return $this->is_premium && 
               (!$this->premium_until || $this->premium_until->isFuture());
    }

    public function calculateSuccessRate()
    {
        if ($this->total_clients == 0) {
            return 0;
        }
        return round(($this->successful_applications / $this->total_clients) * 100, 2);
    }

    public function updateRating()
    {
        $reviews = $this->reviews()->visible()->get();
        $this->total_reviews = $reviews->count();
        $this->average_rating = $reviews->count() > 0 ? round($reviews->avg('rating'), 2) : 0;
        $this->save();
    }

    public function isProfileComplete()
    {
        return !empty($this->name) &&
               !empty($this->email) &&
               !empty($this->phone) &&
               !empty($this->address) &&
               !empty($this->description) &&
               !empty($this->business_type) &&
               !empty($this->services_offered);
    }
}
