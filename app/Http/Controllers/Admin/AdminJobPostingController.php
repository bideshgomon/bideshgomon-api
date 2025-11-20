<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\JobPosting;
use App\Models\Country;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\DB;

class AdminJobPostingController extends Controller
{
    /**
     * Display a listing of job postings.
     */
    public function index(Request $request)
    {
        $query = JobPosting::with(['country'])
            ->withCount('applications');

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('company_name', 'like', "%{$search}%")
                  ->orWhere('category', 'like', "%{$search}%");
            });
        }

        // Filter by country
        if ($request->filled('country_id')) {
            $query->where('country_id', $request->country_id);
        }

        // Filter by category
        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        // Filter by status
        if ($request->filled('status')) {
            if ($request->status === 'active') {
                $query->where('is_active', true);
            } else if ($request->status === 'inactive') {
                $query->where('is_active', false);
            } else if ($request->status === 'expired') {
                $query->where('application_deadline', '<', now());
            }
        }

        // Filter by featured
        if ($request->filled('is_featured')) {
            $query->where('is_featured', $request->is_featured);
        }

        $jobs = $query->latest('published_at')
            ->paginate(20)
            ->withQueryString();

        // Get filter options
        $countries = Country::orderBy('name')->get(['id', 'name']);
        $categories = JobPosting::distinct()->pluck('category')->filter();

        return Inertia::render('Admin/Jobs/Index', [
            'jobs' => $jobs,
            'countries' => $countries,
            'categories' => $categories,
            'filters' => $request->only(['search', 'country_id', 'category', 'status', 'is_featured']),
            'stats' => [
                'total' => JobPosting::count(),
                'active' => JobPosting::where('is_active', true)->count(),
                'featured' => JobPosting::where('is_featured', true)->count(),
                'expired' => JobPosting::where('application_deadline', '<', now())->count(),
            ],
        ]);
    }

    /**
     * Show the form for creating a new job posting.
     */
    public function create()
    {
        $countries = Country::orderBy('name')->get(['id', 'name']);

        return Inertia::render('Admin/Jobs/Create', [
            'countries' => $countries,
        ]);
    }

    /**
     * Store a newly created job posting.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'company_name' => 'required|string|max:255',
            'company_logo' => 'nullable|url',
            'country_id' => 'required|exists:countries,id',
            'city' => 'required|string|max:255',
            'job_type' => 'required|in:full-time,part-time,contract,temporary',
            'category' => 'required|string|max:100',
            'description' => 'required|string',
            'responsibilities' => 'nullable|string',
            'requirements' => 'nullable|string',
            'salary_min' => 'required|numeric|min:0',
            'salary_max' => 'required|numeric|gt:salary_min',
            'salary_currency' => 'required|string|size:3',
            'salary_period' => 'required|in:monthly,yearly,hourly',
            'experience_required' => 'nullable|string|max:50',
            'education_required' => 'nullable|string|max:100',
            'skills_required' => 'nullable|array',
            'benefits' => 'nullable|array',
            'gender_preference' => 'nullable|in:male,female,any',
            'age_min' => 'nullable|integer|min:18|max:100',
            'age_max' => 'nullable|integer|gte:age_min|max:100',
            'positions_available' => 'required|integer|min:1',
            'application_fee' => 'required|numeric|min:0',
            'is_featured' => 'boolean',
            'is_active' => 'boolean',
            'deadline' => 'required|date|after:today',
            'contact_email' => 'nullable|email',
            'contact_phone' => 'nullable|string|max:20',
        ]);

        $validated['published_at'] = now();
        $validated['views_count'] = 0;
        $validated['applications_count'] = 0;

        $job = JobPosting::create($validated);

        return redirect()->route('admin.jobs.index')
            ->with('success', 'Job posting created successfully!');
    }

    /**
     * Display the specified job posting.
     */
    public function show($id)
    {
        $job = JobPosting::with(['country', 'applications.user.userProfile', 'applications.userCv'])
            ->withCount('applications')
            ->findOrFail($id);

        return Inertia::render('Admin/Jobs/Show', [
            'job' => $job,
            'applications' => $job->applications()->with(['user.userProfile', 'userCv'])
                ->latest()
                ->paginate(15),
        ]);
    }

    /**
     * Show the form for editing the specified job posting.
     */
    public function edit($id)
    {
        $job = JobPosting::with('country')->findOrFail($id);
        $countries = Country::orderBy('name')->get(['id', 'name']);

        return Inertia::render('Admin/Jobs/Edit', [
            'job' => $job,
            'countries' => $countries,
        ]);
    }

    /**
     * Update the specified job posting.
     */
    public function update(Request $request, $id)
    {
        $job = JobPosting::findOrFail($id);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'company_name' => 'required|string|max:255',
            'company_logo' => 'nullable|url',
            'country_id' => 'required|exists:countries,id',
            'city' => 'required|string|max:255',
            'job_type' => 'required|in:full-time,part-time,contract,temporary',
            'category' => 'required|string|max:100',
            'description' => 'required|string',
            'responsibilities' => 'nullable|string',
            'requirements' => 'nullable|string',
            'salary_min' => 'required|numeric|min:0',
            'salary_max' => 'required|numeric|gt:salary_min',
            'salary_currency' => 'required|string|size:3',
            'salary_period' => 'required|in:monthly,yearly,hourly',
            'experience_required' => 'nullable|string|max:50',
            'education_required' => 'nullable|string|max:100',
            'skills_required' => 'nullable|array',
            'benefits' => 'nullable|array',
            'gender_preference' => 'nullable|in:male,female,any',
            'age_min' => 'nullable|integer|min:18|max:100',
            'age_max' => 'nullable|integer|gte:age_min|max:100',
            'positions_available' => 'required|integer|min:1',
            'application_fee' => 'required|numeric|min:0',
            'is_featured' => 'boolean',
            'is_active' => 'boolean',
            'deadline' => 'required|date',
            'contact_email' => 'nullable|email',
            'contact_phone' => 'nullable|string|max:20',
        ]);

        $job->update($validated);

        return redirect()->route('admin.jobs.index')
            ->with('success', 'Job posting updated successfully!');
    }

    /**
     * Remove the specified job posting.
     */
    public function destroy($id)
    {
        $job = JobPosting::findOrFail($id);
        
        // Check if there are applications
        $applicationsCount = $job->applications()->count();
        
        if ($applicationsCount > 0) {
            return back()->withErrors([
                'error' => "Cannot delete job posting with {$applicationsCount} applications. Please archive it instead."
            ]);
        }

        $job->delete();

        return redirect()->route('admin.jobs.index')
            ->with('success', 'Job posting deleted successfully!');
    }

    /**
     * Toggle featured status.
     */
    public function toggleFeatured($id)
    {
        $job = JobPosting::findOrFail($id);
        $job->update(['is_featured' => !$job->is_featured]);

        return back()->with('success', 'Featured status updated!');
    }

    /**
     * Toggle active status.
     */
    public function toggleActive($id)
    {
        $job = JobPosting::findOrFail($id);
        $job->update(['is_active' => !$job->is_active]);

        return back()->with('success', 'Active status updated!');
    }

    /**
     * Bulk delete job postings.
     */
    public function bulkDelete(Request $request)
    {
        $validated = $request->validate([
            'job_ids' => 'required|array',
            'job_ids.*' => 'exists:job_postings,id',
        ]);

        // Check for applications
        $jobsWithApplications = JobPosting::whereIn('id', $validated['job_ids'])
            ->has('applications')
            ->count();

        if ($jobsWithApplications > 0) {
            return back()->withErrors([
                'error' => "{$jobsWithApplications} job(s) have applications and cannot be deleted."
            ]);
        }

        $deleted = JobPosting::whereIn('id', $validated['job_ids'])->delete();

        return back()->with('success', "{$deleted} job posting(s) deleted successfully!");
    }

    /**
     * Bulk update status.
     */
    public function bulkUpdateStatus(Request $request)
    {
        $validated = $request->validate([
            'job_ids' => 'required|array',
            'job_ids.*' => 'exists:job_postings,id',
            'status' => 'required|in:active,inactive',
        ]);

        $isActive = $validated['status'] === 'active';
        $updated = JobPosting::whereIn('id', $validated['job_ids'])
            ->update(['is_active' => $isActive]);

        return back()->with('success', "{$updated} job posting(s) updated to {$validated['status']}!");
    }
}
