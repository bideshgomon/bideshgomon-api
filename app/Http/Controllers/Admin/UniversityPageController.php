<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\University;
use App\Models\Country;
use Inertia\Inertia;
use Inertia\Response;

class UniversityPageController extends Controller
{
    /**
     * Display the list of universities.
     */
    public function index(): Response
    {
        $universities = University::with('country')
            ->latest()
            ->paginate(10);

        return Inertia::render('Admin/Universities/Index', [
            'universities' => $universities,
        ]);
    }

    /**
     * Show the form for creating a new university.
     */
    public function create(): Response
    {
        $countries = Country::orderBy('name')->get(['id', 'name']);

        return Inertia::render('Admin/Universities/Create', [
            'countries' => $countries,
        ]);
    }

    /**
     * Show the form for editing the specified university.
     */
    public function edit(University $university): Response
    {
        $countries = Country::orderBy('name')->get(['id', 'name']);

        // Eager load related country for edit form
        $university->load('country');

        return Inertia::render('Admin/Universities/Edit', [
            'university' => $university,
            'countries' => $countries,
        ]);
    }
}
