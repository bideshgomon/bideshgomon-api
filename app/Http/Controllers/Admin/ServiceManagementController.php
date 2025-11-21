<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\JobApplication;
use App\Models\ProfileAssessment;
use App\Models\User;
use App\Models\TravelInsuranceBooking;
use App\Models\CV;
use App\Models\HotelBooking;
use App\Models\FlightRequest;
use App\Models\VisaApplication;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\DB;

class ServiceManagementController extends Controller
{
    public function index()
    {
        // Job Applications Statistics
        $jobApplicationStats = [
            'total' => JobApplication::count(),
            'pending' => JobApplication::where('status', 'pending')->count(),
            'shortlisted' => JobApplication::where('status', 'shortlisted')->count(),
            'approved' => JobApplication::where('status', 'approved')->count(),
            'rejected' => JobApplication::where('status', 'rejected')->count(),
            'today' => JobApplication::whereDate('created_at', today())->count(),
            'this_week' => JobApplication::whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])->count(),
            'this_month' => JobApplication::whereMonth('created_at', now()->month)->count(),
        ];

        // Profile Assessments Statistics
        $profileAssessmentStats = [
            'total' => ProfileAssessment::count(),
            'today' => ProfileAssessment::whereDate('assessed_at', today())->count(),
            'this_week' => ProfileAssessment::whereBetween('assessed_at', [now()->startOfWeek(), now()->endOfWeek()])->count(),
            'average_score' => round(ProfileAssessment::avg('overall_score'), 1),
            'high_risk' => ProfileAssessment::where('risk_level', 'high')->count(),
            'medium_risk' => ProfileAssessment::where('risk_level', 'medium')->count(),
            'low_risk' => ProfileAssessment::where('risk_level', 'low')->count(),
            'score_distribution' => $this->getScoreDistribution(),
        ];

        // Public Profiles Statistics
        $publicProfileStats = [
            'total_public' => User::where('profile_is_public', true)->count(),
            'total_private' => User::where('profile_is_public', false)->count(),
            'total_views' => DB::table('profile_views')->count(),
            'views_today' => DB::table('profile_views')->whereDate('viewed_at', today())->count(),
            'views_this_week' => DB::table('profile_views')->whereBetween('viewed_at', [now()->startOfWeek(), now()->endOfWeek()])->count(),
            'average_views_per_profile' => User::where('profile_is_public', true)->count() > 0
                ? round(DB::table('profile_views')->count() / User::where('profile_is_public', true)->count(), 1)
                : 0,
            'top_viewed_profiles' => $this->getTopViewedProfiles(),
        ];

        // Travel Insurance Statistics
        $insuranceStats = [
            'total' => TravelInsuranceBooking::count(),
            'pending' => TravelInsuranceBooking::where('status', 'pending')->count(),
            'confirmed' => TravelInsuranceBooking::where('status', 'confirmed')->count(),
            'cancelled' => TravelInsuranceBooking::where('status', 'cancelled')->count(),
            'today' => TravelInsuranceBooking::whereDate('created_at', today())->count(),
            'revenue_month' => TravelInsuranceBooking::where('status', 'confirmed')
                ->whereMonth('created_at', now()->month)
                ->sum('amount'),
        ];

        // CV Builder Statistics
        $cvStats = [
            'total' => CV::count(),
            'today' => CV::whereDate('created_at', today())->count(),
            'this_week' => CV::whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])->count(),
            'downloads_month' => CV::whereMonth('created_at', now()->month)->count(),
        ];

        // Hotel Bookings Statistics
        $hotelStats = [
            'total' => HotelBooking::count(),
            'pending' => HotelBooking::where('status', 'pending')->count(),
            'confirmed' => HotelBooking::where('status', 'confirmed')->count(),
            'cancelled' => HotelBooking::where('status', 'cancelled')->count(),
            'today' => HotelBooking::whereDate('created_at', today())->count(),
            'revenue_month' => HotelBooking::where('status', 'confirmed')
                ->whereMonth('created_at', now()->month)
                ->sum('total_amount'),
        ];

        // Flight Requests Statistics
        $flightStats = [
            'total' => FlightRequest::count(),
            'pending' => FlightRequest::where('status', 'pending')->count(),
            'confirmed' => FlightRequest::where('status', 'confirmed')->count(),
            'cancelled' => FlightRequest::where('status', 'cancelled')->count(),
            'today' => FlightRequest::whereDate('created_at', today())->count(),
        ];

        // Visa Applications Statistics
        $visaStats = [
            'total' => VisaApplication::count(),
            'pending' => VisaApplication::where('status', 'pending')->count(),
            'approved' => VisaApplication::where('status', 'approved')->count(),
            'rejected' => VisaApplication::where('status', 'rejected')->count(),
            'today' => VisaApplication::whereDate('created_at', today())->count(),
            'revenue_month' => VisaApplication::where('status', 'approved')
                ->whereMonth('created_at', now()->month)
                ->sum('service_fee'),
        ];

        // Recent Activities - All Services
        $recentJobApplications = JobApplication::with(['user', 'job'])
            ->latest()
            ->take(5)
            ->get();

        $recentAssessments = ProfileAssessment::with('user')
            ->latest('assessed_at')
            ->take(5)
            ->get();

        $recentPublicProfiles = User::where('profile_is_public', true)
            ->withCount('profileViews')
            ->latest('updated_at')
            ->take(5)
            ->get();

        // Service Performance Chart Data (Last 7 Days)
        $serviceChartData = $this->getServiceChartData();

        return Inertia::render('Admin/ServiceManagement', [
            'jobApplicationStats' => $jobApplicationStats,
            'profileAssessmentStats' => $profileAssessmentStats,
            'publicProfileStats' => $publicProfileStats,
            'insuranceStats' => $insuranceStats,
            'cvStats' => $cvStats,
            'hotelStats' => $hotelStats,
            'flightStats' => $flightStats,
            'visaStats' => $visaStats,
            'recentJobApplications' => $recentJobApplications,
            'recentAssessments' => $recentAssessments,
            'recentPublicProfiles' => $recentPublicProfiles,
            'serviceChartData' => $serviceChartData,
        ]);
    }

    private function getScoreDistribution()
    {
        return [
            '90-100' => ProfileAssessment::whereBetween('overall_score', [90, 100])->count(),
            '80-89' => ProfileAssessment::whereBetween('overall_score', [80, 89])->count(),
            '70-79' => ProfileAssessment::whereBetween('overall_score', [70, 79])->count(),
            '60-69' => ProfileAssessment::whereBetween('overall_score', [60, 69])->count(),
            '0-59' => ProfileAssessment::where('overall_score', '<', 60)->count(),
        ];
    }

    private function getTopViewedProfiles()
    {
        return User::where('profile_is_public', true)
            ->withCount('profileViews')
            ->orderByDesc('profile_views_count')
            ->take(5)
            ->get()
            ->map(function ($user) {
                return [
                    'name' => $user->name,
                    'slug' => $user->public_profile_slug,
                    'views' => $user->profile_views_count,
                ];
            });
    }

    private function getServiceChartData()
    {
        $days = [];
        $jobApplicationsData = [];
        $assessmentsData = [];
        $profileViewsData = [];
        $insuranceData = [];
        $hotelData = [];
        $flightData = [];
        $visaData = [];

        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i);
            $days[] = $date->format('M d');

            $jobApplicationsData[] = JobApplication::whereDate('created_at', $date)->count();
            $assessmentsData[] = ProfileAssessment::whereDate('assessed_at', $date)->count();
            $profileViewsData[] = DB::table('profile_views')->whereDate('viewed_at', $date)->count();
            $insuranceData[] = TravelInsuranceBooking::whereDate('created_at', $date)->count();
            $hotelData[] = HotelBooking::whereDate('created_at', $date)->count();
            $flightData[] = FlightRequest::whereDate('created_at', $date)->count();
            $visaData[] = VisaApplication::whereDate('created_at', $date)->count();
        }

        return [
            'labels' => $days,
            'datasets' => [
                [
                    'label' => 'Job Applications',
                    'data' => $jobApplicationsData,
                    'color' => '#3B82F6', // blue-500
                ],
                [
                    'label' => 'Profile Assessments',
                    'data' => $assessmentsData,
                    'color' => '#8B5CF6', // violet-500
                ],
                [
                    'label' => 'Profile Views',
                    'data' => $profileViewsData,
                    'color' => '#10B981', // emerald-500
                ],
                [
                    'label' => 'Insurance Bookings',
                    'data' => $insuranceData,
                    'color' => '#F59E0B', // amber-500
                ],
                [
                    'label' => 'Hotel Bookings',
                    'data' => $hotelData,
                    'color' => '#EF4444', // red-500
                ],
                [
                    'label' => 'Flight Requests',
                    'data' => $flightData,
                    'color' => '#06B6D4', // cyan-500
                ],
                [
                    'label' => 'Visa Applications',
                    'data' => $visaData,
                    'color' => '#EC4899', // pink-500
                ],
            ],
        ];
    }
}
