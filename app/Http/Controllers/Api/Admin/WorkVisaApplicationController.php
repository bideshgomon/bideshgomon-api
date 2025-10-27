<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\WorkVisaApplication;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule; // For status validation

class WorkVisaApplicationController extends Controller
{
    /**
     * Display a listing of the resource (for Admin).
     * Includes filtering capabilities.
     */
    public function index(Request $request)
    {
        // Start query builder
        $query = WorkVisaApplication::with(['user:id,name,email', 'destinationCountry:id,name', 'agency:id,name']) // Eager load necessary relations, select specific columns for efficiency
            ->latest(); // Order by newest first

        // --- Filtering Logic ---
        // Search term (applicant name/email, reference)
        if ($request->filled('search')) {
            $searchTerm = $request->input('search');
            $query->where(function ($q) use ($searchTerm) {
                $q->whereHas('user', function ($uq) use ($searchTerm) {
                    $uq->where('name', 'like', "%{$searchTerm}%")
                       ->orWhere('email', 'like', "%{$searchTerm}%");
                })
                ->orWhere('application_reference', 'like', "%{$searchTerm}%");
            });
        }

        // Filter by User ID
        if ($request->filled('user_id')) {
            $query->where('user_id', $request->input('user_id'));
        }

        // Filter by Destination Country ID
        if ($request->filled('destination_country_id')) {
            $query->where('destination_country_id', $request->input('destination_country_id'));
        }

        // Filter by Agency ID (including 'null' for unassigned)
        if ($request->filled('agency_id')) {
            if ($request->input('agency_id') === 'null') {
                $query->whereNull('agency_id');
            } else {
                $query->where('agency_id', $request->input('agency_id'));
            }
        }

        // Filter by Status
        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        // --- End Filtering ---

        $applications = $query->paginate(15)->withQueryString(); // Append query string to pagination links

        return response()->json($applications);
    }

    /**
     * Store a newly created resource in storage.
     * (Admins typically don't create applications FOR users, so this might be unused or repurposed)
     */
    public function store(Request $request)
    {
        // If needed, implement creation logic specific to admin actions.
        // Usually, users initiate applications.
        abort(501, 'Not Implemented'); // Or return a 403 Forbidden
    }

    /**
     * Display the specified resource (Admin view).
     */
    public function show(WorkVisaApplication $workVisaApplication)
    {
        // Admin can view any application, load all relevant details
        return response()->json(
            $workVisaApplication->load([
                'user:id,name,email',
                'destinationCountry:id,name',
                'jobCategory:id,name',
                'jobPosting:id,title',
                'agency:id,name'
                // Add documents relation later if needed
            ])
        );
    }

    /**
     * Update the specified resource in storage (Admin actions).
     */
    public function update(Request $request, WorkVisaApplication $workVisaApplication)
    {
        // Define possible statuses
        $possibleStatuses = ['pending', 'processing', 'approved', 'rejected', 'document_request'];

        $validated = $request->validate([
            'status' => ['sometimes', 'required', Rule::in($possibleStatuses)],
            'agency_id' => 'sometimes|nullable|exists:agencies,id',
            'admin_notes' => 'sometimes|nullable|string|max:10000',
            // Add validation for other fields admin might update, e.g., 'application_reference'
        ]);

        // Prevent accidental update of user notes or other fields via this endpoint
        unset($validated['user_notes'], $validated['user_id'], $validated['destination_country_id']);

        $workVisaApplication->update($validated);

        // Consider dispatching notifications on status change here

        return response()->json(
            $workVisaApplication->load([
                'user:id,name,email',
                'destinationCountry:id,name',
                'agency:id,name'
            ])
        );
    }

    /**
     * Remove the specified resource from storage (Admin action).
     */
    public function destroy(WorkVisaApplication $workVisaApplication)
    {
        // Admin can delete applications (consider soft deletes)
        $workVisaApplication->delete(); // Use delete() for soft deletes

        return response()->json(null, 204);
    }
}