<?php

namespace App\Services;

use App\Models\User;
use App\Models\ProfileAssessment;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

class ProfileAssessmentService
{
    /**
     * Assess user profile and generate comprehensive score.
     */
    public function assessProfile(User $user, bool $forceRefresh = false): ProfileAssessment
    {
        // Check if recent assessment exists (within 7 days)
        if (!$forceRefresh) {
            $recentAssessment = ProfileAssessment::where('user_id', $user->id)
                ->where('assessed_at', '>', now()->subDays(7))
                ->first();
            
            if ($recentAssessment) {
                return $recentAssessment;
            }
        }

        return DB::transaction(function () use ($user) {
            // Load all relationships needed for assessment
            $user->load([
                'userProfile',
                'educations',
                'workExperiences',
                'languages',
                'familyMembers',
                'userPassports',
                'travelHistory',
                'visaHistory',
                'financialInformation',
                'securityInformation',
            ]);

            // Calculate individual section scores
            $personalInfoScore = $this->calculatePersonalInfoScore($user);
            $educationScore = $this->calculateEducationScore($user);
            $workExperienceScore = $this->calculateWorkExperienceScore($user);
            $languageScore = $this->calculateLanguageScore($user);
            $financialScore = $this->calculateFinancialScore($user);
            $travelHistoryScore = $this->calculateTravelHistoryScore($user);
            $passportScore = $this->calculatePassportScore($user);

            // Calculate overall metrics
            $profileCompleteness = $this->calculateProfileCompleteness([
                $personalInfoScore,
                $educationScore,
                $workExperienceScore,
                $languageScore,
                $financialScore,
                $travelHistoryScore,
                $passportScore,
            ]);

            $documentReadiness = $this->calculateDocumentReadiness($user);
            $visaEligibility = $this->calculateVisaEligibility($user);
            $overallScore = $this->calculateOverallScore(
                $profileCompleteness,
                $documentReadiness,
                $visaEligibility
            );

            // Generate insights
            $strengths = $this->identifyStrengths($user);
            $weaknesses = $this->identifyWeaknesses($user);
            $recommendations = $this->generateRecommendations($user, $weaknesses);
            $missingDocuments = $this->identifyMissingDocuments($user);
            
            // Assess risk
            $riskLevel = $this->assessRiskLevel($user, $overallScore);
            $riskFactors = $this->identifyRiskFactors($user);

            // Visa recommendations
            $eligibleCountries = $this->determineEligibleCountries($user);
            $recommendedVisaTypes = $this->recommendVisaTypes($user);
            $visaEligibilityBreakdown = $this->calculateVisaEligibilityBreakdown($user);

            // Generate AI summary
            $aiSummary = $this->generateAISummary($user, $overallScore, $strengths, $weaknesses);

            // Create or update assessment
            $assessment = ProfileAssessment::updateOrCreate(
                ['user_id' => $user->id],
                [
                    'overall_score' => $overallScore,
                    'profile_completeness' => $profileCompleteness,
                    'document_readiness' => $documentReadiness,
                    'visa_eligibility' => $visaEligibility,
                    'strengths' => $strengths,
                    'weaknesses' => $weaknesses,
                    'recommendations' => $recommendations,
                    'missing_documents' => $missingDocuments,
                    'visa_eligibility_breakdown' => $visaEligibilityBreakdown,
                    'risk_level' => $riskLevel,
                    'risk_factors' => $riskFactors,
                    'personal_info_score' => $personalInfoScore,
                    'education_score' => $educationScore,
                    'work_experience_score' => $workExperienceScore,
                    'language_proficiency_score' => $languageScore,
                    'financial_score' => $financialScore,
                    'travel_history_score' => $travelHistoryScore,
                    'passport_score' => $passportScore,
                    'recommended_visa_types' => $recommendedVisaTypes,
                    'eligible_countries' => $eligibleCountries,
                    'ai_summary' => $aiSummary,
                    'ai_metadata' => [
                        'algorithm_version' => '1.0',
                        'confidence_score' => $this->calculateConfidenceScore($user),
                        'data_quality_score' => $this->assessDataQuality($user),
                    ],
                    'assessed_at' => now(),
                    'assessment_version' => '1.0',
                ]
            );

            // Clear any cached data for this user
            Cache::forget("profile_assessment_{$user->id}");

            return $assessment;
        });
    }

