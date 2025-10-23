<?php

namespace App\Http\Controllers\Api\UserProfile;

use App\Http\Controllers\Controller;
use App\Models\UserEducation;
use Illuminate\Http\Request;

class UserEducationController extends Controller
{
    /**
     * Display a listing of the user's education.
     */
    public function index(Request $request)
    {
        // $request->user() is the authenticated user.
        // We fetch only the education history that belongs to them.
        return $request->user()->educations()->with('degree', 'university')->orderBy('start_date', 'desc')->get();
    }

    /**
     * Store a new education record for the user.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'degree_id' => 'nullable|exists:degrees,id',
            'university_id' => 'nullable|exists:universities,id',
            'custom_degree' => 'nullable|string|max:255',
            'custom_university' => 'nullable|string|max:255',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'is_current' => 'required|boolean',
            'result' => 'nullable|string|max:255',
        ]);

        // Securely create the record associated with the logged-in user
        $education = $request->user()->educations()->create($validated);

        return response()->json($education->load('degree', 'university'), 201);
    }

    /**
     * Update the specified education record.
     */
    public function update(Request $request, string $id)
    {
        $validated = $request->validate([
            'degree_id' => 'nullable|exists:degrees,id',
            'university_id' => 'nullable|exists:universities,id',
            'custom_degree' => 'nullable|string|max:255',
            'custom_university' => 'nullable|string|max:255',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'is_current' => 'required|boolean',
            'result' => 'nullable|string|max:255',
        ]);

        // Find the record *within the user's collection*
        $education = $request->user()->educations()->findOrFail($id);
        
        $education->update($validated);

        return response()->json($education->load('degree', 'university'));
    }

    /**
     * Remove the specified education record.
     */
    public function destroy(Request $request, string $id)
    {
        // Find the record *within the user's collection*
        $education = $request->user()->educations()->findOrFail($id);

        $education->delete();

        return response()->json(null, 204);
    }
}