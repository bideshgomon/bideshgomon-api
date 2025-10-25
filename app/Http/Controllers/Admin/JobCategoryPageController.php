<?php

// CORRECT NAMESPACE - Should be Admin, not Api\Admin
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\JobCategory;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Http\Request; // Keep Request if needed for search/filtering later

class JobCategoryPageController extends Controller // Correct class name matches filename
{
    /**
     * Display the list of job categories.
     */
    public function index(Request $request): Response // Add Request for potential filtering
    {
        $query = JobCategory::query(); // Start query builder

        // Example: Add search functionality if needed later
        // if ($request->has('search')) {
        //     $query->where('name', 'like', '%' . $request->search . '%');
        // }

        $categories = $query->latest()->paginate(10); // Paginate the results

        return Inertia::render('Admin/JobCategories/Index', [
            'categories' => $categories,
            'filters' => $request->only(['search']) // Pass back any active filters
        ]);
    }

    /**
     * Show the form for creating a new job category.
     */
    public function create(): Response
    {
        return Inertia::render('Admin/JobCategories/Create');
    }

    /**
     * Show the form for editing the specified job category.
     */
    public function edit(JobCategory $jobCategory): Response
    {
        return Inertia::render('Admin/JobCategories/Edit', [
            'category' => $jobCategory, // Pass the category data to the Vue component
        ]);
    }

    // Note: Store, Update, Destroy methods are handled by the API controller
    // App\Http\Controllers\Api\Admin\JobCategoryController
}