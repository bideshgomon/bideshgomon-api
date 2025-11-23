<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AgencyCountryAssignment;
use App\Models\VisaRequirement;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;

class AgencyAssignmentController extends Controller
{
    /**
     * Display agency country assignments.
     */
    public function index(Request $request)
    {
        $query = AgencyCountryAssignment::with(['agency', 'assignedBy']);

        // Filters
        if ($request->filled('agency_id')) {
            $query->where('agency_id', $request->agency_id);
        }

        if ($request->filled('country')) {
            $query->where('country', 'like', '%' . $request->country . '%');
        }

        if ($request->filled('is_active')) {
            $query->where('is_active', $request->boolean('is_active'));
        }

        $assignments = $query->orderBy('assigned_at', 'desc')
            ->paginate(20)
            ->withQueryString();

        // Get statistics
        $stats = [
            'total_assignments' => AgencyCountryAssignment::count(),
            'active_assignments' => AgencyCountryAssignment::where('is_active', true)->count(),
            'total_agencies' => AgencyCountryAssignment::distinct('agency_id')->count(),
            'total_applications' => AgencyCountryAssignment::sum('total_applications'),
            'total_revenue' => AgencyCountryAssignment::sum('total_revenue'),
            'platform_earnings' => AgencyCountryAssignment::sum('platform_earnings'),
        ];

        // Get agencies list for filter
        $agencies = User::where('role', 'agency')
            ->where('is_active', true)
            ->orderBy('name')
            ->get(['id', 'name', 'email']);

        return Inertia::render('Admin/AgencyAssignments/Index', [
            'assignments' => $assignments,
            'stats' => $stats,
            'agencies' => $agencies,
            'filters' => $request->only(['agency_id', 'country', 'is_active']),
        ]);
    }

    /**
     * Show form to assign agency to country.
     */
    public function create()
    {
        $agencies = User::where('role', 'agency')
            ->where('is_active', true)
            ->orderBy('name')
            ->get(['id', 'name', 'email']);

        // Get available countries from visa requirements
        $countries = VisaRequirement::select('country', 'country_code', 'visa_type')
            ->distinct()
            ->orderBy('country')
            ->get()
            ->groupBy('country')
            ->map(function ($items, $country) {
                return [
                    'country' => $country,
                    'country_code' => $items->first()->country_code,
                    'visa_types' => $items->pluck('visa_type')->unique()->values()->toArray(),
                ];
            })
            ->values();

        return Inertia::render('Admin/AgencyAssignments/Create', [
            'agencies' => $agencies,
            'countries' => $countries,
        ]);
    }

    /**
     * Store new agency assignment.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'agency_id' => 'required|exists:users,id',
            'country' => 'required|string|max:255',
            'country_code' => 'required|string|max:3',
            'visa_type' => 'required|string|max:255',
            'platform_commission_rate' => 'required|numeric|min:0|max:100',
            'commission_type' => 'required|in:percentage,fixed',
            'fixed_commission_amount' => 'nullable|numeric|min:0',
            'can_edit_requirements' => 'boolean',
            'can_set_fees' => 'boolean',
            'can_process_applications' => 'boolean',
            'assignment_notes' => 'nullable|string',
        ]);

        $validated['assigned_by'] = auth()->id();
        $validated['assigned_at'] = now();

        $assignment = AgencyCountryAssignment::create($validated);

        // Update visa requirements for this country to assign to agency
        if ($request->boolean('auto_assign_requirements', true)) {
            VisaRequirement::where('country', $validated['country'])
                ->where('visa_type', $validated['visa_type'])
                ->whereNull('managed_by_agency')
                ->update([
                    'managed_by_agency' => $validated['agency_id'],
                    'agency_assigned_at' => now(),
                    'platform_commission_rate' => $validated['platform_commission_rate'],
                    'agency_can_edit' => $validated['can_edit_requirements'] ?? true,
                ]);
        }

        return redirect()
            ->route('admin.agency-assignments.show', $assignment)
            ->with('success', 'Agency assigned to country successfully!');
    }

    /**
     * Show assignment details.
     */
    public function show(AgencyCountryAssignment $agencyAssignment)
    {
        $agencyAssignment->load(['agency', 'assignedBy']);

        // Get visa requirements for this assignment
        $requirements = VisaRequirement::where('country', $agencyAssignment->country)
            ->where('visa_type', $agencyAssignment->visa_type)
            ->where('managed_by_agency', $agencyAssignment->agency_id)
            ->with(['documents', 'professionRequirements'])
            ->get();

        return Inertia::render('Admin/AgencyAssignments/Show', [
            'assignment' => $agencyAssignment,
            'requirements' => $requirements,
        ]);
    }

