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
    // --- FIX: Update fillable to match the migration and seeder ---
    protected $fillable = [
        'name',
        'code',
        'iso_code_3',
        'country_code',
        'continent',
        'nationality',
        'is_active',
    ];
    // -----------------------------------------------------------

    /**
     * Get the universities located in this country.
     */
    public function universities(): HasMany
    {
        return $this->hasMany(University::class);
    }

    // Add other relationships later if needed (e.g., states, users)
}