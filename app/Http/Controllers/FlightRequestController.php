<?php

namespace App\Http\Controllers;

use App\Models\FlightRequest;
use App\Models\FlightSearch;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class FlightRequestController extends Controller
{
    /**
     * Display the flight request submission form
     */
    public function create()
    {
        $popularRoutes = FlightSearch::getTrendingRoutes(6);
        
        return Inertia::render('Services/FlightRequest/Create', [
            'popularRoutes' => $popularRoutes,
        ]);
    }

    /**
     * Store a new flight request
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'trip_type' => 'required|in:one_way,round_trip,multi_city',
            'origin_airport_code' => 'required_if:trip_type,one_way,round_trip|string|max:10',
            'destination_airport_code' => 'required_if:trip_type,one_way,round_trip|string|max:10',
            'travel_date' => 'required_if:trip_type,one_way,round_trip|date|after_or_equal:today',
            'return_date' => 'required_if:trip_type,round_trip|date|after:travel_date',
            'multi_city_segments' => 'required_if:trip_type,multi_city|array|min:2',
            'multi_city_segments.*.origin' => 'required|string|max:10',
            'multi_city_segments.*.destination' => 'required|string|max:10',
            'multi_city_segments.*.date' => 'required|date|after_or_equal:today',
            'passengers_count' => 'required|integer|min:1|max:9',
            'flight_class' => 'required|in:economy,business,first',
            'passengers' => 'required|array',
            'passengers.*.name' => 'required|string',
            'passengers.*.age' => 'required|integer|min:0',
            'passengers.*.passport_number' => 'nullable|string',
            'contact_name' => 'required|string|max:255',
            'contact_email' => 'required|email',
            'contact_phone' => 'required|string|max:20',
            'special_requests' => 'nullable|string',
            'budget_min' => 'nullable|numeric|min:0',
            'budget_max' => 'nullable|numeric|min:0|gte:budget_min',
            'prefer_direct_flights' => 'boolean',
            'preferred_airlines' => 'nullable|array',
            'preferred_time' => 'nullable|in:morning,afternoon,evening,night,flexible',
        ]);

        // Create flight request
        $flightRequest = Auth::user()->flightRequests()->create(array_merge($validated, [
            'status' => 'pending',
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]));

        // Track the search
        FlightSearch::create([
            'user_id' => Auth::id(),
            'trip_type' => $validated['trip_type'],
            'origin_airport_code' => $validated['origin_airport_code'] ?? null,
            'destination_airport_code' => $validated['destination_airport_code'] ?? null,
            'travel_date' => $validated['travel_date'] ?? null,
            'return_date' => $validated['return_date'] ?? null,
            'multi_city_segments' => $validated['multi_city_segments'] ?? null,
            'passengers_count' => $validated['passengers_count'],
            'flight_class' => $validated['flight_class'],
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'search_count' => 1,
        ]);

        return redirect()
            ->route('flight-requests.show', $flightRequest->id)
            ->with('success', 'Your flight request has been submitted. We will notify you when agencies submit quotes.');
    }

    /**
     * Display user's flight requests
     */
    public function index(Request $request)
    {
        $filter = $request->get('filter', 'all');

        $query = Auth::user()->flightRequests()
            ->with(['quotes', 'assignedTo'])
            ->orderBy('created_at', 'desc');

        if ($filter !== 'all') {
            $query->where('status', $filter);
        }

        $flightRequests = $query->paginate(10);

        return Inertia::render('Services/FlightRequest/Index', [
            'flightRequests' => $flightRequests,
            'filter' => $filter,
        ]);
    }

    /**
     * Show single flight request with quotes
     */
    public function show($id)
    {
        $flightRequest = Auth::user()->flightRequests()
            ->with(['quotes.quotedBy', 'assignedTo'])
            ->findOrFail($id);

        return Inertia::render('Services/FlightRequest/Show', [
            'flightRequest' => $flightRequest,
        ]);
    }

    /**
     * Accept a quote
     */
    public function acceptQuote(Request $request, $requestId, $quoteId)
    {
        $flightRequest = Auth::user()->flightRequests()->findOrFail($requestId);

        if (!in_array($flightRequest->status, ['quoted', 'assigned'])) {
            return back()->with('error', 'This request cannot accept quotes at this time.');
        }

        $quote = $flightRequest->quotes()->findOrFail($quoteId);

        if ($quote->isExpired()) {
            return back()->with('error', 'This quote has expired.');
        }

        $user = Auth::user();

        // Check wallet balance
        if ($user->wallet->balance < $quote->quoted_price) {
            return back()->with('error', 'Insufficient wallet balance. Please recharge your wallet.');
        }

        // Deduct from wallet
        $user->wallet->deduct($quote->quoted_price, 'Flight booking from quote #' . $quote->id);

        // Update quote status
        $quote->update(['status' => 'accepted']);

        // Update other quotes to rejected
        $flightRequest->quotes()
            ->where('id', '!=', $quoteId)
            ->update(['status' => 'rejected']);

        // Update request status
        $flightRequest->update([
            'status' => 'completed',
        ]);

        return redirect()
            ->route('flight-requests.show', $requestId)
            ->with('success', 'Quote accepted and payment processed successfully!');
    }

    /**
     * Cancel a flight request
     */
    public function cancel($id)
    {
        $flightRequest = Auth::user()->flightRequests()->findOrFail($id);

        if (!in_array($flightRequest->status, ['pending', 'assigned', 'quoted'])) {
            return back()->with('error', 'This request cannot be cancelled.');
        }

        $flightRequest->update([
            'status' => 'cancelled',
        ]);

        return back()->with('success', 'Flight request cancelled successfully.');
    }
}
