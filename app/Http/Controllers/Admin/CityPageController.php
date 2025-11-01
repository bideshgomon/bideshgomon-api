<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\City; // 1. Import the City model
use Inertia\Inertia;

class CityPageController extends Controller
{
    /**
     * Display the list of cities.
     * This single page will handle list, create, and edit.
     */
    public function index()
    {
        // 2. Fetch paginated cities and pass them to the new Vue page
        // Eager-load the state and country relationships from your City model
        return Inertia::render('Admin/Cities/Index', [
            'cities' => City::with('state.country')->latest()->paginate(10),
        ]);
    }
}
