<?php

namespace App\Http\Controllers;

use App\Models\Country;
use App\Models\TravelInsuranceBooking;
use App\Models\TravelInsurancePackage;
use App\Services\WalletService;
use App\Traits\CreatesServiceApplications;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class TravelInsuranceController extends Controller
{
    use CreatesServiceApplications;

    protected WalletService $walletService;

    public function __construct(WalletService $walletService)
    {
        $this->walletService = $walletService;
    }

    /**
     * Display travel insurance packages listing
     */
    public function index(): Response
    {
        $packages = TravelInsurancePackage::active()
            ->ordered()
            ->get();

        $popularPackages = $packages->where('is_popular', true);

        return Inertia::render('Services/TravelInsurance/Index', [
            'packages' => $packages,
            'popularPackages' => $popularPackages,
        ]);
    }

    /**
     * Show package details
     */
    public function show(string $slug): Response
    {
        $package = TravelInsurancePackage::where('slug', $slug)
            ->where('is_active', true)
            ->firstOrFail();

        $countries = Country::where('is_active', true)
            ->orderBy('name')
            ->get();

        return Inertia::render('Services/TravelInsurance/Show', [
            'package' => $package,
            'countries' => $countries,
        ]);
    }

    /**
     * Show booking form
     */
    public function bookingForm(string $slug): Response
    {
        $package = TravelInsurancePackage::where('slug', $slug)
            ->where('is_active', true)
            ->firstOrFail();

        $countries = Country::where('is_active', true)
            ->orderBy('name')
            ->get();

        return Inertia::render('Services/TravelInsurance/Booking', [
            'package' => $package,
            'countries' => $countries,
        ]);
    }

    /**
     * Process booking
     */
    public function book(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'package_id' => 'required|exists:travel_insurance_packages,id',
            'destination_country_id' => 'required|exists:countries,id',
            'trip_start_date' => 'required|date|after:today',
            'trip_end_date' => 'required|date|after:trip_start_date',
            'trip_purpose' => 'nullable|string|max:255',
            'travelers' => 'required|array|min:1',
            'travelers.*.name' => 'required|string|max:255',
            'travelers.*.age' => 'required|integer|min:1|max:100',
            'travelers.*.passport_number' => 'required|string|max:50',
        ]);

        $user = $request->user();
        $package = TravelInsurancePackage::findOrFail($validated['package_id']);

        // Calculate duration
        $startDate = new \DateTime($validated['trip_start_date']);
        $endDate = new \DateTime($validated['trip_end_date']);
        $duration = $startDate->diff($endDate)->days + 1;

        // Calculate price
        $travelersCount = count($validated['travelers']);
        $packagePrice = $package->calculatePrice($duration, $travelersCount);
        $taxAmount = $packagePrice * 0.05; // 5% tax
        $totalAmount = $packagePrice + $taxAmount;

        // Check wallet balance
        if (!$user->wallet || $user->wallet->balance < $totalAmount) {
            return redirect()->back()->with('error', 'Insufficient wallet balance. Please add funds first.');
        }

        try {
            // Create booking
            $booking = TravelInsuranceBooking::create([
                'user_id' => $user->id,
                'package_id' => $package->id,
                'destination_country_id' => $validated['destination_country_id'],
                'trip_start_date' => $validated['trip_start_date'],
                'trip_end_date' => $validated['trip_end_date'],
                'duration_days' => $duration,
                'trip_purpose' => $validated['trip_purpose'] ?? 'Tourism',
                'travelers' => $validated['travelers'],
                'travelers_count' => $travelersCount,
                'package_price' => $packagePrice,
                'tax_amount' => $taxAmount,
                'total_amount' => $totalAmount,
                'payment_status' => 'pending',
                'status' => 'pending',
            ]);

            // Process payment via wallet
            $this->walletService->debitWallet(
                wallet: $user->wallet,
                amount: $totalAmount,
                description: "Travel Insurance - {$package->name} ({$duration} days, {$travelersCount} travelers)",
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
                'policy_number' => 'POL' . date('Ymd') . str_pad($booking->id, 6, '0', STR_PAD_LEFT),
                'policy_issued_at' => now(),
            ]);

            // 🔥 PLUGIN SYSTEM: Create ServiceApplication for agency assignment
            $this->createServiceApplicationFor(
                $booking,
                'travel-insurance',
                [
                    'package_name' => $package->name,
                    'destination_country_id' => $validated['destination_country_id'],
                    'trip_start_date' => $validated['trip_start_date'],
                    'trip_end_date' => $validated['trip_end_date'],
                    'duration_days' => $duration,
                    'travelers_count' => $travelersCount,
                    'total_amount' => $totalAmount,
                    'policy_number' => $booking->policy_number,
                ]
            );

            return redirect()->route('travel-insurance.my-bookings')
                ->with('success', "Booking confirmed! Policy number: {$booking->policy_number}");
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Booking failed. Please try again.');
        }
    }

    /**
     * Show user's bookings
     */
    public function myBookings(Request $request): Response
    {
        $bookings = TravelInsuranceBooking::forUser($request->user()->id)
            ->with(['package', 'destinationCountry'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return Inertia::render('Services/TravelInsurance/MyBookings', [
            'bookings' => $bookings,
        ]);
    }

    /**
     * Show booking details
     */
    public function bookingDetails(int $id): Response
    {
        $booking = TravelInsuranceBooking::with(['package', 'destinationCountry', 'user'])
            ->where('user_id', auth()->id())
            ->findOrFail($id);

        return Inertia::render('Services/TravelInsurance/BookingDetails', [
            'booking' => $booking,
        ]);
    }
}
