<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AgencyReview extends Model
{
    protected $fillable = [
        'agency_id',
        'user_id',
        'service_application_id',
        'rating',
        'review',
        'agency_response',
        'responded_at',
        'is_verified',
        'is_visible',
    ];

    protected $casts = [
        'rating' => 'integer',
        'responded_at' => 'datetime',
        'is_verified' => 'boolean',
        'is_visible' => 'boolean',
    ];

    public function agency(): BelongsTo
    {
        return $this->belongsTo(Agency::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function serviceApplication(): BelongsTo
    {
        return $this->belongsTo(ServiceApplication::class);
    }

    public function scopeVisible($query)
    {
        return $query->where('is_visible', true);
    }

    public function scopeVerified($query)
    {
        return $query->where('is_verified', true);
    }

    public function scopeRecent($query)
    {
        return $query->latest();
    }
}
