<?php

namespace App\Http\Controllers;

use App\Models\VisaRequirement;
use App\Models\VisaFee;
use Illuminate\Http\Request;

class VisaRequirementController extends Controller
{
    /**
     * Get visa requirements and fees for a specific country and profession
     */
    public function getRequirements(Request $request)
    {
        $request->validate([
            'country_id' => 'required|exists:countries,id',
            'profession' => 'required|string',
        ]);

        $countryId = $request->input('country_id');
        $profession = $request->input('profession');

        // Get visa requirements for the country and profession
        $requirement = VisaRequirement::where('country_id', $countryId)
            ->where(function($query) use ($profession) {
                $query->where('profession', $profession)
                      ->orWhere('profession', 'all')
                      ->orWhereNull('profession');
            })
            ->where('visa_type', 'tourist')
            ->where('is_active', true)
            ->first();

        // Get visa fees
        $fees = VisaFee::where('country_id', $countryId)
            ->where(function($query) use ($profession) {
                $query->where('profession', $profession)
                      ->orWhere('profession', 'all')
                      ->orWhereNull('profession');
            })
            ->where('visa_type', 'tourist')
            ->first();

        // If no specific requirement found, get default
        if (!$requirement) {
            $requirement = VisaRequirement::where('country_id', $countryId)
                ->where('is_template', true)
                ->where('visa_type', 'tourist')
                ->where('is_active', true)
                ->first();
        }

        // If no specific fees found, get default
        if (!$fees) {
            $fees = VisaFee::where('country_id', $countryId)
                ->whereNull('agency_id')
                ->where('visa_type', 'tourist')
                ->first();
        }

        return response()->json([
            'requirements' => [
                'processing_time' => $requirement->processing_time ?? 'Contact us',
                'validity_period' => $requirement->validity_period ?? null,
                'interview_required' => $requirement->interview_required ?? false,
                'biometrics_required' => $requirement->biometrics_required ?? false,
                'required_documents' => $requirement->required_documents ?? [],
                'profession_specific_docs' => $requirement->profession_specific_docs ?? [],
            ],
            'fees' => [
                'embassy_fee' => $fees->embassy_fee ?? 0,
                'service_fee' => $fees->service_fee ?? 0,
                'processing_fee' => $fees->processing_fee ?? 0,
                'total_fee' => $fees->total_fee ?? 0,
                'currency' => $fees->currency ?? 'BDT',
                'urgent_processing_fee' => $fees->urgent_processing_fee ?? null,
            ],
        ]);
    }
}
