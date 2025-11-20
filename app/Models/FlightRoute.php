<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class FlightRoute extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'route_code',
        'airline_name',
        'airline_code',
        'flight_number',
        'aircraft_type',
        'origin_country_id',
        'origin_city',
        'origin_airport',
        'origin_airport_code',
        'destination_country_id',
        'destination_city',
        'destination_airport',
        'destination_airport_code',
        'departure_time',
        'arrival_time',
        'flight_duration',
        'available_days',
        'is_direct',
        'stops',
        'stopover_cities',
        'economy_price',
        'business_price',
        'first_class_price',
        'currency_id',
        'total_seats',
        'economy_seats',
        'business_seats',
        'first_class_seats',
        'cabin_baggage',
        'checked_baggage',
        'extra_baggage_price',
        'amenities',
        'meal_type',
        'has_wifi',
        'has_entertainment',
        'booking_deadline_hours',
        'booking_fee',
        'cancellation_fee_percentage',
        'is_active',
        'is_popular',
        'total_bookings',
        'average_rating',
        'display_order',
        'description',
        'tags',
    ];

    protected $casts = [
        'available_days' => 'array',
        'stopover_cities' => 'array',
        'amenities' => 'array',
        'tags' => 'array',
        'economy_price' => 'decimal:2',
        'business_price' => 'decimal:2',
        'first_class_price' => 'decimal:2',
        'booking_fee' => 'decimal:2',
        'cancellation_fee_percentage' => 'decimal:2',
        'average_rating' => 'decimal:2',
        'is_direct' => 'boolean',
        'has_wifi' => 'boolean',
        'has_entertainment' => 'boolean',
        'is_active' => 'boolean',
        'is_popular' => 'boolean',
        'departure_time' => 'datetime',
        'arrival_time' => 'datetime',
    ];

    /**
     * Relationships
     */
    public function originCountry(): BelongsTo
    {
        return $this->belongsTo(Country::class, 'origin_country_id');
    }

    public function destinationCountry(): BelongsTo
    {
        return $this->belongsTo(Country::class, 'destination_country_id');
    }

    public function currency(): BelongsTo
    {
        return $this->belongsTo(Currency::class);
    }

    public function bookings(): HasMany
    {
        return $this->hasMany(FlightBooking::class);
    }

    /**
     * Scopes
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopePopular($query)
    {
        return $query->where('is_popular', true);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('display_order')->orderBy('economy_price');
    }

    public function scopeDirect($query)
    {
        return $query->where('is_direct', true);
    }

    public function scopeFromAirport($query, $airportCode)
    {
        return $query->where('origin_airport_code', $airportCode);
    }

    public function scopeToAirport($query, $airportCode)
    {
        return $query->where('destination_airport_code', $airportCode);
    }

    public function scopeByAirline($query, $airlineCode)
    {
        return $query->where('airline_code', $airlineCode);
    }

    public function scopeSearchRoute($query, $origin, $destination, $date = null)
    {
        $query->where('origin_airport_code', $origin)
              ->where('destination_airport_code', $destination)
              ->where('is_active', true);

        if ($date) {
            $dayOfWeek = strtolower(date('l', strtotime($date)));
            $query->whereJsonContains('available_days', $dayOfWeek);
        }

        return $query;
    }

    /**
     * Calculate total price for booking
     */
    public function calculateTotalPrice(string $flightClass, int $passengers, array $extras = []): float
    {
        $baseFare = match($flightClass) {
            'business' => (float) $this->business_price,
            'first_class' => (float) $this->first_class_price,
            default => (float) $this->economy_price,
        };

        $subtotal = $baseFare * $passengers;
        
        // Add booking fee
        $subtotal += (float) $this->booking_fee;

        // Add taxes (approximately 15%)
        $taxes = $subtotal * 0.15;
        
        // Add extras
        $extrasTotal = 0;
        if (!empty($extras['extra_baggage'])) {
            $extrasTotal += $extras['extra_baggage'];
        }
        if (!empty($extras['seat_selection'])) {
            $extrasTotal += $extras['seat_selection'];
        }
        if (!empty($extras['meals'])) {
            $extrasTotal += $extras['meals'];
        }
        if (!empty($extras['insurance'])) {
            $extrasTotal += $extras['insurance'];
        }

        return $subtotal + $taxes + $extrasTotal;
    }

    /**
     * Get formatted price
     */
    public function getFormattedEconomyPriceAttribute(): string
    {
        return '৳ ' . number_format((float) $this->economy_price, 0);
    }

    public function getFormattedBusinessPriceAttribute(): string
    {
        return $this->business_price ? '৳ ' . number_format((float) $this->business_price, 0) : null;
    }

    /**
     * Get route display name
     */
    public function getRouteNameAttribute(): string
    {
        return "{$this->origin_city} → {$this->destination_city}";
    }

    /**
     * Check if route is available for a specific date
     */
    public function isAvailableOnDate($date): bool
    {
        if (!$this->is_active) {
            return false;
        }

        $dayOfWeek = strtolower(date('l', strtotime($date)));
        return in_array($dayOfWeek, $this->available_days ?? []);
    }

    /**
     * Increment booking counter
     */
    public function incrementBookings(): void
    {
        $this->increment('total_bookings');
    }
}
