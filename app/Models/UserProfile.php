<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserProfile extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        // Passport-standard name fields
        'first_name',
        'middle_name',
        'last_name',
        'name_as_per_passport',
        // Profile fields
        'bio',
        'avatar',
        'dob',
        'gender',
        'nationality',
        'marital_status',
        'present_address_line',
        'present_city',
        'present_division',
        'present_district',
        'present_postal_code',
        'permanent_address_line',
        'permanent_city',
        'permanent_division',
        'permanent_district',
        'permanent_postal_code',
        'nid',
        'passport_number',
        'passport_issue_date',
        'passport_expiry_date',
        // Financial fields
        'employer_name',
        'employer_address',
        'employment_start_date',
        'monthly_income_bdt',
        'annual_income_bdt',
        'bank_name',
        'bank_branch',
        'bank_account_number',
        'bank_account_type',
        'bank_balance_bdt',
        'bank_statement_path',
        'owns_property',
        'property_type',
        'property_address',
        'property_value_bdt',
        'property_documents_path',
        'owns_vehicle',
        'vehicle_type',
        'vehicle_make_model',
        'vehicle_year',
        'vehicle_value_bdt',
        'has_investments',
        'investment_types',
        'investment_value_bdt',
        'has_liabilities',
        'liability_types',
        'liabilities_amount_bdt',
        'total_assets_bdt',
        'net_worth_bdt',
        'tax_return_path',
        'salary_certificate_path',
        'financial_sponsor_info',
        'social_links',
        // Emergency Contact
        'emergency_contact_name',
        'emergency_contact_relationship',
        'emergency_contact_phone',
        'emergency_contact_email',
        'emergency_contact_address',
        // Medical Information
        'blood_group',
        'allergies',
        'medical_conditions',
        'vaccinations',
        'health_insurance_provider',
        'health_insurance_policy_number',
        'health_insurance_expiry_date',
        // References
        'references',
        // Certifications
        'certifications',
        // Privacy
        'privacy_settings',
        'data_downloaded_at',
        'preferences',
    ];

    protected $casts = [
        'dob' => 'date',
        'passport_issue_date' => 'date',
        'passport_expiry_date' => 'date',
        'health_insurance_expiry_date' => 'date',
        'data_downloaded_at' => 'datetime',
        'social_links' => 'array',
        'vaccinations' => 'array',
        'references' => 'array',
        'certifications' => 'array',
        'privacy_settings' => 'array',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
