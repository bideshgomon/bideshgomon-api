<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\JobPosting;  // <-- CORRECT MODEL
use App\Models\JobCategory;
use App\Models\Country;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Http\Request; // <-- Add Request

class JobPostingPageController extends Controller
{
    /**
     * Display the list of job postings.
     */
    public function index(Request $request): Response // <-- Add Request
    {
        // Use the correct model: JobPosting
        $query = JobPosting::with(['jobCategory', 'country']);

        // Optional: Add search logic if needed
        if ($request->has('search')) {
            $query->where('title', 'like', '%' . $request->search . '%');
        }
        
        $postings = $query->latest()->paginate(10);

        return Inertia::render('Admin/JobPostings/Index', [
            'postings' => $postings,
            'filters' => $request->only(['search']), // Pass filters back
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
    public function edit(JobPosting $jobPosting): Response // <-- Use correct model JobPosting
    {
        $categories = JobCategory::orderBy('name')->get(['id', 'name']);
        $countries = Country::orderBy('name')->get(['id', 'name']);

        // Eager load relationships
        $jobPosting->load(['jobCategory', 'country']);

        return Inertia::render('Admin/JobPostings/Edit', [
            'posting' => $jobPosting, // 'posting' prop matches the Vue file
            'categories' => $categories,
            'countries' => $countries,
        ]);
    }
}