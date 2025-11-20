<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Hotel extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'city',
        'area',
        'address',
        'latitude',
        'longitude',
        'country',
        'phone',
        'email',
        'website',
        'rating',
        'total_reviews',
        'star_rating',
        'property_type',
        'amenities',
        'featured_image',
        'images',
        'price_from',
        'currency',
        'check_in_time',
        'check_out_time',
        'cancellation_policy',
        'house_rules',
        'is_active',
        'is_featured',
        'meta_title',
        'meta_description',
    ];

    protected $casts = [
        'amenities' => 'array',
        'images' => 'array',
        'latitude' => 'decimal:7',
        'longitude' => 'decimal:7',
        'rating' => 'decimal:1',
        'price_from' => 'decimal:2',
        'is_active' => 'boolean',
        'is_featured' => 'boolean',
        'check_in_time' => 'datetime:H:i',
        'check_out_time' => 'datetime:H:i',
    ];

    // Relationships
    public function rooms()
    {
        return $this->hasMany(HotelRoom::class);
    }

    public function bookings()
    {
        return $this->hasMany(HotelBooking::class);
    }

    public function activeRooms()
    {
        return $this->hasMany(HotelRoom::class)->where('is_available', true);
    }

    public function availableRooms()
    {
        return $this->hasMany(HotelRoom::class)
            ->where('is_available', true)
            ->where('total_rooms', '>', 0);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    public function scopeInCity($query, $city)
    {
        return $query->where('city', $city);
    }

    public function scopeWithStarRating($query, $rating)
    {
        return $query->where('star_rating', $rating);
    }

    public function scopeMinRating($query, $rating)
    {
        return $query->where('rating', '>=', $rating);
    }

    public function scopePriceBetween($query, $min, $max)
    {
        return $query->whereBetween('price_from', [$min, $max]);
    }

    public function scopeWithAmenity($query, $amenity)
    {
        return $query->whereJsonContains('amenities', $amenity);
    }

    public function scopePopular($query)
    {
        return $query->orderBy('total_reviews', 'desc')->orderBy('rating', 'desc');
    }

    // Accessors
    public function getFullAddressAttribute()
    {
        return "{$this->address}, {$this->city}, {$this->country}";
    }

    public function getStarsAttribute()
    {
        return str_repeat('⭐', $this->star_rating);
    }

    public function getRatingTextAttribute()
    {
        if ($this->rating >= 4.5) return 'Excellent';
        if ($this->rating >= 4.0) return 'Very Good';
        if ($this->rating >= 3.5) return 'Good';
        if ($this->rating >= 3.0) return 'Average';
        return 'Below Average';
    }

    public function getLowestPriceAttribute()
    {
        return $this->rooms()->min('price_per_night') ?? $this->price_from;
    }

    public function getHasAvailabilityAttribute()
    {
        return $this->availableRooms()->exists();
    }

    // Mutators
    public function setNameAttribute($value)
    {
        $this->attributes['name'] = $value;
        $this->attributes['slug'] = Str::slug($value) . '-' . Str::random(6);
    }

    // Helper Methods
    public function hasAmenity($amenity)
    {
        return in_array($amenity, $this->amenities ?? []);
    }

    public function updateRating()
    {
        $bookings = $this->bookings()
            ->whereNotNull('rating')
            ->get();

        if ($bookings->count() > 0) {
            $this->rating = $bookings->avg('rating');
            $this->total_reviews = $bookings->count();
            $this->save();
        }
    }

    public function getTotalBookingsCount()
    {
        return $this->bookings()
            ->whereIn('status', ['confirmed', 'checked_in', 'checked_out'])
            ->count();
    }

    public function getRevenue($startDate = null, $endDate = null)
    {
        $query = $this->bookings()
            ->where('payment_status', 'paid');

        if ($startDate) {
            $query->where('created_at', '>=', $startDate);
        }

        if ($endDate) {
            $query->where('created_at', '<=', $endDate);
        }

        return $query->sum('total_amount');
    }

    // Boot
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($hotel) {
            if (empty($hotel->slug)) {
                $hotel->slug = Str::slug($hotel->name) . '-' . Str::random(6);
            }
        });
    }
}
