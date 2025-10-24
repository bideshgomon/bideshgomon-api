<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\JobCategory;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Validation\Rule;

class JobCategoryController extends Controller
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
     * Store a newly created job category in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:job_categories',
            'description' => 'nullable|string',
            'is_active' => 'sometimes|boolean',
        ]);

        JobCategory::create($validated);

        return redirect()->route('admin.job-categories.index')
                         ->with('success', 'Job Category created successfully.');
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

    /**
     * Update the specified job category in storage.
     */
    public function update(Request $request, JobCategory $jobCategory): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required','string','max:255', Rule::unique('job_categories')->ignore($jobCategory->id)],
            'description' => 'nullable|string',
            'is_active' => 'sometimes|boolean',
        ]);

        // Use request->boolean() for a cleaner way to handle default false
        $data = $validated;
        $data['is_active'] = $request->boolean('is_active');

        $jobCategory->update($data);

        return redirect()->route('admin.job-categories.index')
                         ->with('success', 'Job Category updated successfully.');
    }

    /**
     * Remove the specified job category from storage.
     */
    public function destroy(JobCategory $jobCategory): RedirectResponse
    {
        $jobCategory->delete();

        return redirect()->route('admin.job-categories.index')
                         ->with('success', 'Job Category deleted successfully.');
    }
}