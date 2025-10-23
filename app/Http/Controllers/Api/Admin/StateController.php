<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\State;
use App\Models\Country; // Import Country model
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class StateController extends Controller
{
    /**
     * Display a listing of the resource.
     * We can filter states by country_id.
     */
    public function index(Request $request)
    {
        $query = State::query()->with('country'); // Eager load the country

        // Filter by country
        if ($request->has('country_id')) {
            $query->where('country_id', $request->country_id);
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
            'country_id' => 'required|exists:countries,id', // Ensure country exists
            'is_active' => 'required|boolean',
        ]);

        $state = State::create($validated);

        return response()->json($state->load('country'), 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(State $state)
    {
        return $state->load('country');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, State $state)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'country_id' => 'required|exists:countries,id',
            'is_active' => 'required|boolean',
        ]);

        $state->update($validated);

        return response()->json($state->load('country'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(State $state)
    {
        $state->delete();

        return response()->json(null, 204);
    }
}