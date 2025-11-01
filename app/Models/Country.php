<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Country extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'iso_code', // Changed from iso2
        'iso_code_3', // Changed from iso3
        'country_code', // Changed from phone_code
        'capital',
        'currency',
        'continent', // Changed from region
        'subregion',
        'nationality', // Added
        'is_active', // Added
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Get the universities located in this country.
     */
    public function universities(): HasMany
    {
        return $this->hasMany(University::class);
    }

    /**
     * Get the states for the country.
     */
    public function states(): HasMany
    {
        return $this->hasMany(State::class);
    }
}
