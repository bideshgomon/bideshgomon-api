<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserVisaHistory extends Model
{
    use HasFactory;

    protected $table = 'user_visa_history';

    protected $fillable = [
        'user_id',
        'user_passport_id',
        'country',
        'visa_type',
        'visa_category',
        'visa_number',
        'application_date',
        'issue_date',
        'expiry_date',
        'entry_date',
        'exit_date',
        'duration_of_stay',
        'purpose_of_visit',
        'status',
        'was_visa_rejected',
        'rejection_reason',
        'overstay_occurred',
        'overstay_days',
        'application_reference',
        'embassy_location',
        'visa_fee',
        'currency',
        'visa_scan_path',
        'approval_letter_path',
        'rejection_letter_path',
        'notes',
    ];

    protected $casts = [
        'application_date' => 'date',
        'issue_date' => 'date',
        'expiry_date' => 'date',
        'entry_date' => 'date',
        'exit_date' => 'date',
        'was_visa_rejected' => 'boolean',
        'overstay_occurred' => 'boolean',
        'duration_of_stay' => 'integer',
        'overstay_days' => 'integer',
        'visa_fee' => 'decimal:2',
    ];

    /**
     * Get the user that owns the visa history.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the passport associated with this visa.
     */
    public function passport(): BelongsTo
    {
        return $this->belongsTo(UserPassport::class, 'user_passport_id');
    }

    /**
     * Check if visa was rejected.
     */
    public function wasRejected(): bool
    {
        return $this->was_visa_rejected === true;
    }

    /**
     * Check if overstay occurred.
     */
    public function hadOverstay(): bool
    {
        return $this->overstay_occurred === true;
    }

    /**
     * Check if visa is expired.
     */
    public function isExpired(): bool
    {
        return $this->expiry_date && $this->expiry_date < now();
    }

    /**
     * Get the country name from ISO code.
     */
    public function getCountryNameAttribute(): string
    {
        $countries = config('countries');
        return $countries[$this->country] ?? $this->country;
    }

    /**
     * Scope to get all rejections.
     */
    public function scopeRejected($query)
    {
        return $query->where('was_visa_rejected', true);
    }

    /**
     * Scope to get all overstays.
     */
    public function scopeWithOverstay($query)
    {
        return $query->where('overstay_occurred', true);
    }

    /**
     * Scope to get visas for a specific country.
     */
    public function scopeForCountry($query, string $countryCode)
    {
        return $query->where('country', $countryCode);
    }
}
