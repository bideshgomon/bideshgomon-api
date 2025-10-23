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
        'iso2',
        'iso3',
        'phone_code',
        'capital',
        'currency',
        'region',
        'subregion',
    ];

    /**
     * Get the universities located in this country.
     */
    public function universities(): HasMany
    {
        return $this->hasMany(University::class);
    }

    // Add other relationships later if needed (e.g., states, users)
}