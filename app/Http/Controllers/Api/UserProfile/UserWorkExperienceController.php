<?php

namespace App\Http\Controllers\Api\UserProfile;

use App\Http\Controllers\Controller;
use App\Models\UserWorkExperience;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserWorkExperienceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $experiences = Auth::user()->experiences()->orderBy('end_date', 'desc')->get();
        return response()->json($experiences);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'company_name' => 'required|string|max:255',
            'position' => 'required|string|max:255',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'description' => 'nullable|string',
            'is_current' => 'boolean',
        ]);

        $experience = Auth::user()->experiences()->create($validated);

        return response()->json($experience, 201);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, UserWorkExperience $workExperience)
    {
        // ✅ [SECURITY FIX] Check ownership
        if ($workExperience->user_id !== Auth::id()) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $validated = $request->validate([
            'company_name' => 'required|string|max:255',
            'position' => 'required|string|max:255',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'description' => 'nullable|string',
            'is_current' => 'boolean',
        ]);

        $workExperience->update($validated);

        return response()->json($workExperience);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(UserWorkExperience $workExperience)
    {
        // ✅ [SECURITY FIX] Check ownership
        if ($workExperience->user_id !== Auth::id()) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $workExperience->delete();

        return response()->json(null, 204);
    }
}