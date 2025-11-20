<?php

namespace App\Http\Controllers;

use App\Models\FlightRoute;
use App\Models\FlightBooking;
use App\Models\Country;
use App\Services\WalletService;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\DB;

class FlightBookingController extends Controller
{
    protected WalletService $walletService;

    public function __construct(WalletService $walletService)
    {
        $this->walletService = $walletService;
    }

    /**
     * Display flight search page with popular routes
     */
    public function index()
    {
        $popularRoutes = FlightRoute::active()
            ->popular()
            ->with(['originCountry', 'destinationCountry'])
            ->limit(6)
            ->get();

        $origins = FlightRoute::active()
            ->select('origin_city', 'origin_airport_code')
            ->distinct()
            ->get()
            ->map(fn($route) => [
                'code' => $route->origin_airport_code,
                'city' => $route->origin_city,
            ]);

        $destinations = FlightRoute::active()
            ->select('destination_city', 'destination_airport_code', 'destination_country_id')
            ->distinct()
            ->with('destinationCountry:id,name')
            ->get()
            ->map(fn($route) => [
                'code' => $route->destination_airport_code,
                'city' => $route->destination_city,
                'country' => $route->destinationCountry->name ?? '',
            ]);

        return Inertia::render('Services/FlightBooking/Index', [
            'popularRoutes' => $popularRoutes,
            'origins' => $origins,
            'destinations' => $destinations,
        ]);
    }

    /**
     * Search for flights
     */
    public function search(Request $request)
    {
        $validated = $request->validate([
            'origin' => 'required|string|size:3',
            'destination' => 'required|string|size:3',
            'travel_date' => 'required|date|after:today',
            'passengers' => 'required|integer|min:1|max:9',
            'flight_class' => 'nullable|string|in:economy,business,first_class',
        ]);

        $routes = FlightRoute::searchRoute(
            $validated['origin'],
            $validated['destination'],
            $validated['travel_date']
        )
            ->with(['originCountry', 'destinationCountry'])
            ->ordered()
            ->get();

        return Inertia::render('Services/FlightBooking/SearchResults', [
            'routes' => $routes,
            'searchParams' => $validated,
        ]);
    }

    /**
     * Show booking form for a specific route
     */
    public function bookingForm(Request $request, $routeId)
    {
        $route = FlightRoute::with(['originCountry', 'destinationCountry'])
            ->findOrFail($routeId);

        $travelDate = $request->query('travel_date');
        $passengers = $request->query('passengers', 1);
        $flightClass = $request->query('flight_class', 'economy');

        // Check if route is available on selected date
        if (!$route->isAvailableOnDate($travelDate)) {
            return redirect()->route('flight-booking.index')
                ->with('error', 'This flight is not available on the selected date.');
        }

        // Calculate pricing
        $baseFare = match($flightClass) {
            'business' => $route->business_price,
            'first_class' => $route->first_class_price,
            default => $route->economy_price,
        };

        $subtotal = $baseFare * $passengers;
        $taxes = $subtotal * 0.15; // 15% tax
        $bookingFee = $route->booking_fee;
        $totalAmount = $subtotal + $taxes + $bookingFee;

        return Inertia::render('Services/FlightBooking/Book', [
            'route' => $route,
            'travelDate' => $travelDate,
            'passengers' => $passengers,
            'flightClass' => $flightClass,
            'pricing' => [
                'base_fare' => $baseFare,
                'subtotal' => $subtotal,
                'taxes_fees' => $taxes,
                'booking_fee' => $bookingFee,
                'total_amount' => $totalAmount,
            ],
        ]);
    }

