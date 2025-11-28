<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class VisaRequirement extends Model
{
    protected $fillable = [
        'service_module_id',
        'country_id',
        'managed_by_agency',
        'agency_assigned_at',
        'country',
        'country_code',
        'profession',
        'visa_type',
        'visa_category',
        'required_documents',
        'profession_specific_docs',
        'processing_time',
        'validity_period',
        'is_template',
        'general_requirements',
        'eligibility_criteria',
        'processing_time_info',
        'validity_info',
        'important_notes',
        'min_bank_balance',
        'bank_statement_months',
        'financial_requirements',
        'government_fee',
        'service_fee',
        'agency_service_fee',
        'agency_processing_fee',
        'platform_commission',
        'platform_commission_rate',
        'total_agency_earnings',
        'total_applicant_cost',
        'processing_fee_standard',
        'processing_fee_express',
        'processing_fee_urgent',
        'currency',
        'processing_days_standard',
        'processing_days_express',
        'processing_days_urgent',
        'interview_required',
        'interview_details',
        'biometrics_required',
        'biometrics_details',
        'application_method',
        'embassy_website',
        'application_process',
        'specific_conditions',
        'prohibited_items',
        'tips_for_applicants',
        'is_active',
        'agency_can_edit',
        'admin_notes',
        'sort_order',
    ];

    protected $casts = [
        'agency_assigned_at' => 'datetime',
        'min_bank_balance' => 'decimal:2',
        'government_fee' => 'decimal:2',
        'service_fee' => 'decimal:2',
        'agency_service_fee' => 'decimal:2',
        'agency_processing_fee' => 'decimal:2',
        'platform_commission' => 'decimal:2',
        'platform_commission_rate' => 'decimal:2',
        'total_agency_earnings' => 'decimal:2',
        'total_applicant_cost' => 'decimal:2',
        'processing_fee_standard' => 'decimal:2',
        'processing_fee_express' => 'decimal:2',
        'processing_fee_urgent' => 'decimal:2',
        'bank_statement_months' => 'integer',
        'processing_days_standard' => 'integer',
        'processing_days_express' => 'integer',
        'processing_days_urgent' => 'integer',
        'interview_required' => 'boolean',
        'biometrics_required' => 'boolean',
        'is_active' => 'boolean',
        'agency_can_edit' => 'boolean',
        'is_template' => 'boolean',
        'specific_conditions' => 'array',
        'prohibited_items' => 'array',
        'tips_for_applicants' => 'array',
        'required_documents' => 'array',
        'profession_specific_docs' => 'array',
    ];

    /**
     * Get the service module this requirement belongs to.
     */
    public function serviceModule(): BelongsTo
    {
        return $this->belongsTo(ServiceModule::class);
    }

    /**
     * Get the agency managing this requirement.
     */
    public function managedByAgency(): BelongsTo
    {
        return $this->belongsTo(User::class, 'managed_by_agency');
    }

    /**
     * Get all documents required for this visa.
     */
    public function documents(): HasMany
    {
        return $this->hasMany(VisaRequirementDocument::class);
    }

    /**
     * Get mandatory documents only.
     */
    public function mandatoryDocuments(): HasMany
    {
        return $this->documents()->where('is_mandatory', true);
    }

    /**
     * Get profession-specific requirements.
     */
    public function professionRequirements(): HasMany
    {
        return $this->hasMany(ProfessionVisaRequirement::class);
    }

    /**
     * Scope: Active requirements only.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope: Filter by country.
     */
    public function scopeForCountry($query, string $country)
    {
        return $query->where('country', $country);
    }

    /**
     * Scope: Filter by visa type.
     */
    public function scopeForVisaType($query, string $visaType)
    {
        return $query->where('visa_type', $visaType);
    }

    /**
     * Get requirements for a specific profession.
     */
    public function getRequirementsForProfession(string $professionCategory, ?string $professionTitle = null)
    {
        $query = $this->professionRequirements()
            ->where('profession_category', $professionCategory)
            ->where('is_active', true);

        if ($professionTitle) {
            $query->where(function ($q) use ($professionTitle) {
                $q->where('profession_title', $professionTitle)
                    ->orWhereNull('profession_title');
            });
        }

        return $query->first();
    }

    /**
     * Calculate total fee for a specific processing type.
     */
    public function getTotalFee(string $processingType = 'standard', ?string $professionCategory = null): float
    {
        $total = $this->government_fee + $this->service_fee;

        // Add processing fee
        $processingFee = match ($processingType) {
            'express' => $this->processing_fee_express,
            'urgent' => $this->processing_fee_urgent,
            default => $this->processing_fee_standard,
        };

        $total += $processingFee ?? 0;

        // Add profession-specific fee adjustment
        if ($professionCategory) {
            $professionReq = $this->professionRequirements()
                ->where('profession_category', $professionCategory)
                ->where('is_active', true)
                ->first();

            if ($professionReq && $professionReq->fee_adjustment) {
                if ($professionReq->fee_adjustment_type === 'percentage') {
                    $total += ($total * $professionReq->fee_adjustment / 100);
                } else {
                    $total += $professionReq->fee_adjustment;
                }
            }
        }

        return round($total, 2);
    }

    /**
     * Get processing days for specific type.
     */
    public function getProcessingDays(string $processingType = 'standard'): ?int
    {
        return match ($processingType) {
            'express' => $this->processing_days_express,
            'urgent' => $this->processing_days_urgent,
            default => $this->processing_days_standard,
        };
    }

    /**
     * Get formatted country display.
     */
    public function getFullCountryNameAttribute(): string
    {
        return "{$this->country} ({$this->country_code})";
    }

    /**
     * Get formatted visa type display.
     */
    public function getVisaTypeDisplayAttribute(): string
    {
        return ucwords(str_replace('_', ' ', $this->visa_type));
    }

    /**
     * Calculate total applicant cost with agency fees and commission.
     */
    public function calculateTotalApplicantCost(string $processingType = 'standard'): float
    {
        $total = $this->government_fee ?? 0;
        $total += $this->service_fee ?? 0;
        $total += $this->agency_service_fee ?? 0;
        $total += $this->agency_processing_fee ?? 0;

        // Add processing fee
        $processingFee = match ($processingType) {
            'express' => $this->processing_fee_express,
            'urgent' => $this->processing_fee_urgent,
            default => $this->processing_fee_standard,
        };

        $total += $processingFee ?? 0;

        return round($total, 2);
    }

    /**
     * Calculate platform commission from agency fees.
     */
    public function calculatePlatformCommission(): float
    {
        if (!$this->managed_by_agency) {
            return 0;
        }

        $agencyFees = ($this->agency_service_fee ?? 0) + ($this->agency_processing_fee ?? 0);
        $commission = ($agencyFees * $this->platform_commission_rate) / 100;

        return round($commission, 2);
    }

    /**
     * Calculate agency earnings after platform commission.
     */
    public function calculateAgencyEarnings(): float
    {
        if (!$this->managed_by_agency) {
            return 0;
        }

        $agencyFees = ($this->agency_service_fee ?? 0) + ($this->agency_processing_fee ?? 0);
        $commission = $this->calculatePlatformCommission();

        return round($agencyFees - $commission, 2);
    }

    /**
     * Auto-calculate and save commission fields.
     */
    public function recalculateCommissions(): void
    {
        $this->attributes['platform_commission'] = $this->calculatePlatformCommission();
        $this->attributes['total_agency_earnings'] = $this->calculateAgencyEarnings();
        $this->attributes['total_applicant_cost'] = $this->calculateTotalApplicantCost();
    }

    /**
     * Scope: Managed by specific agency.
     */
    public function scopeManagedByAgency($query, int $agencyId)
    {
        return $query->where('managed_by_agency', $agencyId);
    }

    /**
     * Scope: Unassigned (no agency).
     */
    public function scopeUnassigned($query)
    {
        return $query->whereNull('managed_by_agency');
    }

    /**
     * Check if agency can edit this requirement.
     */
    public function canBeEditedByAgency(int $agencyId): bool
    {
        return $this->managed_by_agency === $agencyId && $this->agency_can_edit;
    }

    /**
     * Check if requirement is managed by agency.
     */
    public function isManagedByAgency(): bool
    {
        return !is_null($this->managed_by_agency);
    }
}