    /**
     * Update assignment.
     */
    public function update(Request $request, AgencyCountryAssignment $agencyAssignment)
    {
        $validated = $request->validate([
            'platform_commission_rate' => 'required|numeric|min:0|max:100',
            'commission_type' => 'required|in:percentage,fixed',
            'fixed_commission_amount' => 'nullable|numeric|min:0',
            'can_edit_requirements' => 'boolean',
            'can_set_fees' => 'boolean',
            'can_process_applications' => 'boolean',
            'assignment_notes' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        $agencyAssignment->update($validated);

        // Update visa requirements commission rates
        VisaRequirement::where('country', $agencyAssignment->country)
            ->where('visa_type', $agencyAssignment->visa_type)
            ->where('managed_by_agency', $agencyAssignment->agency_id)
            ->update([
                'platform_commission_rate' => $validated['platform_commission_rate'],
                'agency_can_edit' => $validated['can_edit_requirements'] ?? true,
            ]);

        return back()->with('success', 'Assignment updated successfully!');
    }

    /**
     * Remove assignment.
     */
    public function destroy(AgencyCountryAssignment $agencyAssignment)
    {
        // Unassign visa requirements
        VisaRequirement::where('country', $agencyAssignment->country)
            ->where('visa_type', $agencyAssignment->visa_type)
            ->where('managed_by_agency', $agencyAssignment->agency_id)
            ->update([
                'managed_by_agency' => null,
                'agency_assigned_at' => null,
                'agency_service_fee' => null,
                'agency_processing_fee' => null,
            ]);

        $agencyAssignment->delete();

        return redirect()
            ->route('admin.agency-assignments.index')
            ->with('success', 'Assignment removed successfully!');
    }

    /**
     * Toggle assignment active status.
     */
    public function toggleActive(AgencyCountryAssignment $agencyAssignment)
    {
        $agencyAssignment->update([
            'is_active' => !$agencyAssignment->is_active,
        ]);

        return back()->with('success', 'Assignment status updated!');
    }

    /**
     * Assign visa requirement to agency.
     */
    public function assignRequirement(Request $request)
    {
        $validated = $request->validate([
            'visa_requirement_id' => 'required|exists:visa_requirements,id',
            'agency_id' => 'required|exists:users,id',
            'platform_commission_rate' => 'required|numeric|min:0|max:100',
        ]);

        $requirement = VisaRequirement::findOrFail($validated['visa_requirement_id']);
        
        $requirement->update([
            'managed_by_agency' => $validated['agency_id'],
            'agency_assigned_at' => now(),
            'platform_commission_rate' => $validated['platform_commission_rate'],
            'agency_can_edit' => true,
        ]);

        return back()->with('success', 'Visa requirement assigned to agency!');
    }

    /**
     * Unassign visa requirement from agency.
     */
    public function unassignRequirement(VisaRequirement $visaRequirement)
    {
        $visaRequirement->update([
            'managed_by_agency' => null,
            'agency_assigned_at' => null,
            'agency_service_fee' => null,
            'agency_processing_fee' => null,
            'platform_commission' => null,
            'total_agency_earnings' => null,
        ]);

        return back()->with('success', 'Visa requirement unassigned from agency!');
    }

    /**
     * Update platform commission for requirement.
     */
    public function updateCommission(Request $request, VisaRequirement $visaRequirement)
    {
        $validated = $request->validate([
            'platform_commission_rate' => 'required|numeric|min:0|max:100',
        ]);

        $visaRequirement->update($validated);
        $visaRequirement->recalculateCommissions();
        $visaRequirement->save();

        return back()->with('success', 'Commission rate updated!');
    }
}
