<?php

namespace App\Http\Controllers\Api\UserProfile;

use App\Http\Controllers\Controller;
use App\Models\UserTravelHistory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class UserTravelHistoryController extends Controller
{
    /**
     * Display a listing of the resource for the authenticated user.
     */
    public function index()
    {
        $travelHistories = UserTravelHistory::where('user_id', Auth::id())
            ->orderBy('start_date', 'desc')
            ->get();
            
        return response()->json($travelHistories);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'country' => 'required|string|max:255',
            'city' => 'nullable|string|max:255',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'purpose_of_visit' => 'required|string|max:255',
        ]);

        $travelHistory = Auth::user()->travelHistories()->create($validated);

        return response()->json($travelHistory, 201);
    }

    /**
     * Display the specified resource.
     * (Not typically needed if index returns all)
     */
    public function show(UserTravelHistory $userTravelHistory)
    {
        // Authorize: Ensure the user owns this record
        if ($userTravelHistory->user_id !== Auth::id()) {
            return response()->json(['message' => 'Forbidden'], 403);
        }
        
        return response()->json($userTravelHistory);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, UserTravelHistory $userTravelHistory)
    {
        // Authorize: Ensure the user owns this record
        if ($userTravelHistory->user_id !== Auth::id()) {
            return response()->json(['message' => 'Forbidden'], 403);
        }
        
        $validated = $request->validate([
            'country' => 'required|string|max:255',
            'city' => 'nullable|string|max:255',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'purpose_of_visit' => 'required|string|max:255',
        ]);

        $userTravelHistory->update($validated);

        return response()->json($userTravelHistory);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(UserTravelHistory $userTravelHistory)
    {
        // Authorize: Ensure the user owns this record
        if ($userTravelHistory->user_id !== Auth::id()) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $userTravelHistory->delete();

        return response()->json(['message' => 'Travel record deleted successfully.'], 200);
    }
}