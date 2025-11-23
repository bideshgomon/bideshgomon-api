<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AgencyCountryAssignment extends Model
{
    protected $fillable = [
        'agency_id',
        'country',
        'country_code',
        'visa_type',
        'platform_commission_rate',
        'commission_type',
        'fixed_commission_amount',
        'can_edit_requirements',
        'can_set_fees',
        'can_process_applications',
        'total_applications',
        'approved_applications',
        'rejected_applications',
        'total_revenue',
        'platform_earnings',
        'is_active',
        'assigned_at',
        'assigned_by',
        'assignment_notes',
    ];

    protected $casts = [
        'platform_commission_rate' => 'decimal:2',
        'fixed_commission_amount' => 'decimal:2',
        'total_revenue' => 'decimal:2',
        'platform_earnings' => 'decimal:2',
        'can_edit_requirements' => 'boolean',
        'can_set_fees' => 'boolean',
        'can_process_applications' => 'boolean',
        'is_active' => 'boolean',
        'assigned_at' => 'datetime',
    ];

    /**
     * Get the agency user.
     */
    public function agency(): BelongsTo
    {
        return $this->belongsTo(User::class, 'agency_id');
    }

    /**
     * Get the admin who assigned this.
     */
    public function assignedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_by');
    }

    /**
     * Get visa requirements for this assignment.
     */
    public function visaRequirements(): HasMany
    {
        return $this->hasMany(VisaRequirement::class, 'managed_by_agency', 'agency_id')
            ->where('country', $this->country)
            ->where('visa_type', $this->visa_type);
    }

    /**
     * Scope: Active assignments only.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope: Filter by agency.
     */
    public function scopeForAgency($query, int $agencyId)
    {
        return $query->where('agency_id', $agencyId);
    }

    /**
     * Scope: Filter by country.
     */
    public function scopeForCountry($query, string $country)
    {
        return $query->where('country', $country);
    }

    /**
     * Calculate platform commission for given amount.
     */
    public function calculatePlatformCommission(float $amount): float
    {
        if ($this->commission_type === 'fixed') {
            return $this->fixed_commission_amount ?? 0;
        }

        return ($amount * $this->platform_commission_rate) / 100;
    }

    /**
     * Calculate agency earnings after platform commission.
     */
    public function calculateAgencyEarnings(float $totalFees): float
    {
        $commission = $this->calculatePlatformCommission($totalFees);
        return $totalFees - $commission;
    }

    /**
     * Get approval rate percentage.
     */
    public function getApprovalRateAttribute(): float
    {
        if ($this->total_applications == 0) {
            return 0;
        }

        return round(($this->approved_applications / $this->total_applications) * 100, 2);
    }

    /**
     * Get rejection rate percentage.
     */
    public function getRejectionRateAttribute(): float
    {
        if ($this->total_applications == 0) {
            return 0;
        }

        return round(($this->rejected_applications / $this->total_applications) * 100, 2);
    }

    /**
     * Get performance status.
     */
    public function getPerformanceStatusAttribute(): string
    {
        $approvalRate = $this->approval_rate;

        if ($approvalRate >= 80) {
            return 'excellent';
        } elseif ($approvalRate >= 60) {
            return 'good';
        } elseif ($approvalRate >= 40) {
            return 'fair';
        } else {
            return 'poor';
        }
    }

    /**
     * Increment application counters.
     */
    public function incrementApplications(string $status): void
    {
        $this->increment('total_applications');

        if ($status === 'approved') {
            $this->increment('approved_applications');
        } elseif ($status === 'rejected') {
            $this->increment('rejected_applications');
        }
    }

    /**
     * Add revenue and calculate platform earnings.
     */
    public function addRevenue(float $amount): void
    {
        $commission = $this->calculatePlatformCommission($amount);

        $this->increment('total_revenue', $amount);
        $this->increment('platform_earnings', $commission);
    }

    /**
     * Check if agency can perform action.
     */
    public function canEditRequirements(): bool
    {
        return $this->is_active && $this->can_edit_requirements;
    }

    public function canSetFees(): bool
    {
        return $this->is_active && $this->can_set_fees;
    }

    public function canProcessApplications(): bool
    {
        return $this->is_active && $this->can_process_applications;
    }
}
