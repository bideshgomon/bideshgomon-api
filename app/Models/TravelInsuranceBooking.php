<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TravelInsuranceBooking extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'booking_reference',
        'user_id',
        'package_id',
        'destination_country_id',
        'trip_start_date',
        'trip_end_date',
        'duration_days',
        'trip_purpose',
        'travelers',
        'travelers_count',
        'package_price',
        'tax_amount',
        'discount_amount',
        'total_amount',
        'payment_status',
        'payment_method',
        'payment_reference',
        'paid_at',
        'status',
        'cancellation_reason',
        'cancelled_at',
        'policy_number',
        'policy_issued_at',
        'policy_document_url',
        'notes',
        'metadata',
    ];

    protected $casts = [
        'trip_start_date' => 'date',
        'trip_end_date' => 'date',
        'travelers' => 'array',
        'package_price' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'paid_at' => 'datetime',
        'cancelled_at' => 'datetime',
        'policy_issued_at' => 'datetime',
        'metadata' => 'array',
    ];

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function package()
    {
        return $this->belongsTo(TravelInsurancePackage::class, 'package_id');
    }

    public function destinationCountry()
    {
        return $this->belongsTo(Country::class, 'destination_country_id');
    }

    // Scopes
    public function scopePaid($query)
    {
        return $query->where('payment_status', 'paid');
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    // Helpers
    public function isPaid(): bool
    {
        return $this->payment_status === 'paid';
    }

    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    public function getFormattedTotalAttribute(): string
    {
        return '৳ ' . number_format($this->total_amount, 2);
    }

    public function getStatusBadgeColorAttribute(): string
    {
        return match($this->status) {
            'pending' => 'yellow',
            'confirmed' => 'blue',
            'active' => 'green',
            'expired' => 'gray',
            'cancelled' => 'red',
            default => 'gray',
        };
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($booking) {
            if (empty($booking->booking_reference)) {
                $booking->booking_reference = 'TI' . date('Ymd') . str_pad($booking->id ?? rand(1, 9999), 4, '0', STR_PAD_LEFT);
            }
        });
    }
}