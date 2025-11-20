<?php

namespace App\Http\Controllers\Profile;

use App\Http\Controllers\Controller;
use App\Models\UserTravelHistory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

class TravelHistoryController extends Controller
{
    /**
     * Display a listing of the user's travel history.
     */
    public function index()
    {
        $travelHistory = Auth::user()->travelHistory()
            ->orderBy('entry_date', 'desc')
            ->get()
            ->map(function ($travel) {
                return [
                    'id' => $travel->id,
                    'user_passport_id' => $travel->user_passport_id,
                    'user_visa_history_id' => $travel->user_visa_history_id,
                    'country' => $travel->country_visited,
                    'city' => $travel->city_visited,
                    'purpose' => $travel->purpose,
                    'entry_date' => $travel->entry_date ? $travel->entry_date->format('Y-m-d') : null,
                    'exit_date' => $travel->exit_date ? $travel->exit_date->format('Y-m-d') : null,
                    'duration_days' => $travel->duration_days,
                    'visa_type_used' => $travel->visa_type_used ?? null,
                    'accommodation_type' => $travel->accommodation_type,
                    'accommodation_address' => $travel->accommodation_address,
                    'transportation_mode' => $travel->transportation_mode ?? null,
                    'entry_port' => $travel->entry_port,
                    'exit_port' => $travel->exit_port,
                    'sponsoring_organization' => $travel->sponsoring_organization ?? null,
                    'travel_companions' => $travel->travel_companions,
                    'notes' => $travel->notes,
                    'created_at' => $travel->created_at,
                    'updated_at' => $travel->updated_at,
                ];
            });

        return response()->json($travelHistory);
    }

    /**
     * Store a newly created travel history record in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'country' => 'required|string|max:100',
            'city' => 'nullable|string|max:100',
            'purpose' => 'required|in:tourism,business,education,family,medical,transit',
            'entry_date' => 'required|date',
            'exit_date' => 'required|date|after_or_equal:entry_date',
            'duration_days' => 'required|integer|min:1',
            'accommodation_type' => 'nullable|in:hotel,hostel,airbnb,family,company,university,other',
            'accommodation_address' => 'nullable|string|max:500',
            'transportation_mode' => 'nullable|in:air,land,sea',
            'entry_port' => 'nullable|string|max:255',
            'exit_port' => 'nullable|string|max:255',
            'visa_type_used' => 'nullable|string|max:100',
            'sponsoring_organization' => 'nullable|string|max:255',
            'travel_companions' => 'nullable|string|max:500',
            'notes' => 'nullable|string|max:1000',
        ]);

        UserTravelHistory::create([
            'user_id' => Auth::id(),
            'country_visited' => $validated['country'],
            'city_visited' => $validated['city'],
            'purpose' => $validated['purpose'],
            'entry_date' => $validated['entry_date'],
            'exit_date' => $validated['exit_date'],
            'duration_days' => $validated['duration_days'],
            'accommodation_type' => $validated['accommodation_type'],
            'accommodation_address' => $validated['accommodation_address'],
            'transportation_mode' => $validated['transportation_mode'],
            'entry_port' => $validated['entry_port'],
            'exit_port' => $validated['exit_port'],
            'visa_type_used' => $validated['visa_type_used'] ?? null,
            'sponsoring_organization' => $validated['sponsoring_organization'] ?? null,
            'travel_companions' => $validated['travel_companions'],
            'notes' => $validated['notes'],
        ]);

        return response()->json(['message' => 'Travel history record added successfully.'], 201);
    }

    /**
     * Update the specified travel history record in storage.
     */
    public function update(Request $request, $id)
    {
        $travelHistory = Auth::user()->travelHistory()->findOrFail($id);

        $validated = $request->validate([
            'country' => 'required|string|max:100',
            'city' => 'nullable|string|max:100',
            'purpose' => 'required|in:tourism,business,education,family,medical,transit',
            'entry_date' => 'required|date',
            'exit_date' => 'required|date|after_or_equal:entry_date',
            'duration_days' => 'required|integer|min:1',
            'accommodation_type' => 'nullable|in:hotel,hostel,airbnb,family,company,university,other',
            'accommodation_address' => 'nullable|string|max:500',
            'transportation_mode' => 'nullable|in:air,land,sea',
            'entry_port' => 'nullable|string|max:255',
            'exit_port' => 'nullable|string|max:255',
            'visa_type_used' => 'nullable|string|max:100',
            'sponsoring_organization' => 'nullable|string|max:255',
            'travel_companions' => 'nullable|string|max:500',
            'notes' => 'nullable|string|max:1000',
        ]);

        $travelHistory->update([
            'country_visited' => $validated['country'],
            'city_visited' => $validated['city'],
            'purpose' => $validated['purpose'],
            'entry_date' => $validated['entry_date'],
            'exit_date' => $validated['exit_date'],
            'duration_days' => $validated['duration_days'],
            'accommodation_type' => $validated['accommodation_type'],
            'accommodation_address' => $validated['accommodation_address'],
            'transportation_mode' => $validated['transportation_mode'],
            'entry_port' => $validated['entry_port'],
            'exit_port' => $validated['exit_port'],
            'visa_type_used' => $validated['visa_type_used'] ?? null,
            'sponsoring_organization' => $validated['sponsoring_organization'] ?? null,
            'travel_companions' => $validated['travel_companions'],
            'notes' => $validated['notes'],
        ]);

        return response()->json(['message' => 'Travel history record updated successfully.']);
    }

    /**
     * Remove the specified travel history record from storage.
     */
    public function destroy($id)
    {
        $travelHistory = Auth::user()->travelHistory()->findOrFail($id);
        $travelHistory->delete();

        return response()->json(['message' => 'Travel history record deleted successfully.']);
    }
}
