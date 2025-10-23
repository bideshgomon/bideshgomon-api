<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\JobCategory;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Validation\Rule;

class JobCategoryController extends Controller
{
    /**
     * Display a listing of the job categories.
     */
    public function index()
    {
        // Simple pagination for now
        return JobCategory::latest()->paginate(10);
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

        // Redirect back to the (yet to be created) admin list page
        return redirect()->route('admin.job-categories.index') // Assuming this route name
                         ->with('success', 'Job Category created successfully.');
    }

    /**
     * Display the specified job category.
     */
    public function show(JobCategory $jobCategory)
    {
        // Renamed variable to avoid conflict with route parameter name
        return $jobCategory;
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

        // Ensure is_active defaults to false if not present in request
        $data = $validated;
        if (!isset($data['is_active'])) {
            $data['is_active'] = false;
        }


        $jobCategory->update($data);

        return redirect()->route('admin.job-categories.index') // Assuming this route name
                         ->with('success', 'Job Category updated successfully.');
    }

    /**
     * Remove the specified job category from storage.
     */
    public function destroy(JobCategory $jobCategory): RedirectResponse
    {
        // Add check if category is in use before deleting later if needed
        $jobCategory->delete();

        return redirect()->route('admin.job-categories.index') // Assuming this route name
                         ->with('success', 'Job Category deleted successfully.');
    }
}