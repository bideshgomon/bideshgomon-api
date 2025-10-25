<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany; // <-- IMPORT

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
    public function states(): HasMany // <-- ADD THIS METHOD
    {
        return $this->hasMany(State::class);
    }

    // You might also have these if needed, but 'states' is essential here
    // public function cities(): HasManyThrough { ... }
    // public function airports(): HasManyThrough { ... }
}