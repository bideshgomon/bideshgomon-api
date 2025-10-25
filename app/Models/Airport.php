<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Airport extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     * Ensure these match your migration column names exactly.
     */
    protected $fillable = [
        'name',
        'iata_code', // <-- CORRECTED: Was 'code'
        'city_id',   // <-- CORRECTED: Removed 'country_id'
    ];

    /**
     * Get the city this airport belongs to.
     */
    public function city(): BelongsTo
    {
        return $this->belongsTo(City::class);
    }

    // REMOVED incorrect country() relationship

    public function departingFlights(): HasMany
    {
        return $this->hasMany(Flight::class, 'origin_airport_id');
    }

    public function arrivingFlights(): HasMany
    {
        return $this->hasMany(Flight::class, 'destination_airport_id');
    }
}