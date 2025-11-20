<?php

namespace App\Http\Controllers;

use App\Models\JobPosting;
use App\Models\JobApplication;
use App\Models\Country;
use App\Services\WalletService;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class JobController extends Controller
{
    protected $walletService;

    public function __construct(WalletService $walletService)
    {
        $this->walletService = $walletService;
    }

    /**
     * Display job listings with filters
     */
    public function index(Request $request)
    {
        $query = JobPosting::with('country')->active();

        // Apply search filter
        if ($request->search) {
            $query->search($request->search);
        }

        // Apply country filter
        if ($request->country_id) {
            $query->byCountry($request->country_id);
        }

        // Apply category filter
        if ($request->category) {
            $query->byCategory($request->category);
        }

        // Apply job type filter
        if ($request->job_type) {
            $query->byJobType($request->job_type);
        }

        // Apply salary filters
        if ($request->salary_min) {
            $query->where('salary_min', '>=', $request->salary_min);
        }

        if ($request->salary_max) {
            $query->where('salary_max', '<=', $request->salary_max);
        }

        // Featured jobs first, then by posted date
        $jobs = $query->orderBy('is_featured', 'desc')
            ->orderBy('is_urgent', 'desc')
            ->orderBy('published_at', 'desc')
            ->paginate(12);

        // Get filter options
        $countries = Country::whereIn('id', JobPosting::active()->pluck('country_id')->unique())
            ->orderBy('name')
            ->get(['id', 'name']);

        $categories = JobPosting::active()
            ->distinct()
            ->pluck('category')
            ->sort()
            ->values();

        $jobTypes = JobPosting::active()
            ->distinct()
            ->pluck('job_type')
            ->sort()
            ->values();

        // Get user's applications if authenticated
        $userApplications = [];
        if (Auth::check()) {
            $userApplications = JobApplication::where('user_id', Auth::id())
                ->pluck('job_posting_id')
                ->toArray();
        }

        return Inertia::render('Jobs/Index', [
            'jobs' => $jobs,
            'countries' => $countries,
            'categories' => $categories,
            'jobTypes' => $jobTypes,
            'filters' => [
                'search' => $request->search,
                'country_id' => $request->country_id,
                'category' => $request->category,
                'job_type' => $request->job_type,
                'salary_min' => $request->salary_min,
                'salary_max' => $request->salary_max,
            ],
            'userApplications' => $userApplications,
        ]);
    }

    /**
     * Display job details
     */
    public function show($id)
    {
        $job = JobPosting::with('country')->findOrFail($id);

        // Increment view count
        $job->incrementViews();

        // Check if user has applied
        $hasApplied = false;
        $application = null;
        
        if (Auth::check()) {
            $application = JobApplication::where('job_posting_id', $job->id)
                ->where('user_id', Auth::id())
                ->first();
            $hasApplied = $application !== null;
        }

        // Get related jobs (same category, different job)
        $relatedJobs = JobPosting::with('country')
            ->active()
            ->where('category', $job->category)
            ->where('id', '!=', $job->id)
            ->limit(3)
            ->get();

        return Inertia::render('Jobs/Show', [
            'job' => $job,
            'hasApplied' => $hasApplied,
            'application' => $application,
            'relatedJobs' => $relatedJobs,
        ]);
    }

    /**
     * Apply for a job
     */
    public function apply(Request $request, $id)
    {
        $request->validate([
            'cover_letter' => 'nullable|string|max:5000',
            'user_cv_id' => 'nullable|exists:user_cvs,id',
        ]);

        $job = JobPosting::findOrFail($id);

        // Check if already applied
        $existingApplication = JobApplication::where('job_posting_id', $job->id)
            ->where('user_id', Auth::id())
            ->first();

        if ($existingApplication) {
            return back()->with('error', 'You have already applied for this job.');
        }

        // Check if job is expired
        if ($job->isExpired()) {
            return back()->with('error', 'This job posting has expired.');
        }

        DB::beginTransaction();

        try {
            $walletTransactionId = null;

            // Process application fee if required
            if ($job->application_fee > 0) {
                $user = Auth::user();
                
                // Check wallet balance
                if (!$user->wallet || $user->wallet->balance < $job->application_fee) {
                    return back()->with('error', 'Insufficient wallet balance. Please add funds to your wallet.');
                }

                // Debit from wallet
                $transaction = $this->walletService->debitWallet(
                    $user->wallet,
                    $job->application_fee,
                    "Application fee for {$job->title}",
                    'job_application',
                    $job->id,
                    [
                        'job_title' => $job->title,
                        'company' => $job->company_name,
                    ]
                );

                $walletTransactionId = $transaction->id;
            }

            // Create application
            $application = JobApplication::create([
                'job_posting_id' => $job->id,
                'user_id' => Auth::id(),
                'user_cv_id' => $request->user_cv_id,
                'cover_letter' => $request->cover_letter,
                'status' => 'pending',
                'application_fee_paid' => $job->application_fee,
                'payment_status' => $job->application_fee > 0 ? 'paid' : 'unpaid',
                'payment_reference' => $walletTransactionId ? "WT-{$walletTransactionId}" : null,
                'payment_date' => $job->application_fee > 0 ? now() : null,
                'submitted_at' => now(),
            ]);

            // Increment applications count
            $job->incrementApplications();

            DB::commit();

            return redirect()->route('jobs.my-applications')
                ->with('success', 'Application submitted successfully!');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to submit application. Please try again.');
        }
    }

    /**
     * Display user's job applications
     */
    public function myApplications()
    {
        $applications = JobApplication::with(['jobPosting.country', 'userCv'])
            ->where('user_id', Auth::id())
            ->orderBy('submitted_at', 'desc')
            ->paginate(12);

        // Get application statistics
        $stats = [
            'total' => JobApplication::where('user_id', Auth::id())->count(),
            'pending' => JobApplication::where('user_id', Auth::id())->pending()->count(),
            'under_review' => JobApplication::where('user_id', Auth::id())->underReview()->count(),
            'shortlisted' => JobApplication::where('user_id', Auth::id())->shortlisted()->count(),
            'rejected' => JobApplication::where('user_id', Auth::id())->rejected()->count(),
            'accepted' => JobApplication::where('user_id', Auth::id())->accepted()->count(),
        ];

        return Inertia::render('Jobs/MyApplications', [
            'applications' => $applications,
            'stats' => $stats,
        ]);
    }
}