    /**
     * Calculate personal information score (0-100).
     */
    protected function calculatePersonalInfoScore(User $user): float
    {
        $score = 0;
        $maxScore = 100;
        $profile = $user->userProfile;

        if (!$profile) {
            return 0;
        }

        // Basic fields (60 points)
        if ($profile->full_name) $score += 10;
        if ($profile->phone) $score += 10;
        if ($profile->email) $score += 10;
        if ($profile->date_of_birth) $score += 10;
        if ($profile->gender) $score += 10;
        if ($profile->nationality) $score += 10;

        // Address fields (20 points)
        if ($profile->present_address) $score += 5;
        if ($profile->permanent_address) $score += 5;
        if ($profile->city) $score += 5;
        if ($profile->postal_code) $score += 5;

        // Additional details (20 points)
        if ($profile->marital_status) $score += 5;
        if ($profile->religion) $score += 5;
        if ($profile->nid_number) $score += 5;
        if ($profile->passport_number) $score += 5;

        return min($score, $maxScore);
    }

    /**
     * Calculate education score (0-100).
     */
    protected function calculateEducationScore(User $user): float
    {
        $educations = $user->educations;
        
        if ($educations->isEmpty()) {
            return 0;
        }

        $score = 40; // Base score for having at least one education

        // Quality factors
        foreach ($educations as $education) {
            if ($education->certificates_upload) $score += 10;
            if ($education->gpa) $score += 5;
            if ($education->degree_level === 'masters' || $education->degree_level === 'phd') {
                $score += 15;
            }
        }

        return min($score, 100);
    }

    /**
     * Calculate work experience score (0-100).
     */
    protected function calculateWorkExperienceScore(User $user): float
    {
        $experiences = $user->workExperiences;
        
        if ($experiences->isEmpty()) {
            return 0;
        }

        $score = 30; // Base score

        // Calculate total years of experience
        $totalMonths = 0;
        foreach ($experiences as $exp) {
            if ($exp->start_date && $exp->end_date) {
                $totalMonths += $exp->start_date->diffInMonths($exp->end_date);
            } elseif ($exp->start_date && $exp->currently_working) {
                $totalMonths += $exp->start_date->diffInMonths(now());
            }
        }

        $years = $totalMonths / 12;
        if ($years >= 5) $score += 30;
        elseif ($years >= 3) $score += 20;
        elseif ($years >= 1) $score += 10;

        // Completeness bonus
        foreach ($experiences as $exp) {
            if ($exp->job_description && $exp->company_name) {
                $score += 5;
            }
        }

        return min($score, 100);
    }

    /**
     * Calculate language proficiency score (0-100).
     */
    protected function calculateLanguageScore(User $user): float
    {
        $languages = $user->languages;
        
        if ($languages->isEmpty()) {
            return 0;
        }

        $score = 20; // Base score

        foreach ($languages as $lang) {
            // English is heavily weighted for international migration
            if (strtolower($lang->language_name) === 'english') {
                if ($lang->ielts_score && $lang->ielts_score >= 7.0) {
                    $score += 40;
                } elseif ($lang->ielts_score && $lang->ielts_score >= 6.0) {
                    $score += 30;
                } elseif ($lang->ielts_score) {
                    $score += 20;
                }
                
                if ($lang->toefl_score && $lang->toefl_score >= 100) {
                    $score += 30;
                } elseif ($lang->toefl_score) {
                    $score += 20;
                }
            } else {
                // Other languages
                if ($lang->proficiency_level === 'fluent' || $lang->proficiency_level === 'native') {
                    $score += 15;
                } elseif ($lang->proficiency_level === 'intermediate') {
                    $score += 10;
                }
            }
        }

        return min($score, 100);
    }

