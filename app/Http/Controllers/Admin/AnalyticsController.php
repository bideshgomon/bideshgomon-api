<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\WalletTransaction;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Inertia\Inertia;

class AnalyticsController extends Controller
{
    public function dashboard()
    {
        // Quick Stats
        $stats = [
            'totalUsers' => User::count(),
            'newUsersToday' => User::whereDate('created_at', today())->count(),
            'totalRevenue' => WalletTransaction::where('transaction_type', 'credit')
                ->where('status', 'completed')
                ->sum('amount'),
            'revenueToday' => WalletTransaction::where('transaction_type', 'credit')
                ->where('status', 'completed')
                ->whereDate('created_at', today())
                ->sum('amount'),
            'activeAgencies' => User::whereHas('role', function ($query) {
                $query->where('slug', 'agency');
            })->where('is_active', true)->count(),
            'pendingVerification' => User::whereHas('role', function ($query) {
                $query->where('slug', 'agency');
            })->whereHas('agency', function ($query) {
                $query->where('verification_status', 'pending');
            })->count(),
            'pendingTransactions' => WalletTransaction::where('status', 'pending')->count(),
            'pendingAmount' => WalletTransaction::where('status', 'pending')->sum('amount'),
        ];

        // User Growth Data (Last 7 Days)
        $userGrowthData = $this->getUserGrowthData();

        // Revenue Data (Last 7 Days)
        $revenueData = $this->getRevenueData();

        // Recent Users
        $recentUsers = User::with('role')
            ->latest()
            ->limit(10)
            ->get();

        // Recent Transactions
        $recentTransactions = WalletTransaction::with('user')
            ->latest()
            ->limit(10)
            ->get();

        return Inertia::render('Admin/Analytics/Dashboard', [
            'stats' => $stats,
            'userGrowthData' => $userGrowthData,
            'revenueData' => $revenueData,
            'recentUsers' => $recentUsers,
            'recentTransactions' => $recentTransactions,
        ]);
    }

    private function getUserGrowthData()
    {
        $labels = [];
        $data = [];

        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::today()->subDays($i);
            $labels[] = $date->format('M d');
            $data[] = User::whereDate('created_at', $date)->count();
        }

        return [
            'labels' => $labels,
            'data' => $data,
        ];
    }

    private function getRevenueData()
    {
        $labels = [];
        $data = [];

        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::today()->subDays($i);
            $labels[] = $date->format('M d');
            $data[] = WalletTransaction::where('transaction_type', 'credit')
                ->where('status', 'completed')
                ->whereDate('created_at', $date)
                ->sum('amount');
        }

        return [
            'labels' => $labels,
            'data' => $data,
        ];
    }
}
