<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use App\Models\Country; // Import Country model

class CountryPageController extends Controller
{
    /**
     * Display the country management index page.
     */
    public function index(Request $request): Response
    {
        // Start building the query to fetch countries
        $query = Country::query()->orderBy('name', 'asc'); // Order by name

        // Add filtering
        if ($request->filled('search')) {
            $searchTerm = $request->input('search');
            $query->where(function ($q) use ($searchTerm) {
                $q->where('name', 'LIKE', "%{$searchTerm}%")
                  ->orWhere('iso2', 'LIKE', "%{$searchTerm}%")
                  ->orWhere('iso3', 'LIKE', "%{$searchTerm}%")
                  ->orWhere('phone_code', 'LIKE', "%{$searchTerm}%");
            });
        }

        // Paginate the results
        $countries = $query->paginate(25)->withQueryString(); // 25 per page

        // Pass countries and filters to the Inertia view
        return Inertia::render('Admin/Countries/Index', [
             'countries' => $countries, // <-- Pass the paginated countries
             'filters' => $request->only(['search']), // Pass current filters back
        ]);
    }
}