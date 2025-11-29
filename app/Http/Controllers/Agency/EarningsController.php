<?php

namespace App\Http\Controllers\Agency;

use App\Http\Controllers\Controller;
use App\Models\Agency;
use App\Models\ServiceApplication;
use App\Models\ServiceQuote;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class EarningsController extends Controller
{
    public function index(Request $request)
    {
        $agency = auth()->user()->agency;

        if (!$agency) {
            // Auto-create agency profile if not exists
            $agency = Agency::create([
                'user_id' => auth()->id(),
                'name' => auth()->user()->name . "'s Agency",
                'email' => auth()->user()->email,
                'is_active' => true,
                'is_verified' => false,
            ]);
        }

        // Date filter (default: last 30 days)
        $period = $request->get('period', '30');
        $startDate = now()->subDays((int)$period);

        // Financial Overview
        $financials = [
            'total_earnings' => ServiceApplication::where('agency_id', $agency->id)
                ->where('status', 'completed')
                ->sum('agency_earnings'),
            
            'pending_earnings' => ServiceApplication::where('agency_id', $agency->id)
                ->whereIn('status', ['accepted', 'in_progress'])
                ->sum('agency_earnings'),
            
            'total_quotes' => ServiceQuote::where('agency_id', $agency->id)
                ->count(),
            
            'accepted_quotes' => ServiceQuote::where('agency_id', $agency->id)
                ->where('status', 'accepted')
                ->count(),
            
            'pending_quotes' => ServiceQuote::where('agency_id', $agency->id)
                ->where('status', 'pending')
                ->count(),
            
            'rejected_quotes' => ServiceQuote::where('agency_id', $agency->id)
                ->where('status', 'rejected')
                ->count(),
        ];

        // Win Rate
        $financials['win_rate'] = $financials['total_quotes'] > 0 
            ? round(($financials['accepted_quotes'] / $financials['total_quotes']) * 100, 1)
            : 0;

        // Average Quote Amount
        $financials['avg_quote_amount'] = ServiceQuote::where('agency_id', $agency->id)
            ->avg('quoted_amount') ?? 0;

        // Period-based earnings (for chart)
        $periodEarnings = ServiceApplication::where('agency_id', $agency->id)
            ->where('status', 'completed')
            ->where('completed_at', '>=', $startDate)
            ->selectRaw('DATE(completed_at) as date, SUM(agency_earnings) as earnings')
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        // Monthly breakdown (last 6 months)
        $monthlyBreakdown = ServiceApplication::where('agency_id', $agency->id)
            ->where('status', 'completed')
            ->where('completed_at', '>=', now()->subMonths(6))
            ->selectRaw('strftime("%Y-%m", completed_at) as month, COUNT(*) as count, SUM(agency_earnings) as earnings')
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        // Top services by earnings
        $topServices = ServiceApplication::where('agency_id', $agency->id)
            ->where('status', 'completed')
            ->with('serviceModule')
            ->selectRaw('service_module_id, COUNT(*) as count, SUM(agency_earnings) as earnings')
            ->groupBy('service_module_id')
            ->orderByDesc('earnings')
            ->limit(5)
            ->get();

        // Recent completed applications
        $recentCompletions = ServiceApplication::with(['user', 'serviceModule', 'touristVisa.destinationCountry'])
            ->where('agency_id', $agency->id)
            ->where('status', 'completed')
            ->latest('completed_at')
            ->take(10)
            ->get();

        // Pending payments (accepted but not completed)
        $pendingPayments = ServiceApplication::with(['user', 'serviceModule'])
            ->where('agency_id', $agency->id)
            ->whereIn('status', ['accepted', 'in_progress'])
            ->orderBy('accepted_at', 'desc')
            ->get();

        return Inertia::render('Agency/Earnings/Index', [
            'financials' => $financials,
            'periodEarnings' => $periodEarnings,
            'monthlyBreakdown' => $monthlyBreakdown,
            'topServices' => $topServices,
            'recentCompletions' => $recentCompletions,
            'pendingPayments' => $pendingPayments,
            'period' => $period,
        ]);
    }
}
