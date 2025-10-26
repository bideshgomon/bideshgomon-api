<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\TravelInsurance;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Models\User; // To get user details if needed directly
use App\Models\Country; // To get country code if needed directly

class IssueTravelInsurancePolicy implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public TravelInsurance $insurance;

    // Optional: Define job properties like retries, timeout
    public int $tries = 3; // Retry 3 times if it fails
    public int $timeout = 120; // Allow 2 minutes for the job to run

    /**
     * Create a new job instance.
     */
    public function __construct(TravelInsurance $insurance)
    {
        // Use withoutRelations() to prevent eager loaded relations from being serialized
        // which can sometimes cause issues if models change between queueing and execution.
        $this->insurance = $insurance->withoutRelations();
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        // Reload the model instance with necessary relations inside the job
        $this->insurance->load(['user.userProfile', 'destinationCountry']);

        Log::info('Processing IssueTravelInsurancePolicy job', ['insurance_id' => $this->insurance->id]);

        // Ensure the status is correct ('processing' means payment was confirmed)
        if ($this->insurance->status !== 'processing') {
            Log::warning('IssueTravelInsurancePolicy job skipped: Insurance status is not processing.', [
                'insurance_id' => $this->insurance->id,
                'status' => $this->insurance->status
            ]);
            return; // Don't proceed if payment wasn't confirmed or already processed
        }

        // --- Prepare Data for Bimafy API ---
        $user = $this->insurance->user;
        $country = $this->insurance->destinationCountry;
        // Retrieve traveler data stored during payment initiation
        $travelerData = $this->insurance->coverage_details['travelers'] ?? [];

        // **CRUCIAL**: Build the $payload exactly as Bimafy's /issue-policy endpoint requires
        $payload = [
            // Use the SSLCommerz tran_id stored in bimafy_policy_reference as the quote_reference
            // OR use a specific quote_reference if Bimafy provided one earlier
            'quote_reference' => $this->insurance->bimafy_policy_reference,
            'package_id' => $this->insurance->package_id ?? null, // Store this during quote/purchase form
            'package_name' => $this->insurance->package_name,
            'destination_country_code' => $country->code ?? null, // Ensure Country model has 'code'
            'start_date' => $this->insurance->start_date->format('Y-m-d'),
            'end_date' => $this->insurance->end_date->format('Y-m-d'),
            'total_premium' => (float) $this->insurance->premium_paid, // Ensure it's a number
            'currency' => $this->insurance->currency,

            // Travelers - Map data stored in coverage_details
            'travelers' => array_map(function ($traveler) {
                return [
                    'name' => $traveler['name'] ?? null,
                    'dob' => $traveler['dob'] ?? null, // Ensure format YYYY-MM-DD if required
                    'passport_no' => $traveler['passport_no'] ?? null,
                    // Add other required traveler fields (gender, nationality?) from $traveler array
                ];
            }, $travelerData),

            // Policyholder (assuming it's the user)
            'policyholder_name' => $user->name,
            'policyholder_email' => $user->email,
            'policyholder_phone' => $user->phone ?? 'N/A', // Make sure user has phone
            'policyholder_address' => $user->userProfile->address ?? 'N/A', // Make sure UserProfile relation exists and has address
            // Add ALL other required policyholder fields (e.g., city, postcode)

            // Payment reference might be the SSLCommerz tran_id or bank_tran_id
            'payment_reference' => $this->insurance->bimafy_policy_reference,
            // 'bank_transaction_id' => $this->insurance->admin_notes contains bank ID? Check IPN data.
        ];

         // Remove null values if Bimafy API is sensitive to them
         $payload = array_filter($payload, fn($value) => $value !== null && (!is_array($value) || !empty($value)));
         // Ensure travelers array isn't empty after filtering
         if (empty($payload['travelers'])) {
             // Handle error - traveler data missing
             Log::error('Bimafy issue-policy: Traveler data missing.', ['insurance_id' => $this->insurance->id]);
              $this->fail(new \Exception('Traveler data missing for policy issuance.')); // Mark job as failed
             return;
         }


        // --- Make API Call to Bimafy ---
        try {
            $baseUrl = config('bimafy.base_url');
            $apiKey = config('bimafy.api_key');
            $apiSecret = config('bimafy.api_secret'); // Secret might be needed

            if (empty($apiKey)) {
                 throw new \Exception('Bimafy API Key is not configured.');
            }

            // **ADJUST HEADERS/AUTH BASED ON BIMAFY DOCS**
            $headers = [
                'Accept' => 'application/json',
                'X-Api-Key' => $apiKey,
                // 'Authorization' => 'Bearer your_token', // If needed
            ];

            // **ADJUST ENDPOINT IF DIFFERENT**
            $url = rtrim($baseUrl, '/') . '/issue-policy';

            Log::info('Calling Bimafy issue-policy API', ['url' => $url, 'payload_keys' => array_keys($payload)]); // Avoid logging sensitive payload details

            $response = Http::withHeaders($headers)
                            ->timeout(60) // Allow 1 minute
                            ->post($url, $payload);

            if ($response->failed()) {
                Log::error('Bimafy issue-policy API request failed', [
                    'insurance_id' => $this->insurance->id,
                    'status' => $response->status(),
                    'response' => $response->body() // Log the error response from Bimafy
                ]);
                // Throw exception to potentially retry the job
                $response->throw();
            }

            $policyDetails = $response->json();
            Log::info('Bimafy issue-policy API Success', ['insurance_id' => $this->insurance->id, 'response' => $policyDetails]);

            // --- Process SUCCESSFUL Response ---
            // **IMPORTANT**: Extract the *actual* policy reference/number from Bimafy
            $bimafyPolicyReference = $policyDetails['policy_reference'] ?? $policyDetails['policy_number'] ?? null; // Adjust field name based on actual response

            if (!$bimafyPolicyReference) {
                 Log::error('Bimafy response missing policy reference/number.', ['insurance_id' => $this->insurance->id, 'response' => $policyDetails]);
                 throw new \Exception('Bimafy response missing policy reference/number.');
            }

            // Update our local record to 'active' and store the real reference
            $this->insurance->update([
                'status' => 'active',
                'bimafy_policy_reference' => $bimafyPolicyReference, // Store the actual reference
                'admin_notes' => 'Policy issued via Bimafy. Ref: ' . $bimafyPolicyReference,
                // Optionally update coverage_details with more info from $policyDetails
                'coverage_details' => $policyDetails['coverage'] ?? $this->insurance->coverage_details, // Keep old if none provided
            ]);

            // TODO: Send notification (e.g., email) to the user with policy details/document link

        } catch (\Exception $e) {
            Log::error('Failed to issue travel insurance policy via Bimafy API', [
                'insurance_id' => $this->insurance->id,
                'error' => $e->getMessage(),
                // 'trace' => $e->getTraceAsString() // Uncomment for very detailed debugging
            ]);

            // Update status to 'issue_failed'
            $this->insurance->update([
                'status' => 'issue_failed',
                'admin_notes' => 'Bimafy API call failed after payment: ' . $e->getMessage()
            ]);

            // TODO: Notify admin about the failure

            // Mark the job as failed so it might retry based on $tries
             $this->fail($e);
        }
    }

     /**
      * Handle a job failure.
      */
     public function failed(\Throwable $exception): void
     {
         Log::critical('IssueTravelInsurancePolicy Job Failed Permanently', [
             'insurance_id' => $this->insurance->id,
             'error' => $exception->getMessage()
         ]);
         // Optionally notify admin that manual intervention is needed
         $this->insurance->update([
            'status' => 'issue_failed',
            'admin_notes' => 'Bimafy API call failed permanently after retries: ' . $exception->getMessage()
         ]);
     }
}