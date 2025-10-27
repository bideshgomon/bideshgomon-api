<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\WorkVisaApplication;
use App\Models\User;
use App\Models\Country;
use App\Models\Agency;
use Illuminate\Http\Request;
use Inertia\Inertia;

class WorkVisaApplicationPageController extends Controller
{
    /**
     * Display the admin index page for work visa applications.
     */
    public function index()
    {
        // Data will be fetched via API, but pass initial filter options
        return Inertia::render('Admin/WorkVisaApplications/Index', [
            'users' => User::orderBy('name')->select('id', 'name')->get(), // For filtering by user
            'countries' => Country::orderBy('name')->select('id', 'name')->get(), // For filtering by country
            'agencies' => Agency::orderBy('name')->select('id', 'name')->get(), // For filtering/assignment
            'statuses' => ['pending', 'processing', 'approved', 'rejected', 'document_request'], // Example statuses
        ]);
    }

    /**
     * Display the details/edit page for a specific application.
     * Admin might not 'create' applications directly, but rather manage existing ones.
     */
    public function show(WorkVisaApplication $workVisaApplication) // Using show instead of edit for viewing/managing
    {
        // Load necessary data for the admin view
        $workVisaApplication->load(['user', 'destinationCountry', 'jobCategory', 'jobPosting', 'agency']);

        return Inertia::render('Admin/WorkVisaApplications/Show', [
            'application' => $workVisaApplication,
            'users' => User::orderBy('name')->select('id', 'name')->get(),
            'countries' => Country::orderBy('name')->select('id', 'name')->get(),
            'agencies' => Agency::orderBy('name')->select('id', 'name')->get(),
            'statuses' => ['pending', 'processing', 'approved', 'rejected', 'document_request'], // Example statuses
        ]);
    }

    // Admins might not typically 'create' applications for users.
    // public function create() { ... }

    // Edit might be combined with 'show' for admins, or handle specific bulk actions.
    // public function edit(WorkVisaApplication $workVisaApplication) { ... }
}