    /**
     * Calculate financial readiness score (0-100).
     */
    protected function calculateFinancialScore(User $user): float
    {
        $financial = $user->financialInformation;
        
        if (!$financial) {
            return 0;
        }

        $score = 20; // Base score for having financial info

        // Bank statements
        if ($financial->bank_statements_upload) $score += 20;
        
        // Income verification
        if ($financial->income_tax_return_upload) $score += 15;
        if ($financial->salary_slips_upload) $score += 15;
        
        // Property/assets
        if ($financial->property_documents_upload) $score += 10;
        
        // Sponsorship (if applicable)
        if ($financial->sponsor_documents_upload) $score += 10;
        
        // Income level (if specified)
        if ($financial->monthly_income_bdt) {
            if ($financial->monthly_income_bdt >= 100000) $score += 10;
            elseif ($financial->monthly_income_bdt >= 50000) $score += 5;
        }

        return min($score, 100);
    }

    /**
     * Calculate travel history score (0-100).
     */
    protected function calculateTravelHistoryScore(User $user): float
    {
        $travelHistory = $user->travelHistory;
        
        if ($travelHistory->isEmpty()) {
            return 30; // Not having travel history isn't necessarily bad
        }

        $score = 50; // Base score for having travel history

        // Bonus for multiple trips
        if ($travelHistory->count() >= 5) $score += 20;
        elseif ($travelHistory->count() >= 3) $score += 15;
        elseif ($travelHistory->count() >= 1) $score += 10;

        // Bonus for developed country visits
        $developedCountries = ['USA', 'UK', 'Canada', 'Australia', 'Germany', 'France', 'Japan'];
        foreach ($travelHistory as $travel) {
            if (in_array($travel->country, $developedCountries)) {
                $score += 5;
            }
        }

        return min($score, 100);
    }

    /**
     * Calculate passport score (0-100).
     */
    protected function calculatePassportScore(User $user): float
    {
        $passports = $user->userPassports;
        
        if ($passports->isEmpty()) {
            return 0;
        }

        $score = 40; // Base score for having passport

        foreach ($passports as $passport) {
            if ($passport->scan_front_upload) $score += 10;
            if ($passport->scan_back_upload) $score += 10;
            
            // Check validity (at least 6 months remaining)
            if ($passport->expiry_date && $passport->expiry_date->gt(now()->addMonths(6))) {
                $score += 20;
            }
            
            if ($passport->is_primary) $score += 10;
        }

        return min($score, 100);
    }

    /**
     * Calculate profile completeness (average of all sections).
     */
    protected function calculateProfileCompleteness(array $sectionScores): float
    {
        return collect($sectionScores)->average();
    }

    /**
     * Calculate document readiness.
     */
    protected function calculateDocumentReadiness(User $user): float
    {
        $score = 0;
        $totalRequired = 10;
        
        // Check essential documents
        if ($user->userPassports()->exists()) $score++;
        if ($user->educations()->whereNotNull('certificates_upload')->exists()) $score++;
        if ($user->userProfile && $user->userProfile->nid_scan_upload) $score++;
        
        $financial = $user->financialInformation;
        if ($financial) {
            if ($financial->bank_statements_upload) $score++;
            if ($financial->income_tax_return_upload) $score++;
            if ($financial->salary_slips_upload) $score++;
        }
        
        if ($user->workExperiences()->exists()) $score++;
        if ($user->languages()->whereNotNull('test_certificate_upload')->exists()) $score++;
        
        // Add more document checks as needed
        $score += 2; // Placeholder for additional document types
        
        return ($score / $totalRequired) * 100;
    }

