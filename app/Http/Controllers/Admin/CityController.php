<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\City;
use App\Models\Country;
use App\Models\State;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Support\Facades\DB; // If using DB::statement for complex queries if needed

class CityController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): Response
    {
        // Eager load state (with its country) OR the direct country
        $query = City::with(['state.country', 'country']);

        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                // Search city name
                $q->where('name', 'like', '%' . $search . '%')
                  // Search related state name
                  ->orWhereHas('state', function($stateQuery) use ($search) {
                      $stateQuery->where('name', 'like', '%' . $search . '%');
                  })
                  // Search related country name (via state)
                  ->orWhereHas('state.country', function($countryQuery) use ($search) {
                       $countryQuery->where('name', 'like', '%' . $search . '%');
                  })
                  // Search related country name (direct link)
                  ->orWhereHas('country', function($countryQuery) use ($search) {
                       $countryQuery->where('name', 'like', '%' . $search . '%');
                  });
            });
        }

        // Handle fetching a single city for the edit modal
        $cityToEdit = null;
        if ($request->has('edit_id')) {
            // Load both potential parents
            $cityToEdit = City::with(['state.country', 'country'])->find($request->edit_id);
        }

        $cities = $query->orderBy('name')->paginate(10)->withQueryString();

        // Get countries for dropdowns
        $countries = Country::orderBy('name')->get(['id', 'name']);

        // States are loaded dynamically via getStatesForCountry or when editing

        return Inertia::render('Admin/Cities/Index', [
            'cities' => $cities,
            'countries' => $countries,
            // 'states' prop removed - fetched dynamically
            'filters' => $request->only(['search']), // Remove country_id filter here
            'cityToEdit' => $cityToEdit,
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
                 // Unique check needs refinement: unique within state OR unique within country if no state
                 // Simple approach: Unique within the parent (state or country)
                 // Advanced: Rule::unique('cities')->where(...) checking both state_id and country_id conditions.
                 // Let's stick to a simpler unique check for now, you might refine later if needed.
                 // This basic unique check might allow duplicate city names if one is under a state and another directly under country.
                 'unique:cities,name',
            ],
            'state_id' => 'nullable|exists:states,id',
            'country_id' => 'nullable|required_without:state_id|exists:countries,id', // Must have state OR country
            'is_active' => 'required|boolean',
        ], [
            'country_id.required_without' => 'Either State or Country must be selected.' // Custom error message
        ]);

        // Ensure only state_id OR country_id is set
        if (!empty($validated['state_id'])) {
            $validated['country_id'] = null; // Prefer state link if provided
        } else {
             $validated['state_id'] = null; // Ensure state_id is null if country_id is set
        }


        City::create($validated);

        return Redirect::route('admin.cities.index')->with('success', 'City created successfully.');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, City $city)
    {
         $validated = $request->validate([
             'name' => [
                'required',
                'string',
                'max:255',
                // Rule::unique('cities')->ignore($city->id)->where(...) // Complex unique rule if needed
                Rule::unique('cities', 'name')->ignore($city->id), // Simpler unique check
            ],
            'state_id' => 'nullable|exists:states,id',
            'country_id' => 'nullable|required_without:state_id|exists:countries,id',
            'is_active' => 'required|boolean',
        ], [
            'country_id.required_without' => 'Either State or Country must be selected.'
        ]);

        // Ensure only state_id OR country_id is set
        if (!empty($validated['state_id'])) {
            $validated['country_id'] = null; // Prefer state link if provided
        } else {
             $validated['state_id'] = null; // Ensure state_id is null if country_id is set
        }

        $city->update($validated);

        return Redirect::route('admin.cities.index')->with('success', 'City updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(City $city)
    {
        // Potential check if city is linked elsewhere before deleting
        // e.g., if ($city->universities()->exists()) { return Redirect::back()->with('error', 'Cannot delete city with linked universities.'); }
        $city->delete();

        return Redirect::route('admin.cities.index')->with('success', 'City deleted successfully.');
    }

    /**
     * Get states for a given country ID (for AJAX calls from Vue).
     */
    public function getStatesForCountry(Request $request)
    {
        $request->validate(['country_id' => 'required|exists:countries,id']);
        $states = State::where('country_id', $request->country_id)
                       ->where('is_active', true) // Only fetch active states
                       ->orderBy('name')
                       ->get(['id', 'name']);
        // Return an empty array if no states found, Vue component will handle this
        return response()->json($states);
    }
}