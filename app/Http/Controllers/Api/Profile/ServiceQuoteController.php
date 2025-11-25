<?php

namespace App\Http\Controllers\Api\Profile;

use App\Http\Controllers\Controller;
use App\Models\ServiceApplication;
use App\Models\ServiceQuote;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ServiceQuoteController extends Controller
{
    /**
     * Get all quotes for a user's service application
     */
    public function index(Request $request, ServiceApplication $application)
    {
        // Verify ownership
        if ($application->user_id !== $request->user()->id) {
            abort(403, 'Unauthorized access to this application.');
        }

        $quotes = $application->quotes()
            ->with(['agency' => function($query) {
                $query->select('id', 'name', 'email', 'phone', 'logo_path');
            }])
            ->orderBy('quoted_amount', 'asc') // Show cheapest first
            ->get()
            ->map(function($quote) {
                return [
                    'id' => $quote->id,
                    'agency_name' => $quote->agency->name ?? 'Unknown Agency',
                    'agency_logo' => $quote->agency->logo_path ?? null,
                    'agency_phone' => $quote->agency->phone ?? null,
                    'quoted_amount' => $quote->quoted_amount,
                    'service_fee' => $quote->service_fee,
                    'processing_time_days' => $quote->processing_time_days,
                    'valid_until' => $quote->valid_until,
                    'quote_notes' => $quote->quote_notes,
                    'status' => $quote->status,
                    'is_expired' => $quote->isExpired(),
                    'created_at' => $quote->created_at,
                ];
            });

        return response()->json([
            'application' => [
                'id' => $application->id,
                'application_number' => $application->application_number,
                'status' => $application->status,
                'service_name' => $application->serviceModule->name ?? 'Unknown Service',
            ],
            'quotes' => $quotes,
            'has_accepted_quote' => $application->status === 'accepted',
        ]);
    }

    /**
     * Accept a quote
     */
    public function accept(Request $request, ServiceQuote $quote)
    {
        $application = $quote->serviceApplication;

        // Verify ownership
        if ($application->user_id !== $request->user()->id) {
            abort(403, 'Unauthorized access to this quote.');
        }

        // Check if quote is already accepted or rejected
        if ($quote->status !== 'pending') {
            return response()->json([
                'message' => 'This quote has already been ' . $quote->status,
            ], 422);
        }

        // Check if quote is expired
        if ($quote->isExpired()) {
            return response()->json([
                'message' => 'This quote has expired. Please request a new quote from the agency.',
            ], 422);
        }

        // Check if application already has an accepted quote
        if ($application->agency_id !== null) {
            return response()->json([
                'message' => 'You have already accepted a quote for this application.',
            ], 422);
        }

        DB::beginTransaction();
        try {
            // Use the accept method from ServiceQuote model
            $quote->accept();

            // Update the linked TouristVisa status if applicable
            if ($application->touristVisa) {
                $application->touristVisa->update([
                    'status' => 'assigned',
                    'admin_notes' => 'Quote accepted - Assigned to agency: ' . $quote->agency->name,
                ]);
            }

            DB::commit();

            Log::info('Quote accepted by user', [
                'quote_id' => $quote->id,
                'service_application_id' => $application->id,
                'user_id' => $request->user()->id,
                'agency_id' => $quote->agency_id,
                'quoted_amount' => $quote->quoted_amount,
            ]);

            return response()->json([
                'message' => 'Quote accepted successfully! The agency will begin processing your application.',
                'application' => [
                    'id' => $application->id,
                    'status' => $application->fresh()->status,
                    'agency_name' => $quote->agency->name,
                    'quoted_amount' => $quote->quoted_amount,
                ],
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to accept quote', [
                'quote_id' => $quote->id,
                'error' => $e->getMessage(),
                'user_id' => $request->user()->id,
            ]);

            return response()->json([
                'message' => 'Failed to accept quote. Please try again.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Reject a quote
     */
    public function reject(Request $request, ServiceQuote $quote)
    {
        $application = $quote->serviceApplication;

        // Verify ownership
        if ($application->user_id !== $request->user()->id) {
            abort(403, 'Unauthorized access to this quote.');
        }

        // Check if quote is already accepted or rejected
        if ($quote->status !== 'pending') {
            return response()->json([
                'message' => 'This quote has already been ' . $quote->status,
            ], 422);
        }

        // Can only reject if no quote has been accepted yet
        if ($application->agency_id !== null) {
            return response()->json([
                'message' => 'You have already accepted a different quote for this application.',
            ], 422);
        }

        $quote->reject();

        Log::info('Quote rejected by user', [
            'quote_id' => $quote->id,
            'service_application_id' => $application->id,
            'user_id' => $request->user()->id,
            'agency_id' => $quote->agency_id,
        ]);

        return response()->json([
            'message' => 'Quote rejected successfully.',
        ]);
    }

    /**
     * Get quote comparison for user to decide
     */
    public function compare(Request $request, ServiceApplication $application)
    {
        // Verify ownership
        if ($application->user_id !== $request->user()->id) {
            abort(403, 'Unauthorized access to this application.');
        }

        $quotes = $application->quotes()
            ->with(['agency'])
            ->where('status', 'pending')
            ->where('valid_until', '>', now())
            ->get();

        if ($quotes->isEmpty()) {
            return response()->json([
                'message' => 'No active quotes available for this application.',
                'quotes' => [],
            ]);
        }

        // Calculate comparison metrics
        $cheapestQuote = $quotes->sortBy('quoted_amount')->first();
        $fastestQuote = $quotes->sortBy('processing_time_days')->first();
        
        $comparison = $quotes->map(function($quote) use ($cheapestQuote, $fastestQuote) {
            return [
                'id' => $quote->id,
                'agency' => [
                    'id' => $quote->agency->id,
                    'name' => $quote->agency->name,
                    'logo' => $quote->agency->logo_path,
                    'phone' => $quote->agency->phone,
                    'email' => $quote->agency->email,
                ],
                'quoted_amount' => $quote->quoted_amount,
                'processing_time_days' => $quote->processing_time_days,
                'valid_until' => $quote->valid_until,
                'quote_notes' => $quote->quote_notes,
                'badges' => [
                    'cheapest' => $quote->id === $cheapestQuote->id,
                    'fastest' => $quote->id === $fastestQuote->id,
                ],
                'expires_in_hours' => now()->diffInHours($quote->valid_until),
            ];
        });

        return response()->json([
            'application' => [
                'id' => $application->id,
                'application_number' => $application->application_number,
                'service_name' => $application->serviceModule->name,
            ],
            'quotes' => $comparison,
            'summary' => [
                'total_quotes' => $quotes->count(),
                'price_range' => [
                    'min' => $quotes->min('quoted_amount'),
                    'max' => $quotes->max('quoted_amount'),
                ],
                'processing_time_range' => [
                    'min' => $quotes->min('processing_time_days'),
                    'max' => $quotes->max('processing_time_days'),
                ],
            ],
        ]);
    }
}
