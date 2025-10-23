<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\JobCategory; // <-- ADD THIS
use Inertia\Inertia;
use Inertia\Response;

class JobCategoryPageController extends Controller
{
    /**
     * Display the list of job categories.
     */
    public function index(): Response
    {
        $categories = JobCategory::latest()->paginate(10);

        return Inertia::render('Admin/JobCategories/Index', [
            'categories' => $categories,
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
            'category' => $jobCategory,
        ]);
    }
}