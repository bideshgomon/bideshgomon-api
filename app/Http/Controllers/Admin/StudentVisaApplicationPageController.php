<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\StudentVisaApplication;
use App\Models\User;
use App\Models\Country;
use App\Models\Agency;
use App\Models\University;
use Illuminate\Http\Request;
use Inertia\Inertia;

class StudentVisaApplicationPageController extends Controller
{
    /**
     * Display the admin index page for student visa applications.
     */
    public function index()
    {
        // Pass data for filters
        return Inertia::render('Admin/StudentVisaApplications/Index', [
            'users' => User::orderBy('name')->select('id', 'name')->get(),
            'countries' => Country::orderBy('name')->select('id', 'name')->get(),
            'universities' => University::orderBy('name')->select('id', 'name')->get(),
            'agencies' => Agency::orderBy('name')->select('id', 'name')->get(),
             // Define relevant statuses
            'statuses' => ['pending', 'documents_required', 'submitted_to_uni', 'offer_received', 'visa_processing', 'visa_approved', 'rejected'],
        ]);
        // Actual data fetched via Admin API endpoint
    }

    /**
     * Display the details/edit page for a specific application.
     */
    public function show(StudentVisaApplication $studentVisaApplication)
    {
        $studentVisaApplication->load(['user', 'destinationCountry', 'university', 'course', 'agency']);

        return Inertia::render('Admin/StudentVisaApplications/Show', [
            'application' => $studentVisaApplication,
            'agencies' => Agency::orderBy('name')->select('id', 'name')->get(),
             // Define relevant statuses
            'statuses' => ['pending', 'documents_required', 'submitted_to_uni', 'offer_received', 'visa_processing', 'visa_approved', 'rejected'],
            // Pass other lists if needed (e.g., universities for reassignment?)
        ]);
    }
}