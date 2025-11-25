<?php

namespace App\Http\Controllers;

use App\Models\Hotel;
use App\Models\HotelRoom;
use App\Models\HotelBooking;
use App\Traits\CreatesServiceApplications;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class HotelBookingController extends Controller
{
    use CreatesServiceApplications;
    /**
     * Display hotel search page with filters
     */
    public function index(Request $request)
    {
        $query = Hotel::with(['rooms' => function($q) {
            $q->where('is_available', true)->orderBy('price_per_night', 'asc');
        }])->active();

        // Search filters
        if ($request->filled('city')) {
            $query->where('city', $request->city);
        }

        if ($request->filled('star_rating')) {
            $query->where('star_rating', $request->star_rating);
        }

        if ($request->filled('min_price') || $request->filled('max_price')) {
            $min = $request->input('min_price', 0);
            $max = $request->input('max_price', 999999);
            $query->priceBetween($min, $max);
        }

        if ($request->filled('amenities')) {
            $amenities = is_array($request->amenities) ? $request->amenities : [$request->amenities];
            foreach ($amenities as $amenity) {
                $query->withAmenity($amenity);
            }
        }

        // Sorting
        $sortBy = $request->input('sort', 'popular');
        switch ($sortBy) {
            case 'price_low':
                $query->orderBy('price_from', 'asc');
                break;
            case 'price_high':
                $query->orderBy('price_from', 'desc');
                break;
            case 'rating':
                $query->orderBy('rating', 'desc');
                break;
            case 'popular':
            default:
                $query->popular();
                break;
        }

        $hotels = $query->paginate(12)->withQueryString();

        // Get unique cities for filter
        $cities = Hotel::active()->distinct('city')->pluck('city')->sort()->values();

        // Popular destinations
        $featured = Hotel::active()->featured()->with('rooms')->limit(4)->get();

        return Inertia::render('Services/Hotels/Index', [
            'hotels' => $hotels,
            'cities' => $cities,
            'featured' => $featured,
            'filters' => $request->only(['city', 'star_rating', 'min_price', 'max_price', 'amenities', 'sort']),
        ]);
    }

    /**
     * Display hotel details with available rooms
     */
    public function show(Request $request, Hotel $hotel)
    {
        $hotel->load(['rooms' => function($q) {
            $q->where('is_available', true)->orderBy('price_per_night', 'asc');
        }]);

        // Check availability for specific dates if provided
        $checkIn = $request->input('check_in');
        $checkOut = $request->input('check_out');
        $roomsNeeded = $request->input('rooms', 1);

        if ($checkIn && $checkOut) {
            foreach ($hotel->rooms as $room) {
                $room->available_for_dates = $room->isAvailableForDates($checkIn, $checkOut, $roomsNeeded);
            }
        }

        // Similar hotels
        $similarHotels = Hotel::active()
            ->where('id', '!=', $hotel->id)
            ->where('city', $hotel->city)
            ->with('rooms')
            ->limit(4)
            ->get();

        return Inertia::render('Services/Hotels/Show', [
            'hotel' => $hotel,
            'similarHotels' => $similarHotels,
            'checkIn' => $checkIn,
            'checkOut' => $checkOut,
            'rooms' => $roomsNeeded,
        ]);
    }

    /**
     * Show booking form for selected room
     */
    public function create(Request $request, Hotel $hotel, HotelRoom $room)
    {
        $validated = $request->validate([
            'check_in' => 'required|date|after_or_equal:today',
            'check_out' => 'required|date|after:check_in',
            'rooms' => 'required|integer|min:1',
            'adults' => 'required|integer|min:1',
            'children' => 'nullable|integer|min:0',
        ]);

        // Calculate nights and pricing
        $checkIn = \Carbon\Carbon::parse($validated['check_in']);
        $checkOut = \Carbon\Carbon::parse($validated['check_out']);
        $nights = $checkIn->diffInDays($checkOut);
        $roomsCount = $validated['rooms'];

        // Check availability
        if (!$room->isAvailableForDates($checkIn, $checkOut, $roomsCount)) {
            return back()->with('error', 'Selected room is not available for these dates.');
        }

        // Calculate pricing
        $pricePerNight = $room->current_price;
        $subtotal = $pricePerNight * $nights * $roomsCount;
        $taxAmount = $subtotal * 0.05; // 5% tax
        $serviceCharge = 500; // Flat service charge
        $totalAmount = $subtotal + $taxAmount + $serviceCharge;

        return Inertia::render('Services/Hotels/Create', [
            'hotel' => $hotel,
            'room' => $room,
            'booking_details' => [
                'check_in' => $checkIn->format('Y-m-d'),
                'check_out' => $checkOut->format('Y-m-d'),
                'nights' => $nights,
                'rooms_count' => $roomsCount,
                'adults_count' => $validated['adults'],
                'children_count' => $validated['children'] ?? 0,
                'price_per_night' => $pricePerNight,
                'subtotal' => $subtotal,
                'tax_amount' => $taxAmount,
                'service_charge' => $serviceCharge,
                'total_amount' => $totalAmount,
            ],
        ]);
    }

    /**
     * Store a new hotel booking
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'hotel_id' => 'required|exists:hotels,id',
            'hotel_room_id' => 'required|exists:hotel_rooms,id',
            'check_in_date' => 'required|date|after_or_equal:today',
            'check_out_date' => 'required|date|after:check_in_date',
            'rooms_count' => 'required|integer|min:1',
            'adults_count' => 'required|integer|min:1',
            'children_count' => 'nullable|integer|min:0',
            'guests' => 'required|array',
            'guests.*.name' => 'required|string',
            'guests.*.age' => 'nullable|integer',
            'guest_name' => 'required|string|max:255',
            'guest_email' => 'required|email',
            'guest_phone' => 'required|string',
            'special_requests' => 'nullable|string',
        ]);

        $room = HotelRoom::findOrFail($validated['hotel_room_id']);
        
        // Verify availability
        $checkIn = \Carbon\Carbon::parse($validated['check_in_date']);
        $checkOut = \Carbon\Carbon::parse($validated['check_out_date']);
        
        if (!$room->isAvailableForDates($checkIn, $checkOut, $validated['rooms_count'])) {
            return back()->with('error', 'Room is no longer available for these dates.');
        }

        // Calculate amounts
        $nights = $checkIn->diffInDays($checkOut);
        $pricePerNight = $room->current_price;
        $subtotal = $pricePerNight * $nights * $validated['rooms_count'];
        $taxAmount = $subtotal * 0.05;
        $serviceCharge = 500;
        $totalAmount = $subtotal + $taxAmount + $serviceCharge;

        // Create booking within transaction
        DB::beginTransaction();
        
        try {
            $booking = HotelBooking::create([
                'user_id' => Auth::id(),
                'hotel_id' => $validated['hotel_id'],
                'hotel_room_id' => $validated['hotel_room_id'],
                'check_in_date' => $validated['check_in_date'],
                'check_out_date' => $validated['check_out_date'],
                'nights' => $nights,
                'rooms_count' => $validated['rooms_count'],
                'adults_count' => $validated['adults_count'],
                'children_count' => $validated['children_count'] ?? 0,
                'guests' => $validated['guests'],
                'guest_name' => $validated['guest_name'],
                'guest_email' => $validated['guest_email'],
                'guest_phone' => $validated['guest_phone'],
                'special_requests' => $validated['special_requests'],
                'room_price_per_night' => $pricePerNight,
                'subtotal' => $subtotal,
                'tax_amount' => $taxAmount,
                'service_charge' => $serviceCharge,
                'total_amount' => $totalAmount,
                'payment_method' => 'wallet',
                'payment_status' => 'pending',
                'status' => 'pending',
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);

            // Create ServiceApplication for agency commission tracking
            $hotel = Hotel::find($validated['hotel_id']);
            $this->createServiceApplicationFor(
                $booking,
                'hotel-booking',
                [
                    'hotel_name' => $hotel->name,
                    'hotel_city' => $hotel->city,
                    'room_type' => $room->room_type,
                    'check_in_date' => $validated['check_in_date'],
                    'check_out_date' => $validated['check_out_date'],
                    'nights' => $nights,
                    'rooms_count' => $validated['rooms_count'],
                    'adults_count' => $validated['adults_count'],
                    'children_count' => $validated['children_count'] ?? 0,
                    'total_amount' => $totalAmount,
                ]
            );

            DB::commit();

            return redirect()->route('hotel-bookings.payment', $booking)
                ->with('success', 'Booking created successfully. Please complete payment.');
                
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Hotel booking failed', ['error' => $e->getMessage()]);
            return back()->with('error', 'Booking failed. Please try again.');
        }
    }

    /**
     * Show payment page
     */
    public function payment(HotelBooking $booking)
    {
        // Ensure user owns this booking
        if ($booking->user_id !== Auth::id()) {
            abort(403);
        }

        if ($booking->payment_status === 'paid') {
            return redirect()->route('hotel-bookings.confirmation', $booking);
        }

        $booking->load(['hotel', 'room']);

        return Inertia::render('Services/Hotels/Payment', [
            'booking' => $booking,
        ]);
    }

    /**
     * Process payment
     */
    public function processPayment(Request $request, HotelBooking $booking)
    {
        // Ensure user owns this booking
        if ($booking->user_id !== Auth::id()) {
            abort(403);
        }

        if ($booking->payment_status === 'paid') {
            return redirect()->route('hotel-bookings.confirmation', $booking);
        }

        $validated = $request->validate([
            'payment_method' => 'required|in:wallet,card,cash',
        ]);

        // TODO: Integrate with wallet system
        // For now, mark as paid directly
        
        $booking->markAsPaid('TXN' . time() . rand(1000, 9999));
        $booking->confirm();

        return redirect()->route('hotel-bookings.confirmation', $booking)
            ->with('success', 'Payment successful! Your booking is confirmed.');
    }

    /**
     * Show booking confirmation
     */
    public function confirmation(HotelBooking $booking)
    {
        // Ensure user owns this booking
        if ($booking->user_id !== Auth::id()) {
            abort(403);
        }

        $booking->load(['hotel', 'room']);

        return Inertia::render('Services/Hotels/Confirmation', [
            'booking' => $booking,
        ]);
    }

    /**
     * Display user's bookings
     */
    public function myBookings(Request $request)
    {
        $status = $request->input('status', 'all');

        $query = HotelBooking::with(['hotel', 'room'])
            ->forUser(Auth::id())
            ->recent();

        if ($status !== 'all') {
            $query->byStatus($status);
        }

        $bookings = $query->paginate(10);

        // Get status counts
        $statusCounts = [
            'all' => HotelBooking::forUser(Auth::id())->count(),
            'upcoming' => HotelBooking::forUser(Auth::id())->upcoming()->count(),
            'active' => HotelBooking::forUser(Auth::id())->active()->count(),
            'past' => HotelBooking::forUser(Auth::id())->past()->count(),
            'cancelled' => HotelBooking::forUser(Auth::id())->cancelled()->count(),
        ];

        return Inertia::render('Services/Hotels/MyBookings', [
            'bookings' => $bookings,
            'statusCounts' => $statusCounts,
            'currentStatus' => $status,
        ]);
    }

    /**
     * Show single booking details
     */
    public function showBooking(HotelBooking $booking)
    {
        // Ensure user owns this booking
        if ($booking->user_id !== Auth::id()) {
            abort(403);
        }

        $booking->load(['hotel', 'room', 'user']);

        return Inertia::render('Services/Hotels/ShowBooking', [
            'booking' => $booking,
        ]);
    }

    /**
     * Cancel a booking
     */
    public function cancel(Request $request, HotelBooking $booking)
    {
        // Ensure user owns this booking
        if ($booking->user_id !== Auth::id()) {
            abort(403);
        }

        if (!$booking->is_cancellable) {
            return back()->with('error', 'This booking cannot be cancelled.');
        }

        $validated = $request->validate([
            'reason' => 'required|string|max:500',
        ]);

        $booking->cancel($validated['reason']);

        // TODO: Process refund through wallet system

        return back()->with('success', 'Booking cancelled successfully. Refund will be processed within 3-5 business days.');
    }
}
