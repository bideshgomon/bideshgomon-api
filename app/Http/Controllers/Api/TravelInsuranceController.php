<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\TravelInsurance; // To potentially store issued policies
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log; // For logging API errors
use Illuminate\Support\Facades\Validator; // For validation
use Illuminate\Validation\ValidationException;

class TravelInsuranceController extends Controller
{
    protected string $baseUrl;

    protected string $apiKey;

    protected string $apiSecret;

    public function __construct()
    {
        // Load credentials from config
        $this->baseUrl = config('bimafy.base_url');
        $this->apiKey = config('bimafy.api_key');
        $this->apiSecret = config('bimafy.api_secret');

        // Basic check if credentials are set
        if (empty($this->apiKey) || empty($this->apiSecret)) {
            Log::error('Bimafy API credentials are not configured in .env file.');
            // Depending on requirements, you might throw an exception here
        }
    }

    /**
     * Helper method for making authenticated requests to Bimafy API.
     */
    protected function makeApiRequest(string $method, string $endpoint, array $data = [])
    {
        // Basic authentication or token generation might be needed based on Bimafy docs
        // For now, assuming API Key/Secret might be used in headers or data
        // Adjust this part based on actual Bimafy authentication requirements!
        $headers = [
            'Accept' => 'application/json',
            'X-Api-Key' => $this->apiKey, // Example header, adjust as needed
            // 'Authorization' => 'Bearer ' . $this->getToken(), // If token-based
        ];

        $url = rtrim($this->baseUrl, '/').'/'.ltrim($endpoint, '/');

        // Add secret to data if required by Bimafy (check their docs)
        // $data['api_secret'] = $this->apiSecret; // Example

        $response = Http::withHeaders($headers)
            ->$method($url, $data);

        if ($response->failed()) {
            Log::error('Bimafy API request failed', [
                'url' => $url,
                'method' => $method,
                'status' => $response->status(),
                'response' => $response->body(),
                'request_data' => $data, // Be careful logging sensitive data
            ]);
            $response->throw(); // Throw an exception to handle the failure
        }

        return $response->json();
    }

    /**
     * Fetch available travel insurance packages.
     * GET /travel-packages
     */
    public function getPackages(Request $request): JsonResponse
    {
        try {
            // Adjust endpoint and data structure based on Bimafy docs
            $packages = $this->makeApiRequest('get', '/travel-packages'); // Example endpoint

            return response()->json($packages);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Failed to fetch insurance packages.', 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * Calculate the premium for a selected package and travel details.
     * POST /calculate-premium
     */
    public function calculatePremium(Request $request): JsonResponse
    {
        // **VALIDATION IS CRUCIAL HERE**
        $validator = Validator::make($request->all(), [
            'package_id' => 'required', // ID from Bimafy's getPackages response
            'destination_country_code' => 'required|string|size:2', // Or whatever Bimafy expects
            'start_date' => 'required|date|after_or_equal:today',
            'end_date' => 'required|date|after:start_date',
            'traveler_ages' => 'required|array|min:1',
            'traveler_ages.*' => 'required|integer|min:0|max:120',
            // Add any other required fields by Bimafy
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => 'Invalid input.', 'errors' => $validator->errors()], 422);
        }

        try {
            // Prepare data payload exactly as Bimafy requires
            $payload = $validator->validated();
            // Remap field names if necessary, e.g., traveler_ages might be 'ages'
            // $payload['ages'] = $payload['traveler_ages']; unset($payload['traveler_ages']);

            // Adjust endpoint based on Bimafy docs
            $premiumDetails = $this->makeApiRequest('post', '/calculate-premium', $payload); // Example endpoint

            return response()->json($premiumDetails);

        } catch (ValidationException $e) {
            return response()->json(['message' => 'Invalid input.', 'errors' => $e->errors()], 422);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Failed to calculate premium.', 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * Issue (purchase) a travel insurance policy.
     * POST /issue-policy
     */
    public function issuePolicy(Request $request): JsonResponse
    {
        // **VALIDATION IS CRUCIAL HERE - includes all fields needed by Bimafy**
        $validator = Validator::make($request->all(), [
            'quote_reference' => 'required|string', // Reference from calculatePremium response
            'package_id' => 'required',
            'destination_country_code' => 'required|string|size:2',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'travelers' => 'required|array|min:1',
            'travelers.*.name' => 'required|string|max:255',
            'travelers.*.dob' => 'required|date',
            'travelers.*.passport_no' => 'required|string|max:50',
            // Add ALL other required traveler/policyholder details (address, phone, email, etc.)
            'payment_reference' => 'required|string', // Reference from your payment gateway
            // ... potentially many other fields ...
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => 'Invalid policy details.', 'errors' => $validator->errors()], 422);
        }

        // Ensure user is authenticated before issuing policy
        if (! auth()->check()) {
            return response()->json(['message' => 'Unauthenticated.'], 401);
        }

        try {
            // Prepare data payload exactly as Bimafy requires
            $payload = $validator->validated();
            // Add any other required data, potentially user details from auth()->user()

            // Adjust endpoint based on Bimafy docs
            $policyDetails = $this->makeApiRequest('post', '/issue-policy', $payload); // Example endpoint

            // **IMPORTANT: Store the relevant policy details in your database**
            // Extract key details from $policyDetails (policy number, dates, premium, etc.)
            $policyReference = $policyDetails['policy_reference'] ?? null; // Example field
            $premium = $policyDetails['total_premium'] ?? 0; // Example field
            // Calculate duration if not provided directly
            $startDate = new \DateTime($payload['start_date']);
            $endDate = new \DateTime($payload['end_date']);
            $duration = $endDate->diff($startDate)->days + 1; // Inclusive duration

            if (! $policyReference) {
                Log::error('Bimafy issue policy response missing policy reference', $policyDetails);
                throw new \Exception('Policy reference missing from Bimafy response.');
            }

            $insurance = TravelInsurance::create([
                'user_id' => auth()->id(),
                'bimafy_policy_reference' => $policyReference,
                'package_name' => $policyDetails['package_name'] ?? null, // Example field
                'destination_country_id' => \App\Models\Country::where('code', $payload['destination_country_code'])->value('id'), // Find local country ID
                'start_date' => $payload['start_date'],
                'end_date' => $payload['end_date'],
                'duration_days' => $duration,
                'premium_paid' => $premium,
                'currency' => $policyDetails['currency'] ?? 'BDT', // Example field
                'status' => 'active', // Assuming Bimafy confirms success
                'coverage_details' => $policyDetails['coverage'] ?? null, // Example field: Store coverage summary
            ]);

            // Return success response, potentially including the local record ID and Bimafy details
            return response()->json([
                'message' => 'Travel insurance policy issued successfully!',
                'policy_details' => $policyDetails, // From Bimafy
                'local_record_id' => $insurance->id, // Your DB record ID
            ], 201); // 201 Created

        } catch (ValidationException $e) {
            return response()->json(['message' => 'Invalid policy details.', 'errors' => $e->errors()], 422);
        } catch (\Exception $e) {
            // Log the detailed error
            Log::error('Bimafy policy issuance failed.', ['user_id' => auth()->id(), 'error' => $e->getMessage(), 'request_data' => $request->except(['payment_reference'])]); // Avoid logging payment refs if sensitive

            return response()->json(['message' => 'Failed to issue policy.', 'error' => $e->getMessage()], 500);
        }
    }
}
