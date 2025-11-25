<?php

namespace App\Http\Controllers\Agency;

use App\Http\Controllers\Controller;
use App\Models\ServiceApplication;
use App\Models\ServiceQuote;
use Illuminate\Http\Request;
use Inertia\Inertia;

class QuoteController extends Controller
{
    /**
     * Show the form for creating a quote
     */
    public function create(ServiceApplication $application)
    {
        $agency = auth()->user()->agency;

        if (!$agency) {
            abort(403, 'Agency not found');
        }

        // Check if agency can quote on this application
        if ($application->agency_id !== null && $application->agency_id !== $agency->id) {
            abort(403, 'Cannot quote on this application');
        }

        // Check if agency already has a quote for this application
        $existingQuote = ServiceQuote::where('service_application_id', $application->id)
            ->where('agency_id', $agency->id)
            ->first();

        if ($existingQuote) {
            return redirect()->route('agency.quotes.edit', $existingQuote)
                ->with('info', 'You already have a quote for this application. You can edit it here.');
        }

        $application->load(['user', 'serviceModule', 'touristVisa.destinationCountry']);

        return Inertia::render('Agency/Quotes/Create', [
            'application' => $application,
        ]);
    }

    /**
     * Store a newly created quote
     */
    public function store(Request $request, ServiceApplication $application)
    {
        $agency = auth()->user()->agency;

        if (!$agency) {
            abort(403, 'Agency not found');
        }

        $validated = $request->validate([
            'quoted_amount' => 'required|numeric|min:0',
            'processing_time_days' => 'required|integer|min:1|max:365',
            'valid_until' => 'required|date|after:today',
            'quote_notes' => 'nullable|string|max:2000',
        ]);

        // Calculate commission
        $serviceModule = $application->serviceModule;
        $commissionRate = $serviceModule->platform_commission_rate / 100;
        $platformCommission = $validated['quoted_amount'] * $commissionRate;
        $agencyEarnings = $validated['quoted_amount'] - $platformCommission;

        $quote = ServiceQuote::create([
            'service_application_id' => $application->id,
            'agency_id' => $agency->id,
            'quoted_amount' => $validated['quoted_amount'],
            'service_fee' => $validated['quoted_amount'],
            'platform_commission' => $platformCommission,
            'agency_earnings' => $agencyEarnings,
            'processing_time_days' => $validated['processing_time_days'],
            'valid_until' => $validated['valid_until'],
            'quote_notes' => $validated['quote_notes'] ?? null,
            'status' => 'pending',
        ]);

        // Update application status to 'quoted' if it's still pending
        if ($application->status === 'pending') {
            $application->update(['status' => 'quoted']);
        }

        return redirect()->route('agency.applications.show', $application)
            ->with('success', 'Quote submitted successfully.');
    }

    /**
     * Show the form for editing the quote
     */
    public function edit(ServiceQuote $quote)
    {
        $agency = auth()->user()->agency;

        if (!$agency || $quote->agency_id !== $agency->id) {
            abort(403, 'Unauthorized');
        }

        if ($quote->status !== 'pending') {
            return redirect()->route('agency.applications.show', $quote->service_application_id)
                ->with('error', 'Cannot edit a quote that has been ' . $quote->status);
        }

        $quote->load(['serviceApplication.user', 'serviceApplication.serviceModule', 'serviceApplication.touristVisa.destinationCountry']);

        return Inertia::render('Agency/Quotes/Edit', [
            'quote' => $quote,
        ]);
    }

    /**
     * Update the quote
     */
    public function update(Request $request, ServiceQuote $quote)
    {
        $agency = auth()->user()->agency;

        if (!$agency || $quote->agency_id !== $agency->id) {
            abort(403, 'Unauthorized');
        }

        if ($quote->status !== 'pending') {
            return redirect()->back()->with('error', 'Cannot edit a quote that has been ' . $quote->status);
        }

        $validated = $request->validate([
            'quoted_amount' => 'required|numeric|min:0',
            'processing_time_days' => 'required|integer|min:1|max:365',
            'valid_until' => 'required|date|after:today',
            'quote_notes' => 'nullable|string|max:2000',
        ]);

        // Recalculate commission
        $serviceModule = $quote->serviceApplication->serviceModule;
        $commissionRate = $serviceModule->platform_commission_rate / 100;
        $platformCommission = $validated['quoted_amount'] * $commissionRate;
        $agencyEarnings = $validated['quoted_amount'] - $platformCommission;

        $quote->update([
            'quoted_amount' => $validated['quoted_amount'],
            'service_fee' => $validated['quoted_amount'],
            'platform_commission' => $platformCommission,
            'agency_earnings' => $agencyEarnings,
            'processing_time_days' => $validated['processing_time_days'],
            'valid_until' => $validated['valid_until'],
            'quote_notes' => $validated['quote_notes'] ?? $quote->quote_notes,
        ]);

        return redirect()->route('agency.applications.show', $quote->service_application_id)
            ->with('success', 'Quote updated successfully.');
    }
}
