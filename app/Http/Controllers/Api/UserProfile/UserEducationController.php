<?php

namespace App\Http\Controllers\Api\UserProfile;

use App\Http\Controllers\Controller;
use App\Models\UserEducation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserEducationController extends Controller
{
    /**
     * Display a listing of the user's education.
     */
    public function index()
    {
        $educations = Auth::user()->educations()->orderBy('end_date', 'desc')->get();
        return response()->json($educations);
    }

    /**
     * Store a newly created education record in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'institution' => 'required|string|max:255',
            'degree' => 'required|string|max:255',
            'field_of_study' => 'nullable|string|max:255',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'description' => 'nullable|string',
            'is_current' => 'boolean',
        ]);

        $education = Auth::user()->educations()->create($validated);

        return response()->json($education, 201);
    }

    /**
     * Update the specified education record in storage.
     */
    public function update(Request $request, UserEducation $education)
    {
        // ✅ [SECURITY FIX] Check ownership
        if ($education->user_id !== Auth::id()) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $validated = $request->validate([
            'institution' => 'required|string|max:255',
            'degree' => 'required|string|max:255',
            'field_of_study' => 'nullable|string|max:255',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'description' => 'nullable|string',
            'is_current' => 'boolean',
        ]);

        $education->update($validated);

        return response()->json($education);
    }

    /**
     * Remove the specified education record from storage.
     */
    public function destroy(UserEducation $education)
    {
        // ✅ [SECURITY FIX] Check ownership
        if ($education->user_id !== Auth::id()) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $education->delete();

        return response()->json(null, 204);
    }
}