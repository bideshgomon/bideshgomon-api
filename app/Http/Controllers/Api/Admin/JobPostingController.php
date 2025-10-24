<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\JobPosting;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Validation\Rule;

class JobPostingController extends Controller
{
    /**
     * Display a listing of job postings.
     */
    public function index(Request $request)
    {
        // Allow filtering by category or country later if needed
        return JobPosting::with(['jobCategory', 'country']) // Eager load relationships
                        ->latest()
                        ->paginate(10);
    }

    /**
     * Store a newly created job posting in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'job_category_id' => 'nullable|exists:job_categories,id',
            'country_id' => 'nullable|exists:countries,id',
            'title' => 'required|string|max:255',
            'company_name' => 'required|string|max:255',
            'location_city' => 'nullable|string|max:255',
            'employment_type' => 'nullable|string|max:100',
            'description' => 'required|string',
            'responsibilities' => 'nullable|string',
            'qualifications' => 'nullable|string',
            'skills_required' => 'nullable|string',
            'salary_min' => 'nullable|numeric|min:0',
            'salary_max' => 'nullable|numeric|min:0|gte:salary_min',
            'salary_currency' => 'nullable|string|max:3',
            'salary_period' => 'nullable|string|max:50',
            'apply_url' => 'nullable|url',
            'posting_date' => 'nullable|date',
            'closing_date' => 'nullable|date|after_or_equal:posting_date',
            'status' => 'sometimes|string|in:active,expired,filled',
            'is_featured' => 'sometimes|boolean',
        ]);

        JobPosting::create($validated);

        return redirect()->route('admin.job-postings.index') // Assumes admin list route name
                         ->with('success', 'Job Posting created successfully.');
    }

    /**
     * Display the specified job posting.
     */
    public function show(JobPosting $jobPosting)
    {
        return $jobPosting->load(['jobCategory', 'country']);
    }

    /**
     * Update the specified job posting in storage.
     */
    public function update(Request $request, JobPosting $jobPosting): RedirectResponse
    {
         $validated = $request->validate([ // Reuse validation rules from store
            'job_category_id' => 'nullable|exists:job_categories,id',
            'country_id' => 'nullable|exists:countries,id',
            'title' => 'required|string|max:255',
            'company_name' => 'required|string|max:255',
            // ... include all other fields from store validation ...
             'location_city' => 'nullable|string|max:255',
            'employment_type' => 'nullable|string|max:100',
            'description' => 'required|string',
            'responsibilities' => 'nullable|string',
            'qualifications' => 'nullable|string',
            'skills_required' => 'nullable|string',
            'salary_min' => 'nullable|numeric|min:0',
            'salary_max' => 'nullable|numeric|min:0|gte:salary_min',
            'salary_currency' => 'nullable|string|max:3',
            'salary_period' => 'nullable|string|max:50',
            'apply_url' => 'nullable|url',
            'posting_date' => 'nullable|date',
            'closing_date' => 'nullable|date|after_or_equal:posting_date',
            'status' => 'sometimes|string|in:active,expired,filled',
            'is_featured' => 'sometimes|boolean',
        ]);

        // Ensure boolean defaults correctly
        $data = $validated;
        if (!isset($data['is_featured'])) {
            $data['is_featured'] = false;
        }

        $jobPosting->update($data);

        return redirect()->route('admin.job-postings.index') // Assumes admin list route name
                         ->with('success', 'Job Posting updated successfully.');
    }

    /**
     * Remove the specified job posting from storage.
     */
    public function destroy(JobPosting $jobPosting): RedirectResponse
    {
        $jobPosting->delete();

        return redirect()->route('admin.job-postings.index') // Assumes admin list route name
                         ->with('success', 'Job Posting deleted successfully.');
    }
}