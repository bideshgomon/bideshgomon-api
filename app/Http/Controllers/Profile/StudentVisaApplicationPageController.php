<?php

namespace App\Http\Controllers\Profile;

use App\Http\Controllers\Controller;
use App\Models\Country;
use App\Models\Course;
use App\Models\StudentVisaApplication;
use App\Models\University;
use Illuminate\Http\Request;
use Inertia\Inertia;

class StudentVisaApplicationPageController extends Controller
{
    /**
     * Display the user's student visa application history.
     */
    public function index()
    {
        // Data fetched via Api/StudentVisaApplicationController
        return Inertia::render('Profile/StudentVisa/Index');
    }

    /**
     * Show the form for creating a new student visa application.
     */
    public function create()
    {
        // Pass data for form dropdowns
        return Inertia::render('Profile/StudentVisa/Create', [
            'countries' => Country::where('is_active', true)->orderBy('name')->select('id', 'name')->get(),
            // Potentially load universities/courses based on country selection later via API
            'universities' => University::where('is_active', true)->orderBy('name')->select('id', 'name')->get(), // Example: Load all initially
            'courses' => Course::orderBy('name')->select('id', 'name')->get(), // Example: Load all initially - refine this!
        ]);
    }

    /**
     * Display the details of a specific application belonging to the user.
     */
    public function show(Request $request, StudentVisaApplication $studentVisaApplication)
    {
        // Basic authorization
        if ($request->user()->id !== $studentVisaApplication->user_id) {
            abort(403);
        }

        $studentVisaApplication->load(['destinationCountry', 'university', 'course', 'agency']);

        return Inertia::render('Profile/StudentVisa/Show', [
            'application' => $studentVisaApplication,
            // Pass data needed for viewing/potentially editing notes etc.
            'countries' => Country::where('is_active', true)->orderBy('name')->select('id', 'name')->get(),
            'universities' => University::where('is_active', true)->orderBy('name')->select('id', 'name')->get(),
            'courses' => Course::orderBy('name')->select('id', 'name')->get(),
        ]);
    }
}