    /**
     * Calculate visa eligibility score.
     */
    protected function calculateVisaEligibility(User $user): float
    {
        $score = 0;
        
        // Strong passport
        if ($user->userPassports()->where('expiry_date', '>', now()->addMonths(6))->exists()) {
            $score += 20;
        }
        
        // Good education
        if ($user->educations()->whereIn('degree_level', ['bachelors', 'masters', 'phd'])->exists()) {
            $score += 20;
        }
        
        // Work experience
        if ($user->workExperiences()->count() >= 2) {
            $score += 15;
        }
        
        // Language proficiency
        $hasGoodEnglish = $user->languages()
            ->where('language_name', 'English')
            ->where(function($q) {
                $q->where('ielts_score', '>=', 6.0)
                  ->orWhere('toefl_score', '>=', 80);
            })
            ->exists();
        
        if ($hasGoodEnglish) {
            $score += 25;
        }
        
        // Financial stability
        if ($user->financialInformation && $user->financialInformation->bank_statements_upload) {
            $score += 15;
        }
        
        // No red flags
        $security = $user->securityInformation;
        if (!$security || (!$security->criminal_record && !$security->visa_refusals)) {
            $score += 5;
        }
        
        return min($score, 100);
    }

    /**
     * Calculate overall score (weighted average).
     */
    protected function calculateOverallScore(float $completeness, float $documents, float $eligibility): float
    {
        return ($completeness * 0.3) + ($documents * 0.3) + ($eligibility * 0.4);
    }

    /**
     * Identify profile strengths.
     */
    protected function identifyStrengths(User $user): array
    {
        $strengths = [];
        
        if ($user->educations()->whereIn('degree_level', ['masters', 'phd'])->exists()) {
            $strengths[] = 'Advanced education credentials (Master\'s/PhD)';
        }
        
        if ($user->workExperiences()->count() >= 3) {
            $strengths[] = 'Extensive work experience across multiple roles';
        }
        
        if ($user->languages()->where('language_name', 'English')->where('ielts_score', '>=', 7.0)->exists()) {
            $strengths[] = 'Excellent English proficiency (IELTS 7.0+)';
        }
        
        if ($user->travelHistory()->count() >= 3) {
            $strengths[] = 'Strong international travel history';
        }
        
        if ($user->financialInformation && $user->financialInformation->bank_statements_upload) {
            $strengths[] = 'Financial documentation available';
        }
        
        return $strengths;
    }

    /**
     * Identify profile weaknesses.
     */
    protected function identifyWeaknesses(User $user): array
    {
        $weaknesses = [];
        
        // Use loaded relationship collections instead of calling builder-specific methods like isEmpty()
        if ($user->relationLoaded('educations') ? $user->educations->isEmpty() : $user->educations()->count() === 0) {
            $weaknesses[] = 'No education records added';
        }
        
        if ($user->relationLoaded('workExperiences') ? $user->workExperiences->isEmpty() : $user->workExperiences()->count() === 0) {
            $weaknesses[] = 'No work experience documented';
        }
        
        if ($user->relationLoaded('languages') ? $user->languages->isEmpty() : $user->languages()->count() === 0) {
            $weaknesses[] = 'Language proficiency not documented';
        }
        
        if (!$user->userPassports()->exists()) {
            $weaknesses[] = 'Passport information missing';
        }
        
        if (!$user->financialInformation) {
            $weaknesses[] = 'Financial information incomplete';
        }
        
        return $weaknesses;
    }

