<?php

namespace App\Http\Controllers\Public; // Changed namespace to Public

use App\Http\Controllers\Controller;
use App\Models\University;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class UniversityController extends Controller
{
    /**
     * Display a listing of the public universities.
     */
    public function index(Request $request): Response
    {
        $query = University::query()
            ->where('is_active', true) // Only show active universities
            // Eager load relationships needed for display
            ->with(['city.state.country', 'city.country']);

        // --- Basic Search ---
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                  ->orWhere('description', 'like', '%' . $search . '%')
                  // Search by City name
                  ->orWhereHas('city', function($cityQuery) use ($search) {
                      $cityQuery->where('name', 'like', '%' . $search . '%');
                  })
                   // Search by State name
                  ->orWhereHas('city.state', function($stateQuery) use ($search) {
                      $stateQuery->where('name', 'like', '%' . $search . '%');
                  })
                   // Search by Country name (via state OR direct city link)
                  ->orWhereHas('city.state.country', function($countryQuery) use ($search) {
                       $countryQuery->where('name', 'like', '%' . $search . '%');
                  })
                  ->orWhereHas('city.country', function($countryQuery) use ($search) {
                       $countryQuery->where('name', 'like', '%' . $search . '%');
                  });
            });
        }

        // --- TODO: Add Filters (e.g., by country, city) ---
        // if ($request->filled('country_id')) { ... }

        $universities = $query->orderBy('name')->paginate(12)->withQueryString(); // 12 per page for a grid

        return Inertia::render('Public/Universities/Index', [ // Adjusted view path
            'universities' => $universities,
            'filters' => $request->only(['search']), // Add other filters here later
        ]);
    }

    /**
     * Display the specified university (Details Page - Placeholder for now).
     */
    public function show(University $university): Response
    {
        // You would load more details here for the single university page later
         $university->load(['city.state.country', 'city.country', 'courses']); // Load courses too

        // For now, redirect back or render a placeholder
        // return Redirect::route('universities.index'); // Or create a Detail view

         return Inertia::render('Public/Universities/Show', [ // Adjusted view path
             'university' => $university,
         ]);
    }
}