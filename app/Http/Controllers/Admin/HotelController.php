<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Hotel;
use App\Models\HotelRoom;
use App\Models\HotelBooking;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Str;

class HotelController extends Controller
{
    /**
     * Display hotels management dashboard
     */
    public function index(Request $request)
    {
        $query = Hotel::with('rooms');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('city', 'like', "%{$search}%")
                  ->orWhere('address', 'like', "%{$search}%");
            });
        }

        if ($request->filled('city')) {
            $query->where('city', $request->city);
        }

        if ($request->filled('status')) {
            $query->where('is_active', $request->status === 'active');
        }

        $hotels = $query->latest()->paginate(15)->withQueryString();

        $stats = [
            'total_hotels' => Hotel::count(),
            'active_hotels' => Hotel::active()->count(),
            'inactive_hotels' => Hotel::where('is_active', false)->count(),
            'featured_hotels' => Hotel::featured()->count(),
            'total_rooms' => HotelRoom::count(),
            'available_rooms' => HotelRoom::available()->count(),
        ];

        return Inertia::render('Admin/Hotels/Index', [
            'hotels' => $hotels,
            'stats' => $stats,
            'filters' => $request->only(['search', 'city', 'status']),
        ]);
    }

    /**
     * Show hotel creation form
     */
    public function create()
    {
        return Inertia::render('Admin/Hotels/Create');
    }

    /**
     * Store a new hotel
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'city' => 'required|string|max:255',
            'area' => 'nullable|string|max:255',
            'address' => 'required|string',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
            'country' => 'required|string|default:Bangladesh',
            'phone' => 'nullable|string',
            'email' => 'nullable|email',
            'website' => 'nullable|url',
            'star_rating' => 'required|integer|min:1|max:5',
            'property_type' => 'required|string',
            'amenities' => 'nullable|array',
            'featured_image' => 'nullable|string',
            'images' => 'nullable|array',
            'check_in_time' => 'nullable|date_format:H:i',
            'check_out_time' => 'nullable|date_format:H:i',
            'cancellation_policy' => 'nullable|string',
            'house_rules' => 'nullable|string',
            'is_active' => 'boolean',
            'is_featured' => 'boolean',
        ]);

        $hotel = Hotel::create($validated);

        return redirect()->route('admin.hotels.show', $hotel)
            ->with('success', 'Hotel created successfully.');
    }

    /**
     * Show hotel details
     */
    public function show(Hotel $hotel)
    {
        $hotel->load(['rooms', 'bookings' => function($q) {
            $q->latest()->limit(10);
        }]);

        $stats = [
            'total_rooms' => $hotel->rooms()->count(),
            'available_rooms' => $hotel->availableRooms()->count(),
            'total_bookings' => $hotel->getTotalBookingsCount(),
            'revenue' => $hotel->getRevenue(),
            'current_bookings' => $hotel->bookings()->active()->count(),
            'upcoming_bookings' => $hotel->bookings()->upcoming()->count(),
        ];

        return Inertia::render('Admin/Hotels/Show', [
            'hotel' => $hotel,
            'stats' => $stats,
        ]);
    }

    /**
     * Show hotel edit form
     */
    public function edit(Hotel $hotel)
    {
        return Inertia::render('Admin/Hotels/Edit', [
            'hotel' => $hotel,
        ]);
    }

    /**
     * Update hotel
     */
    public function update(Request $request, Hotel $hotel)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'city' => 'required|string|max:255',
            'area' => 'nullable|string|max:255',
            'address' => 'required|string',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
            'country' => 'required|string',
            'phone' => 'nullable|string',
            'email' => 'nullable|email',
            'website' => 'nullable|url',
            'star_rating' => 'required|integer|min:1|max:5',
            'property_type' => 'required|string',
            'amenities' => 'nullable|array',
            'featured_image' => 'nullable|string',
            'images' => 'nullable|array',
            'check_in_time' => 'nullable|date_format:H:i',
            'check_out_time' => 'nullable|date_format:H:i',
            'cancellation_policy' => 'nullable|string',
            'house_rules' => 'nullable|string',
            'is_active' => 'boolean',
            'is_featured' => 'boolean',
        ]);

        $hotel->update($validated);

        return redirect()->route('admin.hotels.show', $hotel)
            ->with('success', 'Hotel updated successfully.');
    }

    /**
     * Delete hotel
     */
    public function destroy(Hotel $hotel)
    {
        // Check if hotel has bookings
        if ($hotel->bookings()->exists()) {
            return back()->with('error', 'Cannot delete hotel with existing bookings.');
        }

        $hotel->delete();

        return redirect()->route('admin.hotels.index')
            ->with('success', 'Hotel deleted successfully.');
    }

    /**
     * Toggle hotel active status
     */
    public function toggleStatus(Hotel $hotel)
    {
        $hotel->is_active = !$hotel->is_active;
        $hotel->save();

        return back()->with('success', 'Hotel status updated successfully.');
    }

    /**
     * Hotel bookings overview
     */
    public function bookings(Request $request)
    {
        $query = HotelBooking::with(['hotel', 'room', 'user']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('booking_reference', 'like', "%{$search}%")
                  ->orWhere('guest_name', 'like', "%{$search}%")
                  ->orWhere('guest_email', 'like', "%{$search}%");
            });
        }

        if ($request->filled('hotel_id')) {
            $query->where('hotel_id', $request->hotel_id);
        }

        if ($request->filled('status')) {
            $query->byStatus($request->status);
        }

        if ($request->filled('payment_status')) {
            $query->byPaymentStatus($request->payment_status);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('check_in_date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('check_in_date', '<=', $request->date_to);
        }

        $bookings = $query->latest()->paginate(20)->withQueryString();

        $stats = [
            'total_bookings' => HotelBooking::count(),
            'pending' => HotelBooking::pending()->count(),
            'confirmed' => HotelBooking::confirmed()->count(),
            'active' => HotelBooking::active()->count(),
            'completed' => HotelBooking::completed()->count(),
            'cancelled' => HotelBooking::cancelled()->count(),
            'total_revenue' => HotelBooking::paid()->sum('total_amount'),
            'pending_payment' => HotelBooking::where('payment_status', 'pending')->sum('total_amount'),
        ];

        $hotels = Hotel::active()->orderBy('name')->get(['id', 'name']);

        return Inertia::render('Admin/Hotels/Bookings', [
            'bookings' => $bookings,
            'stats' => $stats,
            'hotels' => $hotels,
            'filters' => $request->only(['search', 'hotel_id', 'status', 'payment_status', 'date_from', 'date_to']),
        ]);
    }

    /**
     * Show booking details
     */
    public function showBooking(HotelBooking $booking)
    {
        $booking->load(['hotel', 'room', 'user']);

        return Inertia::render('Admin/Hotels/ShowBooking', [
            'booking' => $booking,
        ]);
    }

    /**
     * Update booking status
     */
    public function updateBookingStatus(Request $request, HotelBooking $booking)
    {
        $validated = $request->validate([
            'status' => 'required|in:pending,confirmed,checked_in,checked_out,cancelled,no_show',
            'admin_notes' => 'nullable|string',
        ]);

        $booking->status = $validated['status'];
        
        if ($validated['status'] === 'confirmed' && !$booking->confirmed_at) {
            $booking->confirmed_at = now();
        } elseif ($validated['status'] === 'checked_in' && !$booking->checked_in_at) {
            $booking->checked_in_at = now();
        } elseif ($validated['status'] === 'checked_out' && !$booking->checked_out_at) {
            $booking->checked_out_at = now();
        } elseif ($validated['status'] === 'cancelled' && !$booking->cancelled_at) {
            $booking->cancelled_at = now();
        }

        if ($validated['admin_notes']) {
            $booking->admin_notes = $validated['admin_notes'];
        }

        $booking->save();

        return back()->with('success', 'Booking status updated successfully.');
    }

    /**
     * Analytics dashboard
     */
    public function analytics(Request $request)
    {
        $period = $request->input('period', '30days');
        
        $startDate = match($period) {
            '7days' => now()->subDays(7),
            '30days' => now()->subDays(30),
            '90days' => now()->subDays(90),
            'year' => now()->subYear(),
            default => now()->subDays(30),
        };

        // Bookings stats
        $totalBookings = HotelBooking::where('created_at', '>=', $startDate)->count();
        $confirmedBookings = HotelBooking::confirmed()
            ->where('created_at', '>=', $startDate)->count();
        $revenue = HotelBooking::paid()
            ->where('created_at', '>=', $startDate)
            ->sum('total_amount');
        $cancelledBookings = HotelBooking::cancelled()
            ->where('created_at', '>=', $startDate)->count();

        // Top hotels
        $topHotels = Hotel::withCount(['bookings' => function($q) use ($startDate) {
            $q->where('created_at', '>=', $startDate)
              ->whereIn('status', ['confirmed', 'checked_in', 'checked_out']);
        }])
        ->orderBy('bookings_count', 'desc')
        ->limit(10)
        ->get();

        // Popular cities
        $popularCities = HotelBooking::where('created_at', '>=', $startDate)
            ->join('hotels', 'hotel_bookings.hotel_id', '=', 'hotels.id')
            ->selectRaw('hotels.city, count(*) as bookings_count')
            ->groupBy('hotels.city')
            ->orderBy('bookings_count', 'desc')
            ->limit(10)
            ->get();

        // Daily bookings chart data
        $dailyBookings = HotelBooking::where('created_at', '>=', $startDate)
            ->selectRaw('DATE(created_at) as date, count(*) as count, sum(total_amount) as revenue')
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        return Inertia::render('Admin/Hotels/Analytics', [
            'stats' => [
                'total_bookings' => $totalBookings,
                'confirmed_bookings' => $confirmedBookings,
                'revenue' => $revenue,
                'cancelled_bookings' => $cancelledBookings,
                'cancellation_rate' => $totalBookings > 0 ? round(($cancelledBookings / $totalBookings) * 100, 2) : 0,
            ],
            'topHotels' => $topHotels,
            'popularCities' => $popularCities,
            'dailyBookings' => $dailyBookings,
            'period' => $period,
        ]);
    }

    /**
     * Manage hotel rooms
     */
    public function rooms(Hotel $hotel)
    {
        $hotel->load('rooms');

        return Inertia::render('Admin/Hotels/Rooms', [
            'hotel' => $hotel,
        ]);
    }

    /**
     * Store new room for hotel
     */
    public function storeRoom(Request $request, Hotel $hotel)
    {
        $validated = $request->validate([
            'room_type' => 'required|string|max:255',
            'name' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'max_adults' => 'required|integer|min:1',
            'max_children' => 'nullable|integer|min:0',
            'total_rooms' => 'required|integer|min:1',
            'size_sqm' => 'nullable|integer',
            'bed_type' => 'nullable|string',
            'bed_count' => 'required|integer|min:1',
            'price_per_night' => 'required|numeric|min:0',
            'weekend_price' => 'nullable|numeric|min:0',
            'amenities' => 'nullable|array',
            'images' => 'nullable|array',
            'is_available' => 'boolean',
        ]);

        $validated['hotel_id'] = $hotel->id;
        $room = HotelRoom::create($validated);

        // Update hotel's lowest price
        $hotel->price_from = $hotel->rooms()->min('price_per_night');
        $hotel->save();

        return back()->with('success', 'Room added successfully.');
    }

    /**
     * Update room
     */
    public function updateRoom(Request $request, Hotel $hotel, HotelRoom $room)
    {
        $validated = $request->validate([
            'room_type' => 'required|string|max:255',
            'name' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'max_adults' => 'required|integer|min:1',
            'max_children' => 'nullable|integer|min:0',
            'total_rooms' => 'required|integer|min:1',
            'size_sqm' => 'nullable|integer',
            'bed_type' => 'nullable|string',
            'bed_count' => 'required|integer|min:1',
            'price_per_night' => 'required|numeric|min:0',
            'weekend_price' => 'nullable|numeric|min:0',
            'amenities' => 'nullable|array',
            'images' => 'nullable|array',
            'is_available' => 'boolean',
        ]);

        $room->update($validated);

        // Update hotel's lowest price
        $hotel->price_from = $hotel->rooms()->min('price_per_night');
        $hotel->save();

        return back()->with('success', 'Room updated successfully.');
    }

    /**
     * Delete room
     */
    public function destroyRoom(Hotel $hotel, HotelRoom $room)
    {
        // Check if room has bookings
        if ($room->bookings()->exists()) {
            return back()->with('error', 'Cannot delete room with existing bookings.');
        }

        $room->delete();

        // Update hotel's lowest price
        $hotel->price_from = $hotel->rooms()->min('price_per_night');
        $hotel->save();

        return back()->with('success', 'Room deleted successfully.');
    }
}
