<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class UserPassport extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'passport_number',
        'passport_type',
        'issuing_country',
        'issuing_authority',
        'issue_date',
        'expiry_date',
        'place_of_issue',
        'is_current_passport',
        'is_lost_or_stolen',
        'reported_lost_date',
        'surname',
        'given_names',
        'nationality',
        'sex',
        'date_of_birth',
        'place_of_birth',
        'passport_scan_path',
        'additional_pages_path',
        'notes',
    ];

    protected $casts = [
        'issue_date' => 'date',
        'expiry_date' => 'date',
        'reported_lost_date' => 'date',
        'date_of_birth' => 'date',
        'is_current_passport' => 'boolean',
        'is_lost_or_stolen' => 'boolean',
    ];

    /**
     * Get the user that owns the passport.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the visa history associated with this passport.
     */
    public function visaHistory(): HasMany
    {
        return $this->hasMany(UserVisaHistory::class);
    }

    /**
     * Get the travel history associated with this passport.
     */
    public function travelHistory(): HasMany
    {
        return $this->hasMany(UserTravelHistory::class);
    }

    /**
     * Check if passport is expired.
     */
    public function isExpired(): bool
    {
        return $this->expiry_date < now();
    }

    /**
     * Check if passport is expiring soon (within 6 months).
     */
    public function isExpiringSoon(): bool
    {
        return $this->expiry_date <= now()->addMonths(6);
    }

    /**
     * Get days until expiry.
     */
    public function daysUntilExpiry(): int
    {
        return now()->diffInDays($this->expiry_date, false);
    }

    /**
     * Check if passport has sufficient validity for a country (6 months).
     */
    public function hasMinimumValidity(int $months = 6): bool
    {
        return $this->expiry_date >= now()->addMonths($months);
    }
}
