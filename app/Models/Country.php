<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
// Added for new/existing relationships
use App\Models\State; 
use App\Models\TouristVisa;

class Country extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'code', // e.g., 'US', 'CA'
        'is_active',
    ];

    /**
     * Get the states for the country.
     */
    public function states(): HasMany
    {
        return $this->hasMany(State::class);
    }

    /**
     * Get the tourist visa applications where this country is the destination.
     * (MERGED FROM SNIPPET 1)
     */
    public function destinationTouristVisas(): HasMany
    {
        return $this->hasMany(TouristVisa::class, 'destination_country_id');
    }

    // You might also have these if needed
    // public function cities(): HasManyThrough { ... }
    // public function airports(): HasManyThrough { ... }
}