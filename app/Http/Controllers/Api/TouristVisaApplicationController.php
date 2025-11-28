<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\TouristVisa;
use App\Models\ServiceApplication;
use App\Models\ServiceModule;
use App\Traits\CreatesServiceApplications;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class TouristVisaApplicationController extends Controller
{
    use CreatesServiceApplications;
    /**
     * Display a listing of the resource for the authenticated user.
     */
    public function index(Request $request)
    {
        $applications = $request->user()->touristVisas()
            ->with(['destinationCountry', 'documents.documentType', 'documents.userDocument'])
            ->latest()
            ->paginate(15);

        return response()->json($applications);
    }

    /**
     * Store a newly created resource in storage for the authenticated user.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'destination_country_id' => 'required|exists:countries,id',
            'intended_travel_date' => 'nullable|date|after:today',
            'duration_days' => 'nullable|integer|min:1|max:365',
            'profession' => 'required|string|in:Student,Job Holder,Business Person',
            'user_notes' => 'nullable|string|max:5000',
        ]);

        $validated['user_id'] = $request->user()->id;
        $validated['status'] = 'pending';

        DB::beginTransaction();
        try {
            // Create the legacy TouristVisa record
            $application = TouristVisa::create($validated);

            // Get the Tourist Visa service module (slug: 'tourist-visa')
            $touristVisaModule = ServiceModule::where('slug', 'tourist-visa')->first();

            if ($touristVisaModule) {
                // Create ServiceApplication for agency processing
                $serviceApplication = ServiceApplication::create([
                    'user_id' => $request->user()->id,
                    'service_module_id' => $touristVisaModule->id,
                    'tourist_visa_id' => $application->id,
                    'status' => 'pending',
                    'application_data' => [
                        'destination_country_id' => $validated['destination_country_id'],
                        'intended_travel_date' => $validated['intended_travel_date'] ?? null,
                        'duration_days' => $validated['duration_days'] ?? null,
                        'profession' => $validated['profession'] ?? null,
                        'user_notes' => $validated['user_notes'] ?? null,
                    ],
                ]);

                Log::info('ServiceApplication created for tourist visa', [
                    'service_application_id' => $serviceApplication->id,
                    'application_number' => $serviceApplication->application_number,
                ]);
            }

            DB::commit();

            Log::info('Tourist visa application created', [
                'tourist_visa_id' => $application->id,
                'user_id' => $request->user()->id,
            ]);

            return redirect()
                ->route('profile.tourist-visa.show', $application)
                ->with('success', 'Tourist visa application created successfully.');
                
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to create tourist visa application', [
                'error' => $e->getMessage(),
                'user_id' => $request->user()->id,
            ]);
            throw $e;
        }
    }

    /**
     * Display the specified resource if it belongs to the authenticated user.
     */
    public function show(Request $request, TouristVisa $touristVisaApplication)
    {
        if ($touristVisaApplication->user_id !== $request->user()->id) {
            abort(403, 'Unauthorized access.');
        }

        $touristVisaApplication->load([
            'destinationCountry',
            'documents.documentType',
            'documents.userDocument',
        ]);

        return response()->json($touristVisaApplication);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, TouristVisa $touristVisaApplication)
    {
        if ($touristVisaApplication->user_id !== $request->user()->id) {
            abort(403, 'Unauthorized access.');
        }

        $validated = $request->validate([
            'user_notes' => 'nullable|string|max:5000',
            'intended_travel_date' => 'nullable|date|after:today',
            'duration_days' => 'nullable|integer|min:1|max:365',
            'profession' => 'nullable|string|in:Student,Job Holder,Business Person',
        ]);

        if (in_array($touristVisaApplication->status, ['approved', 'rejected'])) {
            return response()->json([
                'message' => 'Cannot update an application that has been ' . $touristVisaApplication->status,
            ], 422);
        }

        $touristVisaApplication->update($validated);

        return response()->json($touristVisaApplication->fresh(['destinationCountry']));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, TouristVisa $touristVisaApplication)
    {
        if ($touristVisaApplication->user_id !== $request->user()->id) {
            abort(403, 'Unauthorized access.');
        }

        if ($touristVisaApplication->status === 'approved') {
            return response()->json([
                'message' => 'Cannot delete an approved application',
            ], 422);
        }

        $touristVisaApplication->delete();

        return response()->json(null, 204);
    }
}
