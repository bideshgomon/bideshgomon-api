<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;

class OnboardingController extends Controller
{
    /**
     * Show the onboarding welcome page
     */
    public function welcome()
    {
        $user = auth()->user();
        $profile = $user->profile;

        // Calculate profile completion percentage
        $profileCompletion = $this->calculateProfileCompletion($profile);

        return Inertia::render('Onboarding/Welcome', [
            'profile' => $profile,
            'profileCompletion' => $profileCompletion,
        ]);
    }

    /**
     * Calculate profile completion percentage
     */
    private function calculateProfileCompletion($profile)
    {
        if (!$profile) {
            return 0;
        }

        $fields = [
            // Basic Info (20%)
            'phone' => 4,
            'date_of_birth' => 4,
            'gender' => 4,
            'nationality' => 4,
            'marital_status' => 4,
            
            // Address (15%)
            'present_address_line' => 3,
            'present_city' => 3,
            'present_division' => 3,
            'present_district' => 3,
            'present_postal_code' => 3,
            
            // Documents (20%)
            'nid' => 10,
            'passport_number' => 10,
            
            // Employment (20%)
            'employer_name' => 5,
            'employer_address' => 5,
            'monthly_income_bdt' => 5,
            'employment_start_date' => 5,
            
            // Financial (15%)
            'bank_name' => 3,
            'bank_account_number' => 3,
            'bank_balance_bdt' => 3,
            'has_investments' => 3,
            'net_worth_bdt' => 3,
            
            // Documents (10%)
            'tax_return_path' => 5,
            'salary_certificate_path' => 5,
        ];

        $totalPoints = 0;
        foreach ($fields as $field => $points) {
            if (!empty($profile->$field)) {
                $totalPoints += $points;
            }
        }

        return min(100, $totalPoints);
    }
}
