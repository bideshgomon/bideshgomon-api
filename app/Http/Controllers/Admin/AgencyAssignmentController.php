<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AgencyCountryAssignment;
use App\Models\Country;
use App\Models\ServiceModule;
use App\Models\VisaRequirement;
use App\Models\VisaType;
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
    public function create(Request $request)
    {
        // Get all agencies by default
        $agenciesQuery = User::whereHas('role', function($query) {
            $query->where('slug', 'agency');
        });

        // Filter agencies by service module if provided
        if ($request->filled('service_module_id')) {
            $serviceModuleId = $request->service_module_id;
            
            // Get agencies already assigned to this service module
            $assignedAgencyIds = AgencyCountryAssignment::where('service_module_id', $serviceModuleId)
                ->distinct()
                ->pluck('agency_id');
            
            // Optionally show only assigned or unassigned agencies
            if ($request->filter === 'assigned') {
                $agenciesQuery->whereIn('id', $assignedAgencyIds);
            } elseif ($request->filter === 'unassigned') {
                $agenciesQuery->whereNotIn('id', $assignedAgencyIds);
            }
        }

        $agencies = $agenciesQuery
            ->orderBy('name')
            ->get(['id', 'name', 'email']);

        // Get all service modules
        $serviceModules = ServiceModule::where('is_active', true)
            ->with('category')
            ->orderBy('name')
            ->get(['id', 'name', 'slug', 'service_category_id', 'service_type']);

        // Get all active countries
        $countries = Country::where('is_active', true)
            ->orderBy('name')
            ->get(['id', 'name', 'iso_code_2', 'iso_code_3']);

        // Get all active visa types
        $visaTypes = VisaType::where('is_active', true)
            ->orderBy('name')
            ->get(['id', 'name', 'slug']);

        return Inertia::render('Admin/AgencyAssignments/Create', [
            'agencies' => $agencies,
            'serviceModules' => $serviceModules,
            'countries' => $countries,
            'visaTypes' => $visaTypes,
            'filters' => $request->only(['service_module_id', 'filter']),
        ]);
    }

    /**
     * Store new agency assignment.
     * Supports single or multiple country assignments.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'agency_id' => 'required|exists:users,id',
            'service_module_id' => 'nullable|exists:service_modules,id',
            'service_module_ids' => 'nullable|array',
            'service_module_ids.*' => 'exists:service_modules,id',
            'country_id' => 'nullable|exists:countries,id',
            'country_ids' => 'nullable|array',
            'country_ids.*' => 'exists:countries,id',
            'country' => 'nullable|string|max:255',
            'country_code' => 'nullable|string|max:3',
            'visa_type_id' => 'nullable|exists:visa_types,id',
            'visa_type' => 'nullable|string|max:255',
            'assignment_scope' => 'required|in:global,country_specific,visa_specific',
            'platform_commission_rate' => 'required|numeric|min:0|max:100',
            'commission_type' => 'required|in:percentage,fixed',
            'fixed_commission_amount' => 'nullable|numeric|min:0',
            'can_edit_requirements' => 'boolean',
            'can_set_fees' => 'boolean',
            'can_process_applications' => 'boolean',
            'assignment_notes' => 'nullable|string',
        ]);

        // Check if multiple services should be assigned
        $serviceIds = $validated['service_module_ids'] ?? [];
        $countryIds = $validated['country_ids'] ?? [];
        
        // Handle multiple services
        if (!empty($serviceIds) && count($serviceIds) > 0) {
            $totalAssignments = 0;
            
            foreach ($serviceIds as $serviceId) {
                $serviceModule = ServiceModule::find($serviceId);
                if (!$serviceModule) continue;
                
                // If multiple countries too, create assignment for each combination
                if (!empty($countryIds) && count($countryIds) > 0) {
                    foreach ($countryIds as $countryId) {
                        $country = Country::find($countryId);
                        if (!$country) continue;
                        
                        $assignmentData = $validated;
                        $assignmentData['service_module_id'] = $serviceId;
                        $assignmentData['country_id'] = $country->id;
                        $assignmentData['country'] = $country->name;
                        $assignmentData['country_code'] = $country->iso_code_2;
                        
                        if ($validated['visa_type_id']) {
                            $visaType = VisaType::find($validated['visa_type_id']);
                            $assignmentData['visa_type'] = $visaType->name;
                        } else {
                            // Set default for global assignments
                            $assignmentData['visa_type'] = 'general';
                        }
                        
                        $assignmentData['assigned_by'] = auth()->id();
                        $assignmentData['assigned_at'] = now();
                        unset($assignmentData['country_ids'], $assignmentData['service_module_ids']);
                        
                        try {
                            AgencyCountryAssignment::create($assignmentData);
                            $totalAssignments++;
                        } catch (\Exception $e) {
                            \Log::error('Failed to create assignment', [
                                'service' => $serviceModule->name,
                                'country' => $country->name,
                                'error' => $e->getMessage()
                            ]);
                        }
                    }
                } else {
                    // Multiple services, single or no country
                    $assignmentData = $validated;
                    $assignmentData['service_module_id'] = $serviceId;
                    
                    if ($validated['country_id']) {
                        $country = Country::find($validated['country_id']);
                        $assignmentData['country'] = $country->name;
                        $assignmentData['country_code'] = $country->iso_code_2;
                    }
                    
                    if ($validated['visa_type_id']) {
                        $visaType = VisaType::find($validated['visa_type_id']);
                        $assignmentData['visa_type'] = $visaType->name;
                    } else {
                        // Set default for global assignments
                        $assignmentData['visa_type'] = 'general';
                    }
                    
                    $assignmentData['assigned_by'] = auth()->id();
                    $assignmentData['assigned_at'] = now();
                    unset($assignmentData['country_ids'], $assignmentData['service_module_ids']);
                    
                    try {
                        AgencyCountryAssignment::create($assignmentData);
                        $totalAssignments++;
                    } catch (\Exception $e) {
                        \Log::error('Failed to create assignment', [
                            'service' => $serviceModule->name,
                            'error' => $e->getMessage()
                        ]);
                    }
                }
            }
            
            if ($totalAssignments === 0) {
                return back()->with('error', 'Failed to create any assignments. Please check the logs.');
            }
            
            $serviceCount = count($serviceIds);
            $countryCount = count($countryIds) > 0 ? count($countryIds) : 1;
            
            return redirect()
                ->route('admin.agency-assignments.index')
                ->with('success', "Successfully created {$totalAssignments} assignments ({$serviceCount} services × {$countryCount} countries)!");
        }
        
        // Check if multiple countries should be assigned (single service)
        $countryIds = $validated['country_ids'] ?? [];
        
        if (!empty($countryIds) && count($countryIds) > 0) {
            // Bulk assignment for multiple countries
            $assignments = [];
            $successCount = 0;
            
            foreach ($countryIds as $countryId) {
                $country = Country::find($countryId);
                if (!$country) continue;
                
                $assignmentData = $validated;
                $assignmentData['country_id'] = $country->id;
                $assignmentData['country'] = $country->name;
                $assignmentData['country_code'] = $country->iso_code_2;
                
                // Set visa type name if visa_type_id provided
                if ($validated['visa_type_id']) {
                    $visaType = VisaType::find($validated['visa_type_id']);
                    $assignmentData['visa_type'] = $visaType->name;
                } else {
                    // Set default for global assignments
                    $assignmentData['visa_type'] = 'general';
                }
                
                $assignmentData['assigned_by'] = auth()->id();
                $assignmentData['assigned_at'] = now();
                
                // Remove country_ids from the data before creating
                unset($assignmentData['country_ids']);
                
                try {
                    $assignment = AgencyCountryAssignment::create($assignmentData);
                    
                    // Auto-assign existing visa requirements if applicable
                    if ($request->boolean('auto_assign_requirements', true) && $assignmentData['country'] && $assignmentData['visa_type']) {
                        VisaRequirement::where('country', $assignmentData['country'])
                            ->where('visa_type', $assignmentData['visa_type'])
                            ->whereNull('managed_by_agency')
                            ->update([
                                'managed_by_agency' => $validated['agency_id'],
                                'agency_assigned_at' => now(),
                                'platform_commission_rate' => $validated['platform_commission_rate'],
                                'agency_can_edit' => $validated['can_edit_requirements'] ?? true,
                            ]);
                    }
                    
                    $assignments[] = $assignment;
                    $successCount++;
                } catch (\Exception $e) {
                    // Log error but continue with other countries
                    \Log::error('Failed to create assignment for country ' . $country->name, [
                        'error' => $e->getMessage()
                    ]);
                }
            }
            
            if ($successCount === 0) {
                return back()->with('error', 'Failed to create any assignments. Please check the logs.');
            }
            
            return redirect()
                ->route('admin.agency-assignments.index')
                ->with('success', "Successfully assigned agency to {$successCount} countries!");
                
        } else {
            // Single country assignment (original logic)
            // Set country name and code if country_id provided
            if ($validated['country_id']) {
                $country = Country::find($validated['country_id']);
                $validated['country'] = $country->name;
                $validated['country_code'] = $country->iso_code_2;
            }

            // Set visa type name if visa_type_id provided
            if ($validated['visa_type_id']) {
                $visaType = VisaType::find($validated['visa_type_id']);
                $validated['visa_type'] = $visaType->name;
            } else {
                // Set default for global assignments
                $validated['visa_type'] = 'general';
            }

            $validated['assigned_by'] = auth()->id();
            $validated['assigned_at'] = now();
            
            // Remove country_ids from the data
            unset($validated['country_ids']);

            $assignment = AgencyCountryAssignment::create($validated);

            // Auto-assign existing visa requirements if applicable
            if ($request->boolean('auto_assign_requirements', true) && $validated['country'] && $validated['visa_type']) {
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
                ->with('success', 'Agency assigned to service successfully!');
        }
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
