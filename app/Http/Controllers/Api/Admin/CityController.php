<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\City;
use App\Models\State; // Import State model for relationships
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class CityController extends Controller
{
    /**
     * Display a listing of the resource.
     * We can filter cities by state_id.
     */
    public function index(Request $request)
    {
        $query = City::query()->with('state'); // Eager load the state

        // Filter by state
        if ($request->has('state_id')) {
            $query->where('state_id', $request->state_id);
        }

        if ($request->has('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        return $query->orderBy('name')->paginate(10);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'state_id' => 'required|exists:states,id', // Ensure state exists
            'is_active' => 'required|boolean',
        ]);

        $city = City::create($validated);

        return response()->json($city->load('state'), 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(City $city)
    {
        return $city->load('state');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, City $city)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'state_id' => 'required|exists:states,id',
            'is_active' => 'required|boolean',
        ]);

        $city->update($validated);

        return response()->json($city->load('state'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(City $city)
    {
        $city->delete();

        return response()->json(null, 204);
    }
}