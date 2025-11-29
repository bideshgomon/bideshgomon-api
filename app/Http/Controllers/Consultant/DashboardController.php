<?php

namespace App\Http\Controllers\Consultant;

use App\Http\Controllers\Controller;
use App\Models\ServiceApplication;
use App\Models\AgencyTeamMember;
use Illuminate\Http\Request;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $agency = $user->agency;

        // Get consultant's team member record
        $teamMember = AgencyTeamMember::where('agency_id', $agency->id)
            ->where('user_id', $user->id)
            ->first();

        // Get permissions
        $permissions = $teamMember ? $teamMember->permissions : [];

        // Get agency applications (consultants see all agency applications)
        $applications = ServiceApplication::where('agency_id', $agency->id)
            ->with(['user', 'serviceModule', 'quotes'])
            ->latest()
            ->take(10)
            ->get();

        // Calculate stats
        $stats = [
            'total_applications' => ServiceApplication::where('agency_id', $agency->id)->count(),
            'pending_applications' => ServiceApplication::where('agency_id', $agency->id)
                ->where('status', 'pending')
                ->count(),
            'completed_applications' => ServiceApplication::where('agency_id', $agency->id)
                ->where('status', 'completed')
                ->count(),
            'my_role' => $teamMember ? $teamMember->role : 'consultant',
        ];

        return Inertia::render('Consultant/Dashboard', [
            'stats' => $stats,
            'recentApplications' => $applications,
            'permissions' => $permissions,
            'agency' => $agency,
            'teamMember' => $teamMember,
        ]);
    }
}
