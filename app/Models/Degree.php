<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Degree extends Model
{
    protected $fillable = [
        'name',
        'name_bn',
        'short_name',
        'level',
        'typical_duration_years',
        'is_active',
    ];

    protected $casts = [
        'typical_duration_years' => 'integer',
        'is_active' => 'boolean',
    ];

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();
    }

    /**
     * Scope: Only active degrees.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope: Order by level.
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('level')->orderBy('name');
    }
}
