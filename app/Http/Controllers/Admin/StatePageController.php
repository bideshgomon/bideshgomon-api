<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use App\Models\State;    // Import State
use App\Models\Country; // Import Country

class StatePageController extends Controller
{
    /**
     * Display the state management index page.
     */
    public function index(Request $request): Response
    {
        // Fetch countries for the filter dropdown
        $countries = Country::orderBy('name', 'asc')->get(['id', 'name']);

        // Build query for states, eager load country
        $query = State::query()->with('country')->orderBy('name', 'asc');

        // Filter by Country ID
        if ($request->filled('country_id')) {
            $query->where('country_id', $request->input('country_id'));
        }

        // Filter by Search Term (State Name)
        if ($request->filled('search')) {
            $searchTerm = $request->input('search');
            $query->where('name', 'LIKE', "%{$searchTerm}%");
        }

        // Paginate results
        $states = $query->paginate(25)->withQueryString();

        // Pass data to the Inertia view
        return Inertia::render('Admin/States/Index', [
             'states' => $states,
             'countries' => $countries, // For the filter dropdown
             'filters' => $request->only(['search', 'country_id']), // Current filters
        ]);
    }
}