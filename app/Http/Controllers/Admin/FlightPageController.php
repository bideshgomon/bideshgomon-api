<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Airline; // Import Airline
use App\Models\Airport; // Import Airport
use App\Models\Flight;
use Inertia\Inertia;

class FlightPageController extends Controller
{
    public function index()
    {
        return Inertia::render('Admin/Flights/Index');
    }

    public function create()
    {
        return Inertia::render('Admin/Flights/Create', [
            'airlines' => Airline::select('id', 'name')->get(),
            'airports' => Airport::select('id', 'name', 'code')->get(),
        ]);
    }

    public function edit(Flight $flight)
    {
        return Inertia::render('Admin/Flights/Edit', [
            'flight' => $flight,
            'airlines' => Airline::select('id', 'name')->get(),
            'airports' => Airport::select('id', 'name', 'code')->get(),
        ]);
    }
}
