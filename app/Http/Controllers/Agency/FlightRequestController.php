<?php

namespace App\Http\Controllers\Agency;

use App\Http\Controllers\Controller;
use App\Models\FlightRequest;
use App\Models\FlightQuote;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class FlightRequestController extends Controller
{
    /**
     * Display requests assigned to this agency
     */
    public function index(Request $request)
    {
        $filter = $request->get('filter', 'all');

        $query = FlightRequest::with(['user', 'quotes' => function ($q) {
                $q->where('quoted_by', Auth::id());
            }])
            ->where('assigned_to', Auth::id())
            ->orderBy('created_at', 'desc');

        if ($filter !== 'all') {
            if ($filter === 'needs_quote') {
                $query->where('status', 'assigned')
                    ->whereDoesntHave('quotes', function ($q) {
                        $q->where('quoted_by', Auth::id());
                    });
            } else {
                $query->whereHas('quotes', function ($q) use ($filter) {
                    $q->where('quoted_by', Auth::id())
                      ->where('status', $filter);
                });
            }
        }

        $flightRequests = $query->paginate(15);

        // Statistics
        $stats = [
            'assigned' => FlightRequest::where('assigned_to', Auth::id())->count(),
            'needs_quote' => FlightRequest::where('assigned_to', Auth::id())
                ->where('status', 'assigned')
                ->whereDoesntHave('quotes', function ($q) {
                    $q->where('quoted_by', Auth::id());
                })
                ->count(),
            'quoted' => FlightQuote::where('quoted_by', Auth::id())
                ->where('status', 'pending')
                ->count(),
            'accepted' => FlightQuote::where('quoted_by', Auth::id())
                ->where('status', 'accepted')
                ->count(),
        ];

        return Inertia::render('Agency/FlightRequests/Index', [
            'flightRequests' => $flightRequests,
            'filter' => $filter,
            'stats' => $stats,
        ]);
    }

    /**
     * Show request details
     */
    public function show($id)
    {
        $flightRequest = FlightRequest::with(['user', 'quotes' => function ($q) {
                $q->where('quoted_by', Auth::id());
            }])
            ->where('assigned_to', Auth::id())
            ->findOrFail($id);

        return Inertia::render('Agency/FlightRequests/Show', [
            'flightRequest' => $flightRequest,
        ]);
    }

    /**
     * Show quote submission form
     */
    public function createQuote($id)
    {
        $flightRequest = FlightRequest::with(['user'])
            ->where('assigned_to', Auth::id())
            ->findOrFail($id);

        // Check if already quoted
        $existingQuote = $flightRequest->quotes()
            ->where('quoted_by', Auth::id())
            ->first();

        return Inertia::render('Agency/FlightRequests/CreateQuote', [
            'flightRequest' => $flightRequest,
            'existingQuote' => $existingQuote,
        ]);
    }

    /**
     * Submit a quote
     */
    public function storeQuote(Request $request, $id)
    {
        $flightRequest = FlightRequest::where('assigned_to', Auth::id())
            ->findOrFail($id);

        if (!$flightRequest->canReceiveQuote()) {
            return back()->with('error', 'This request is no longer accepting quotes.');
        }

        $validated = $request->validate([
            'airline_name' => 'required|string|max:255',
            'flight_number' => 'required|string|max:50',
            'quoted_price' => 'required|numeric|min:0',
            'price_breakdown' => 'required|string',
            'flight_details' => 'required|string',
            'valid_until' => 'required|date|after:today',
            'notes' => 'nullable|string',
        ]);

        // Create quote
        $quote = $flightRequest->quotes()->create([
            'quoted_by' => Auth::id(),
            'airline_name' => $validated['airline_name'],
            'flight_number' => $validated['flight_number'],
            'quoted_price' => $validated['quoted_price'],
            'price_breakdown' => $validated['price_breakdown'],
            'flight_details' => $validated['flight_details'],
            'valid_until' => $validated['valid_until'],
            'is_valid' => true,
            'status' => 'pending',
            'notes' => $validated['notes'] ?? null,
        ]);

        // Update request status to quoted
        if ($flightRequest->status === 'assigned') {
            $flightRequest->update([
                'status' => 'quoted',
                'quoted_at' => now(),
            ]);
        }

        // Update quotes count
        $flightRequest->update([
            'quotes_count' => $flightRequest->quotes()->count(),
        ]);

        return redirect()
            ->route('agency.flight-requests.show', $id)
            ->with('success', 'Quote submitted successfully!');
    }

    /**
     * Update a quote
     */
    public function updateQuote(Request $request, $id, $quoteId)
    {
        $flightRequest = FlightRequest::where('assigned_to', Auth::id())
            ->findOrFail($id);

        $quote = $flightRequest->quotes()
            ->where('quoted_by', Auth::id())
            ->where('status', 'pending')
            ->findOrFail($quoteId);

        $validated = $request->validate([
            'airline_name' => 'required|string|max:255',
            'flight_number' => 'required|string|max:50',
            'quoted_price' => 'required|numeric|min:0',
            'price_breakdown' => 'required|string',
            'flight_details' => 'required|string',
            'valid_until' => 'required|date|after:today',
            'notes' => 'nullable|string',
        ]);

        $quote->update($validated);

        return back()->with('success', 'Quote updated successfully!');
    }
}
