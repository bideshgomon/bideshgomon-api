<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ServiceQuote;
use App\Models\ServiceApplication;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ServiceQuoteController extends Controller
{
    /**
     * Display a listing of service quotes
     */
    public function index(Request $request)
    {
        $query = ServiceQuote::with(['serviceApplication.serviceModule', 'serviceApplication.user', 'agency'])
            ->latest();

        // Search filter
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('quote_number', 'like', "%{$search}%")
                    ->orWhereHas('agency', function ($q) use ($search) {
                        $q->where('name', 'like', "%{$search}%");
                    })
                    ->orWhereHas('serviceApplication', function ($q) use ($search) {
                        $q->where('application_number', 'like', "%{$search}%");
                    });
            });
        }

        // Status filter
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $quotes = $query->paginate(20)->withQueryString();

        // Calculate stats
        $stats = [
            'total' => ServiceQuote::count(),
            'pending' => ServiceQuote::where('status', 'pending')->count(),
            'accepted' => ServiceQuote::where('status', 'accepted')->count(),
            'rejected' => ServiceQuote::where('status', 'rejected')->count(),
            'total_amount' => ServiceQuote::where('status', 'accepted')->sum('quoted_amount'),
        ];

        return Inertia::render('Admin/ServiceQuotes/Index', [
            'quotes' => $quotes,
            'stats' => $stats,
            'filters' => $request->only(['search', 'status']),
        ]);
    }

    /**
     * Display the specified service quote
     */
    public function show(ServiceQuote $serviceQuote)
    {
        $serviceQuote->load([
            'serviceApplication.user',
            'serviceApplication.serviceModule',
            'agency',
        ]);

        return Inertia::render('Admin/ServiceQuotes/Show', [
            'quote' => $serviceQuote,
        ]);
    }

    /**
     * Update the status of a service quote
     */
    public function updateStatus(Request $request, ServiceQuote $serviceQuote)
    {
        $validated = $request->validate([
            'status' => 'required|in:pending,accepted,rejected',
            'admin_notes' => 'nullable|string',
        ]);

        $serviceQuote->update($validated);

        return redirect()->back()->with('success', 'Quote status updated successfully.');
    }

    /**
     * Delete a service quote
     */
    public function destroy(ServiceQuote $serviceQuote)
    {
        $serviceQuote->delete();

        return redirect()->route('admin.service-quotes.index')
            ->with('success', 'Quote deleted successfully.');
    }
}
