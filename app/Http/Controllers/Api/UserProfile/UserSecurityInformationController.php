<?php

namespace App\Http\Controllers\Api\UserProfile;

use App\Http\Controllers\Controller;
use App\Models\UserSecurityInformation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class UserSecurityInformationController extends Controller
{
    /**
     * Get the user's security information
     */
    public function show(Request $request)
    {
        $security = $request->user()->securityInformation;
        
        return response()->json([
            'security' => $security,
        ]);
    }

    /**
     * Store or update the user's security information
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            // Criminal Records
            'has_criminal_record' => 'boolean',
            'criminal_record_details' => 'nullable|string|max:5000',
            'conviction_date' => 'nullable|date',
            'conviction_location' => 'nullable|string|max:255',
            'sentence_details' => 'nullable|string|max:2000',
            'rehabilitation_completed' => 'boolean',
            'rehabilitation_details' => 'nullable|string|max:2000',
            
            // Police Clearance
            'has_police_clearance' => 'boolean',
            'police_clearance_country' => 'nullable|string|max:255',
            'police_clearance_issue_date' => 'nullable|date',
            'police_clearance_expiry_date' => 'nullable|date|after:police_clearance_issue_date',
            'police_clearance_certificate_number' => 'nullable|string|max:255',
            'police_clearance_certificate' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
            
            // Court Cases
            'has_pending_court_cases' => 'boolean',
            'court_case_details' => 'nullable|string|max:5000',
            'court_case_date' => 'nullable|date',
            'court_name' => 'nullable|string|max:255',
            'case_status' => 'nullable|in:pending,resolved,dismissed',
            
            // Immigration
            'has_immigration_violations' => 'boolean',
            'immigration_violation_details' => 'nullable|string|max:5000',
            'has_deportation_history' => 'boolean',
            'deportation_details' => 'nullable|string|max:5000',
            'deportation_date' => 'nullable|date',
            'deportation_country' => 'nullable|string|max:255',
            'has_visa_overstay' => 'boolean',
            'visa_overstay_details' => 'nullable|string|max:2000',
            'has_visa_refusal' => 'boolean',
            'visa_refusal_details' => 'nullable|string|max:5000',
            'visa_refusal_count' => 'nullable|integer|min:0|max:99',
            
            // Military Service
            'has_military_service' => 'boolean',
            'military_branch' => 'nullable|string|max:255',
            'military_rank' => 'nullable|string|max:255',
            'military_service_start' => 'nullable|date',
            'military_service_end' => 'nullable|date|after:military_service_start',
            'discharge_type' => 'nullable|in:honorable,dishonorable,general,medical,other',
            'military_service_details' => 'nullable|string|max:5000',
            'military_documents' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
            
            // Security Clearance
            'has_security_clearance' => 'boolean',
            'security_clearance_level' => 'nullable|in:confidential,secret,top-secret',
            'security_clearance_country' => 'nullable|string|max:255',
            'security_clearance_issue_date' => 'nullable|date',
            'security_clearance_expiry_date' => 'nullable|date|after:security_clearance_issue_date',
            'security_clearance_organization' => 'nullable|string|max:255',
            
            // Affiliations
            'has_terrorist_affiliations' => 'boolean',
            'terrorist_affiliation_details' => 'nullable|string|max:5000',
            'has_gang_affiliations' => 'boolean',
            'gang_affiliation_details' => 'nullable|string|max:5000',
            
            // Medical
            'has_communicable_diseases' => 'boolean',
            'communicable_disease_details' => 'nullable|string|max:2000',
            'has_mental_health_issues' => 'boolean',
            'mental_health_details' => 'nullable|string|max:2000',
            'has_drug_addiction' => 'boolean',
            'drug_addiction_details' => 'nullable|string|max:2000',
            'medical_clearance_certificate' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
            'medical_exam_date' => 'nullable|date',
            'medical_exam_location' => 'nullable|string|max:255',
            
            // Background Check
            'background_check_certificate' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
            'background_check_date' => 'nullable|date',
            'background_check_agency' => 'nullable|string|max:255',
            'character_references' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
            
            // Biometrics
            'fingerprints_submitted' => 'boolean',
            'fingerprints_submission_date' => 'nullable|date',
            'fingerprints_location' => 'nullable|string|max:255',
            'biometrics_submitted' => 'boolean',
            'biometrics_submission_date' => 'nullable|date',
            'biometrics_location' => 'nullable|string|max:255',
            
            // Watchlist
            'on_interpol_watchlist' => 'boolean',
            'on_sanctions_list' => 'boolean',
            'watchlist_details' => 'nullable|string|max:5000',
            
            // Employment Verification
            'employment_verified' => 'boolean',
            'employment_verification_date' => 'nullable|date',
            'employment_verification_agency' => 'nullable|string|max:255',
            
            // Risk Assessment
            'risk_assessment_notes' => 'nullable|string|max:5000',
            'additional_screening_required' => 'boolean',
            'additional_screening_details' => 'nullable|string|max:2000',
            
            // Administrative (admin only)
            'security_notes' => 'nullable|string|max:5000',
        ]);

        $user = $request->user();
        $security = $user->securityInformation ?? new UserSecurityInformation(['user_id' => $user->id]);

        // Handle file uploads
        if ($request->hasFile('police_clearance_certificate')) {
            // Delete old file if exists
            if ($security->police_clearance_file_path) {
                Storage::disk('public')->delete($security->police_clearance_file_path);
            }
            
            $path = $request->file('police_clearance_certificate')->store(
                "security-documents/{$user->id}/police-clearance",
                'public'
            );
            $validated['police_clearance_file_path'] = $path;
        }

        if ($request->hasFile('military_documents')) {
            if ($security->military_documents_path) {
                Storage::disk('public')->delete($security->military_documents_path);
            }
            
            $path = $request->file('military_documents')->store(
                "security-documents/{$user->id}/military",
                'public'
            );
            $validated['military_documents_path'] = $path;
        }

        if ($request->hasFile('medical_clearance_certificate')) {
            if ($security->medical_clearance_file_path) {
                Storage::disk('public')->delete($security->medical_clearance_file_path);
            }
            
            $path = $request->file('medical_clearance_certificate')->store(
                "security-documents/{$user->id}/medical",
                'public'
            );
            $validated['medical_clearance_file_path'] = $path;
        }

        if ($request->hasFile('background_check_certificate')) {
            if ($security->background_check_file_path) {
                Storage::disk('public')->delete($security->background_check_file_path);
            }
            
            $path = $request->file('background_check_certificate')->store(
                "security-documents/{$user->id}/background-check",
                'public'
            );
            $validated['background_check_file_path'] = $path;
        }

        if ($request->hasFile('character_references')) {
            if ($security->character_references_file_path) {
                Storage::disk('public')->delete($security->character_references_file_path);
            }
            
            $path = $request->file('character_references')->store(
                "security-documents/{$user->id}/references",
                'public'
            );
            $validated['character_references_file_path'] = $path;
        }

        // Calculate risk level automatically
        $security->fill($validated);
        $validated['security_risk_level'] = $security->calculateRiskLevel();
        
        $security->fill($validated);
        $security->save();

        return response()->json([
            'message' => 'Security information saved successfully',
            'security' => $security->fresh(),
        ]);
    }

    /**
     * Delete the user's security information
     */
    public function destroy(Request $request)
    {
        $security = $request->user()->securityInformation;
        
        if (!$security) {
            return response()->json([
                'message' => 'No security information found',
            ], 404);
        }

        // Delete all uploaded files
        $filePaths = [
            $security->police_clearance_file_path,
            $security->military_documents_path,
            $security->medical_clearance_file_path,
            $security->background_check_file_path,
            $security->character_references_file_path,
        ];

        foreach ($filePaths as $path) {
            if ($path) {
                Storage::disk('public')->delete($path);
            }
        }

        $security->delete();

        return response()->json([
            'message' => 'Security information deleted successfully',
        ]);
    }
}

