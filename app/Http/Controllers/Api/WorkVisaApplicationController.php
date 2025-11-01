<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\WorkVisaApplication;
// For dropdowns
// For dropdowns
use Illuminate\Http\Request;

class WorkVisaApplicationController extends Controller
{
    /**
     * Display a listing of the resource for the authenticated user.
     * Admins/Agencies might need a different controller or policy adjustments.
     */
    public function index(Request $request)
    {
        // Scope to the logged-in user
        $applications = $request->user()->workVisaApplications()
            ->with(['destinationCountry', 'jobCategory', 'jobPosting', 'agency']) // Eager load relations
            ->latest()
            ->paginate(15);

        return response()->json($applications);
    }

    /**
     * Store a newly created resource in storage for the authenticated user.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'destination_country_id' => 'required|exists:countries,id',
            'job_category_id' => 'nullable|exists:job_categories,id',
            'job_posting_id' => 'nullable|exists:job_postings,id',
            // 'agency_id' => 'nullable|exists:agencies,id', // Agency likely assigned by admin/process, not user
            'user_notes' => 'nullable|string|max:5000',
            // Status and reference are usually set server-side
        ]);

        // Add user_id and potentially default status
        $validated['user_id'] = $request->user()->id;
        $validated['status'] = 'pending'; // Initial status

        $application = WorkVisaApplication::create($validated);

        return response()->json($application->load(['destinationCountry', 'jobCategory', 'jobPosting']), 201);
    }

    /**
     * Display the specified resource if it belongs to the authenticated user.
     */
    public function show(Request $request, WorkVisaApplication $workVisaApplication)
    {
        // Basic authorization: Ensure the user owns this application
        if ($request->user()->id !== $workVisaApplication->user_id) {
            abort(403, 'Unauthorized access.');
        }

        return response()->json($workVisaApplication->load(['destinationCountry', 'jobCategory', 'jobPosting', 'agency']));
    }

    /**
     * Update the specified resource in storage (limited fields for users).
     */
    public function update(Request $request, WorkVisaApplication $workVisaApplication)
    {
        // Basic authorization: Ensure the user owns this application
        if ($request->user()->id !== $workVisaApplication->user_id) {
            abort(403, 'Unauthorized access.');
        }

        // Only allow users to update certain fields, e.g., notes, maybe resubmit documents?
        // Admin/Agency would likely use a different endpoint/controller to update status, agency, etc.
        $validated = $request->validate([
            // 'destination_country_id' => 'sometimes|required|exists:countries,id', // Should users be allowed to change destination?
            // 'job_category_id' => 'sometimes|nullable|exists:job_categories,id',
            // 'job_posting_id' => 'sometimes|nullable|exists:job_postings,id',
            'user_notes' => 'sometimes|nullable|string|max:5000',
        ]);

        // Prevent users from updating status or admin notes
        unset($validated['status'], $validated['admin_notes'], $validated['agency_id']);

        $workVisaApplication->update($validated);

        return response()->json($workVisaApplication->load(['destinationCountry', 'jobCategory', 'jobPosting', 'agency']));
    }

    /**
     * Remove the specified resource from storage (or soft delete).
     * Consider if users should be allowed to delete applications. Maybe only if 'pending'?
     */
    public function destroy(Request $request, WorkVisaApplication $workVisaApplication)
    {
        // Basic authorization: Ensure the user owns this application
        if ($request->user()->id !== $workVisaApplication->user_id) {
            abort(403, 'Unauthorized access.');
        }

        // Add logic here - maybe only allow deletion if status is 'pending'?
        // if ($workVisaApplication->status !== 'pending') {
        //     abort(403, 'Cannot delete applications that are being processed.');
        // }

        $workVisaApplication->delete(); // Use delete() for soft deletes if enabled

        return response()->json(null, 204);
    }
}
