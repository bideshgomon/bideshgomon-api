<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\City;
use App\Models\Country;
use App\Models\State;
use Illuminate\Http\Request;
use Inertia\Inertia;

class CityPageController extends Controller
{
    /**
     * Display the list of cities.
     * This single page will handle list, create, and edit.
     */
    public function index()
    {
        return Inertia::render('Admin/Cities/Index', [
            // Pass the paginated list of cities, including their parent state and country
            'cities' => City::with('state', 'country')
                ->latest()
                ->paginate(10),
            
            // Pass all countries for the first dropdown
            'countries' => Country::orderBy('name')->get(['id', 'name']),

            // Pass all states for the second (dynamic) dropdown
            'states' => State::orderBy('name')->get(['id', 'name', 'country_id']),
        ]);
    }
}