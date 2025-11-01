<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\StudentVisaApplication;
use Illuminate\Http\Request;

class StudentVisaApplicationController extends Controller
{
    /**
     * Display a listing of the resource for the authenticated user.
     */
    public function index(Request $request)
    {
        $applications = $request->user()->studentVisaApplications() // Assumes HasMany relationship named 'studentVisaApplications' on User model
            ->with(['destinationCountry', 'university', 'course', 'agency']) // Eager load relations
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
            'university_id' => 'nullable|exists:universities,id',
            'course_id' => 'nullable|exists:courses,id',
            // 'agency_id' => 'nullable|exists:agencies,id', // Usually assigned by admin
            'intended_intake_month' => 'nullable|string|max:50',
            'intended_intake_year' => 'nullable|digits:4|integer|min:1900|max:'.(date('Y') + 5), // Example year range
            'current_education_level' => 'nullable|string|max:255',
            'english_proficiency_test' => 'nullable|string|max:50',
            'english_proficiency_score' => 'nullable|string|max:50',
            'user_notes' => 'nullable|string|max:5000',
        ]);

        $validated['user_id'] = $request->user()->id;
        $validated['status'] = 'pending'; // Initial status

        $application = StudentVisaApplication::create($validated);

        return response()->json($application->load(['destinationCountry', 'university', 'course']), 201);
    }

    /**
     * Display the specified resource if it belongs to the authenticated user.
     */
    public function show(Request $request, StudentVisaApplication $studentVisaApplication)
    {
        // Basic authorization
        if ($request->user()->id !== $studentVisaApplication->user_id) {
            abort(403);
        }

        return response()->json(
            $studentVisaApplication->load(['destinationCountry', 'university', 'course', 'agency'])
        );
    }

    /**
     * Update the specified resource in storage (limited fields for users).
     */
    public function update(Request $request, StudentVisaApplication $studentVisaApplication)
    {
        // Basic authorization
        if ($request->user()->id !== $studentVisaApplication->user_id) {
            abort(403);
        }

        // Define fields users might update (e.g., notes, maybe selected course/uni if pending)
        $validated = $request->validate([
            // Allow changing uni/course only if status is 'pending'? Add logic if needed.
            // 'university_id' => 'sometimes|nullable|exists:universities,id',
            // 'course_id' => 'sometimes|nullable|exists:courses,id',
            'intended_intake_month' => 'sometimes|nullable|string|max:50',
            'intended_intake_year' => 'sometimes|nullable|digits:4|integer|min:1900|max:'.(date('Y') + 5),
            'current_education_level' => 'sometimes|nullable|string|max:255',
            'english_proficiency_test' => 'sometimes|nullable|string|max:50',
            'english_proficiency_score' => 'sometimes|nullable|string|max:50',
            'user_notes' => 'sometimes|nullable|string|max:5000',
        ]);

        // Prevent users from updating status, admin notes, agency, etc.
        unset(
            $validated['status'],
            $validated['admin_notes'],
            $validated['agency_id'],
            $validated['user_id'],
            $validated['destination_country_id'] // Should destination change? Probably not.
        );

        $studentVisaApplication->update($validated);

        return response()->json(
            $studentVisaApplication->load(['destinationCountry', 'university', 'course', 'agency'])
        );
    }

    /**
     * Remove the specified resource from storage (or soft delete).
     */
    public function destroy(Request $request, StudentVisaApplication $studentVisaApplication)
    {
        // Basic authorization
        if ($request->user()->id !== $studentVisaApplication->user_id) {
            abort(403);
        }

        // Add logic - maybe only allow deletion if status is 'pending'?
        // if ($studentVisaApplication->status !== 'pending') { ... }

        $studentVisaApplication->delete(); // Uses soft delete if enabled

        return response()->json(null, 204);
    }

    // Add a helper method in User model:
    // public function studentVisaApplications(): HasMany
    // {
    //     return $this->hasMany(StudentVisaApplication::class);
    // }
}
