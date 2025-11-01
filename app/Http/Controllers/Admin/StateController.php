<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Country; // <-- Import Country
use App\Models\State;   // <-- Import State
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Inertia\Response;

class StateController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): Response
    {
        $query = State::with('country'); // Eager load the country relationship

        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                // Search state name
                $q->where('name', 'like', '%'.$search.'%')
                  // Search related country name
                    ->orWhereHas('country', function ($countryQuery) use ($search) {
                        $countryQuery->where('name', 'like', '%'.$search.'%');
                    });
            });
        }

        // Handle fetching a single state for the edit modal
        $stateToEdit = null;
        if ($request->has('edit_id')) {
            $stateToEdit = State::find($request->edit_id);
        }

        $states = $query->orderBy('name')->paginate(10)->withQueryString();

        // Get all countries for the dropdown
        $countries = Country::orderBy('name')->get(['id', 'name']);

        return Inertia::render('Admin/States/Index', [
            'states' => $states,
            'countries' => $countries, // Pass countries list to the view
            'filters' => $request->only(['search']),
            'stateToEdit' => $stateToEdit, // Pass the specific state to be edited
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
                // Ensure name is unique for the given country
                Rule::unique('states')->where(function ($query) use ($request) {
                    return $query->where('country_id', $request->country_id);
                }),
            ],
            'country_id' => 'required|exists:countries,id',
            'is_active' => 'required|boolean',
        ]);

        State::create($validated);

        return Redirect::route('admin.states.index')->with('success', 'State created successfully.');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, State $state)
    {
        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
                // Ensure name is unique for the given country, ignoring the current state
                Rule::unique('states')->where(function ($query) use ($request) {
                    return $query->where('country_id', $request->country_id);
                })->ignore($state->id),
            ],
            'country_id' => 'required|exists:countries,id',
            'is_active' => 'required|boolean',
        ]);

        $state->update($validated);

        return Redirect::route('admin.states.index')->with('success', 'State updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(State $state)
    {
        $state->delete();

        return Redirect::route('admin.states.index')->with('success', 'State deleted successfully.');
    }
}
