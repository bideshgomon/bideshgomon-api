<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AgencyType extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'icon',
        'color',
        'description',
        'allowed_service_modules',
        'required_certifications',
        'capabilities',
        'default_commission_rate',
        'min_commission_rate',
        'max_commission_rate',
        'can_set_own_pricing',
        'requires_verification',
        'requires_insurance',
        'can_manage_team',
        'can_create_resources',
        'needs_country_assignment',
        'is_active',
        'display_order',
    ];

    protected $casts = [
        'allowed_service_modules' => 'array',
        'required_certifications' => 'array',
        'capabilities' => 'array',
        'default_commission_rate' => 'decimal:2',
        'min_commission_rate' => 'decimal:2',
        'max_commission_rate' => 'decimal:2',
        'can_set_own_pricing' => 'boolean',
        'requires_verification' => 'boolean',
        'requires_insurance' => 'boolean',
        'can_manage_team' => 'boolean',
        'can_create_resources' => 'boolean',
        'needs_country_assignment' => 'boolean',
        'is_active' => 'boolean',
        'display_order' => 'integer',
    ];

    /**
     * Get agencies of this type
     */
    public function agencies(): HasMany
    {
        return $this->hasMany(Agency::class, 'agency_type_id');
    }

    /**
     * Scope active agency types
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true)->orderBy('display_order');
    }

    /**
     * Check if this type can offer a specific service
     */
    public function canOfferService(string $serviceSlug): bool
    {
        if (empty($this->allowed_service_modules)) {
            return true; // No restrictions
        }

        return in_array($serviceSlug, $this->allowed_service_modules);
    }

    /**
     * Check if this type has a specific capability
     */
    public function hasCapability(string $capability): bool
    {
        if (empty($this->capabilities)) {
            return false;
        }

        return in_array($capability, $this->capabilities);
    }

    /**
     * Get color classes for UI
     */
    public function getColorClass(string $type = 'bg'): string
    {
        $colors = [
            'blue' => ['bg' => 'bg-blue-100', 'text' => 'text-blue-800', 'border' => 'border-blue-300'],
            'green' => ['bg' => 'bg-green-100', 'text' => 'text-green-800', 'border' => 'border-green-300'],
            'purple' => ['bg' => 'bg-purple-100', 'text' => 'text-purple-800', 'border' => 'border-purple-300'],
            'orange' => ['bg' => 'bg-orange-100', 'text' => 'text-orange-800', 'border' => 'border-orange-300'],
            'indigo' => ['bg' => 'bg-indigo-100', 'text' => 'text-indigo-800', 'border' => 'border-indigo-300'],
        ];

        return $colors[$this->color][$type] ?? $colors['blue'][$type];
    }
}
