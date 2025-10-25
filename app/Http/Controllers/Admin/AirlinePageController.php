<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Airline;
use Illuminate\Http\Request;
use Inertia\Inertia;

class AirlinePageController extends Controller
{
    public function index()
    {
        return Inertia::render('Admin/Airlines/Index');
    }

    public function create()
    {
        return Inertia::render('Admin/Airlines/Create');
    }

    public function edit(Airline $airline)
    {
        return Inertia::render('Admin/Airlines/Edit', [
            'airline' => $airline
        ]);
    }
}