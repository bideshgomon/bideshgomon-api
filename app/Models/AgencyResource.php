<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AgencyResource extends Model
{
    protected $fillable = [
        'agency_id',
        'service_module_id',
        'resource_type',
        'resource_name',
        'resource_code',
        'country_id',
        'city',
        'description',
        'is_primary_owner',
        'requires_admin_approval',
        'is_approved',
        'admin_approved_at',
        'admin_approved_by',
        'admin_notes',
        'special_commission_rate',
        'metadata',
        'is_active',
    ];

    protected $casts = [
        'is_primary_owner' => 'boolean',
        'requires_admin_approval' => 'boolean',
        'is_approved' => 'boolean',
        'is_active' => 'boolean',
        'admin_approved_at' => 'datetime',
        'metadata' => 'array',
        'special_commission_rate' => 'decimal:2',
    ];

    /**
     * Get the agency that owns this resource
     */
    public function agency(): BelongsTo
    {
        return $this->belongsTo(Agency::class);
    }

    /**
     * Get the service module this resource belongs to
     */
    public function serviceModule(): BelongsTo
    {
        return $this->belongsTo(ServiceModule::class);
    }

    /**
     * Get the country where resource is located
     */
    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class);
    }

    /**
     * Get the admin who approved this resource
     */
    public function approvedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'admin_approved_by');
    }

    /**
     * Check if resource is pending approval
     */
    public function isPending(): bool
    {
        return $this->requires_admin_approval && !$this->is_approved;
    }

    /**
     * Approve the resource
     */
    public function approve(int $adminId, ?string $notes = null): void
    {
        $this->update([
            'is_approved' => true,
            'admin_approved_at' => now(),
            'admin_approved_by' => $adminId,
            'admin_notes' => $notes,
        ]);
    }

    /**
     * Reject the resource
     */
    public function reject(?string $notes = null): void
    {
        $this->update([
            'is_approved' => false,
            'admin_notes' => $notes,
        ]);
    }

    /**
     * Get commission rate (special or default from service)
     */
    public function getCommissionRate(): float
    {
        return $this->special_commission_rate ?? $this->serviceModule->platform_commission_rate;
    }

    /**
     * Scope: Only approved resources
     */
    public function scopeApproved($query)
    {
        return $query->where('is_approved', true);
    }

    /**
     * Scope: Pending approval
     */
    public function scopePending($query)
    {
        return $query->where('requires_admin_approval', true)
                    ->where('is_approved', false);
    }

    /**
     * Scope: Active resources
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope: Primary owners only
     */
    public function scopePrimaryOwner($query)
    {
        return $query->where('is_primary_owner', true);
    }
}
