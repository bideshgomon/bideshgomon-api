<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FlightBooking extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'booking_reference',
        'pnr_number',
        'ticket_number',
        'user_id',
        'flight_route_id',
        'trip_type',
        'return_flight_route_id',
        'return_travel_date',
        'multi_city_segments',
        'return_base_fare',
        'multi_city_total',
        'travel_date',
        'flight_class',
        'passengers_count',
        'passengers',
        'contact_name',
        'contact_email',
        'contact_phone',
        'special_requests',
        'selected_seats',
        'seat_selection_fee',
        'extra_baggage_count',
        'extra_baggage_fee',
        'meal_preferences',
        'meal_fee',
        'base_fare',
        'taxes_fees',
        'booking_fee',
        'insurance_fee',
        'subtotal',
        'discount_amount',
        'promo_code',
        'total_amount',
        'payment_status',
        'payment_method',
        'payment_reference',
        'paid_at',
        'status',
        'confirmed_at',
        'cancelled_at',
        'cancellation_reason',
        'refund_amount',
        'ticket_issued',
        'ticket_issued_at',
        'ticket_file_path',
        'checked_in',
        'checked_in_at',
        'boarding_passes',
        'admin_notes',
        'reviewed_by',
        'reviewed_at',
        'booking_source',
        'ip_address',
    ];

    protected $casts = [
        'passengers' => 'array',
        'selected_seats' => 'array',
        'meal_preferences' => 'array',
        'boarding_passes' => 'array',
        'multi_city_segments' => 'array',
        'travel_date' => 'date',
        'return_travel_date' => 'date',
        'paid_at' => 'datetime',
        'confirmed_at' => 'datetime',
        'cancelled_at' => 'datetime',
        'ticket_issued_at' => 'datetime',
        'checked_in_at' => 'datetime',
        'reviewed_at' => 'datetime',
        'base_fare' => 'decimal:2',
        'taxes_fees' => 'decimal:2',
        'booking_fee' => 'decimal:2',
        'insurance_fee' => 'decimal:2',
        'seat_selection_fee' => 'decimal:2',
        'extra_baggage_fee' => 'decimal:2',
        'meal_fee' => 'decimal:2',
        'subtotal' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'refund_amount' => 'decimal:2',
        'return_base_fare' => 'decimal:2',
        'multi_city_total' => 'decimal:2',
        'ticket_issued' => 'boolean',
        'checked_in' => 'boolean',
    ];

    /**
     * Boot method to generate booking reference
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($booking) {
            if (empty($booking->booking_reference)) {
                $booking->booking_reference = 'FB' . date('Ymd') . str_pad(
                    static::whereDate('created_at', today())->count() + 1,
                    3,
                    '0',
                    STR_PAD_LEFT
                );
            }
        });
    }

    /**
     * Relationships
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function flightRoute(): BelongsTo
    {
        return $this->belongsTo(FlightRoute::class);
    }

    public function returnFlightRoute(): BelongsTo
    {
        return $this->belongsTo(FlightRoute::class, 'return_flight_route_id');
    }

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    /**
     * Scopes
     */
    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeConfirmed($query)
    {
        return $query->where('status', 'confirmed');
    }

    public function scopeCancelled($query)
    {
        return $query->where('status', 'cancelled');
    }

    public function scopePaid($query)
    {
        return $query->where('payment_status', 'paid');
    }

    public function scopeUpcoming($query)
    {
        return $query->where('travel_date', '>=', today())
                     ->whereIn('status', ['confirmed', 'pending']);
    }

    public function scopePast($query)
    {
        return $query->where('travel_date', '<', today())
                     ->where('status', 'completed');
    }

    /**
     * Status helpers
     */
    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function isConfirmed(): bool
    {
        return $this->status === 'confirmed';
    }

    public function isCancelled(): bool
    {
        return $this->status === 'cancelled';
    }

    public function isPaid(): bool
    {
        return $this->payment_status === 'paid';
    }

    public function canCancel(): bool
    {
        return in_array($this->status, ['pending', 'confirmed']) 
               && $this->travel_date > today();
    }

    /**
     * Confirm booking
     */
    public function confirm(): void
    {
        $this->update([
            'status' => 'confirmed',
            'confirmed_at' => now(),
        ]);
    }

    /**
     * Cancel booking
     */
    public function cancel(?string $reason = null): void
    {
        $this->update([
            'status' => 'cancelled',
            'cancelled_at' => now(),
            'cancellation_reason' => $reason,
        ]);
    }

    /**
     * Get formatted total
     */
    public function getFormattedTotalAttribute(): string
    {
        return '৳ ' . number_format((float) $this->total_amount, 2);
    }

    /**
     * Get status badge color
     */
    public function getStatusColorAttribute(): string
    {
        return match($this->status) {
            'pending' => 'yellow',
            'confirmed' => 'green',
            'cancelled' => 'red',
            'completed' => 'blue',
            'refunded' => 'gray',
            default => 'gray',
        };
    }

    /**
     * Get payment status color
     */
    public function getPaymentStatusColorAttribute(): string
    {
        return match($this->payment_status) {
            'paid' => 'green',
            'pending' => 'yellow',
            'failed' => 'red',
            'refunded' => 'blue',
            default => 'gray',
        };
    }
}
