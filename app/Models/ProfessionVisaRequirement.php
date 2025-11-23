<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProfessionVisaRequirement extends Model
{
    protected $fillable = [
        'visa_requirement_id',
        'profession_category',
        'profession_title',
        'additional_requirements',
        'additional_documents',
        'min_bank_balance_override',
        'financial_proof_details',
        'fee_adjustment',
        'fee_adjustment_type',
        'required_documents',
        'employment_proof_requirements',
        'income_proof_requirements',
        'special_conditions',
        'rejection_risks',
        'success_tips',
        'risk_level',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'min_bank_balance_override' => 'decimal:2',
        'fee_adjustment' => 'decimal:2',
        'required_documents' => 'array',
        'risk_level' => 'integer',
        'is_active' => 'boolean',
    ];

    /**
     * Get the visa requirement this belongs to.
     */
    public function visaRequirement(): BelongsTo
    {
        return $this->belongsTo(VisaRequirement::class);
    }

    /**
     * Scope: Active profession requirements.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope: Filter by profession category.
     */
    public function scopeByCategory($query, string $category)
    {
        return $query->where('profession_category', $category);
    }

    /**
     * Scope: Filter by risk level.
     */
    public function scopeByRiskLevel($query, int $level)
    {
        return $query->where('risk_level', $level);
    }

    /**
     * Scope: High risk professions.
     */
    public function scopeHighRisk($query)
    {
        return $query->where('risk_level', '>=', 3);
    }

    /**
     * Get the effective minimum bank balance.
     */
    public function getEffectiveMinBankBalance(): ?float
    {
        return $this->min_bank_balance_override 
            ?? $this->visaRequirement->min_bank_balance;
    }

    /**
     * Calculate total fee with adjustment.
     */
    public function calculateFeeWithAdjustment(float $baseFee): float
    {
        if (!$this->fee_adjustment) {
            return $baseFee;
        }

        if ($this->fee_adjustment_type === 'percentage') {
            return $baseFee + ($baseFee * $this->fee_adjustment / 100);
        }

        return $baseFee + $this->fee_adjustment;
    }

    /**
     * Get risk level display text.
     */
    public function getRiskLevelTextAttribute(): string
    {
        return match ($this->risk_level) {
            1 => 'Low Risk',
            2 => 'Medium Risk',
            3 => 'High Risk',
            default => 'Unknown',
        };
    }

    /**
     * Get risk level color for UI.
     */
    public function getRiskLevelColorAttribute(): string
    {
        return match ($this->risk_level) {
            1 => 'green',
            2 => 'yellow',
            3 => 'red',
            default => 'gray',
        };
    }

    /**
     * Get profession display name.
     */
    public function getProfessionDisplayAttribute(): string
    {
        $display = ucwords(str_replace('_', ' ', $this->profession_category));
        
        if ($this->profession_title) {
            $display .= " - {$this->profession_title}";
        }

        return $display;
    }

    /**
     * Check if this profession requires additional documents.
     */
    public function hasAdditionalDocuments(): bool
    {
        return !empty($this->required_documents) || !empty($this->additional_documents);
    }
}
