<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ServiceReview extends Model
{
    protected $fillable = [
        'user_id',
        'service_module_id',
        'service_application_id',
        'rating',
        'review',
        'ratings_breakdown',
        'is_verified',
        'is_featured',
        'is_approved',
        'admin_response',
        'admin_responded_at',
        'helpful_count',
        'not_helpful_count',
    ];

    protected $casts = [
        'rating' => 'integer',
        'ratings_breakdown' => 'array',
        'is_verified' => 'boolean',
        'is_featured' => 'boolean',
        'is_approved' => 'boolean',
        'admin_responded_at' => 'datetime',
        'helpful_count' => 'integer',
        'not_helpful_count' => 'integer',
    ];

    /**
     * Get the user who wrote the review
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the service module
     */
    public function serviceModule(): BelongsTo
    {
        return $this->belongsTo(ServiceModule::class);
    }

    /**
     * Get the related application
     */
    public function application(): BelongsTo
    {
        return $this->belongsTo(ServiceApplication::class, 'service_application_id');
    }

    /**
     * Scope: Approved reviews only
     */
    public function scopeApproved($query)
    {
        return $query->where('is_approved', true);
    }

    /**
     * Scope: Featured reviews
     */
    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true)->where('is_approved', true);
    }

    /**
     * Scope: Verified reviews only
     */
    public function scopeVerified($query)
    {
        return $query->where('is_verified', true);
    }

    /**
     * Scope: Pending approval
     */
    public function scopePending($query)
    {
        return $query->where('is_approved', false);
    }

    /**
     * Get rating stars array for display
     */
    public function getRatingStarsAttribute(): array
    {
        return [
            'full' => floor($this->rating),
            'half' => ($this->rating - floor($this->rating)) >= 0.5 ? 1 : 0,
            'empty' => 5 - ceil($this->rating),
        ];
    }
}
