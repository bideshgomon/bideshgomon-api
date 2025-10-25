<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Airport;
use App\Models\Country; // Import Country
use Illuminate\Http\Request;
use Inertia\Inertia;

class AirportPageController extends Controller
{
    public function index()
    {
        return Inertia::render('Admin/Airports/Index');
    }

    public function create()
    {
        return Inertia::render('Admin/Airports/Create', [
            'countries' => Country::select('id', 'name')->get() // Pass countries
        ]);
    }

    public function edit(Airport $airport)
    {
        return Inertia::render('Admin/Airports/Edit', [
            'airport' => $airport,
            'countries' => Country::select('id', 'name')->get() // Pass countries
        ]);
    }
}