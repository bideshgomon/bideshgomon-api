<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class Airport extends Model
{
    protected $fillable = [
        'city_id',
        'name',
        'name_bn',
        'iata_code',
        'icao_code',
        'latitude',
        'longitude',
        'is_international',
        'is_active',
    ];

    protected $casts = [
        'city_id' => 'integer',
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
        'is_international' => 'boolean',
        'is_active' => 'boolean',
    ];

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();

        // Auto-uppercase IATA and ICAO codes
        static::saving(function ($airport) {
            if ($airport->iata_code) {
                $airport->iata_code = strtoupper($airport->iata_code);
            }
            if ($airport->icao_code) {
                $airport->icao_code = strtoupper($airport->icao_code);
            }
        });
    }

    /**
     * The city this airport belongs to.
     */
    public function city(): BelongsTo
    {
        return $this->belongsTo(City::class);
    }

    /**
     * Get the country through the city relationship.
     */
    public function country()
    {
        return $this->city?->country;
    }

    /**
     * Scope: Only active airports.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope: International airports only.
     */
    public function scopeInternational($query)
    {
        return $query->where('is_international', true);
    }
}
