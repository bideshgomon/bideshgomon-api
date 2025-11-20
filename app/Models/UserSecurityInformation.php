<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

class UserSecurityInformation extends Model
{
    use HasFactory;

    protected $table = 'user_security_information';

    protected $fillable = [
        'user_id',
        
        // Criminal Records
        'has_criminal_record',
        'criminal_record_details',
        'country_of_conviction',
        'conviction_date',
        'offense_type',
        'sentence_duration',
        'sentence_served',
        'sentence_completion_date',
        
        // Deportation History
        'has_been_deported',
        'deportation_country',
        'deportation_date',
        'deportation_reason',
        'deportation_ban_active',
        'deportation_ban_expiry',
        
        // Visa Violations
        'has_overstayed_visa',
        'overstay_country',
        'overstay_duration_days',
        'overstay_date',
        'overstay_explanation',
        'has_worked_illegally',
        'illegal_work_country',
        'illegal_work_details',
        'has_violated_visa_conditions',
        'visa_violation_details',
        
        // Immigration Bans
        'has_immigration_ban',
        'ban_country',
        'ban_start_date',
        'ban_end_date',
        'ban_reason',
        
        // Military Service
        'has_military_service',
        'military_country',
        'military_branch',
        'military_rank',
        'military_service_start_date',
        'military_service_end_date',
        'discharge_type',
        'military_documents_path',
        
        // Police Clearance
        'has_police_clearance',
        'police_clearance_country',
        'police_clearance_issue_date',
        'police_clearance_expiry_date',
        'police_clearance_document_path',
        
        // Background Check
        'background_check_completed',
        'background_check_date',
        'background_check_agency',
        'background_check_document_path',
        
        // Health & Medical
        'has_health_issues',
        'health_issues_details',
        'medical_exam_completed',
        'medical_exam_date',
        'medical_certificate_path',
        
        // Terrorism & Security
        'has_terrorist_links',
        'terrorist_links_details',
        'has_been_denied_entry',
        'denied_entry_country',
        'denied_entry_date',
        'denied_entry_reason',
        
        // Character References
        'character_reference_name_1',
        'character_reference_phone_1',
        'character_reference_email_1',
        'character_reference_name_2',
        'character_reference_phone_2',
        'character_reference_email_2',
        
        // Additional
        'additional_security_notes',
        'declaration_signed',
        'declaration_date',
    ];

    protected $casts = [
        // Booleans
        'has_criminal_record' => 'boolean',
        'sentence_served' => 'boolean',
        'has_been_deported' => 'boolean',
        'deportation_ban_active' => 'boolean',
        'has_overstayed_visa' => 'boolean',
        'has_worked_illegally' => 'boolean',
        'has_violated_visa_conditions' => 'boolean',
        'has_immigration_ban' => 'boolean',
        'has_military_service' => 'boolean',
        'has_police_clearance' => 'boolean',
        'background_check_completed' => 'boolean',
        'has_health_issues' => 'boolean',
        'medical_exam_completed' => 'boolean',
        'has_terrorist_links' => 'boolean',
        'has_been_denied_entry' => 'boolean',
        'declaration_signed' => 'boolean',
        
        // Dates
        'conviction_date' => 'date',
        'sentence_completion_date' => 'date',
        'deportation_date' => 'date',
        'deportation_ban_expiry' => 'date',
        'overstay_date' => 'date',
        'ban_start_date' => 'date',
        'ban_end_date' => 'date',
        'military_service_start_date' => 'date',
        'military_service_end_date' => 'date',
        'police_clearance_issue_date' => 'date',
        'police_clearance_expiry_date' => 'date',
        'background_check_date' => 'date',
        'medical_exam_date' => 'date',
        'denied_entry_date' => 'date',
        'declaration_date' => 'date',
        
        // Integers
        'overstay_duration_days' => 'integer',
    ];

    /**
     * Get the user that owns the security information
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the admin who reviewed the security information
     */
    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    /**
     * Check if police clearance is expired
     */
    public function isPoliceCleared(): bool
    {
        if (!$this->has_police_clearance || !$this->police_clearance_expiry_date) {
            return false;
        }
        
        return $this->police_clearance_expiry_date > now();
    }

    /**
     * Calculate risk level based on security factors
     */
    public function calculateRiskLevel(): string
    {
        $criticalFlags = [
            'has_criminal_record',
            'has_terrorist_links',
            'has_been_deported',
        ];

        $highFlags = [
            'has_immigration_ban',
            'deportation_ban_active',
            'has_worked_illegally',
        ];

        $mediumFlags = [
            'has_overstayed_visa',
            'has_violated_visa_conditions',
            'has_been_denied_entry',
        ];

        // Check critical flags
        foreach ($criticalFlags as $flag) {
            if ($this->$flag) {
                return 'critical';
            }
        }

        // Check high flags
        foreach ($highFlags as $flag) {
            if ($this->$flag) {
                return 'high';
            }
        }

        // Check medium flags
        foreach ($mediumFlags as $flag) {
            if ($this->$flag) {
                return 'medium';
            }
        }

        // Check overstay duration
        if ($this->overstay_duration_days >= 90) {
            return 'high';
        } elseif ($this->overstay_duration_days >= 30) {
            return 'medium';
        }

        return 'low';
    }
}