    /**
     * Process flight booking
     */
    public function book(Request $request)
    {
        $validated = $request->validate([
            'flight_route_id' => 'required|exists:flight_routes,id',
            'travel_date' => 'required|date|after:today',
            'flight_class' => 'required|string|in:economy,business,first_class',
            'passengers_count' => 'required|integer|min:1|max:9',
            'passengers' => 'required|array|min:1',
            'passengers.*.name' => 'required|string|max:255',
            'passengers.*.passport_number' => 'required|string|max:50',
            'passengers.*.age' => 'required|integer|min:1|max:120',
            'passengers.*.gender' => 'required|string|in:male,female',
            'contact_name' => 'required|string|max:100',
            'contact_email' => 'required|email|max:100',
            'contact_phone' => 'required|string|max:20',
            'special_requests' => 'nullable|string|max:1000',
            'selected_seats' => 'nullable|array',
            'extra_baggage_count' => 'nullable|integer|min:0|max:5',
            'meal_preferences' => 'nullable|array',
            'add_insurance' => 'nullable|boolean',
        ]);

        $user = $request->user();
        $route = FlightRoute::findOrFail($validated['flight_route_id']);

        // Verify route availability
        if (!$route->isAvailableOnDate($validated['travel_date'])) {
            return back()->with('error', 'This flight is not available on the selected date.');
        }

        // Calculate pricing
        $baseFare = match($validated['flight_class']) {
            'business' => (float) $route->business_price,
            'first_class' => (float) $route->first_class_price,
            default => (float) $route->economy_price,
        };

        $subtotal = $baseFare * $validated['passengers_count'];
        $taxesFees = $subtotal * 0.15;
        $bookingFee = (float) $route->booking_fee;

        // Calculate extras
        $seatSelectionFee = 0;
        if (!empty($validated['selected_seats'])) {
            $seatSelectionFee = count($validated['selected_seats']) * 500; // ৳500 per seat
        }

        $extraBaggageFee = 0;
        if (!empty($validated['extra_baggage_count'])) {
            $extraBaggageFee = $validated['extra_baggage_count'] * 2000; // ৳2000 per bag
        }

        $mealFee = 0;
        if (!empty($validated['meal_preferences'])) {
            $mealFee = count($validated['meal_preferences']) * 800; // ৳800 per meal
        }

        $insuranceFee = 0;
        if (!empty($validated['add_insurance'])) {
            $insuranceFee = $validated['passengers_count'] * 1500; // ৳1500 per passenger
        }

        $totalAmount = $subtotal + $taxesFees + $bookingFee + $seatSelectionFee + $extraBaggageFee + $mealFee + $insuranceFee;

        // Check wallet balance
        if (!$user->wallet || $user->wallet->balance < $totalAmount) {
            return back()->with('error', 'Insufficient wallet balance. Please add funds first.');
        }

        try {
            DB::beginTransaction();

            // Create booking
            $booking = FlightBooking::create([
                'user_id' => $user->id,
                'flight_route_id' => $route->id,
                'travel_date' => $validated['travel_date'],
                'flight_class' => $validated['flight_class'],
                'passengers_count' => $validated['passengers_count'],
                'passengers' => $validated['passengers'],
                'contact_name' => $validated['contact_name'],
                'contact_email' => $validated['contact_email'],
                'contact_phone' => $validated['contact_phone'],
                'special_requests' => $validated['special_requests'] ?? null,
                'selected_seats' => $validated['selected_seats'] ?? null,
                'seat_selection_fee' => $seatSelectionFee,
                'extra_baggage_count' => $validated['extra_baggage_count'] ?? 0,
                'extra_baggage_fee' => $extraBaggageFee,
                'meal_preferences' => $validated['meal_preferences'] ?? null,
                'meal_fee' => $mealFee,
                'base_fare' => $baseFare,
                'taxes_fees' => $taxesFees,
                'booking_fee' => $bookingFee,
                'insurance_fee' => $insuranceFee,
                'subtotal' => $subtotal,
                'total_amount' => $totalAmount,
                'payment_status' => 'pending',
                'status' => 'pending',
                'booking_source' => 'web',
                'ip_address' => $request->ip(),
            ]);

            // Process payment via wallet
            $this->walletService->debitWallet(
                wallet: $user->wallet,
                amount: $totalAmount,
                description: "Flight Booking - {$route->route_name} on {$validated['travel_date']}",
                referenceType: 'service_payment',
                referenceId: $booking->booking_reference,
            );

            // Update booking payment status
            $booking->update([
                'payment_status' => 'paid',
                'payment_method' => 'wallet',
                'payment_reference' => $booking->booking_reference,
                'paid_at' => now(),
                'status' => 'confirmed',
                'confirmed_at' => now(),
                'pnr_number' => 'PNR' . strtoupper(substr(md5($booking->id . time()), 0, 6)),
            ]);

            // Increment route booking counter
            $route->incrementBookings();

            DB::commit();

            return redirect()->route('flight-booking.booking-details', $booking->id)
                ->with('success', "Booking confirmed! Your PNR: {$booking->pnr_number}");

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Booking failed. Please try again.');
        }
    }

