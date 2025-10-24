<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\JobPosting;
use App\Models\JobCategory; // <-- ADD
use App\Models\Country;     // <-- ADD
use Inertia\Inertia;
use Inertia\Response;

class JobPostingPageController extends Controller
{
    /**
     * Display the list of job postings.
     */
    public function index(): Response
    {
        $postings = JobPosting::with(['jobCategory', 'country'])
                        ->latest()
                        ->paginate(10);

        return Inertia::render('Admin/JobPostings/Index', [
            'postings' => $postings,
        ]);
    }

    /**
     * Show the form for creating a new job posting.
     */
    public function create(): Response
    {
        $categories = JobCategory::where('is_active', true)->orderBy('name')->get(['id', 'name']);
        $countries = Country::orderBy('name')->get(['id', 'name']);

        return Inertia::render('Admin/JobPostings/Create', [
            'categories' => $categories,
            'countries' => $countries,
        ]);
    }

    /**
     * Show the form for editing the specified job posting.
     */
    public function edit(JobPosting $jobPosting): Response
    {
        $categories = JobCategory::orderBy('name')->get(['id', 'name']); // Include inactive for editing existing
        $countries = Country::orderBy('name')->get(['id', 'name']);

        // Eager load relationships
        $jobPosting->load(['jobCategory', 'country']);

        return Inertia::render('Admin/JobPostings/Edit', [
            'posting' => $jobPosting,
            'categories' => $categories,
            'countries' => $countries,
        ]);
    }
}