<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Country;
use Illuminate\Http\Request;
use Inertia\Inertia;

class CountryPageController extends Controller
{
    /**
     * Display the list of countries.
     * This single page will handle list, create, and edit.
     */
    public function index()
    {
        // Fetch paginated countries and pass them as a prop to the Vue page.
        return Inertia::render('Admin/Countries/Index', [
            'countries' => Country::latest()->paginate(10)
        ]);
    }
}