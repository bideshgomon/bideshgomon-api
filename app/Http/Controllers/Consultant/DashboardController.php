<?php

namespace App\Http\Controllers\Consultant;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function index()
    {
        return Inertia::render('Consultant/Dashboard', [
            'stats' => [
                'total_clients' => 0,
                'active_applications' => 0,
                'completed_applications' => 0,
                'total_earnings' => 0,
            ],
        ]);
    }
}
