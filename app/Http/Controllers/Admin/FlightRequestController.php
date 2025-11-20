<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\FlightRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;

class FlightRequestController extends Controller
{
    /**
     * Display all flight requests (admin view)
     */
    public function index(Request $request)
    {
        $filter = $request->get('filter', 'all');
        $search = $request->get('search');

        $query = FlightRequest::with(['user', 'quotes', 'assignedTo'])
            ->orderBy('created_at', 'desc');

        if ($filter !== 'all') {
            $query->where('status', $filter);
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('request_reference', 'like', "%{$search}%")
                  ->orWhere('origin_airport_code', 'like', "%{$search}%")
                  ->orWhere('destination_airport_code', 'like', "%{$search}%")
                  ->orWhereHas('user', function ($q) use ($search) {
                      $q->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                  });
            });
        }

        $flightRequests = $query->paginate(15);

        // Get agencies for assignment dropdown
        $agencies = User::whereHas('role', function ($q) {
            $q->where('slug', 'agency');
        })->get(['id', 'name', 'email']);

        // Summary statistics
        $stats = [
            'total' => FlightRequest::count(),
            'pending' => FlightRequest::where('status', 'pending')->count(),
            'assigned' => FlightRequest::where('status', 'assigned')->count(),
            'quoted' => FlightRequest::where('status', 'quoted')->count(),
            'completed' => FlightRequest::where('status', 'completed')->count(),
        ];

        return Inertia::render('Admin/FlightRequests/Index', [
            'flightRequests' => $flightRequests,
            'agencies' => $agencies,
            'filter' => $filter,
            'search' => $search,
            'stats' => $stats,
        ]);
    }

    /**
     * Show single flight request details
     */
    public function show($id)
    {
        $flightRequest = FlightRequest::with(['user', 'quotes.quotedBy', 'assignedTo'])
            ->findOrFail($id);

        $agencies = User::whereHas('role', function ($q) {
            $q->where('slug', 'agency');
        })->get(['id', 'name', 'email']);

        return Inertia::render('Admin/FlightRequests/Show', [
            'flightRequest' => $flightRequest,
            'agencies' => $agencies,
        ]);
    }

    /**
     * Assign request to agency
     */
    public function assign(Request $request, $id)
    {
        $validated = $request->validate([
            'agency_id' => 'required|exists:users,id',
        ]);

        $flightRequest = FlightRequest::findOrFail($id);

        // Verify the assigned user is an agency
        $agency = User::whereHas('role', function ($q) {
            $q->where('slug', 'agency');
        })->findOrFail($validated['agency_id']);

        $flightRequest->update([
            'assigned_to' => $agency->id,
            'assigned_at' => now(),
            'status' => 'assigned',
        ]);

        return back()->with('success', 'Flight request assigned to ' . $agency->name . ' successfully.');
    }

    /**
     * Update admin notes
     */
    public function updateNotes(Request $request, $id)
    {
        $validated = $request->validate([
            'admin_notes' => 'required|string',
        ]);

        $flightRequest = FlightRequest::findOrFail($id);
        $flightRequest->update($validated);

        return back()->with('success', 'Notes updated successfully.');
    }

    /**
     * Cancel request
     */
    public function cancel(Request $request, $id)
    {
        $validated = $request->validate([
            'rejection_reason' => 'required|string',
        ]);

        $flightRequest = FlightRequest::findOrFail($id);

        $flightRequest->update([
            'status' => 'cancelled',
            'rejection_reason' => $validated['rejection_reason'],
        ]);

        return back()->with('success', 'Flight request cancelled.');
    }

    /**
     * Bulk assign requests to agencies
     */
    public function bulkAssign(Request $request)
    {
        $validated = $request->validate([
            'request_ids' => 'required|array',
            'request_ids.*' => 'exists:flight_requests,id',
            'agency_id' => 'required|exists:users,id',
        ]);

        $agency = User::whereHas('role', function ($q) {
            $q->where('slug', 'agency');
        })->findOrFail($validated['agency_id']);

        FlightRequest::whereIn('id', $validated['request_ids'])
            ->update([
                'assigned_to' => $agency->id,
                'assigned_at' => now(),
                'status' => 'assigned',
            ]);

        $count = count($validated['request_ids']);

        return back()->with('success', "{$count} request(s) assigned to {$agency->name} successfully.");
    }
}
