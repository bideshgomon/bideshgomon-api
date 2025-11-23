<?php

namespace App\Http\Controllers\Agency;

use App\Http\Controllers\Controller;
use App\Models\AgencyCountryAssignment;
use App\Models\VisaRequirement;
use App\Models\VisaRequirementDocument;
use App\Models\ProfessionVisaRequirement;
use Illuminate\Http\Request;
use Inertia\Inertia;

class VisaManagementController extends Controller
{
    /**
     * Display agency's assigned countries.
     */
    public function index()
    {
        $agencyId = auth()->id();

        // Get assignments
        $assignments = AgencyCountryAssignment::where('agency_id', $agencyId)
            ->where('is_active', true)
            ->with(['visaRequirements' => function ($query) {
                $query->orderBy('country');
            }])
            ->orderBy('assigned_at', 'desc')
            ->get();

        // Get statistics
        $stats = [
            'total_countries' => $assignments->count(),
            'total_applications' => $assignments->sum('total_applications'),
            'approved_applications' => $assignments->sum('approved_applications'),
            'pending_applications' => $assignments->sum('pending_applications'),
            'total_revenue' => $assignments->sum('total_revenue'),
            'total_earnings' => $assignments->sum('total_revenue') - $assignments->sum('platform_earnings'),
            'platform_commission' => $assignments->sum('platform_earnings'),
            'average_approval_rate' => $assignments->avg('approval_rate') ?? 0,
        ];

        return Inertia::render('Agency/VisaManagement/Index', [
            'assignments' => $assignments,
            'stats' => $stats,
        ]);
    }

    /**
     * Show visa requirements for a country.
     */
    public function show(string $country, string $visaType)
    {
        $agencyId = auth()->id();

        // Check if agency is assigned to this country
        $assignment = AgencyCountryAssignment::where('agency_id', $agencyId)
            ->where('country', $country)
            ->where('visa_type', $visaType)
            ->where('is_active', true)
            ->firstOrFail();

        // Get visa requirements
        $requirements = VisaRequirement::where('country', $country)
            ->where('visa_type', $visaType)
            ->where('managed_by_agency', $agencyId)
            ->with(['documents', 'professionRequirements'])
            ->get();

        return Inertia::render('Agency/VisaManagement/Show', [
            'assignment' => $assignment,
            'requirements' => $requirements,
        ]);
    }

    /**
     * Update visa requirement fees.
     */
    public function updateFees(Request $request, VisaRequirement $visaRequirement)
    {
        $agencyId = auth()->id();

        // Verify ownership and permission
        if ($visaRequirement->managed_by_agency !== $agencyId) {
            abort(403, 'You are not assigned to this visa requirement.');
        }

        $assignment = AgencyCountryAssignment::where('agency_id', $agencyId)
            ->where('country', $visaRequirement->country)
            ->where('visa_type', $visaRequirement->visa_type)
            ->where('is_active', true)
            ->first();

        if (!$assignment || !$assignment->canSetFees()) {
            abort(403, 'You do not have permission to set fees for this country.');
        }

        $validated = $request->validate([
            'agency_service_fee' => 'required|numeric|min:0',
            'agency_processing_fee' => 'required|numeric|min:0',
        ]);

        $visaRequirement->update($validated);
        $visaRequirement->recalculateCommissions();
        $visaRequirement->save();

        return back()->with('success', 'Fees updated successfully!');
    }

    /**
     * Update visa requirement details (if allowed).
     */
    public function updateRequirement(Request $request, VisaRequirement $visaRequirement)
    {
        $agencyId = auth()->id();

        // Verify ownership and permission
        if ($visaRequirement->managed_by_agency !== $agencyId) {
            abort(403, 'You are not assigned to this visa requirement.');
        }

        if (!$visaRequirement->canBeEditedByAgency($agencyId)) {
            abort(403, 'You do not have permission to edit this requirement.');
        }

        $validated = $request->validate([
            'processing_time' => 'nullable|string|max:255',
            'validity_period' => 'nullable|string|max:255',
            'max_stay_duration' => 'nullable|string|max:255',
            'entry_type' => 'nullable|in:single,multiple',
            'description' => 'nullable|string',
            'important_notes' => 'nullable|string',
        ]);

        $visaRequirement->update($validated);

        return back()->with('success', 'Requirement updated successfully!');
    }

    /**
     * Add document requirement.
     */
    public function storeDocument(Request $request, VisaRequirement $visaRequirement)
    {
        $agencyId = auth()->id();

        // Verify ownership and permission
        if ($visaRequirement->managed_by_agency !== $agencyId) {
            abort(403, 'You are not assigned to this visa requirement.');
        }

        if (!$visaRequirement->canBeEditedByAgency($agencyId)) {
            abort(403, 'You do not have permission to edit this requirement.');
        }

        $validated = $request->validate([
            'document_name' => 'required|string|max:255',
            'document_type' => 'required|string|max:255',
            'description' => 'nullable|string',
            'is_required' => 'boolean',
            'format_requirements' => 'nullable|string',
            'sample_link' => 'nullable|url',
        ]);

        $validated['visa_requirement_id'] = $visaRequirement->id;

        VisaRequirementDocument::create($validated);

        return back()->with('success', 'Document requirement added!');
    }

