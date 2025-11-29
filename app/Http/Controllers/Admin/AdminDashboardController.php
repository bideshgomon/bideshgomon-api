<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\WalletTransaction;
use App\Models\TravelInsuranceBooking;
use App\Models\UserCv;
use App\Models\HotelBooking;
use App\Models\FlightRequest;
use App\Models\VisaApplication;
use App\Models\AdminImpersonationLog;
use App\Models\JobApplication;
use App\Models\ProfileAssessment;
use App\Models\ProfileView;
use App\Models\SupportTicket;
use App\Models\Appointment;
use App\Models\MarketingCampaign;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\DB;

class AdminDashboardController extends Controller
{
    public function index()
    {
        // User Statistics
        $totalUsers = User::count();
        $activeUsers = User::where('created_at', '>=', now()->subDays(30))->count();
        $newUsersToday = User::whereDate('created_at', today())->count();
        $newUsersThisMonth = User::whereMonth('created_at', now()->month)->count();

        // Revenue Statistics
        $totalRevenue = WalletTransaction::where('transaction_type', 'credit')
            ->where('description', 'like', '%payment%')
            ->sum('amount');
        $revenueToday = WalletTransaction::where('transaction_type', 'credit')
            ->where('description', 'like', '%payment%')
            ->whereDate('created_at', today())
            ->sum('amount');
        $revenueThisMonth = WalletTransaction::where('transaction_type', 'credit')
            ->where('description', 'like', '%payment%')
            ->whereMonth('created_at', now()->month)
            ->sum('amount');

        // Service Statistics
        $totalInsuranceBookings = TravelInsuranceBooking::count();
        $insuranceBookingsToday = TravelInsuranceBooking::whereDate('created_at', today())->count();
        $totalCvsCreated = UserCv::count();
        $cvsCreatedToday = UserCv::whereDate('created_at', today())->count();
        
        // Hotel Booking Statistics
        $totalHotelBookings = HotelBooking::count();
        $hotelBookingsToday = HotelBooking::whereDate('created_at', today())->count();
        $confirmedHotelBookings = HotelBooking::where('status', 'confirmed')->count();
        $hotelRevenueThisMonth = HotelBooking::where('payment_status', 'paid')
            ->whereMonth('created_at', now()->month)
            ->sum('total_amount');
        
        // Flight Request Statistics
        $totalFlightRequests = FlightRequest::count();
        $flightRequestsToday = FlightRequest::whereDate('created_at', today())->count();
        $pendingFlightRequests = FlightRequest::where('status', 'pending')->count();

        // Visa Application Statistics
        $totalVisaApplications = VisaApplication::count();
        $visaApplicationsToday = VisaApplication::whereDate('created_at', today())->count();
        $pendingVisaApplications = VisaApplication::where('status', 'pending')->count();
        $approvedVisaApplications = VisaApplication::where('status', 'approved')->count();
        $visaRevenueThisMonth = VisaApplication::where('payment_status', 'paid')
            ->whereMonth('created_at', now()->month)
            ->sum('total_amount');

        // Wallet Statistics
        $totalWalletBalance = DB::table('wallets')->sum('balance');
        $totalTransactions = WalletTransaction::count();
        $pendingWithdrawals = WalletTransaction::where('transaction_type', 'debit')
            ->where('status', 'pending')
            ->count();

        // Job Application Statistics
        $totalJobApplications = JobApplication::count();
        $jobApplicationsToday = JobApplication::whereDate('created_at', today())->count();
        $pendingJobApplications = JobApplication::where('status', 'pending')->count();
        $shortlistedJobApplications = JobApplication::where('status', 'shortlisted')->count();

        // Profile Assessment Statistics
        $totalAssessments = ProfileAssessment::count();
        $assessmentsToday = ProfileAssessment::whereDate('assessed_at', today())->count();
        $averageOverallScore = ProfileAssessment::avg('overall_score');
        $highRiskProfiles = ProfileAssessment::where('risk_level', 'high')->count();

        // Public Profile Statistics
        $publicProfiles = User::where('profile_is_public', true)->count();
        $totalProfileViews = ProfileView::count();
        $profileViewsToday = ProfileView::whereDate('viewed_at', today())->count();

        // Support Tickets Statistics
        $totalTickets = SupportTicket::count();
        $ticketsToday = SupportTicket::whereDate('created_at', today())->count();
        $openTickets = SupportTicket::where('status', 'open')->count();
        $urgentTickets = SupportTicket::where('priority', 'urgent')->count();

        // Appointments Statistics
        $totalAppointments = Appointment::count();
        $appointmentsToday = Appointment::whereDate('appointment_date', today())->count();
        $pendingAppointments = Appointment::where('status', 'pending')->count();
        $confirmedAppointments = Appointment::where('status', 'confirmed')->count();

        // Marketing Campaigns Statistics
        $totalCampaigns = MarketingCampaign::count();
        $activeCampaigns = MarketingCampaign::where('status', 'sending')->orWhere('status', 'scheduled')->count();
        $completedCampaigns = MarketingCampaign::where('status', 'sent')->count();
        $campaignReachThisMonth = MarketingCampaign::whereMonth('created_at', now()->month)->sum('total_recipients');

        // Recent Activities
        $recentUsers = User::with('role')->latest()->take(10)->get(['id', 'name', 'email', 'created_at', 'role_id']);
        $recentTransactions = WalletTransaction::with(['wallet' => function($query) {
                $query->select('id', 'user_id', 'balance');
            }])
            ->latest()
            ->take(10)
            ->get();
        $recentBookings = TravelInsuranceBooking::with(['package'])
            ->latest()
            ->take(10)
            ->get();
        $recentHotelBookings = HotelBooking::with(['hotel' => function($query) {
                $query->select('id', 'name');
            }, 'room' => function($query) {
                $query->select('id', 'room_type');
            }])
            ->latest()
            ->take(5)
            ->get();
        $recentVisaApplications = VisaApplication::latest()
            ->take(5)
            ->get();

        // Chart Data - Last 7 days user registrations
        $userChartData = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i);
            $userChartData[] = [
                'date' => $date->format('M d'),
                'count' => User::whereDate('created_at', $date)->count()
            ];
        }

        // Revenue Chart - Last 7 days
        $revenueChartData = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i);
            $revenueChartData[] = [
                'date' => $date->format('M d'),
                'amount' => WalletTransaction::where('transaction_type', 'credit')
                    ->where('description', 'like', '%payment%')
                    ->whereDate('created_at', $date)
                    ->sum('amount')
            ];
        }

        // Recent Admin Impersonations (Security / Audit Widget)
        $recentImpersonations = AdminImpersonationLog::with([
                'impersonator:id,name',
                'target:id,name'
            ])
            ->latest('started_at')
            ->limit(10)
            ->get()
            ->map(function ($log) {
                return [
                    'id' => $log->id,
                    'impersonator' => $log->impersonator ? [
                        'id' => $log->impersonator->id,
                        'name' => $log->impersonator->name,
                    ] : null,
                    'target' => $log->target ? [
                        'id' => $log->target->id,
                        'name' => $log->target->name,
                    ] : null,
                    'purpose' => $log->purpose,
                    'started_at' => $log->started_at, // Carbon -> serialized ISO8601 by Inertia
                    'ended_at' => $log->ended_at,
                    'duration_minutes' => $log->ended_at ? $log->ended_at->diffInMinutes($log->started_at) : null,
                    'status' => $log->ended_at ? 'ended' : 'active',
                ];
            });

        return Inertia::render('Admin/Dashboard', [
            'stats' => [
                'users' => [
                    'total' => $totalUsers,
                    'active' => $activeUsers,
                    'today' => $newUsersToday,
                    'this_month' => $newUsersThisMonth,
                ],
                'revenue' => [
                    'total' => $totalRevenue,
                    'today' => $revenueToday,
                    'this_month' => $revenueThisMonth,
                ],
                'services' => [
                    'insurance_bookings' => $totalInsuranceBookings,
                    'insurance_today' => $insuranceBookingsToday,
                    'cvs_created' => $totalCvsCreated,
                    'cvs_today' => $cvsCreatedToday,
                    'hotel_bookings' => $totalHotelBookings,
                    'hotel_bookings_today' => $hotelBookingsToday,
                    'confirmed_hotel_bookings' => $confirmedHotelBookings,
                    'hotel_revenue_month' => $hotelRevenueThisMonth,
                    'flight_requests' => $totalFlightRequests,
                    'flight_requests_today' => $flightRequestsToday,
                    'pending_flight_requests' => $pendingFlightRequests,
                    'visa_applications' => $totalVisaApplications,
                    'visa_applications_today' => $visaApplicationsToday,
                    'pending_visa_applications' => $pendingVisaApplications,
                    'approved_visa_applications' => $approvedVisaApplications,
                    'visa_revenue_month' => $visaRevenueThisMonth,
                ],
                'wallet' => [
                    'total_balance' => $totalWalletBalance,
                    'total_transactions' => $totalTransactions,
                    'pending_withdrawals' => $pendingWithdrawals,
                ],
                'jobs' => [
                    'total_applications' => $totalJobApplications,
                    'applications_today' => $jobApplicationsToday,
                    'pending_applications' => $pendingJobApplications,
                    'shortlisted_applications' => $shortlistedJobApplications,
                ],
                'assessments' => [
                    'total_assessments' => $totalAssessments,
                    'assessments_today' => $assessmentsToday,
                    'average_score' => round($averageOverallScore, 2),
                    'high_risk_profiles' => $highRiskProfiles,
                ],
                'public_profiles' => [
                    'total_public' => $publicProfiles,
                    'total_views' => $totalProfileViews,
                    'views_today' => $profileViewsToday,
                ],
                'support' => [
                    'total_tickets' => $totalTickets,
                    'tickets_today' => $ticketsToday,
                    'open_tickets' => $openTickets,
                    'urgent_tickets' => $urgentTickets,
                ],
                'appointments' => [
                    'total_appointments' => $totalAppointments,
                    'appointments_today' => $appointmentsToday,
                    'pending_appointments' => $pendingAppointments,
                    'confirmed_appointments' => $confirmedAppointments,
                ],
                'campaigns' => [
                    'total_campaigns' => $totalCampaigns,
                    'active_campaigns' => $activeCampaigns,
                    'completed_campaigns' => $completedCampaigns,
                    'campaign_reach_month' => $campaignReachThisMonth,
                ],
            ],
            'recentUsers' => $recentUsers,
            'recentTransactions' => $recentTransactions,
            'recentBookings' => $recentBookings,
            'recentHotelBookings' => $recentHotelBookings,
            'recentVisaApplications' => $recentVisaApplications,
            'userChartData' => $userChartData,
            'revenueChartData' => $revenueChartData,
            'recentImpersonations' => $recentImpersonations,
        ]);
    }
}
