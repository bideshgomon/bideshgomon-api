<?php

namespace App\Http\Controllers\Api\UserProfile;

use App\Http\Controllers\Controller;
use App\Models\UserTechnicalEducation;
use Illuminate\Http\Request;

class UserTechnicalEducationController extends Controller
{
    /**
     * Display a listing of the user's technical education.
     */
    public function index(Request $request)
    {
        return $request->user()->technicalEducations()->orderBy('start_date', 'desc')->get();
    }

    /**
     * Store a newly created technical education record.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'course_name' => 'required|string|max:255',
            'institution' => 'required|string|max:255',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'is_current' => 'required|boolean',
        ]);

        // Unset end_date if currently studying
        if ($validated['is_current']) {
            $validated['end_date'] = null;
        }

        $education = $request->user()->technicalEducations()->create($validated);

        return response()->json($education, 201);
    }

    /**
     * Update the specified technical education record.
     */
    public function update(Request $request, UserTechnicalEducation $technicalEducation)
    {
        // Authorize
        if ($request->user()->id !== $technicalEducation->user_id) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $validated = $request->validate([
            'course_name' => 'required|string|max:255',
            'institution' => 'required|string|max:255',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'is_current' => 'required|boolean',
        ]);

        // Unset end_date if currently studying
        if ($validated['is_current']) {
            $validated['end_date'] = null;
        }

        $technicalEducation->update($validated);

        return response()->json($technicalEducation);
    }

    /**
     * Remove the specified technical education record.
     */
    public function destroy(Request $request, UserTechnicalEducation $technicalEducation)
    {
        // Authorize
        if ($request->user()->id !== $technicalEducation->user_id) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $technicalEducation->delete();

        return response()->json(null, 204);
    }
}