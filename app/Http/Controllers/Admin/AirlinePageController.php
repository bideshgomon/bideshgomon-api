<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Airline;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Validation\Rule;

class AirlinePageController extends Controller
{
    /**
     * Display a listing of the airlines.
     */
    public function index(Request $request)
    {
        $query = Airline::query();

        // Handle search
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where('name', 'like', "%{$search}%")
                  ->orWhere('iata_code', 'like', "%{$search}%");
        }

        // Paginate results
        $airlines = $query->orderBy('name')->paginate(10)->withQueryString();

        return Inertia::render('Admin/Airlines/Index', [
            'airlines' => $airlines,
            'filters' => $request->only(['search']), // Pass filters back to the view
        ]);
    }

    /**
     * Show the form for creating a new airline.
     */
    public function create()
    {
        return Inertia::render('Admin/Airlines/Create');
    }

    /**
     * Store a newly created airline in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:airlines',
            'iata_code' => 'required|string|size:2|unique:airlines',
        ]);

        Airline::create($validated);

        return redirect()->route('admin.airlines.index')->with('success', 'Airline created successfully.');
    }

    /**
     * Show the form for editing the specified airline.
     */
    public function edit(Airline $airline)
    {
        return Inertia::render('Admin/Airlines/Edit', [
            'airline' => $airline
        ]);
    }

    /**
     * Update the specified airline in storage.
     */
    public function update(Request $request, Airline $airline)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', Rule::unique('airlines')->ignore($airline->id)],
            'iata_code' => ['required', 'string', 'size:2', Rule::unique('airlines')->ignore($airline->id)],
        ]);

        $airline->update($validated);

        return redirect()->route('admin.airlines.index')->with('success', 'Airline updated successfully.');
    }

    /**
     * Remove the specified airline from storage.
     */
    public function destroy(Airline $airline)
    {
        $airline->delete();

        return redirect()->route('admin.airlines.index')->with('success', 'Airline deleted successfully.');
    }
}