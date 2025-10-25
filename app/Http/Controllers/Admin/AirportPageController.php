<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Airport;
use App\Models\Country; // For fetching cities grouped by country/state
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Validation\Rule;

class AirportPageController extends Controller
{
    /**
     * Display a listing of airports.
     */
    public function index(Request $request)
    {
        $query = Airport::with('city.state.country'); // Eager load relations

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where('name', 'like', "%{$search}%")
                  ->orWhere('iata_code', 'like', "%{$search}%")
                  // Search by related city or country name
                  ->orWhereHas('city.state.country', fn($q) => $q->where('name', 'like', "%{$search}%"))
                  ->orWhereHas('city', fn($q) => $q->where('name', 'like', "%{$search}%"));
        }

        $airports = $query->orderBy('name')->paginate(10)->withQueryString();

        return Inertia::render('Admin/Airports/Index', [
            'airports' => $airports,
            'filters' => $request->only(['search']),
        ]);
    }

    /**
     * Show the form for creating a new airport.
     */
    public function create()
    {
        // Get Cities grouped by Country and State for the dropdown
        $countries = Country::with(['states.cities' => fn($q) => $q->select('id', 'name', 'state_id')->orderBy('name')])
                            ->select('id', 'name') // Select only necessary country columns
                            ->whereHas('states.cities') // Only include countries that have cities
                            ->orderBy('name')
                            ->get();

        return Inertia::render('Admin/Airports/Create', [
            'countries' => $countries, // Pass the nested structure
        ]);
    }

    /**
     * Store a newly created airport.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'iata_code' => 'required|string|size:3|unique:airports,iata_code', // Match migration
            'city_id' => 'required|exists:cities,id',
        ]);

        Airport::create($validated);

        return redirect()->route('admin.airports.index')->with('success', 'Airport created.');
    }

    /**
     * Show the form for editing the specified airport.
     */
    public function edit(Airport $airport)
    {
         $countries = Country::with(['states.cities' => fn($q) => $q->select('id', 'name', 'state_id')->orderBy('name')])
                            ->select('id', 'name')
                            ->whereHas('states.cities')
                            ->orderBy('name')
                            ->get();

        return Inertia::render('Admin/Airports/Edit', [
            'airport' => $airport, // Loads existing data
            'countries' => $countries, // Pass the nested structure for the dropdown
        ]);
    }

    /**
     * Update the specified airport.
     */
    public function update(Request $request, Airport $airport)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'iata_code' => ['required', 'string', 'size:3', Rule::unique('airports','iata_code')->ignore($airport->id)],
            'city_id' => ['required', 'exists:cities,id'],
        ]);

        $airport->update($validated);

        return redirect()->route('admin.airports.index')->with('success', 'Airport updated.');
    }

    /**
     * Remove the specified airport.
     */
    public function destroy(Airport $airport)
    {
        $airport->delete();

        return redirect()->route('admin.airports.index')->with('success', 'Airport deleted.');
    }
}