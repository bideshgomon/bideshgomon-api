<?php

namespace App\Http\Controllers;

use App\Models\TranslationRequest;
use App\Models\ServiceApplication;
use App\Models\ServiceModule;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
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

        DB::beginTransaction();
        try {
            // Create translation request
            $translation = TranslationRequest::create($validated);
            $translation->calculateTotal();
            $translation->save();

            // Get Translation service module
            $translationModule = ServiceModule::where('slug', 'translation')->first();

            if ($translationModule) {
                // Create ServiceApplication for agency processing
                $serviceApplication = ServiceApplication::create([
                    'user_id' => auth()->id(),
                    'service_module_id' => $translationModule->id,
                    'status' => 'pending',
                    'application_data' => [
                        'translation_request_id' => $translation->id,
                        'source_language' => $validated['source_language'],
                        'target_language' => $validated['target_language'],
                        'document_type' => $validated['document_type'],
                        'certification_type' => $validated['certification_type'],
                        'page_count' => $validated['page_count'],
                        'word_count' => $validated['word_count'] ?? null,
                        'urgency' => $validated['urgency'],
                        'required_by' => $validated['required_by'] ?? null,
                        'special_instructions' => $validated['special_instructions'] ?? null,
                        'estimated_total' => $translation->total_cost,
                    ],
                ]);

                Log::info('ServiceApplication created for translation', [
                    'service_application_id' => $serviceApplication->id,
                    'application_number' => $serviceApplication->application_number,
                    'translation_request_id' => $translation->id,
                ]);
            }

            DB::commit();

            return redirect()->route('translation.show', $translation)
                ->with('success', 'Translation request created successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to create translation request', [
                'error' => $e->getMessage(),
                'user_id' => auth()->id(),
            ]);
            throw $e;
        }
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
