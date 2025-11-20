<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class HotelBooking extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'booking_reference',
        'user_id',
        'hotel_id',
        'hotel_room_id',
        'check_in_date',
        'check_out_date',
        'nights',
        'rooms_count',
        'adults_count',
        'children_count',
        'guests',
        'guest_name',
        'guest_email',
        'guest_phone',
        'special_requests',
        'room_price_per_night',
        'subtotal',
        'tax_amount',
        'service_charge',
        'discount_amount',
        'total_amount',
        'currency',
        'payment_method',
        'payment_status',
        'paid_at',
        'transaction_id',
        'status',
        'confirmed_at',
        'checked_in_at',
        'checked_out_at',
        'cancelled_at',
        'cancellation_reason',
        'refund_amount',
        'refunded_at',
        'admin_notes',
        'hotel_notes',
        'ip_address',
        'user_agent',
    ];

    protected $casts = [
        'guests' => 'array',
        'check_in_date' => 'date',
        'check_out_date' => 'date',
        'room_price_per_night' => 'decimal:2',
        'subtotal' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'service_charge' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'refund_amount' => 'decimal:2',
        'paid_at' => 'datetime',
        'confirmed_at' => 'datetime',
        'checked_in_at' => 'datetime',
        'checked_out_at' => 'datetime',
        'cancelled_at' => 'datetime',
        'refunded_at' => 'datetime',
    ];

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function hotel()
    {
        return $this->belongsTo(Hotel::class);
    }

    public function room()
    {
        return $this->belongsTo(HotelRoom::class, 'hotel_room_id');
    }

    // Scopes
    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function scopeByPaymentStatus($query, $status)
    {
        return $query->where('payment_status', $status);
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

    public function scopeCompleted($query)
    {
        return $query->where('status', 'checked_out');
    }

    public function scopePaid($query)
    {
        return $query->where('payment_status', 'paid');
    }

    public function scopeUpcoming($query)
    {
        return $query->where('check_in_date', '>', now())
            ->whereIn('status', ['confirmed', 'pending']);
    }

    public function scopeActive($query)
    {
        return $query->whereIn('status', ['confirmed', 'checked_in'])
            ->where('check_out_date', '>=', now());
    }

    public function scopePast($query)
    {
        return $query->where(function ($q) {
            $q->where('status', 'checked_out')
              ->orWhere('check_out_date', '<', now());
        });
    }

    public function scopeRecent($query)
    {
        return $query->orderBy('created_at', 'desc');
    }

    // Accessors
    public function getStatusBadgeAttribute()
    {
        return match($this->status) {
            'pending' => ['text' => 'Pending', 'class' => 'warning'],
            'confirmed' => ['text' => 'Confirmed', 'class' => 'success'],
            'checked_in' => ['text' => 'Checked In', 'class' => 'info'],
            'checked_out' => ['text' => 'Completed', 'class' => 'secondary'],
            'cancelled' => ['text' => 'Cancelled', 'class' => 'danger'],
            'no_show' => ['text' => 'No Show', 'class' => 'dark'],
            default => ['text' => $this->status, 'class' => 'light'],
        };
    }

    public function getPaymentBadgeAttribute()
    {
        return match($this->payment_status) {
            'pending' => ['text' => 'Payment Pending', 'class' => 'warning'],
            'paid' => ['text' => 'Paid', 'class' => 'success'],
            'refunded' => ['text' => 'Refunded', 'class' => 'info'],
            'failed' => ['text' => 'Failed', 'class' => 'danger'],
            default => ['text' => $this->payment_status, 'class' => 'light'],
        };
    }

    public function getIsUpcomingAttribute()
    {
        return $this->check_in_date > now() && in_array($this->status, ['confirmed', 'pending']);
    }

    public function getIsActiveAttribute()
    {
        return in_array($this->status, ['confirmed', 'checked_in']) && $this->check_out_date >= now();
    }

    public function getIsPastAttribute()
    {
        return $this->status === 'checked_out' || $this->check_out_date < now();
    }

    public function getIsCancellableAttribute()
    {
        return in_array($this->status, ['pending', 'confirmed']) && 
               $this->check_in_date > now()->addDay(); // At least 1 day before check-in
    }

    public function getIsModifiableAttribute()
    {
        return in_array($this->status, ['pending', 'confirmed']) && 
               $this->check_in_date > now()->addDays(2); // At least 2 days before check-in
    }

    public function getTotalGuestsAttribute()
    {
        return $this->adults_count + $this->children_count;
    }

    public function getStayDurationTextAttribute()
    {
        return "{$this->nights} " . ($this->nights === 1 ? 'Night' : 'Nights');
    }

    public function getFormattedTotalAttribute()
    {
        return number_format($this->total_amount, 2) . ' ' . $this->currency;
    }

    public function getDaysUntilCheckInAttribute()
    {
        return now()->diffInDays($this->check_in_date, false);
    }

    // Helper Methods
    public function calculateNights()
    {
        return $this->check_in_date->diffInDays($this->check_out_date);
    }

    public function calculateSubtotal()
    {
        return $this->room_price_per_night * $this->nights * $this->rooms_count;
    }

    public function calculateTotal()
    {
        $subtotal = $this->subtotal ?? $this->calculateSubtotal();
        $tax = $this->tax_amount ?? 0;
        $serviceCharge = $this->service_charge ?? 0;
        $discount = $this->discount_amount ?? 0;

        return $subtotal + $tax + $serviceCharge - $discount;
    }

    public function confirm()
    {
        $this->status = 'confirmed';
        $this->confirmed_at = now();
        $this->save();
    }

    public function checkIn()
    {
        $this->status = 'checked_in';
        $this->checked_in_at = now();
        $this->save();
    }

    public function checkOut()
    {
        $this->status = 'checked_out';
        $this->checked_out_at = now();
        $this->save();
    }

    public function cancel($reason = null)
    {
        $this->status = 'cancelled';
        $this->cancelled_at = now();
        $this->cancellation_reason = $reason;
        $this->save();
    }

    public function markAsPaid($transactionId = null)
    {
        $this->payment_status = 'paid';
        $this->paid_at = now();
        $this->transaction_id = $transactionId;
        $this->save();
    }

    public function refund($amount = null)
    {
        $this->payment_status = 'refunded';
        $this->refund_amount = $amount ?? $this->total_amount;
        $this->refunded_at = now();
        $this->save();
    }

    // Boot
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($booking) {
            // Auto-generate booking reference
            if (empty($booking->booking_reference)) {
                $booking->booking_reference = 'HB' . date('Ymd') . str_pad(
                    static::whereDate('created_at', today())->count() + 1,
                    4,
                    '0',
                    STR_PAD_LEFT
                );
            }

            // Auto-calculate nights if not set
            if (empty($booking->nights)) {
                $booking->nights = $booking->calculateNights();
            }

            // Auto-calculate amounts if not set
            if (empty($booking->subtotal)) {
                $booking->subtotal = $booking->calculateSubtotal();
            }

            if (empty($booking->total_amount)) {
                $booking->total_amount = $booking->calculateTotal();
            }
        });
    }
}