    /**
     * Generate actionable recommendations.
     */
    protected function generateRecommendations(User $user, array $weaknesses): array
    {
        $recommendations = [];
        
        foreach ($weaknesses as $weakness) {
            if (str_contains($weakness, 'education')) {
                $recommendations[] = [
                    'priority' => 'high',
                    'action' => 'Add your education credentials',
                    'benefit' => 'Increases visa eligibility by 20%',
                    'route' => 'profile.edit',
                ];
            }
            
            if (str_contains($weakness, 'work experience')) {
                $recommendations[] = [
                    'priority' => 'high',
                    'action' => 'Document your work history',
                    'benefit' => 'Strengthens visa applications significantly',
                    'route' => 'profile.edit',
                ];
            }
            
            if (str_contains($weakness, 'Language')) {
                $recommendations[] = [
                    'priority' => 'high',
                    'action' => 'Take IELTS or TOEFL test',
                    'benefit' => 'Essential for most visa types',
                    'route' => 'profile.edit',
                ];
            }
            
            if (str_contains($weakness, 'Passport')) {
                $recommendations[] = [
                    'priority' => 'critical',
                    'action' => 'Upload passport details',
                    'benefit' => 'Required for all visa applications',
                    'route' => 'profile.edit',
                ];
            }
            
            if (str_contains($weakness, 'Financial')) {
                $recommendations[] = [
                    'priority' => 'medium',
                    'action' => 'Upload financial documents',
                    'benefit' => 'Demonstrates financial stability',
                    'route' => 'profile.edit',
                ];
            }
        }
        
        return $recommendations;
    }

    /**
     * Identify missing documents.
     */
    protected function identifyMissingDocuments(User $user): array
    {
        $missing = [];
        
        if (!$user->userPassports()->exists()) {
            $missing[] = 'Passport copy (front & back)';
        }
        
        if (!$user->educations()->whereNotNull('certificates_upload')->exists()) {
            $missing[] = 'Education certificates';
        }
        
        if (!$user->languages()->whereNotNull('test_certificate_upload')->exists()) {
            $missing[] = 'Language test certificate (IELTS/TOEFL)';
        }
        
        $financial = $user->financialInformation;
        if (!$financial || !$financial->bank_statements_upload) {
            $missing[] = 'Bank statements (last 6 months)';
        }
        
        if (!$financial || !$financial->income_tax_return_upload) {
            $missing[] = 'Income tax returns';
        }
        
        return $missing;
    }

    /**
     * Assess risk level.
     */
    protected function assessRiskLevel(User $user, float $overallScore): string
    {
        if ($overallScore >= 75) {
            return 'low';
        } elseif ($overallScore >= 50) {
            return 'medium';
        } else {
            return 'high';
        }
    }

    /**
     * Identify risk factors.
     */
    protected function identifyRiskFactors(User $user): array
    {
        $risks = [];
        
        $security = $user->securityInformation;
        if ($security) {
            if ($security->criminal_record) {
                $risks[] = 'Criminal record disclosed';
            }
            if ($security->visa_refusals) {
                $risks[] = 'Previous visa refusals';
            }
        }
        
        if ($user->visaHistory()->where('visa_status', 'rejected')->exists()) {
            $risks[] = 'History of visa rejections';
        }
        
        if (!$user->travelHistory()->exists()) {
            $risks[] = 'No international travel history';
        }
        
        return $risks;
    }

    /**
     * Determine eligible countries.
     */
    protected function determineEligibleCountries(User $user): array
    {
        $countries = [];
        
        // This would be more sophisticated with real ML/rules engine
        $hasPassport = $user->userPassports()->exists();
        $hasEducation = $user->educations()->exists();
        $hasExperience = $user->workExperiences()->count() >= 1;
        $hasEnglish = $user->languages()->where('language_name', 'English')->exists();
        
        if ($hasPassport && $hasEducation && $hasEnglish) {
            $countries[] = ['code' => 'US', 'name' => 'United States', 'probability' => 65];
            $countries[] = ['code' => 'CA', 'name' => 'Canada', 'probability' => 70];
            $countries[] = ['code' => 'UK', 'name' => 'United Kingdom', 'probability' => 75];
            $countries[] = ['code' => 'AU', 'name' => 'Australia', 'probability' => 68];
        }
        
        if ($hasPassport) {
            $countries[] = ['code' => 'AE', 'name' => 'United Arab Emirates', 'probability' => 85];
            $countries[] = ['code' => 'MY', 'name' => 'Malaysia', 'probability' => 80];
        }
        
        return $countries;
    }

