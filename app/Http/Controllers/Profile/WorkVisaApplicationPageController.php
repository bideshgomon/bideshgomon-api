<?php

namespace App\Http\Controllers\Profile;

use App\Http\Controllers\Controller;
use App\Models\WorkVisaApplication;
use App\Models\Country;
use App\Models\JobCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class WorkVisaApplicationPageController extends Controller
{
    /**
     * Display the user's work visa application history.
     */
    public function index()
    {
        // Data will be fetched via API (Api\WorkVisaApplicationController)
        return Inertia::render('Profile/WorkVisa/Index');
    }

    /**
     * Show the form for creating a new work visa application.
     */
    public function create()
    {
        // Pass necessary data for the form dropdowns
        return Inertia::render('Profile/WorkVisa/Create', [
            'countries' => Country::where('is_active', true)->orderBy('name')->select('id', 'name')->get(),
            'jobCategories' => JobCategory::where('is_active', true)->orderBy('name')->select('id', 'name')->get(),
            // Pass job postings if applicable/searchable
        ]);
    }

    /**
     * Display the details of a specific application belonging to the user.
     * Edit might be handled within the 'show' view or a modal.
     */
    public function show(Request $request, WorkVisaApplication $workVisaApplication)
    {
         // Basic authorization: Ensure the user owns this application
         // This check might be better handled by route model binding policies later
        if ($request->user()->id !== $workVisaApplication->user_id) {
            abort(403);
        }

        $workVisaApplication->load(['destinationCountry', 'jobCategory', 'jobPosting', 'agency']);

        return Inertia::render('Profile/WorkVisa/Show', [
            'application' => $workVisaApplication,
            // Pass countries/categories again if editing is allowed on this page
            'countries' => Country::where('is_active', true)->orderBy('name')->select('id', 'name')->get(),
            'jobCategories' => JobCategory::where('is_active', true)->orderBy('name')->select('id', 'name')->get(),
        ]);
    }
}