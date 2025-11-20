<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class HotelRoom extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'hotel_id',
        'room_type',
        'name',
        'description',
        'max_adults',
        'max_children',
        'total_rooms',
        'size_sqm',
        'bed_type',
        'bed_count',
        'price_per_night',
        'weekend_price',
        'currency',
        'amenities',
        'images',
        'is_available',
        'discount_percentage',
        'discount_valid_from',
        'discount_valid_until',
    ];

    protected $casts = [
        'amenities' => 'array',
        'images' => 'array',
        'price_per_night' => 'decimal:2',
        'weekend_price' => 'decimal:2',
        'discount_percentage' => 'decimal:2',
        'is_available' => 'boolean',
        'discount_valid_from' => 'date',
        'discount_valid_until' => 'date',
    ];

    // Relationships
    public function hotel()
    {
        return $this->belongsTo(Hotel::class);
    }

    public function bookings()
    {
        return $this->hasMany(HotelBooking::class);
    }

    public function activeBookings()
    {
        return $this->hasMany(HotelBooking::class)
            ->whereIn('status', ['confirmed', 'checked_in']);
    }

    // Scopes
    public function scopeAvailable($query)
    {
        return $query->where('is_available', true)
            ->where('total_rooms', '>', 0);
    }

    public function scopeByType($query, $type)
    {
        return $query->where('room_type', $type);
    }

    public function scopePriceBetween($query, $min, $max)
    {
        return $query->whereBetween('price_per_night', [$min, $max]);
    }

    public function scopeWithCapacity($query, $adults, $children = 0)
    {
        return $query->where('max_adults', '>=', $adults)
            ->where('max_children', '>=', $children);
    }

    public function scopeWithAmenity($query, $amenity)
    {
        return $query->whereJsonContains('amenities', $amenity);
    }

    public function scopeCheapestFirst($query)
    {
        return $query->orderBy('price_per_night', 'asc');
    }

    // Accessors
    public function getDisplayNameAttribute()
    {
        return $this->name ?? $this->room_type;
    }

    public function getCurrentPriceAttribute()
    {
        $price = $this->price_per_night;

        // Check if today is weekend and weekend pricing exists
        if ($this->weekend_price && in_array(now()->dayOfWeek, [5, 6])) { // Friday, Saturday
            $price = $this->weekend_price;
        }

        // Apply discount if valid
        if ($this->hasActiveDiscount()) {
            $price = $price - ($price * $this->discount_percentage / 100);
        }

        return round($price, 2);
    }

    public function getFormattedPriceAttribute()
    {
        return number_format($this->current_price, 2) . ' ' . $this->currency;
    }

    public function getMaxCapacityAttribute()
    {
        return $this->max_adults + $this->max_children;
    }

    public function getCapacityTextAttribute()
    {
        $text = "{$this->max_adults} Adults";
        if ($this->max_children > 0) {
            $text .= ", {$this->max_children} Children";
        }
        return $text;
    }

    public function getAvailableRoomsCountAttribute()
    {
        // Calculate rooms currently booked
        $bookedRooms = $this->activeBookings()
            ->sum('rooms_count');

        return max(0, $this->total_rooms - $bookedRooms);
    }

    public function getIsFullyBookedAttribute()
    {
        return $this->available_rooms_count === 0;
    }

    public function getDiscountedPriceAttribute()
    {
        if (!$this->hasActiveDiscount()) {
            return null;
        }

        return $this->price_per_night - ($this->price_per_night * $this->discount_percentage / 100);
    }

    public function getSavingsAttribute()
    {
        if (!$this->discounted_price) {
            return 0;
        }

        return $this->price_per_night - $this->discounted_price;
    }

    // Helper Methods
    public function hasAmenity($amenity)
    {
        return in_array($amenity, $this->amenities ?? []);
    }

    public function hasActiveDiscount()
    {
        if (!$this->discount_percentage || !$this->discount_valid_from || !$this->discount_valid_until) {
            return false;
        }

        $today = now()->startOfDay();
        return $today->between($this->discount_valid_from, $this->discount_valid_until);
    }

    public function isAvailableForDates($checkIn, $checkOut, $roomsNeeded = 1)
    {
        if (!$this->is_available || $this->total_rooms === 0) {
            return false;
        }

        // Get bookings that overlap with requested dates
        $overlappingBookings = $this->bookings()
            ->whereIn('status', ['confirmed', 'checked_in'])
            ->where(function ($query) use ($checkIn, $checkOut) {
                $query->whereBetween('check_in_date', [$checkIn, $checkOut])
                    ->orWhereBetween('check_out_date', [$checkIn, $checkOut])
                    ->orWhere(function ($q) use ($checkIn, $checkOut) {
                        $q->where('check_in_date', '<=', $checkIn)
                          ->where('check_out_date', '>=', $checkOut);
                    });
            })
            ->sum('rooms_count');

        $availableRooms = $this->total_rooms - $overlappingBookings;

        return $availableRooms >= $roomsNeeded;
    }

    public function calculateTotalPrice($nights, $roomsCount = 1)
    {
        return $this->current_price * $nights * $roomsCount;
    }
}
