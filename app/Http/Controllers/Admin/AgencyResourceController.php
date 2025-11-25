<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AgencyResource;
use App\Models\Agency;
use App\Models\ServiceModule;
use App\Models\Country;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class AgencyResourceController extends Controller
{
    /**
     * Display a listing of agency resources
     */
    public function index(Request $request)
    {
        $query = AgencyResource::with(['agency', 'serviceModule', 'country', 'approvedBy'])
            ->orderBy('created_at', 'desc');

        // Filter by approval status
        if ($request->has('status')) {
            if ($request->status === 'pending') {
                $query->pending();
            } elseif ($request->status === 'approved') {
                $query->approved();
            }
        }

        // Filter by resource type
        if ($request->has('resource_type')) {
            $query->where('resource_type', $request->resource_type);
        }

        // Filter by agency
        if ($request->has('agency_id')) {
            $query->where('agency_id', $request->agency_id);
        }

        $resources = $query->paginate(20);

        return Inertia::render('Admin/AgencyResources/Index', [
            'resources' => $resources,
            'filters' => $request->only(['status', 'resource_type', 'agency_id']),
        ]);
    }

    /**
     * Show the form for creating a new agency resource
     */
    public function create()
    {
        $agencies = Agency::where('is_active', true)
            ->select('id', 'name', 'email')
            ->get();

        // Only services with exclusive_resource model
        $serviceModules = ServiceModule::where('assignment_model', 'exclusive_resource')
            ->where('is_active', true)
            ->select('id', 'name', 'slug', 'service_type')
            ->get();

        $countries = Country::select('id', 'name', 'iso_code_2')
            ->orderBy('name')
            ->get();

        $resourceTypes = [
            'university' => 'University',
            'school' => 'School',
            'language_center' => 'Language Center',
            'training_institute' => 'Training Institute',
            'job_portal' => 'Job Portal',
            'other' => 'Other',
        ];

        return Inertia::render('Admin/AgencyResources/Create', [
            'agencies' => $agencies,
            'serviceModules' => $serviceModules,
            'countries' => $countries,
            'resourceTypes' => $resourceTypes,
        ]);
    }

    /**
     * Store a newly created agency resource
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'agency_id' => 'required|exists:agencies,id',
            'service_module_id' => 'required|exists:service_modules,id',
            'resource_type' => 'required|in:university,school,language_center,training_institute,job_portal,other',
            'resource_name' => 'required|string|max:255',
            'resource_code' => 'nullable|string|max:100',
            'country_id' => 'required|exists:countries,id',
            'city' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'special_commission_rate' => 'nullable|numeric|min:0|max:100',
            'metadata' => 'nullable|array',
        ]);

        // Check if this resource already has a primary owner
        $existingOwner = AgencyResource::where('service_module_id', $validated['service_module_id'])
            ->where('resource_type', $validated['resource_type'])
            ->where('resource_name', $validated['resource_name'])
            ->where('is_primary_owner', true)
            ->first();

        if ($existingOwner) {
            return back()->withErrors([
                'resource_name' => "This resource is already owned by {$existingOwner->agency->name}. Only one agency can be the primary owner."
            ]);
        }

        $validated['is_primary_owner'] = true;
        $validated['requires_admin_approval'] = true;
        $validated['is_approved'] = false;

        $resource = AgencyResource::create($validated);

        return redirect()->route('admin.agency-resources.index')
            ->with('success', 'Agency resource created successfully. Pending approval.');
    }

    /**
     * Display the specified agency resource
     */
    public function show(AgencyResource $agencyResource)
    {
        $agencyResource->load(['agency', 'serviceModule', 'country', 'approvedBy']);

        return Inertia::render('Admin/AgencyResources/Show', [
            'resource' => $agencyResource,
        ]);
    }

    /**
     * Show the form for editing the specified agency resource
     */
    public function edit(AgencyResource $agencyResource)
    {
        $agencies = Agency::where('is_active', true)
            ->select('id', 'name', 'email')
            ->get();

        $serviceModules = ServiceModule::where('assignment_model', 'exclusive_resource')
            ->where('is_active', true)
            ->select('id', 'name', 'slug', 'service_type')
            ->get();

        $countries = Country::select('id', 'name', 'iso_code_2')
            ->orderBy('name')
            ->get();

        $resourceTypes = [
            'university' => 'University',
            'school' => 'School',
            'language_center' => 'Language Center',
            'training_institute' => 'Training Institute',
            'job_portal' => 'Job Portal',
            'other' => 'Other',
        ];

        return Inertia::render('Admin/AgencyResources/Edit', [
            'resource' => $agencyResource,
            'agencies' => $agencies,
            'serviceModules' => $serviceModules,
            'countries' => $countries,
            'resourceTypes' => $resourceTypes,
        ]);
    }

    /**
     * Update the specified agency resource
     */
    public function update(Request $request, AgencyResource $agencyResource)
    {
        $validated = $request->validate([
            'agency_id' => 'required|exists:agencies,id',
            'service_module_id' => 'required|exists:service_modules,id',
            'resource_type' => 'required|in:university,school,language_center,training_institute,job_portal,other',
            'resource_name' => 'required|string|max:255',
            'resource_code' => 'nullable|string|max:100',
            'country_id' => 'required|exists:countries,id',
            'city' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'special_commission_rate' => 'nullable|numeric|min:0|max:100',
            'metadata' => 'nullable|array',
            'is_active' => 'boolean',
        ]);

        $agencyResource->update($validated);

        return redirect()->route('admin.agency-resources.index')
            ->with('success', 'Agency resource updated successfully.');
    }

    /**
     * Approve an agency resource
     */
    public function approve(Request $request, AgencyResource $agencyResource)
    {
        $request->validate([
            'admin_notes' => 'nullable|string',
        ]);

        $agencyResource->approve(
            Auth::id(),
            $request->admin_notes
        );

        return back()->with('success', 'Resource approved successfully.');
    }

    /**
     * Reject an agency resource
     */
    public function reject(Request $request, AgencyResource $agencyResource)
    {
        $request->validate([
            'admin_notes' => 'required|string',
        ]);

        $agencyResource->reject($request->admin_notes);

        return back()->with('success', 'Resource rejected.');
    }

    /**
     * Remove the specified agency resource
     */
    public function destroy(AgencyResource $agencyResource)
    {
        $agencyResource->delete();

        return redirect()->route('admin.agency-resources.index')
            ->with('success', 'Agency resource deleted successfully.');
    }

    /**
     * Check if a resource name is available
     */
    public function checkAvailability(Request $request)
    {
        $request->validate([
            'service_module_id' => 'required|exists:service_modules,id',
            'resource_type' => 'required|string',
            'resource_name' => 'required|string',
        ]);

        $exists = AgencyResource::where('service_module_id', $request->service_module_id)
            ->where('resource_type', $request->resource_type)
            ->where('resource_name', $request->resource_name)
            ->where('is_primary_owner', true)
            ->exists();

        return response()->json([
            'available' => !$exists,
            'message' => $exists ? 'This resource is already owned by another agency.' : 'This resource is available.',
        ]);
    }
}
