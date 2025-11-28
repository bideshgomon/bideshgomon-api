<?php

namespace App\Http\Controllers\Agency;

use App\Http\Controllers\Controller;
use App\Models\AgencyCountryAssignment;
use Illuminate\Http\Request;
use Inertia\Inertia;

class CountryAssignmentController extends Controller
{
    /**
     * Display agency's country assignments.
     */
    public function index()
    {
        $agency = auth()->user()->agency;

        if (!$agency) {
            abort(403, 'Agency profile not found');
        }

        $assignments = AgencyCountryAssignment::with(['country', 'visaType', 'serviceModule'])
            ->where('agency_id', $agency->id)
            ->orderBy('is_active', 'desc')
            ->orderBy('assigned_at', 'desc')
            ->paginate(20);

        $stats = [
            'total_countries' => AgencyCountryAssignment::where('agency_id', $agency->id)
                ->distinct('country_id')
                ->count('country_id'),
            'active_assignments' => AgencyCountryAssignment::where('agency_id', $agency->id)
                ->where('is_active', true)
                ->count(),
            'total_applications' => AgencyCountryAssignment::where('agency_id', $agency->id)
                ->sum('total_applications'),
            'total_revenue' => AgencyCountryAssignment::where('agency_id', $agency->id)
                ->sum('total_revenue'),
        ];

        return Inertia::render('Agency/CountryAssignments/Index', [
            'assignments' => $assignments,
            'stats' => $stats,
        ]);
    }
}
