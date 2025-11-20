<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FlightQuote extends Model
{
    use HasFactory;

    protected $fillable = [
        'flight_request_id',
        'quoted_by',
        'airline_name',
        'flight_number',
        'quoted_price',
        'price_breakdown',
        'flight_details',
        'valid_until',
        'is_valid',
        'status',
        'notes',
    ];

    protected $casts = [
        'quoted_price' => 'decimal:2',
        'valid_until' => 'datetime',
        'is_valid' => 'boolean',
    ];

    /**
     * Relationships
     */
    public function flightRequest(): BelongsTo
    {
        return $this->belongsTo(FlightRequest::class);
    }

    public function quotedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'quoted_by');
    }

    /**
     * Scopes
     */
    public function scopeValid($query)
    {
        return $query->where('is_valid', true)
            ->where('valid_until', '>', now());
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeAccepted($query)
    {
        return $query->where('status', 'accepted');
    }

    /**
     * Helpers
     */
    public function isExpired(): bool
    {
        return $this->valid_until < now();
    }

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function isAccepted(): bool
    {
        return $this->status === 'accepted';
    }

    public function getFormattedPriceAttribute(): string
    {
        return '৳ ' . number_format($this->quoted_price);
    }

    public function getStatusColorAttribute(): string
    {
        return match($this->status) {
            'pending' => 'bg-yellow-100 text-yellow-800',
            'accepted' => 'bg-green-100 text-green-800',
            'rejected' => 'bg-red-100 text-red-800',
            'expired' => 'bg-gray-100 text-gray-800',
            default => 'bg-gray-100 text-gray-800',
        };
    }
}
