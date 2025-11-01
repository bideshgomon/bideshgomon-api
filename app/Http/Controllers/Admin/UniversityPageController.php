<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\City;
use App\Models\Country;
use App\Models\State;
use App\Models\University;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request; // <-- ADD THIS LINE
use Inertia\Inertia;
use Inertia\Response;

class UniversityPageController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): Response
    {
        $query = University::with('country', 'state', 'city');

        // Search functionality
        if ($request->has('search')) {
            $search = $request->input('search');
            $query->where('name', 'like', "%{$search}%")
                ->orWhereHas('country', function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%");
                })
                ->orWhereHas('state', function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%");
                })
                ->orWhereHas('city', function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%");
                });
        }

        $universities = $query->orderBy('created_at', 'desc')->paginate(10)->withQueryString();

        return Inertia::render('Admin/Universities/Index', [
            'universities' => $universities,
            'filters' => $request->only(['search']),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): Response
    {
        return Inertia::render('Admin/Universities/Create', [
            'countries' => Country::orderBy('name')->get(),
            'states' => State::orderBy('name')->get(),
            'cities' => City::orderBy('name')->get(),
        ]);
    }

    /**
     * Store a new resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'country_id' => 'required|exists:countries,id',
            'state_id' => 'nullable|exists:states,id',
            'city_id' => 'nullable|exists:cities,id',
            'website_url' => 'nullable|url',
            'description' => 'nullable|string',
            'ranking' => 'nullable|integer',
            'logo_url' => 'nullable|string', // Consider changing to file upload later
            'intake_months' => 'nullable|string', // E.g., "Jan, May, Sep"
            'application_deadline' => 'nullable|string',
        ]);

        University::create($validated);

        return redirect()->route('admin.universities.index')
            ->with('success', 'University created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(University $university)
    {
        // Optional: Implement if you need a "View University" page
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(University $university): Response
    {
        return Inertia::render('Admin/Universities/Edit', [
            'university' => $university,
            'countries' => Country::orderBy('name')->get(),
            'states' => State::orderBy('name')->get(),
            'cities' => City::orderBy('name')->get(),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, University $university): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'country_id' => 'required|exists:countries,id',
            'state_id' => 'nullable|exists:states,id',
            'city_id' => 'nullable|exists:cities,id',
            'website_url' => 'nullable|url',
            'description' => 'nullable|string',
            'ranking' => 'nullable|integer',
            'logo_url' => 'nullable|string',
            'intake_months' => 'nullable|string',
            'application_deadline' => 'nullable|string',
        ]);

        $university->update($validated);

        return redirect()->route('admin.universities.index')
            ->with('success', 'University updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     * --- THIS IS THE NEW METHOD ---
     */
    public function destroy(University $university): RedirectResponse
    {
        $university->delete();

        return redirect()->route('admin.universities.index')
            ->with('success', 'University deleted successfully.');
    }
}
