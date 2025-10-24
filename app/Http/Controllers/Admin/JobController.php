<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Job; // <-- This line must be correct
use App\Models\JobCategory;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Http\RedirectResponse;
// We will create these request files in the next step
use App\Http\Requests\Admin\StoreJobRequest; 
use App\Http\Requests\Admin\UpdateJobRequest;
use Illuminate\Support\Str;

class JobController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): Response
    {
        $postings = Job::with('category')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return Inertia::render('Admin/JobPostings/Index', [
            'postings' => $postings,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): Response
    {
        return Inertia::render('Admin/JobPostings/Create', [
            'categories' => JobCategory::orderBy('name')->get(),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreJobRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $data['slug'] = Str::slug($data['title']);
        
        Job::create($data);

        return redirect()->route('admin.jobs.index')
                         ->with('success', 'Job posting created.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Job $job): Response
    {
        return Inertia::render('Admin/JobPostings/Edit', [
            'job' => $job,
            'categories' => JobCategory::orderBy('name')->get(),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateJobRequest $request, Job $job): RedirectResponse
    {
        $data = $request->validated();
        if (isset($data['title'])) {
            $data['slug'] = Str::slug($data['title']);
        }
        
        $job->update($data);

        return redirect()->route('admin.jobs.index')
                         ->with('success', 'Job posting updated.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Job $job): RedirectResponse
    {
        $job->delete();

        return redirect()->route('admin.jobs.index')
                         ->with('success', 'Job posting deleted.');
    }
}