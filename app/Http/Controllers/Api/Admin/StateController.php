<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\State;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class StateController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return State::with('country')->latest()->paginate(10);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => [
                'required', 'string', 'max:255',
                Rule::unique('states')->where(fn ($query) => $query->where('country_id', $request->country_id)),
            ],
            'country_id' => 'required|exists:countries,id',
        ]);

        $state = State::create($validated);

        return response()->json($state->load('country'), 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(State $state)
    {
        return response()->json($state->load('country'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, State $state)
    {
        $validated = $request->validate([
            'name' => [
                'required', 'string', 'max:255',
                Rule::unique('states')->where(fn ($query) => $query->where('country_id', $request->country_id))->ignore($state->id),
            ],
            'country_id' => 'required|exists:countries,id',
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
