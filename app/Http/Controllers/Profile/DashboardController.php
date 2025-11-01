<?php

namespace App\Http\Controllers\Profile;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    /**
     * Display the user's profile dashboard.
     */
    public function index(Request $request): Response
    {
        // We can pass any necessary data to the dashboard component here.
        // For now, we will just render the page.
        // The 'Dashboard.vue' component is located in 'resources/js/Pages/'

        return Inertia::render('Dashboard');
    }
}
