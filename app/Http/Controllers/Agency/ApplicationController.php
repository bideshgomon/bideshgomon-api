<?php

namespace App\Http\Controllers\Agency;

use App\Http\Controllers\Controller;
use App\Models\ServiceApplication;
use App\Models\AgencyCountryAssignment;
use App\Models\Agency;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ApplicationController extends Controller
{
    /**
     * Display a listing of applications for this agency
     */
    public function index(Request $request)
    {
        $agency = auth()->user()->agency;

        if (!$agency) {
            abort(403, 'Agency not found');
        }

        // Get countries this agency is assigned to
        $assignedCountryIds = AgencyCountryAssignment::where('agency_id', $agency->id)
            ->pluck('country_id')
            ->toArray();

        $query = ServiceApplication::with(['user', 'serviceModule', 'touristVisa.destinationCountry', 'quotes']);

        // Filter by status
        if ($request->has('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        // Show either assigned applications OR available ones from assigned countries
        if ($request->filter === 'available') {
            $query->whereNull('agency_id')
                ->where('status', 'pending');
        } else {
            $query->where('agency_id', $agency->id);
        }

        // Search by application number or user name
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('application_number', 'like', "%{$search}%")
                  ->orWhereHas('user', function($q) use ($search) {
                      $q->where('name', 'like', "%{$search}%");
                  });
            });
        }

        $applications = $query->latest()->paginate(20)->withQueryString();

        // Filter available applications by country
        if ($request->filter === 'available') {
            $applications->getCollection()->transform(function($app) use ($assignedCountryIds) {
                $countryId = $app->application_data['destination_country_id'] ?? null;
                if ($countryId && in_array($countryId, $assignedCountryIds)) {
                    return $app;
                }
                return null;
            })->filter();
        }

        // Calculate stats
        $stats = [
            'pending' => ServiceApplication::whereNull('agency_id')
                ->where('status', 'pending')
                ->count(),
            'my_applications' => ServiceApplication::where('agency_id', $agency->id)
                ->count(),
            'quoted' => ServiceApplication::where('agency_id', $agency->id)
                ->whereHas('quotes', function($q) use ($agency) {
                    $q->where('agency_id', $agency->id)
                      ->where('status', 'pending');
                })
                ->count(),
            'accepted' => ServiceApplication::where('agency_id', $agency->id)
                ->where('status', 'accepted')
                ->count(),
        ];

        return Inertia::render('Agency/Applications/Index', [
            'applications' => $applications,
            'filters' => $request->only(['status', 'filter', 'search']),
            'stats' => $stats,
        ]);
    }

    /**
     * Display the specified application
     */
    public function show(ServiceApplication $application)
    {
        $agency = auth()->user()->agency;

        if (!$agency) {
            // Auto-create agency profile if not exists
            $agency = Agency::create([
                'user_id' => auth()->id(),
                'name' => auth()->user()->name . "'s Agency",
                'email' => auth()->user()->email,
                'is_active' => true,
                'is_verified' => false,
            ]);
        }

        // Check if agency can view this application
        if ($application->agency_id !== null && $application->agency_id !== $agency->id) {
            abort(403, 'Unauthorized to view this application');
        }

        $application->load([
            'user',
            'serviceModule',
            'touristVisa.destinationCountry',
            'touristVisa.documents',
            'quotes' => function($query) use ($agency) {
                $query->where('agency_id', $agency->id);
            }
        ]);

        return Inertia::render('Agency/Applications/Show', [
            'application' => $application,
        ]);
    }

    /**
     * Update application status
     */
    public function updateStatus(Request $request, ServiceApplication $application)
    {
        $agency = auth()->user()->agency;

        if (!$agency || $application->agency_id !== $agency->id) {
            abort(403, 'Unauthorized');
        }

        $validated = $request->validate([
            'status' => 'required|in:in_progress,completed,cancelled',
            'notes' => 'nullable|string|max:1000',
        ]);

        $application->update([
            'status' => $validated['status'],
            'special_notes' => $validated['notes'] ?? $application->special_notes,
        ]);

        // Update the linked TouristVisa status if exists
        if ($application->touristVisa) {
            $touristVisaStatus = match($validated['status']) {
                'in_progress' => 'processing',
                'completed' => 'approved',
                'cancelled' => 'cancelled',
                default => $application->touristVisa->status,
            };
            
            $application->touristVisa->update(['status' => $touristVisaStatus]);
        }

        return redirect()->back()->with('success', 'Application status updated successfully.');
    }
}
