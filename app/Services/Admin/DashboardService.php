<?php

// app/Services/Admin/DashboardService.php

namespace App\Services\Admin;

use App\Models\Role;
use App\Models\User;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class DashboardService
{
    /**
     * Get the aggregated statistics for the admin dashboard.
     * Caches the result for 10 minutes.
     */
    public function getDashboardStats(): array
    {
        try {
            return Cache::remember('admin_dashboard_stats', 600, function () {
                // Fetch role IDs once to perform efficient counts
                $roles = Role::whereIn('slug', ['admin', 'agency', 'consultant', 'user'])
                    ->pluck('id', 'slug');

                $totalUsers = User::where('role_id', $roles['user'] ?? null)->count();
                $totalAgencies = User::where('role_id', $roles['agency'] ?? null)->count();
                $totalConsultants = User::where('role_id', $roles['consultant'] ?? null)->count();
                $totalAdmins = User::where('role_id', $roles['admin'] ?? null)->count();

                // Placeholders for future Phase 1 services
                // We will update these as we build the Visa and Payment modules
                $pendingApplications = 0; // e.g., WorkVisaApplication::where('status', 'pending')->count();
                $totalRevenue = 0; // e.g., Payment::sum('amount');

                return [
                    'totalUsers' => $totalUsers,
                    'totalAgencies' => $totalAgencies,
                    'totalConsultants' => $totalConsultants,
                    'totalAdmins' => $totalAdmins,
                    'pendingApplications' => $pendingApplications,
                    'totalRevenue' => $totalRevenue, // The Vue component should format this (e.g., as $0)
                ];
            });
        } catch (\Exception $e) {
            // In case of a DB or Cache error, log it and return empty stats
            Log::error('Failed to retrieve admin dashboard stats', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ]);

            // Return a default, safe structure
            return [
                'totalUsers' => 0,
                'totalAgencies' => 0,
                'totalConsultants' => 0,
                'totalAdmins' => 0,
                'pendingApplications' => 0,
                'totalRevenue' => 0,
            ];
        }
    }
}
