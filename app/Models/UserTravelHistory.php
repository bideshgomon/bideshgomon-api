<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserTravelHistory extends Model
{
    use HasFactory;

    protected $table = 'user_travel_history';

    protected $fillable = [
        'user_id',
        'user_passport_id',
        'user_visa_history_id',
        'country_visited',
        'city_visited',
        'region_visited',
        'entry_date',
        'exit_date',
        'duration_days',
        'purpose',
        'purpose_details',
        'accommodation_type',
        'accommodation_address',
        'accommodation_name',
        'travel_companions',
        'entry_port',
        'exit_port',
        'flight_number',
        'flight_ticket_path',
        'hotel_booking_path',
        'travel_insurance_path',
        'photos_path',
        'compliance_issues',
        'compliance_notes',
        'notes',
    ];

    protected $casts = [
        'entry_date' => 'date',
        'exit_date' => 'date',
        'duration_days' => 'integer',
        'travel_companions' => 'array',
        'compliance_issues' => 'boolean',
    ];

    /**
     * Get the user that owns the travel history.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the passport used for this travel.
     */
    public function passport(): BelongsTo
    {
        return $this->belongsTo(UserPassport::class, 'user_passport_id');
    }

    /**
     * Get the visa used for this travel.
     */
    public function visaHistory(): BelongsTo
    {
        return $this->belongsTo(UserVisaHistory::class, 'user_visa_history_id');
    }

    /**
     * Calculate duration in days if not set.
     */
    public function calculateDuration(): ?int
    {
        if ($this->entry_date && $this->exit_date) {
            return $this->entry_date->diffInDays($this->exit_date);
        }
        return null;
    }

    /**
     * Check if travel is ongoing (no exit date).
     */
    public function isOngoing(): bool
    {
        return $this->entry_date && !$this->exit_date;
    }

    /**
     * Get the country name from ISO code.
     */
    public function getCountryNameAttribute(): string
    {
        $countries = config('countries');
        return $countries[$this->country_visited] ?? $this->country_visited;
    }

    /**
     * Scope to get travels to a specific country.
     */
    public function scopeToCountry($query, string $countryCode)
    {
        return $query->where('country_visited', $countryCode);
    }

    /**
     * Scope to get travels for a specific purpose.
     */
    public function scopeForPurpose($query, string $purpose)
    {
        return $query->where('purpose', $purpose);
    }

    /**
     * Scope to get recent travels (last X months).
     */
    public function scopeRecent($query, int $months = 12)
    {
        return $query->where('entry_date', '>=', now()->subMonths($months));
    }
}
