<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Translation;
use App\Models\ServiceApplication;
use App\Models\ServiceModule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class TranslationApplicationController extends Controller
{
    /**
     * Display a listing of the resource for the authenticated user.
     */
    public function index(Request $request)
    {
        $applications = $request->user()->translations()
            ->with(['destinationCountry'])
            ->latest()
            ->paginate(15);

        return response()->json($applications);
    }

    /**
     * Store a newly created resource in storage for the authenticated user.
     */
    public function store(Request $request)
    {
        $validated = $request->validate(array (
  'document_type' => 'required|string',
  'source_language' => 'required|string',
  'target_language' => 'required|string',
  'page_count' => 'nullable|integer|min:1',
  'certification_type' => 'nullable|string',
  'is_urgent' => 'boolean',
  'required_by_date' => 'nullable|date',
  'user_notes' => 'nullable|string|max:5000',
));

        $validated['user_id'] = $request->user()->id;
        $validated['status'] = 'pending';

        DB::beginTransaction();
        try {
            // Create the Translation record
            $application = Translation::create($validated);

            // Get the service module (slug: 'translation')
            $serviceModule = ServiceModule::where('slug', 'translation')->first();

            if ($serviceModule) {
                // Create ServiceApplication for agency processing
                $serviceApplication = ServiceApplication::create([
                    'user_id' => $request->user()->id,
                    'service_module_id' => $serviceModule->id,
                    'translation_id' => $application->id,
                    'status' => 'pending',
                    'application_data' => $validated,
                ]);

                Log::info('ServiceApplication created for translation', [
                    'service_application_id' => $serviceApplication->id,
                    'application_number' => $serviceApplication->application_number,
                ]);
            }

            DB::commit();

            Log::info('translation application created', [
                'application_id' => $application->id,
                'user_id' => $request->user()->id,
            ]);

            $application->load(['destinationCountry']);

            return response()->json([
                'message' => 'Application created successfully',
                'application' => $application,
                'redirect' => route('profile.translation.show', $application),
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to create translation application', [
                'error' => $e->getMessage(),
                'user_id' => $request->user()->id,
            ]);

            return response()->json([
                'message' => 'Failed to create application',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, Translation $application)
    {
        // Ensure user can only view their own applications
        if ($application->user_id !== $request->user()->id) {
            return response()->json([
                'message' => 'Unauthorized access to this application.'
            ], 403);
        }

        $application->load(['destinationCountry']);

        return response()->json($application);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Translation $application)
    {
        // Ensure user can only update their own applications
        if ($application->user_id !== $request->user()->id) {
            return response()->json([
                'message' => 'Unauthorized access to this application.'
            ], 403);
        }

        // Only allow updates if status is 'pending'
        if ($application->status !== 'pending') {
            return response()->json([
                'message' => 'Cannot update application after submission.'
            ], 422);
        }

        $validated = $request->validate(array (
  'document_type' => 'required|string',
  'source_language' => 'required|string',
  'target_language' => 'required|string',
  'page_count' => 'nullable|integer|min:1',
  'certification_type' => 'nullable|string',
  'is_urgent' => 'boolean',
  'required_by_date' => 'nullable|date',
  'user_notes' => 'nullable|string|max:5000',
));

        $application->update($validated);

        return response()->json([
            'message' => 'Application updated successfully',
            'application' => $application->load(['destinationCountry']),
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, Translation $application)
    {
        // Ensure user can only delete their own applications
        if ($application->user_id !== $request->user()->id) {
            return response()->json([
                'message' => 'Unauthorized access to this application.'
            ], 403);
        }

        // Only allow deletion if status is 'pending'
        if ($application->status !== 'pending') {
            return response()->json([
                'message' => 'Cannot delete application after submission.'
            ], 422);
        }

        $application->delete();

        return response()->json([
            'message' => 'Application deleted successfully'
        ]);
    }
}