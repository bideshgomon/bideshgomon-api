<?php

namespace App\Http\Controllers\Api\UserProfile;

use App\Http\Controllers\Controller;
use App\Models\UserWorkExperience;
use Illuminate\Http\Request;
// [RECOMMENDATION] No need for Auth facade if using $request->user()

class UserWorkExperienceController extends Controller
{
    /**
     * Display a listing of the user's work experiences.
     */
    public function index(Request $request) // <-- Added Request
    {
        // --- [PATCH START] Use correct relationship name per Bug 3 ---
        return $request->user()->workExperiences()->with('country')->orderBy('start_date', 'desc')->get();
        // --- [PATCH END] ---
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

        // --- [PATCH START] Use correct relationship name per Bug 3 ---
        $workExperience = $request->user()->workExperiences()->create($validated);
        // --- [PATCH END] ---

        return response()->json($workExperience->load('country'), 201);
    }

    /**
     * Update the specified work experience.
     */
    // --- [PATCH START] ---
    public function update(Request $request, UserWorkExperience $workExperience) // Use Route-Model Binding
    {
        // 1. Authorize that the user owns this record
        if ($workExperience->user_id !== $request->user()->id) {
             abort(403, 'This action is unauthorized.');
        }
        // --- [PATCH END] ---

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
        
        $workExperience->update($validated);

        return response()->json($workExperience->load('country'));
    }

    /**
     * Remove the specified work experience.
     */
    // --- [PATCH START] ---
    public function destroy(Request $request, UserWorkExperience $workExperience) // Use Route-Model Binding
    {
        // 1. Authorize that the user owns this record
        if ($workExperience->user_id !== $request->user()->id) {
             abort(403, 'This action is unauthorized.');
        }
        // --- [PATCH END] ---

        $workExperience->delete();

        return response()->json(null, 204);
    }
}