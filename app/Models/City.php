<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class City extends Model
{
    protected $fillable = [
        'country_id',
        'name',
        'name_bn',
        'state_province',
        'timezone',
        'latitude',
        'longitude',
        'is_capital',
        'is_active',
    ];

    protected $casts = [
        'country_id' => 'integer',
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
        'is_capital' => 'boolean',
        'is_active' => 'boolean',
    ];

    /**
     * The country this city belongs to.
     */
    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class);
    }

    /**
     * Airports in this city.
     */
    public function airports(): HasMany
    {
        return $this->hasMany(Airport::class);
    }

    /**
     * Scope: Only active cities.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope: Capital cities only.
     */
    public function scopeCapitals($query)
    {
        return $query->where('is_capital', true);
    }
}
