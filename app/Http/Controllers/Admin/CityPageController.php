<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use App\Models\City;     // Import City
use App\Models\State;    // Import State
use App\Models\Country; // Import Country

class CityPageController extends Controller
{
    /**
     * Display the city management index page.
     */
    public function index(Request $request): Response
    {
        // Fetch countries for the top-level filter dropdown
        $countries = Country::orderBy('name', 'asc')->get(['id', 'name']);

        // Fetch states for the second filter dropdown, optionally filtered by country
        $statesQuery = State::orderBy('name', 'asc');
        if ($request->filled('country_id')) {
            $statesQuery->where('country_id', $request->input('country_id'));
        }
        $states = $statesQuery->get(['id', 'name', 'country_id']); // Include country_id for dependent dropdown logic in Vue

        // Build query for cities, eager load state and state's country
        $query = City::query()->with('state.country')->orderBy('name', 'asc');

        // Filter by State ID
        if ($request->filled('state_id')) {
            $query->where('state_id', $request->input('state_id'));
        }
        // Filter by Country ID (if state filter isn't active)
        elseif ($request->filled('country_id')) {
             $query->whereHas('state', function ($q) use ($request) {
                $q->where('country_id', $request->input('country_id'));
            });
        }

        // Filter by Search Term (City Name)
        if ($request->filled('search')) {
            $searchTerm = $request->input('search');
            $query->where('name', 'LIKE', "%{$searchTerm}%");
        }

        // Paginate results
        $cities = $query->paginate(25)->withQueryString();

        // Pass data to the Inertia view
        return Inertia::render('Admin/Cities/Index', [
             'cities' => $cities,
             'countries' => $countries, // For Country filter
             'states' => $states,       // For State filter (potentially pre-filtered)
             'filters' => $request->only(['search', 'country_id', 'state_id']), // Current filters
        ]);
    }
}