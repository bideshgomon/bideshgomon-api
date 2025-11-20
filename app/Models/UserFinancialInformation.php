<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserFinancialInformation extends Model
{
    use HasFactory;

    protected $table = 'user_financial_information';

    protected $fillable = [
        'user_id',
        'annual_income',
        'monthly_income',
        'currency',
        'source_of_income',
        'income_details',
        'employer_name',
        'employer_address',
        'employer_phone',
        'job_title',
        'employment_start_date',
        'primary_bank_name',
        'bank_account_number',
        'bank_branch',
        'bank_address',
        'current_balance',
        'balance_currency',
        'tax_id_number',
        'tax_id_country',
        'files_tax_returns',
        'property_ownership',
        'property_value',
        'property_value_currency',
        'property_address',
        'owns_vehicle',
        'vehicle_details',
        'vehicle_value',
        'investment_value',
        'investment_details',
        'total_assets',
        'total_liabilities',
        'has_sponsor',
        'sponsor_name',
        'sponsor_relationship',
        'sponsor_country',
        'sponsor_phone',
        'sponsor_email',
        'sponsor_address',
        'sponsor_annual_income',
        'sponsor_income_currency',
        'sponsor_occupation',
        'bank_statement_paths',
        'tax_return_paths',
        'employment_letter_path',
        'salary_certificate_path',
        'payslip_paths',
        'property_document_paths',
        'investment_document_paths',
        'sponsor_document_paths',
        'sponsor_affidavit_path',
        'available_funds',
        'funds_currency',
        'funds_verified_date',
        'notes',
    ];

    protected $casts = [
        'annual_income' => 'decimal:2',
        'monthly_income' => 'decimal:2',
        'current_balance' => 'decimal:2',
        'property_value' => 'decimal:2',
        'vehicle_value' => 'decimal:2',
        'investment_value' => 'decimal:2',
        'total_assets' => 'decimal:2',
        'total_liabilities' => 'decimal:2',
        'sponsor_annual_income' => 'decimal:2',
        'available_funds' => 'decimal:2',
        'employment_start_date' => 'date',
        'funds_verified_date' => 'date',
        'files_tax_returns' => 'boolean',
        'owns_vehicle' => 'boolean',
        'has_sponsor' => 'boolean',
        'bank_statement_paths' => 'array',
        'tax_return_paths' => 'array',
        'payslip_paths' => 'array',
        'property_document_paths' => 'array',
        'investment_document_paths' => 'array',
        'sponsor_document_paths' => 'array',
    ];

    /**
     * Get the user that owns the financial information.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Calculate net worth.
     */
    public function getNetWorthAttribute(): float
    {
        return ($this->total_assets ?? 0) - ($this->total_liabilities ?? 0);
    }

    /**
     * Check if user has sufficient funds for a specific country.
     * 
     * @param string $countryCode ISO 3166-1 alpha-2
     * @param float $requiredAmount
     * @return bool
     */
    public function hasSufficientFunds(string $countryCode, float $requiredAmount): bool
    {
        // You can add country-specific fund requirements here
        return ($this->available_funds ?? 0) >= $requiredAmount;
    }

    /**
     * Check if financial information is complete.
     */
    public function isComplete(): bool
    {
        return $this->annual_income !== null
            && $this->source_of_income !== null
            && $this->primary_bank_name !== null
            && ($this->bank_statement_paths !== null && count($this->bank_statement_paths) > 0);
    }

    /**
     * Get formatted annual income with currency.
     */
    public function getFormattedAnnualIncomeAttribute(): string
    {
        if (!$this->annual_income) {
            return 'Not specified';
        }
        
        return format_bd_currency($this->annual_income) . ' ' . $this->currency;
    }

    /**
     * Check if user is self-employed.
     */
    public function isSelfEmployed(): bool
    {
        return $this->source_of_income === 'business';
    }

    /**
     * Check if user has sponsor.
     */
    public function hasActiveSponsor(): bool
    {
        return $this->has_sponsor && !empty($this->sponsor_name);
    }
}
