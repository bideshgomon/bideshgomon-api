<?php

namespace App\Http\Controllers\Api\UserProfile;

use App\Http\Controllers\Controller;
use App\Models\UserExperience;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserExperienceController extends Controller
{
    /**
     * Display a listing of the user's work experiences.
     */
    public function index(Request $request)
    {
        // Fetch only the experiences belonging to the authenticated user
        return $request->user()->experiences()->orderBy('start_date', 'desc')->get();
    }

    /**
     * Store a new work experience for the user.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'company_name' => 'required|string|max:255',
            'designation' => 'required|string|max:255',
            'description' => 'nullable|string',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'is_current' => 'nullable|boolean',
        ]);

        $data = $validated;

        // If 'is_current' is true, set end_date to null
        if (!empty($data['is_current'])) {
            $data['end_date'] = null;
        }

        // Create the experience record associated with the authenticated user
        $experience = $request->user()->experiences()->create($data);

        return response()->json($experience, 201);
    }
}