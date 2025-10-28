<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\City; // <-- Import City
use App\Models\Country; // <-- Import Country (Needed indirectly for cities)
use App\Models\State; // <-- Import State (Needed indirectly for cities)
use App\Models\University;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Inertia\Response;

class UniversityController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): Response
    {
        $query = University::with(['city.state.country', 'city.country']); // Eager load location

        if ($request->filled('search')) {
            $search = $request->input('search');
             $query->where(function ($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                  ->orWhere('description', 'like', '%' . $search . '%')
                  ->orWhereHas('city', function($cityQuery) use ($search) {
                      $cityQuery->where('name', 'like', '%' . $search . '%');
                  })
                  ->orWhereHas('city.state', function($stateQuery) use ($search) {
                      $stateQuery->where('name', 'like', '%' . $search . '%');
                  })
                  ->orWhereHas('city.state.country', function($countryQuery) use ($search) {
                       $countryQuery->where('name', 'like', '%' . $search . '%');
                  })
                  ->orWhereHas('city.country', function($countryQuery) use ($search) {
                       $countryQuery->where('name', 'like', '%' . $search . '%');
                  });
            });
        }

        // Handle fetching data for the edit modal
        $universityToEdit = null;
        if ($request->has('edit_id')) {
            // Load city relationships needed for the modal dropdowns
            $universityToEdit = University::with(['city.state'])->find($request->edit_id);
        }

        $universities = $query->orderBy('name')->paginate(10)->withQueryString();

        // Get Countries for the modal dropdown
        $countries = Country::orderBy('name')->get(['id', 'name']);
        // States and Cities will be loaded dynamically in the modal

        return Inertia::render('Admin/Universities/Index', [
            'universities' => $universities,
            'countries' => $countries, // Pass countries for the modal
            'filters' => $request->only(['search']),
            'universityToEdit' => $universityToEdit, // Pass data for edit modal
        ]);
    }

    /**
     * Show the form for creating a new resource. (No longer used for page view)
     */
    public function create(): Response
    {
         return $this->index(new Request()); // Redirect to index
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:universities,name',
            'city_id' => 'required|exists:cities,id', // Ensure city exists
            'description' => 'nullable|string',
            'website_url' => 'nullable|url',
            'is_active' => 'required|boolean',
        ]);

        University::create($validated);

        return Redirect::route('admin.universities.index')->with('success', 'University created successfully.'); // Redirect to index
    }

    /**
     * Show the form for editing the specified resource. (No longer used for page view)
     */
    public function edit(University $university): Response
    {
         return $this->index(new Request()); // Redirect to index
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, University $university)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', Rule::unique('universities', 'name')->ignore($university->id)],
            'city_id' => 'required|exists:cities,id',
            'description' => 'nullable|string',
            'website_url' => 'nullable|url',
            'is_active' => 'required|boolean',
        ]);

        $university->update($validated);

        return Redirect::route('admin.universities.index')->with('success', 'University updated successfully.'); // Redirect to index
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(University $university)
    {
        // Add check if university has courses before deleting?
        // if ($university->courses()->exists()) { return Redirect::back()->... }
        $university->delete();

        return Redirect::route('admin.universities.index')->with('success', 'University deleted successfully.'); // Redirect to index
    }

    /**
     * Get cities for a given state ID (for AJAX calls from Vue modal).
     * Moved logic here from CityController for relevance or keep separate helper controller?
     * Let's add it here for now. We might need a dedicated GeoHelperController later.
     */
    public function getCitiesForState(Request $request)
    {
        $request->validate(['state_id' => 'required|exists:states,id']);
        $cities = City::where('state_id', $request->state_id)
                       ->where('is_active', true)
                       ->orderBy('name')
                       ->get(['id', 'name']);
        return response()->json($cities);
    }

     /**
     * Get cities directly linked to a country ID (for AJAX calls from Vue modal).
     */
    public function getCitiesForCountry(Request $request)
    {
        $request->validate(['country_id' => 'required|exists:countries,id']);
        // Fetch cities linked directly to the country (state_id is null)
        $cities = City::where('country_id', $request->country_id)
                       ->whereNull('state_id') // Important condition
                       ->where('is_active', true)
                       ->orderBy('name')
                       ->get(['id', 'name']);
        return response()->json($cities);
    }
}