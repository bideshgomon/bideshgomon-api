<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    public function index(): Response
    {
        // Add logic later to fetch stats (e.g., user count, job count)
        return Inertia::render('Admin/Dashboard/Index');
    }
}