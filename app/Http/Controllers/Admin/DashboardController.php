<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\JobPosting;
use App\Models\University;
use App\Models\User;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    public function index(): Response
    {
        // Fetch stats
        $stats = [
            'total_users' => User::where('role_id', '!=', 1)->count(), // Count non-admins
            'total_jobs' => JobPosting::count(),
            'total_universities' => University::count(),
            'total_courses' => Course::count(),
        ];
        
        // Fetch recent users (e.g., last 5)
        $recentUsers = User::where('role_id', '!=', 1)
            ->with('profile') // Eager load profile if it exists
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get(['id', 'name', 'email', 'created_at']);

        return Inertia::render('Admin/Dashboard/Index', [
            'stats' => $stats,
            'recentUsers' => $recentUsers,
        ]);
    }
}