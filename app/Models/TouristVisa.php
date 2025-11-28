<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class TouristVisa extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'destination_country_id',
        'application_reference',
        'intended_travel_date',
        'duration_days',
        'profession',
        'status',
        'user_notes',
        'admin_notes',
        'submitted_at',
        'processed_at',
    ];

    protected $casts = [
        'intended_travel_date' => 'date',
        'submitted_at' => 'datetime',
        'processed_at' => 'datetime',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($touristVisa) {
            if (empty($touristVisa->application_reference)) {
                $touristVisa->application_reference = 'TV' . strtoupper(uniqid());
            }
        });
    }

    /**
     * Get the user who submitted the application.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the destination country.
     */
    public function destinationCountry(): BelongsTo
    {
        return $this->belongsTo(Country::class, 'destination_country_id');
    }

    /**
     * Get documents attached to this visa application.
     */
    public function documents(): HasMany
    {
        return $this->hasMany(TouristVisaDocument::class);
    }

    /**
     * Get the service application for agency processing.
     */
    public function serviceApplication(): HasOne
    {
        return $this->hasOne(ServiceApplication::class, 'tourist_visa_id');
    }

    /**
     * Scope to get applications for a specific user.
     */
    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Scope to get applications by status.
     */
    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Check if application can be edited by user.
     */
    public function canBeEditedByUser(): bool
    {
        return in_array($this->status, ['pending', 'submitted']);
    }

    /**
     * Check if application can be deleted by user.
     */
    public function canBeDeletedByUser(): bool
    {
        return $this->status === 'pending';
    }
}
