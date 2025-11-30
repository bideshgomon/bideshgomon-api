<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ServiceModule extends Model
{

    protected $fillable = [
        'service_category_id',
        'name',
        'slug',
        'service_type', // Added: query_based, api_based, premade, marketplace
        'short_description',
        'full_description',
        'icon',
        'image',
        'is_active',
        'is_featured',
        'coming_soon',
        'launch_date',
        'base_price',
        'price_type',
        'currency',
        'requirements',
        'documents_required',
        'processing_time',
        'settings',
        'route_prefix',
        'controller',
        'allowed_roles',
        'min_profile_completion',
        'meta_title',
        'meta_description',
        'meta_keywords',
        'views_count',
        'applications_count',
        'completions_count',
        'sort_order',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'is_featured' => 'boolean',
        'coming_soon' => 'boolean',
        'launch_date' => 'date',
        'base_price' => 'decimal:2',
        'requirements' => 'array',
        'documents_required' => 'array',
        'processing_time' => 'array',
        'settings' => 'array',
        'allowed_roles' => 'array',
        'min_profile_completion' => 'integer',
        'views_count' => 'integer',
        'applications_count' => 'integer',
        'completions_count' => 'integer',
        'sort_order' => 'integer',
    ];

    /**
     * Get the category this module belongs to
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(ServiceCategory::class, 'service_category_id');
    }

    /**
     * Get all applications for this service
     */
    public function applications(): HasMany
    {
        return $this->hasMany(ServiceApplication::class);
    }

    /**
     * Get all reviews for this service
     */
    public function reviews(): HasMany
    {
        return $this->hasMany(ServiceReview::class);
    }

    /**
     * Get approved reviews only
     */
    public function approvedReviews(): HasMany
    {
        return $this->hasMany(ServiceReview::class)->where('is_approved', true);
    }

    /**
     * Scope: Only active services
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope: Featured services
     */
    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true)->where('is_active', true);
    }

    /**
     * Scope: Coming soon services
     */
    public function scopeComingSoon($query)
    {
        return $query->where('coming_soon', true);
    }

    /**
     * Scope: Available now (active and not coming soon)
     */
    public function scopeAvailable($query)
    {
        return $query->where('is_active', true)->where('coming_soon', false);
    }

    /**
     * Scope: Ordered by sort_order
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order')->orderBy('name');
    }

    /**
     * Increment views count
     */
    public function incrementViews(): void
    {
        $this->increment('views_count');
    }

    /**
     * Increment applications count
     */
    public function incrementApplications(): void
    {
        $this->increment('applications_count');
    }

    /**
     * Increment completions count
     */
    public function incrementCompletions(): void
    {
        $this->increment('completions_count');
    }

    /**
     * Get average rating
     */
    public function getAverageRatingAttribute(): float
    {
        return round($this->approvedReviews()->avg('rating') ?? 0, 1);
    }

    /**
     * Get total reviews count
     */
    public function getReviewsCountAttribute(): int
    {
        return $this->approvedReviews()->count();
    }

    /**
     * Check if user can access this service
     */
    public function canBeAccessedBy(User $user): bool
    {
        // Check if service is active
        if (!$this->is_active || $this->coming_soon) {
            return false;
        }

        // Check if user's role is allowed
        if (!empty($this->allowed_roles) && !in_array($user->role->name, $this->allowed_roles)) {
            return false;
        }

        // Check profile completion requirement
        if ($this->min_profile_completion > 0) {
            // Calculate user's profile completion percentage
            // This would need to be implemented in User model
            $profileCompletion = $user->profile_completion_percentage ?? 0;
            if ($profileCompletion < $this->min_profile_completion) {
                return false;
            }
        }

        return true;
    }

    /**
     * Get formatted price
     */
    public function getFormattedPriceAttribute(): string
    {
        if ($this->price_type === 'free') {
            return 'Free';
        }

        if ($this->price_type === 'quote') {
            return 'Get Quote';
        }

        $symbol = $this->currency === 'BDT' ? '৳' : '$';
        
        if ($this->price_type === 'variable') {
            return $symbol . number_format($this->base_price, 0) . '+';
        }

        return $symbol . number_format($this->base_price, 0);
    }

    /**
     * Get processing time text
     */
    public function getProcessingTimeTextAttribute(): ?string
    {
        if (empty($this->processing_time)) {
            return null;
        }

        $min = $this->processing_time['min'] ?? 0;
        $max = $this->processing_time['max'] ?? 0;
        $unit = $this->processing_time['unit'] ?? 'days';

        if ($min === $max) {
            return "{$min} {$unit}";
        }

        return "{$min}-{$max} {$unit}";
    }

    /**
     * Get completion rate percentage
     */
    public function getCompletionRateAttribute(): float
    {
        if ($this->applications_count === 0) {
            return 0;
        }

        return round(($this->completions_count / $this->applications_count) * 100, 1);
    }
}
