<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\ServiceApplication;
use App\Models\ServiceQuote;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class UserApplicationController extends Controller
{
    /**
     * Store a new application
     */
    public function store(Request $request)
    {
        $request->validate([
            'service_module_id' => 'required|exists:service_modules,id',
            'destination_country' => 'required|string',
            'profession' => 'required|string',
            'purpose' => 'required|string',
            'travel_dates' => 'required|array',
            'travel_dates.departure' => 'required|date',
            'travel_dates.return' => 'required|date|after:travel_dates.departure',
            'notes' => 'nullable|string',
        ]);

        $user = Auth::user();

        // Check if user already has a pending application for this service
        $existingApplication = ServiceApplication::where('user_id', $user->id)
            ->where('service_module_id', $request->service_module_id)
            ->whereIn('status', ['pending', 'quoted', 'in_progress'])
            ->first();

        if ($existingApplication) {
            return redirect()->back()->with('error', 'You already have an active application for this service.');
        }

        // Prepare application data
        $applicationData = [
            'destination_country' => $request->destination_country,
            'profession' => $request->profession,
            'purpose' => $request->purpose,
            'travel_dates' => $request->travel_dates,
            'user_notes' => $request->notes,
        ];

        $application = ServiceApplication::create([
            'user_id' => $user->id,
            'service_module_id' => $request->service_module_id,
            'status' => 'pending',
            'application_data' => json_encode($applicationData),
            'special_notes' => $request->notes,
        ]);

        return redirect()->route('user.applications.index')
            ->with('success', 'Application submitted successfully! Agencies will send you quotes soon.');
    }

    /**
     * Display user's applications
     */
    public function index()
    {
        $user = Auth::user();

        $applications = ServiceApplication::with(['serviceModule', 'quotes'])
            ->where('user_id', $user->id)
            ->latest()
            ->paginate(10);

        // Add quotes count to each application
        $applications->getCollection()->transform(function ($application) {
            $application->quotes_count = $application->quotes()->count();
            return $application;
        });

        $stats = [
            'total' => ServiceApplication::where('user_id', $user->id)->count(),
            'pending' => ServiceApplication::where('user_id', $user->id)->where('status', 'pending')->count(),
            'quoted' => ServiceApplication::where('user_id', $user->id)->where('status', 'quoted')->count(),
            'in_progress' => ServiceApplication::where('user_id', $user->id)->where('status', 'in_progress')->count(),
            'completed' => ServiceApplication::where('user_id', $user->id)->where('status', 'completed')->count(),
        ];

        return Inertia::render('User/Applications/Index', [
            'applications' => $applications,
            'stats' => $stats,
        ]);
    }

    /**
     * Display a specific application
     */
    public function show($id)
    {
        $user = Auth::user();

        $application = ServiceApplication::with(['serviceModule', 'quotes.agency'])
            ->where('user_id', $user->id)
            ->findOrFail($id);

        return Inertia::render('User/Applications/Show', [
            'application' => $application,
        ]);
    }

    /**
     * Display quotes for a specific application
     */
    public function quotes($id)
    {
        $user = Auth::user();

        $application = ServiceApplication::with(['serviceModule'])
            ->where('user_id', $user->id)
            ->findOrFail($id);

        $quotes = ServiceQuote::with(['agency'])
            ->where('service_application_id', $application->id)
            ->latest()
            ->get();

        return Inertia::render('User/Applications/Quotes', [
            'application' => $application,
            'quotes' => $quotes,
        ]);
    }

    /**
     * Accept a quote
     */
    public function acceptQuote(Request $request, $applicationId, $quoteId)
    {
        $user = Auth::user();

        $application = ServiceApplication::where('user_id', $user->id)->findOrFail($applicationId);
        $quote = ServiceQuote::where('service_application_id', $application->id)->findOrFail($quoteId);

        // Update quote status
        $quote->update(['status' => 'accepted']);

        // Update application status
        $application->update([
            'status' => 'accepted',
            'assigned_agency_id' => $quote->agency_id,
        ]);

        // Reject other quotes
        ServiceQuote::where('service_application_id', $application->id)
            ->where('id', '!=', $quote->id)
            ->update(['status' => 'rejected']);

        return redirect()->route('user.applications.index')
            ->with('success', 'Quote accepted successfully! The agency will contact you soon.');
    }

    /**
     * Reject a quote
     */
    public function rejectQuote(Request $request, $applicationId, $quoteId)
    {
        $user = Auth::user();

        $application = ServiceApplication::where('user_id', $user->id)->findOrFail($applicationId);
        $quote = ServiceQuote::where('service_application_id', $application->id)->findOrFail($quoteId);

        $quote->update(['status' => 'rejected']);

        return redirect()->back()->with('success', 'Quote rejected.');
    }
}
