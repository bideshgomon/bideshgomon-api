<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\City;        // <-- Add City
use App\Models\Country;     // <-- Add Country
use App\Models\Course;
use App\Models\JobPosting;
use App\Models\State;       // <-- Add State
use App\Models\University;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    /**
     * Display the admin dashboard.
     *
     * @return \Inertia\Response
     */
    public function index(): Response
    {
        // Fetch counts for various models
        $stats = [
            ['name' => 'Total Users', 'count' => User::count(), 'href' => '#'], // No user management page yet
            ['name' => 'Total Universities', 'count' => University::count(), 'href' => route('admin.universities.index')],
            ['name' => 'Total Courses', 'count' => Course::count(), 'href' => route('admin.courses.index')],
            ['name' => 'Total Job Postings', 'count' => JobPosting::count(), 'href' => route('admin.jobs.index')],
            ['name' => 'Total Countries', 'count' => Country::count(), 'href' => route('admin.countries.index')], // <-- Add Country stat
            ['name' => 'Total States', 'count' => State::count(), 'href' => route('admin.states.index')],       // <-- Add State stat
            ['name' => 'Total Cities', 'count' => City::count(), 'href' => route('admin.cities.index')],         // <-- Add City stat
        ];

        // Render the Inertia view with the stats data
        return Inertia::render('Admin/Dashboard/Index', [
            'stats' => $stats,
        ]);
    }
}