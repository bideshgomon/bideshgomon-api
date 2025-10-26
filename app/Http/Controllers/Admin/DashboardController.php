<?php
// app/Http/Controllers/Admin/DashboardController.php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\Admin\DashboardService; // Import the service
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response; // Make sure Response is imported

class DashboardController extends Controller
{
    /**
     * @var DashboardService
     */
    protected $dashboardService;

    /**
     * Inject the service via the constructor.
     */
    public function __construct(DashboardService $dashboardService)
    {
        $this->dashboardService = $dashboardService;
    }

    /**
     * Display the admin dashboard.
     */
    public function index(): Response // Use Inertia\Response
    {
        // Call the service to get real, cached stats
        $stats = $this->dashboardService->getDashboardStats();

        // The render path 'Admin/Dashboard/Index' is correct
        return Inertia::render('Admin/Dashboard/Index', [
            'stats' => $stats
        ]);
    }
}