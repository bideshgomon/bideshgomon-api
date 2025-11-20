<?php

namespace App\Http\Controllers;

use App\Models\TranslationRequest;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Inertia\Inertia;

class TranslationRequestController extends Controller
{
    use AuthorizesRequests;

    public function index()
    {
        $languagePairs = [
            ['from' => 'English', 'to' => 'Bengali', 'price' => 250],
            ['from' => 'Bengali', 'to' => 'English', 'price' => 250],
            ['from' => 'English', 'to' => 'Arabic', 'price' => 350],
            ['from' => 'Bengali', 'to' => 'Arabic', 'price' => 400],
            ['from' => 'English', 'to' => 'Spanish', 'price' => 280],
        ];

        return Inertia::render('Services/Translation/Index', [
            'languagePairs' => $languagePairs,
        ]);
    }

    public function create()
    {
        return Inertia::render('Services/Translation/Create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'source_language' => 'required|string|max:50',
            'target_language' => 'required|string|max:50',
            'document_type' => 'required|in:legal,academic,business,medical,technical,personal,certificate,other',
            'certification_type' => 'required|in:standard,certified,notarized',
            'page_count' => 'required|integer|min:1',
            'word_count' => 'nullable|integer|min:1',
            'urgency' => 'required|in:standard,express,urgent',
            'required_by' => 'nullable|date|after:today',
            'special_instructions' => 'nullable|string|max:1000',
            'price_per_page' => 'required|numeric|min:0',
        ]);

        $validated['user_id'] = auth()->id();
        $validated['status'] = 'draft';
        $validated['payment_status'] = 'pending';
        $validated['ip_address'] = $request->ip();
        $validated['user_agent'] = $request->userAgent();

        // Calculate fees
        $validated['certification_fee'] = match($validated['certification_type']) {
            'notarized' => 2000,
            'certified' => 1000,
            default => 0
        };

        $validated['urgency_fee'] = match($validated['urgency']) {
            'urgent' => 1500,
            'express' => 800,
            default => 0
        };

        $validated['delivery_days'] = match($validated['urgency']) {
            'urgent' => 2,
            'express' => 3,
            default => 5
        };

        $translation = TranslationRequest::create($validated);
        $translation->calculateTotal();
        $translation->save();

        return redirect()->route('translation.show', $translation)
            ->with('success', 'Translation request created successfully.');
    }

    public function myRequests()
    {
        $requests = TranslationRequest::forUser(auth()->id())
            ->with(['documents', 'quotes'])
            ->latest()
            ->paginate(10);

        return Inertia::render('Services/Translation/MyRequests', [
            'requests' => $requests,
        ]);
    }

    public function show(TranslationRequest $translation)
    {
        $this->authorize('view', $translation);
        $translation->load(['documents', 'quotes', 'user', 'assignedTranslator']);

        return Inertia::render('Services/Translation/ShowRequest', [
            'request' => $translation,
        ]);
    }

    public function cancel(TranslationRequest $translation)
    {
        $this->authorize('update', $translation);
        $translation->cancel();

        return back()->with('success', 'Translation request cancelled.');
    }
}
