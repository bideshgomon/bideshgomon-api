<?php
// app/Http/Controllers/Admin/JobPostingPageController.php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\JobPosting;
use App\Models\JobCategory; // <-- Import
use App\Models\Country; // <-- Import
use App\Services\Admin\JobPostingService; // <-- Import the service
use Illuminate\Http\Request; // <-- Import
use Illuminate\Support\Facades\Redirect; // <-- Import
use Inertia\Inertia;

class JobPostingPageController extends Controller
{
    protected $jobPostingService;

    // Inject the service
    public function __construct(JobPostingService $jobPostingService)
    {
        $this->jobPostingService = $jobPostingService;
    }

    public function index()
    {
        return Inertia::render('Admin/JobPostings/Index', [
            'jobPostings' => JobPosting::with('jobCategory', 'country')->paginate(10),
        ]);
    }

    public function create()
    {
        return Inertia::render('Admin/JobPostings/Create', [
            'jobCategories' => JobCategory::select('id', 'name')->get(),
            'countries' => Country::select('id', 'name')->get(),
        ]);
    }

    /**
     * NEW METHOD: Store a newly created job posting from Inertia.
     */
    public function store(Request $request)
    {
        try {
            // Use the same service to create the job
            $this->jobPostingService->createJobPosting($request->all());

            // On success, redirect back to the index page
            return Redirect::route('admin.job-postings.index')->with('success', 'Job posting created successfully.');

        } catch (\Illuminate\Validation\ValidationException $e) {
            // On validation error, redirect back to the form with errors
            return Redirect::back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            // On other errors, redirect back with a general error
            \Illuminate\Support\Facades\Log::error('Error creating job posting from PageController', [
                'message' => $e->getMessage(),
            ]);
            return Redirect::back()->with('error', 'An unexpected error occurred. Please try again.');
        }
    }

    public function edit(JobPosting $jobPosting)
    {
        return Inertia::render('Admin/JobPostings/Edit', [
            'jobPosting' => $jobPosting,
            'jobCategories' => JobCategory::select('id', 'name')->get(),
            'countries' => Country::select('id', 'name')->get(),
            // You may need to pass states/cities here if the job has them
        ]);
    }

    // You will also need to add update() and destroy() methods here
    // to handle edit/delete from the Inertia frontend, following
    // the same pattern as this store() method.
}