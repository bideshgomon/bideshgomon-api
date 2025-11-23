<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ServiceCategory extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'icon',
        'description',
        'color',
        'sort_order',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    /**
     * Get all service modules in this category
     */
    public function modules(): HasMany
    {
        return $this->hasMany(ServiceModule::class)->orderBy('sort_order');
    }

    /**
     * Get active service modules only
     */
    public function activeModules(): HasMany
    {
        return $this->hasMany(ServiceModule::class)
            ->where('is_active', true)
            ->orderBy('sort_order');
    }

    /**
     * Scope: Only active categories
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope: Ordered by sort_order
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order')->orderBy('name');
    }

    /**
     * Get total modules count
     */
    public function getModulesCountAttribute(): int
    {
        return $this->modules()->count();
    }

    /**
     * Get active modules count
     */
    public function getActiveModulesCountAttribute(): int
    {
        return $this->modules()->where('is_active', true)->count();
    }
}
