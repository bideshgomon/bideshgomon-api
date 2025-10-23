<?php

namespace App\Http\Controllers\Api\UserProfile;

use App\Http\Controllers\Controller;
use App\Models\UserWorkExperience;
use Illuminate\Http\Request;

class UserWorkExperienceController extends Controller
{
    /**
     * Display a listing of the user's work experiences.
     */
    public function index(Request $request)
    {
        // Fetch only the work experiences belonging to the authenticated user
        return $request->user()->workExperiences()->with('country')->orderBy('start_date', 'desc')->get();
    }

    /**
     * Store a new work experience for the user.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'company_name' => 'required|string|max:255',
            'designation' => 'required|string|max:255',
            'responsibilities' => 'nullable|string',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'is_current' => 'required|boolean',
            'country_id' => 'nullable|exists:countries,id',
            'city' => 'nullable|string|max:255',
        ]);

        // Securely create the record associated with the logged-in user
        $workExperience = $request->user()->workExperiences()->create($validated);

        return response()->json($workExperience->load('country'), 201);
    }

    /**
     * Update the specified work experience.
     */
    public function update(Request $request, string $id)
    {
        $validated = $request->validate([
            'company_name' => 'required|string|max:255',
            'designation' => 'required|string|max:255',
            'responsibilities' => 'nullable|string',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'is_current' => 'required|boolean',
            'country_id' => 'nullable|exists:countries,id',
            'city' => 'nullable|string|max:255',
        ]);

        // Find the record *within the user's collection*
        $workExperience = $request->user()->workExperiences()->findOrFail($id);
        
        $workExperience->update($validated);

        return response()->json($workExperience->load('country'));
    }

    /**
     * Remove the specified work experience.
     */
    public function destroy(Request $request, string $id)
    {
        // Find the record *within the user's collection*
        $workExperience = $request->user()->workExperiences()->findOrFail($id);

        $workExperience->delete();

        return response()->json(null, 204);
    }
}