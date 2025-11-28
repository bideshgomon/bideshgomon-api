<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\JobApplication;
use App\Models\JobPosting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class AdminJobApplicationController extends Controller
{
    /**
     * Display a listing of all job applications.
     */
    public function index(Request $request)
    {
        $query = JobApplication::with([
            'user.userProfile',
            'jobPosting.country',
            'userCv'
        ]);

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by job
        if ($request->filled('job_id')) {
            $query->where('job_posting_id', $request->job_id);
        }

        // Search by applicant name or email
        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('user', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        // Filter by payment status
        if ($request->filled('payment_status')) {
            $query->where('payment_status', $request->payment_status);
        }

        $applications = $query->latest('submitted_at')
            ->paginate(20)
            ->withQueryString();

        // Get filter options
        $jobs = JobPosting::select('id', 'title', 'company_name')
            ->orderBy('title')
            ->get();

        return Inertia::render('Admin/JobApplications/Index', [
            'applications' => $applications,
            'jobs' => $jobs,
            'filters' => $request->only(['search', 'job_id', 'status', 'payment_status']),
            'stats' => [
                'total' => JobApplication::count(),
                'pending' => JobApplication::where('status', 'pending')->count(),
                'reviewed' => JobApplication::where('status', 'reviewed')->count(),
                'shortlisted' => JobApplication::where('status', 'shortlisted')->count(),
                'rejected' => JobApplication::where('status', 'rejected')->count(),
                'hired' => JobApplication::where('status', 'hired')->count(),
            ],
        ]);
    }

    /**
     * Display the specified application.
     */
    public function show($id)
    {
        $application = JobApplication::with([
            'user.userProfile',
            'user.educations',
            'user.workExperiences',
            'user.languages',
            'jobPosting.country',
            'userCv',
            'walletTransaction'
        ])->findOrFail($id);

        return Inertia::render('Admin/JobApplications/Show', [
            'application' => $application,
        ]);
    }

    /**
     * Update application status.
     */
    public function updateStatus(Request $request, $id)
    {
        $application = JobApplication::findOrFail($id);

        $validated = $request->validate([
            'status' => 'required|in:pending,reviewed,shortlisted,rejected,hired',
            'admin_notes' => 'nullable|string|max:1000',
        ]);

        $validated['reviewed_at'] = now();
        $validated['reviewed_by'] = Auth::id();
        
        $application->update($validated);

        return back()->with('success', 'Application status updated successfully!');
    }

    /**
     * Bulk update status for multiple applications.
     */
    public function bulkUpdateStatus(Request $request)
    {
        $validated = $request->validate([
            'application_ids' => 'required|array',
            'application_ids.*' => 'exists:job_applications,id',
            'status' => 'required|in:pending,reviewed,shortlisted,rejected,hired',
            'admin_notes' => 'nullable|string|max:1000',
        ]);

        $data = [
            'status' => $validated['status'],
            'reviewed_at' => now(),
        ];

        if (!empty($validated['admin_notes'])) {
            $data['admin_notes'] = $validated['admin_notes'];
        }

        $updated = JobApplication::whereIn('id', $validated['application_ids'])
            ->update($data);

        return back()->with('success', "{$updated} application(s) updated to {$validated['status']}!");
    }

    /**
     * Export applications to CSV.
     */
    public function export(Request $request)
    {
        $query = JobApplication::with(['user', 'jobPosting']);

        if ($request->filled('job_id')) {
            $query->where('job_posting_id', $request->job_id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $applications = $query->get();

        $filename = 'job-applications-' . now()->format('Y-m-d-His') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function() use ($applications) {
            $file = fopen('php://output', 'w');
            
            // Header row
            fputcsv($file, [
                'Application ID',
                'Job Title',
                'Company',
                'Applicant Name',
                'Applicant Email',
                'Status',
                'Application Fee',
                'Payment Status',
                'Applied Date',
                'Reviewed Date',
            ]);

            // Data rows
            foreach ($applications as $app) {
                fputcsv($file, [
                    $app->id,
                    $app->jobPosting->title ?? 'N/A',
                    $app->jobPosting->company_name ?? 'N/A',
                    $app->user->name ?? 'N/A',
                    $app->user->email ?? 'N/A',
                    $app->status,
                    $app->application_fee_paid,
                    $app->payment_status,
                    $app->submitted_at?->format('Y-m-d H:i:s') ?? $app->created_at?->format('Y-m-d H:i:s') ?? 'N/A',
                    $app->reviewed_at?->format('Y-m-d H:i:s') ?? 'N/A',
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
