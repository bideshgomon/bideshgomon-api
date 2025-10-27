<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\JobCategory; // Import JobCategory model
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
// REMOVE unused imports
// use Illuminate\Http\RedirectResponse;
// use Illuminate\Validation\Rule;

class JobCategoryPageController extends Controller
{
    /**
     * Display the list of job categories.
     */
    public function index(Request $request): Response // <-- Added Request
    {
        // Build query for job categories
        $query = JobCategory::query()->orderBy('name', 'asc');

        // Filter by Search Term (Name)
        if ($request->filled('search')) {
            $searchTerm = $request->input('search');
            $query->where('name', 'LIKE', "%{$searchTerm}%");
        }

        // Paginate results
        $jobCategories = $query->paginate(25)->withQueryString();

        // Pass data to the Inertia view
        return Inertia::render('Admin/JobCategories/Index', [
             'jobCategories' => $jobCategories, // <-- Pass paginated data
             'filters' => $request->only(['search']), // Current filters
        ]);
    }

    /**
     * Show the form for creating a new job category.
     * (This method is no longer used by our web routes)
     */
    // public function create(): Response
    // {
    //     return Inertia::render('Admin/JobCategories/Create');
    // }

    /**
     * Store a newly created job category in storage.
     * (This method will be moved to the API controller)
     */
    // public function store(Request $request): RedirectResponse
    // {
    //     // ... logic to be moved ...
    // }

    /**
     * Show the form for editing the specified job category.
     * (This method is no longer used by our web routes)
     */
    // public function edit(JobCategory $jobCategory): Response
    // {
    //     // ... logic to be moved ...
    // }

    /**
     * Update the specified job category in storage.
     * (This method will be moved to the API controller)
     */
    // public function update(Request $request, JobCategory $jobCategory): RedirectResponse
    // {
    //    // ... logic to be moved ...
    // }

    /**
     * Remove the specified job category from storage.
     * (This method will be moved to the API controller)
     */
    // public function destroy(JobCategory $jobCategory): RedirectResponse
    // {
    //    // ... logic to be moved ...
    // }
}