    /**
     * Recommend visa types.
     */
    protected function recommendVisaTypes(User $user): array
    {
        $visaTypes = [];
        
        if ($user->workExperiences()->count() >= 2) {
            $visaTypes[] = [
                'type' => 'Work Visa',
                'suitability' => 80,
                'reason' => 'Strong work experience profile',
            ];
        }
        
        if ($user->educations()->whereIn('degree_level', ['bachelors', 'masters'])->exists()) {
            $visaTypes[] = [
                'type' => 'Student Visa',
                'suitability' => 75,
                'reason' => 'Educational credentials support further study',
            ];
        }
        
        if ($user->familyMembers()->exists()) {
            $visaTypes[] = [
                'type' => 'Family Visa',
                'suitability' => 60,
                'reason' => 'Family documentation available',
            ];
        }
        
        $visaTypes[] = [
            'type' => 'Tourist Visa',
            'suitability' => 70,
            'reason' => 'Generally accessible option',
        ];
        
        return $visaTypes;
    }

    /**
     * Calculate visa eligibility breakdown per country.
     */
    protected function calculateVisaEligibilityBreakdown(User $user): array
    {
        $breakdown = [];
        
        $countries = ['USA', 'Canada', 'UK', 'Australia', 'UAE'];
        
        foreach ($countries as $country) {
            $breakdown[$country] = [
                'score' => rand(40, 90), // Placeholder - would use actual eligibility rules
                'factors' => [
                    'education' => $this->calculateEducationScore($user),
                    'experience' => $this->calculateWorkExperienceScore($user),
                    'language' => $this->calculateLanguageScore($user),
                    'financial' => $this->calculateFinancialScore($user),
                ],
            ];
        }
        
        return $breakdown;
    }

    /**
     * Generate human-readable AI summary.
     */
    protected function generateAISummary(User $user, float $overallScore, array $strengths, array $weaknesses): string
    {
        $name = $user->userProfile->full_name ?? $user->name;
        
        if ($overallScore >= 80) {
            $assessment = "excellent visa application readiness";
        } elseif ($overallScore >= 60) {
            $assessment = "good visa application potential";
        } elseif ($overallScore >= 40) {
            $assessment = "moderate visa application readiness";
        } else {
            $assessment = "early stage visa application preparation";
        }
        
        $summary = "{$name}'s profile shows {$assessment} with an overall score of " . number_format($overallScore, 1) . "/100. ";
        
        if (count($strengths) > 0) {
            $summary .= "Key strengths include " . implode(', ', array_slice($strengths, 0, 2)) . ". ";
        }
        
        if (count($weaknesses) > 0) {
            $summary .= "Areas for improvement: " . implode(', ', array_slice($weaknesses, 0, 2)) . ".";
        }
        
        return $summary;
    }

    /**
     * Calculate confidence score for the assessment.
     */
    protected function calculateConfidenceScore(User $user): float
    {
        $dataPoints = 0;
        
        if ($user->userProfile) $dataPoints += 10;
        $dataPoints += $user->educations()->count() * 5;
        $dataPoints += $user->workExperiences()->count() * 5;
        $dataPoints += $user->languages()->count() * 5;
        if ($user->financialInformation) $dataPoints += 10;
        $dataPoints += $user->travelHistory()->count() * 3;
        if ($user->userPassports()->exists()) $dataPoints += 10;
        
        return min($dataPoints, 100);
    }

    /**
     * Assess data quality.
     */
    protected function assessDataQuality(User $user): float
    {
        $quality = 50; // Base score
        
        // Complete records increase quality
        if ($user->educations()->whereNotNull('certificates_upload')->count() > 0) {
            $quality += 10;
        }
        
        if ($user->workExperiences()->whereNotNull('job_description')->count() > 0) {
            $quality += 10;
        }
        
        if ($user->languages()->whereNotNull('test_certificate_upload')->count() > 0) {
            $quality += 10;
        }
        
        if ($user->userPassports()->whereNotNull('scan_front_upload')->count() > 0) {
            $quality += 10;
        }
        
        return min($quality, 100);
    }
}
