<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\WalletTransaction;
use App\Models\JobApplication;
use App\Models\JobPosting;
use App\Models\TravelInsuranceBooking;
use App\Models\UserCv;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AdminAnalyticsController extends Controller
{
    public function index(Request $request)
    {
        $period = $request->get('period', '30'); // days
        $startDate = now()->subDays($period);

        // User Analytics
        $userStats = [
            'total_users' => User::count(),
            'new_users' => User::where('created_at', '>=', $startDate)->count(),
            'active_users' => User::whereHas('wallet', function ($q) use ($startDate) {
                $q->whereHas('transactions', function ($q2) use ($startDate) {
                    $q2->where('created_at', '>=', $startDate);
                });
            })->count(),
            'verified_users' => User::whereNotNull('email_verified_at')->count(),
            'suspended_users' => User::whereNotNull('suspended_at')->count(),
        ];

        // Revenue Analytics
        $revenueStats = [
            'total_revenue' => WalletTransaction::where('transaction_type', 'credit')
                ->where('status', 'completed')
                ->sum('amount'),
            'period_revenue' => WalletTransaction::where('transaction_type', 'credit')
                ->where('status', 'completed')
                ->where('created_at', '>=', $startDate)
                ->sum('amount'),
            'avg_transaction' => WalletTransaction::where('transaction_type', 'credit')
                ->where('status', 'completed')
                ->where('created_at', '>=', $startDate)
                ->avg('amount'),
            'total_transactions' => WalletTransaction::where('created_at', '>=', $startDate)->count(),
        ];

        // Job Analytics
        $jobStats = [
            'total_postings' => JobPosting::count(),
            'active_postings' => JobPosting::where('is_active', true)->where('application_deadline', '>=', now())->count(),
            'total_applications' => JobApplication::count(),
            'period_applications' => JobApplication::where('created_at', '>=', $startDate)->count(),
            'application_rate' => JobPosting::where('is_active', true)->count() > 0 
                ? round(JobApplication::count() / JobPosting::where('is_active', true)->count(), 2)
                : 0,
        ];

        // Service Analytics
        $serviceStats = [
            'insurance_bookings' => TravelInsuranceBooking::where('created_at', '>=', $startDate)->count(),
            'cvs_created' => UserCv::where('created_at', '>=', $startDate)->count(),
            'total_cvs' => UserCv::count(),
        ];

        // User Growth Chart (Last 30 days)
        $userGrowthChart = [];
        for ($i = 29; $i >= 0; $i--) {
            $date = now()->subDays($i);
            $userGrowthChart[] = [
                'date' => $date->format('M d'),
                'count' => User::whereDate('created_at', $date)->count(),
            ];
        }

        // Revenue Chart (Last 30 days)
        $revenueChart = [];
        for ($i = 29; $i >= 0; $i--) {
            $date = now()->subDays($i);
            $revenueChart[] = [
                'date' => $date->format('M d'),
                'amount' => WalletTransaction::where('transaction_type', 'credit')
                    ->where('status', 'completed')
                    ->whereDate('created_at', $date)
                    ->sum('amount'),
            ];
        }

        // Top Countries by Users
        $topCountries = User::select('country_id', DB::raw('count(*) as user_count'))
            ->with('country:id,name')
            ->whereNotNull('country_id')
            ->groupBy('country_id')
            ->orderByDesc('user_count')
            ->limit(10)
            ->get()
            ->map(function ($item) {
                return [
                    'country' => $item->country?->name ?? 'Unknown',
                    'count' => $item->user_count,
                ];
            });

        // Top Job Categories
        $topJobCategories = JobPosting::select('category', DB::raw('count(*) as job_count'))
            ->groupBy('category')
            ->orderByDesc('job_count')
            ->limit(10)
            ->get();

        // Application Status Distribution
        $applicationStatusDistribution = JobApplication::select('status', DB::raw('count(*) as count'))
            ->groupBy('status')
            ->get()
            ->mapWithKeys(function ($item) {
                return [$item->status => $item->count];
            });

        // Monthly Revenue Comparison (Current vs Previous Month)
        $currentMonthRevenue = WalletTransaction::where('transaction_type', 'credit')
            ->where('status', 'completed')
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->sum('amount');

        $previousMonthRevenue = WalletTransaction::where('transaction_type', 'credit')
            ->where('status', 'completed')
            ->whereMonth('created_at', now()->subMonth()->month)
            ->whereYear('created_at', now()->subMonth()->year)
            ->sum('amount');

        $revenueGrowth = $previousMonthRevenue > 0 
            ? round((($currentMonthRevenue - $previousMonthRevenue) / $previousMonthRevenue) * 100, 2)
            : 0;

        // User Registration Trend
        $currentMonthUsers = User::whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->count();

        $previousMonthUsers = User::whereMonth('created_at', now()->subMonth()->month)
            ->whereYear('created_at', now()->subMonth()->year)
            ->count();

        $userGrowthRate = $previousMonthUsers > 0 
            ? round((($currentMonthUsers - $previousMonthUsers) / $previousMonthUsers) * 100, 2)
            : 0;

        return Inertia::render('Admin/Analytics/Index', [
            'period' => $period,
            'userStats' => $userStats,
            'revenueStats' => $revenueStats,
            'jobStats' => $jobStats,
            'serviceStats' => $serviceStats,
            'userGrowthChart' => $userGrowthChart,
            'revenueChart' => $revenueChart,
            'topCountries' => $topCountries,
            'topJobCategories' => $topJobCategories,
            'applicationStatusDistribution' => $applicationStatusDistribution,
            'revenueGrowth' => $revenueGrowth,
            'userGrowthRate' => $userGrowthRate,
            'currentMonthRevenue' => $currentMonthRevenue,
            'previousMonthRevenue' => $previousMonthRevenue,
        ]);
    }

    public function export(Request $request)
    {
        $type = $request->get('type', 'users');
        $period = $request->get('period', '30');
        $startDate = now()->subDays($period);

        switch ($type) {
            case 'users':
                return $this->exportUsers($startDate);
            case 'revenue':
                return $this->exportRevenue($startDate);
            case 'jobs':
                return $this->exportJobs($startDate);
            default:
                return back()->with('error', 'Invalid export type');
        }
    }

    private function exportUsers($startDate)
    {
        $users = User::with('country')
            ->where('created_at', '>=', $startDate)
            ->get();

        $csv = "ID,Name,Email,Country,Email Verified,Suspended,Registered Date\n";

        foreach ($users as $user) {
            $csv .= sprintf(
                "%d,%s,%s,%s,%s,%s,%s\n",
                $user->id,
                $user->name,
                $user->email,
                $user->country?->name ?? 'N/A',
                $user->email_verified_at ? 'Yes' : 'No',
                $user->suspended_at ? 'Yes' : 'No',
                $user->created_at->format('Y-m-d H:i:s')
            );
        }

        return response($csv, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="users-report-' . now()->format('Y-m-d') . '.csv"',
        ]);
    }

    private function exportRevenue($startDate)
    {
        $transactions = WalletTransaction::with('wallet.user')
            ->where('transaction_type', 'credit')
            ->where('created_at', '>=', $startDate)
            ->get();

        $csv = "Transaction ID,User,Amount,Description,Status,Date\n";

        foreach ($transactions as $transaction) {
            $csv .= sprintf(
                "%d,%s,%.2f,%s,%s,%s\n",
                $transaction->id,
                $transaction->wallet?->user?->name ?? 'N/A',
                $transaction->amount,
                str_replace(',', ';', $transaction->description),
                $transaction->status,
                $transaction->created_at->format('Y-m-d H:i:s')
            );
        }

        return response($csv, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="revenue-report-' . now()->format('Y-m-d') . '.csv"',
        ]);
    }

    private function exportJobs($startDate)
    {
        $jobs = JobPosting::with('country')
            ->where('created_at', '>=', $startDate)
            ->get();

        $csv = "Job ID,Title,Company,Country,Category,Applications,Status,Posted Date\n";

        foreach ($jobs as $job) {
            $csv .= sprintf(
                "%d,%s,%s,%s,%s,%d,%s,%s\n",
                $job->id,
                str_replace(',', ';', $job->title),
                str_replace(',', ';', $job->company_name),
                $job->country?->name ?? 'N/A',
                $job->category,
                $job->applications_count ?? 0,
                $job->is_active ? 'Active' : 'Inactive',
                $job->created_at->format('Y-m-d')
            );
        }

        return response($csv, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="jobs-report-' . now()->format('Y-m-d') . '.csv"',
        ]);
    }
}