    /**
     * Show user's flight bookings
     */
    public function myBookings(Request $request)
    {
        $filter = $request->query('filter', 'all');

        $query = FlightBooking::forUser($request->user()->id)
            ->with(['flightRoute.originCountry', 'flightRoute.destinationCountry']);

        $bookings = match($filter) {
            'upcoming' => $query->upcoming()->latest()->paginate(10),
            'past' => $query->past()->latest()->paginate(10),
            'cancelled' => $query->cancelled()->latest()->paginate(10),
            default => $query->latest()->paginate(10),
        };

        // Add can_cancel flag to each booking
        $bookings->getCollection()->transform(function ($booking) {
            $booking->can_cancel = $booking->canCancel();
            return $booking;
        });

        return Inertia::render('Services/FlightBooking/MyBookings', [
            'bookings' => $bookings,
            'filter' => $filter,
        ]);
    }

    /**
     * Show booking details
     */
    public function bookingDetails($id)
    {
        $booking = FlightBooking::with(['flightRoute.originCountry', 'flightRoute.destinationCountry', 'user'])
            ->where('user_id', auth()->id())
            ->findOrFail($id);

        return Inertia::render('Services/FlightBooking/BookingDetails', [
            'booking' => $booking,
        ]);
    }

    /**
     * Cancel booking
     */
    public function cancel(Request $request, $id)
    {
        $booking = FlightBooking::where('user_id', $request->user()->id)->findOrFail($id);

        if (!$booking->canCancel()) {
            return back()->with('error', 'This booking cannot be cancelled.');
        }

        $validated = $request->validate([
            'cancellation_reason' => 'required|string|max:500',
        ]);

        try {
            DB::beginTransaction();

            // Calculate refund (minus cancellation fee)
            $cancellationFeePercentage = $booking->flightRoute->cancellation_fee_percentage / 100;
            $cancellationFee = $booking->total_amount * $cancellationFeePercentage;
            $refundAmount = $booking->total_amount - $cancellationFee;

            // Process refund
            $this->walletService->creditWallet(
                wallet: $request->user()->wallet,
                amount: $refundAmount,
                description: "Flight Booking Refund - {$booking->booking_reference} (৳{$cancellationFee} cancellation fee deducted)",
                referenceType: 'refund',
                referenceId: $booking->booking_reference,
            );

            // Update booking
            $booking->cancel($validated['cancellation_reason']);
            $booking->update([
                'refund_amount' => $refundAmount,
                'payment_status' => 'refunded',
            ]);

            DB::commit();

            return redirect()->route('flight-booking.my-bookings')
                ->with('success', "Booking cancelled. Refund of ৳" . number_format($refundAmount, 2) . " processed to your wallet.");

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Cancellation failed. Please try again.');
        }
    }

    /**
     * Download ticket (PDF)
     */
    public function downloadTicket($id)
    {
        $booking = FlightBooking::with(['flightRoute', 'user'])
            ->where('user_id', auth()->id())
            ->where('status', 'confirmed')
            ->findOrFail($id);

        // In a real app, generate PDF ticket here
        // For now, redirect back with message
        return back()->with('info', 'Ticket download feature coming soon!');
    }
}