    /**
     * Update document requirement.
     */
    public function updateDocument(Request $request, VisaRequirementDocument $document)
    {
        $agencyId = auth()->id();
        $visaRequirement = $document->visaRequirement;

        // Verify ownership and permission
        if ($visaRequirement->managed_by_agency !== $agencyId) {
            abort(403, 'You are not assigned to this visa requirement.');
        }

        if (!$visaRequirement->canBeEditedByAgency($agencyId)) {
            abort(403, 'You do not have permission to edit this requirement.');
        }

        $validated = $request->validate([
            'document_name' => 'required|string|max:255',
            'document_type' => 'required|string|max:255',
            'description' => 'nullable|string',
            'is_required' => 'boolean',
            'format_requirements' => 'nullable|string',
            'sample_link' => 'nullable|url',
        ]);

        $document->update($validated);

        return back()->with('success', 'Document requirement updated!');
    }

    /**
     * Delete document requirement.
     */
    public function destroyDocument(VisaRequirementDocument $document)
    {
        $agencyId = auth()->id();
        $visaRequirement = $document->visaRequirement;

        // Verify ownership and permission
        if ($visaRequirement->managed_by_agency !== $agencyId) {
            abort(403, 'You are not assigned to this visa requirement.');
        }

        if (!$visaRequirement->canBeEditedByAgency($agencyId)) {
            abort(403, 'You do not have permission to edit this requirement.');
        }

        $document->delete();

        return back()->with('success', 'Document requirement removed!');
    }

    /**
     * Add profession-specific requirement.
     */
    public function storeProfession(Request $request, VisaRequirement $visaRequirement)
    {
        $agencyId = auth()->id();

        // Verify ownership and permission
        if ($visaRequirement->managed_by_agency !== $agencyId) {
            abort(403, 'You are not assigned to this visa requirement.');
        }

        if (!$visaRequirement->canBeEditedByAgency($agencyId)) {
            abort(403, 'You do not have permission to edit this requirement.');
        }

        $validated = $request->validate([
            'profession' => 'required|string|max:255',
            'additional_requirements' => 'nullable|string',
            'additional_documents' => 'nullable|string',
            'government_fee' => 'required|numeric|min:0',
            'service_fee' => 'required|numeric|min:0',
            'processing_fee' => 'required|numeric|min:0',
        ]);

        $validated['visa_requirement_id'] = $visaRequirement->id;

        ProfessionVisaRequirement::create($validated);

        return back()->with('success', 'Profession requirement added!');
    }

    /**
     * Update profession-specific requirement.
     */
    public function updateProfession(Request $request, ProfessionVisaRequirement $profession)
    {
        $agencyId = auth()->id();
        $visaRequirement = $profession->visaRequirement;

        // Verify ownership and permission
        if ($visaRequirement->managed_by_agency !== $agencyId) {
            abort(403, 'You are not assigned to this visa requirement.');
        }

        if (!$visaRequirement->canBeEditedByAgency($agencyId)) {
            abort(403, 'You do not have permission to edit this requirement.');
        }

        $validated = $request->validate([
            'profession' => 'required|string|max:255',
            'additional_requirements' => 'nullable|string',
            'additional_documents' => 'nullable|string',
            'government_fee' => 'required|numeric|min:0',
            'service_fee' => 'required|numeric|min:0',
            'processing_fee' => 'required|numeric|min:0',
        ]);

        $profession->update($validated);

        return back()->with('success', 'Profession requirement updated!');
    }

    /**
     * Delete profession-specific requirement.
     */
    public function destroyProfession(ProfessionVisaRequirement $profession)
    {
        $agencyId = auth()->id();
        $visaRequirement = $profession->visaRequirement;

        // Verify ownership and permission
        if ($visaRequirement->managed_by_agency !== $agencyId) {
            abort(403, 'You are not assigned to this visa requirement.');
        }

        if (!$visaRequirement->canBeEditedByAgency($agencyId)) {
            abort(403, 'You do not have permission to edit this requirement.');
        }

        $profession->delete();

        return back()->with('success', 'Profession requirement removed!');
    }

    /**
     * Get agency statistics.
     */
    public function statistics()
    {
        $agencyId = auth()->id();

        $assignments = AgencyCountryAssignment::where('agency_id', $agencyId)
            ->where('is_active', true)
            ->get();

        $stats = [
            'overview' => [
                'total_countries' => $assignments->count(),
                'total_applications' => $assignments->sum('total_applications'),
                'approved_applications' => $assignments->sum('approved_applications'),
                'rejected_applications' => $assignments->sum('rejected_applications'),
                'pending_applications' => $assignments->sum('pending_applications'),
            ],
            'revenue' => [
                'total_revenue' => $assignments->sum('total_revenue'),
                'platform_commission' => $assignments->sum('platform_earnings'),
                'net_earnings' => $assignments->sum('total_revenue') - $assignments->sum('platform_earnings'),
                'average_commission_rate' => $assignments->avg('platform_commission_rate') ?? 0,
            ],
            'performance' => [
                'average_approval_rate' => $assignments->avg('approval_rate') ?? 0,
                'average_rejection_rate' => $assignments->avg('rejection_rate') ?? 0,
                'best_performing_country' => $assignments->sortByDesc('approval_rate')->first()?->country,
            ],
            'by_country' => $assignments->map(function ($assignment) {
                return [
                    'country' => $assignment->country,
                    'visa_type' => $assignment->visa_type,
                    'applications' => $assignment->total_applications,
                    'approval_rate' => $assignment->approval_rate,
                    'revenue' => $assignment->total_revenue,
                    'earnings' => $assignment->total_revenue - $assignment->platform_earnings,
                    'performance_status' => $assignment->performance_status,
                ];
            }),
        ];

        return Inertia::render('Agency/VisaManagement/Statistics', [
            'stats' => $stats,
        ]);
    }
}
