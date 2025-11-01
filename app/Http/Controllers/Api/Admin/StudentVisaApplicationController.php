<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\StudentVisaApplication;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class StudentVisaApplicationController extends Controller
{
    /**
     * Display a listing of the resource (Admin view with filtering).
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        $query = StudentVisaApplication::with([
            'user:id,name,email', // Select specific columns for efficiency
            'destinationCountry:id,name',
            'university:id,name',
            'course:id,name',
            'agency:id,name',
        ])->latest(); // Order by newest first

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

        // Filter by University ID
        if ($request->filled('university_id')) {
            $query->where('university_id', $request->input('university_id'));
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

        // Paginate results and include query string parameters in pagination links
        $applications = $query->paginate(15)->withQueryString();

        return response()->json($applications);
    }

    /**
     * Store a newly created resource in storage.
     * (Admins typically don't create applications FOR users, this is likely unused).
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        // If needed, implement creation logic specific to admin actions.
        // Usually, users initiate applications.
        abort(501, 'Not Implemented'); // Or return a 403 Forbidden
    }

    /**
     * Display the specified resource (Admin view).
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(StudentVisaApplication $studentVisaApplication)
    {
        // Admin can view any application, load all relevant details
        return response()->json(
            $studentVisaApplication->load([
                'user:id,name,email',
                'destinationCountry:id,name',
                'university:id,name',
                'course:id,name', // Load course details
                'agency:id,name',
                // Add documents relation later if needed
            ])
        );
    }

    /**
     * Update the specified resource in storage (Admin actions).
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, StudentVisaApplication $studentVisaApplication)
    {
        // Define possible statuses (adjust as needed)
        $possibleStatuses = ['pending', 'documents_required', 'submitted_to_uni', 'offer_received', 'visa_processing', 'visa_approved', 'rejected'];

        $validated = $request->validate([
            'status' => ['sometimes', 'required', Rule::in($possibleStatuses)],
            'agency_id' => 'sometimes|nullable|exists:agencies,id',
            'admin_notes' => 'sometimes|nullable|string|max:10000',
            // Add validation for other fields admin might update, e.g., 'application_reference'
        ]);

        // Prevent accidental update of user notes or other user-managed fields via this admin endpoint
        unset($validated['user_notes'], $validated['user_id'], $validated['destination_country_id']/* ... other user fields ... */);

        $studentVisaApplication->update($validated);

        // Consider dispatching notifications to the user on status change here
        // Example: if ($request->has('status') && $studentVisaApplication->wasChanged('status')) {
        //     $studentVisaApplication->user->notify(new ApplicationStatusUpdated($studentVisaApplication));
        // }

        return response()->json(
            $studentVisaApplication->load([ // Return updated data with relevant relations
                'user:id,name,email',
                'destinationCountry:id,name',
                'university:id,name',
                'course:id,name',
                'agency:id,name',
            ])
        );
    }

    /**
     * Remove the specified resource from storage (Admin action).
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(StudentVisaApplication $studentVisaApplication)
    {
        // Admin can delete applications (respects soft deletes if enabled on the model)
        $studentVisaApplication->delete();

        return response()->json(null, 204); // No content on successful delete
    }
}
