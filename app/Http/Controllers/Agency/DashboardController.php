<?php

namespace App\Http\Controllers\Agency;

use App\Http\Controllers\Controller;
use App\Models\ServiceApplication;
use App\Models\ServiceQuote;
use App\Models\AgencyCountryAssignment;
use Illuminate\Http\Request;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function index()
    {
        $agency = auth()->user()->agency;

        if (!$agency) {
            abort(403, 'Agency not found');
        }

        // Get countries this agency is assigned to
        $assignedCountryIds = AgencyCountryAssignment::where('agency_id', $agency->id)
            ->pluck('country_id')
            ->toArray();

        $stats = [
            // Applications already assigned to this agency
            'my_pending' => ServiceApplication::where('agency_id', $agency->id)
                ->where('status', 'pending')
                ->count(),
            'my_active' => ServiceApplication::where('agency_id', $agency->id)
                ->whereIn('status', ['assigned', 'in_progress', 'accepted'])
                ->count(),
            'my_completed' => ServiceApplication::where('agency_id', $agency->id)
                ->where('status', 'completed')
                ->count(),
            
            // Unassigned applications from countries this agency serves
            'available_applications' => ServiceApplication::whereNull('agency_id')
                ->where('status', 'pending')
                ->whereHas('serviceModule', function($query) use ($assignedCountryIds) {
                    $query->whereJsonContains('application_data->destination_country_id', $assignedCountryIds);
                })
                ->count(),
                
            'pending_quotes' => ServiceQuote::where('agency_id', $agency->id)
                ->where('status', 'pending')
                ->count(),
            'total_earnings' => ServiceApplication::where('agency_id', $agency->id)
                ->where('status', 'completed')
                ->sum('agency_earnings'),
        ];

        // Show recent applications from countries this agency serves
        $availableApplications = ServiceApplication::with(['user', 'serviceModule', 'touristVisa.destinationCountry'])
            ->whereNull('agency_id')
            ->where('status', 'pending')
            ->whereHas('serviceModule', function($query) use ($assignedCountryIds) {
                // For now, we'll filter by checking application_data JSON
                // In future, we might add a direct country_id field
            })
            ->latest()
            ->take(10)
            ->get()
            ->filter(function($app) use ($assignedCountryIds) {
                // Filter by destination country in application_data
                $countryId = $app->application_data['destination_country_id'] ?? null;
                return $countryId && in_array($countryId, $assignedCountryIds);
            });

        $myApplications = ServiceApplication::with(['user', 'serviceModule', 'touristVisa.destinationCountry'])
            ->where('agency_id', $agency->id)
            ->latest()
            ->take(10)
            ->get();

        return Inertia::render('Agency/Dashboard', [
            'stats' => $stats,
            'availableApplications' => $availableApplications->values(),
            'myApplications' => $myApplications,
            'assignedCountries' => $assignedCountryIds,
        ]);
    }
}
