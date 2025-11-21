<?php

namespace App\Services;

use App\Models\User;
use App\Models\SmartSuggestion;
use App\Models\ProfileAssessment;
use Illuminate\Support\Facades\Log;

class SmartSuggestionsService
{
    /**
     * Generate all suggestions for a user
     */
    public function generateSuggestionsForUser(User $user): array
    {
        $suggestions = [];

        // Clear old suggestions that are completed or expired
        $this->clearOldSuggestions($user);

        // Get latest assessment
        $assessment = $user->profileAssessments()->latest('assessed_at')->first();

        // Generate different types of suggestions
        $suggestions = array_merge($suggestions, $this->generateProfileCompletionSuggestions($user));
        $suggestions = array_merge($suggestions, $this->generateDocumentSuggestions($user));
        $suggestions = array_merge($suggestions, $this->generateVisaRecommendations($user, $assessment));
        $suggestions = array_merge($suggestions, $this->generateRiskMitigationSuggestions($user, $assessment));
        $suggestions = array_merge($suggestions, $this->generateNextStepSuggestions($user));

        // Store suggestions in database
        foreach ($suggestions as $suggestionData) {
            SmartSuggestion::updateOrCreate(
                [
                    'user_id' => $user->id,
                    'suggestion_type' => $suggestionData['suggestion_type'],
                    'title' => $suggestionData['title'],
                ],
                $suggestionData
            );
        }

        return $suggestions;
    }

    /**
     * Generate profile completion suggestions
     */
    private function generateProfileCompletionSuggestions(User $user): array
    {
        $suggestions = [];
        $profile = $user->userProfile;

        // Check passport
        if ($user->passports()->count() === 0) {
            $suggestions[] = [
                'suggestion_type' => 'profile_improvement',
                'category' => 'profile',
                'priority' => 'high',
                'title' => 'Add Your Passport Information',
                'description' => 'Complete your profile by adding passport details. This is essential for visa applications and travel bookings.',
                'data' => ['section' => 'passport', 'completion' => 0],
                'action_type' => 'add_passport',
                'action_url' => route('profile.passports.index'),
                'relevance_score' => 95,
                'expires_at' => now()->addDays(30),
            ];
        }

        // Check education
        if ($user->educations()->count() === 0) {
            $suggestions[] = [
                'suggestion_type' => 'profile_improvement',
                'category' => 'profile',
                'priority' => 'medium',
                'title' => 'Add Your Education History',
                'description' => 'Adding education details strengthens your profile for student visas and job applications.',
                'data' => ['section' => 'education', 'completion' => 0],
                'action_type' => 'add_education',
                'action_url' => route('profile.education.index'),
                'relevance_score' => 75,
                'expires_at' => now()->addDays(60),
            ];
        }

        // Check work experience
        if ($user->workExperiences()->count() === 0) {
            $suggestions[] = [
                'suggestion_type' => 'profile_improvement',
                'category' => 'profile',
                'priority' => 'medium',
                'title' => 'Add Work Experience',
                'description' => 'Work experience is crucial for work visas and immigration applications. Add your employment history.',
                'data' => ['section' => 'work_experience', 'completion' => 0],
                'action_type' => 'add_experience',
                'action_url' => route('profile.work-experience.index'),
                'relevance_score' => 80,
                'expires_at' => now()->addDays(60),
            ];
        }

        // Check financial information
        $financialInfo = $user->financialInformation;
        if (!$financialInfo || !$financialInfo->monthly_income) {
            $suggestions[] = [
                'suggestion_type' => 'profile_improvement',
                'category' => 'profile',
                'priority' => 'high',
                'title' => 'Add Financial Information',
                'description' => 'Financial details are required for visa applications to prove you can support yourself abroad.',
                'data' => ['section' => 'financial', 'completion' => 0],
                'action_type' => 'add_financial',
                'action_url' => route('profile.financial.index'),
                'relevance_score' => 90,
                'expires_at' => now()->addDays(45),
            ];
        }

        // Check language proficiency
        if ($user->languages()->count() === 0) {
            $suggestions[] = [
                'suggestion_type' => 'profile_improvement',
                'category' => 'profile',
                'priority' => 'medium',
                'title' => 'Add Language Skills',
                'description' => 'Language proficiency (IELTS, TOEFL) is essential for study and work visas in English-speaking countries.',
                'data' => ['section' => 'languages', 'completion' => 0],
                'action_type' => 'add_language',
                'action_url' => route('profile.languages.index'),
                'relevance_score' => 85,
                'expires_at' => now()->addDays(90),
            ];
        }

        return $suggestions;
    }

    /**
     * Generate document upload suggestions
     */
    private function generateDocumentSuggestions(User $user): array
    {
        $suggestions = [];
        $passports = $user->passports;

        foreach ($passports as $passport) {
            if (!$passport->scan_front_upload) {
                $suggestions[] = [
                    'suggestion_type' => 'document_required',
                    'category' => 'document',
                    'priority' => 'high',
                    'title' => 'Upload Passport Scan',
                    'description' => "Upload a clear scan of your passport (No: {$passport->passport_number}) for visa applications.",
                    'data' => ['passport_id' => $passport->id, 'document_type' => 'passport_scan'],
                    'action_type' => 'upload_document',
                    'action_url' => route('profile.passports.edit', $passport),
                    'relevance_score' => 95,
                    'expires_at' => now()->addDays(30),
                ];
            }
        }

        // Check for CV
        if ($user->cvs()->count() === 0) {
            $suggestions[] = [
                'suggestion_type' => 'document_required',
                'category' => 'document',
                'priority' => 'medium',
                'title' => 'Create Your Professional CV',
                'description' => 'Build a professional CV using our CV Builder. Required for job applications and work visas.',
                'data' => ['document_type' => 'cv'],
                'action_type' => 'create_cv',
                'action_url' => route('cv-builder.create'),
                'relevance_score' => 70,
                'expires_at' => now()->addDays(90),
            ];
        }

        return $suggestions;
    }

    /**
     * Generate visa recommendations based on profile
     */
    private function generateVisaRecommendations(User $user, ?ProfileAssessment $assessment): array
    {
        $suggestions = [];

        if (!$assessment) {
            $suggestions[] = [
                'suggestion_type' => 'next_step',
                'category' => 'assessment',
                'priority' => 'urgent',
                'title' => 'Get Your Profile Assessed',
                'description' => 'Get AI-powered profile assessment to receive personalized visa recommendations and identify improvement areas.',
                'data' => ['action' => 'assessment_needed'],
                'action_type' => 'get_assessment',
                'action_url' => route('profile-assessment.show'),
                'relevance_score' => 100,
                'expires_at' => now()->addDays(7),
            ];
            return $suggestions;
        }

        // Recommend visas based on profile strength and category scores
        $visaRecommendations = $this->calculateVisaEligibility($user, $assessment);

        foreach ($visaRecommendations as $visaRec) {
            if ($visaRec['eligibility'] >= 70) {
                $priority = $visaRec['eligibility'] >= 85 ? 'high' : 'medium';
                
                $suggestions[] = [
                    'suggestion_type' => 'visa_recommendation',
                    'category' => 'visa',
                    'priority' => $priority,
                    'title' => "Consider {$visaRec['country']} {$visaRec['type']} Visa",
                    'description' => $visaRec['reason'],
                    'data' => [
                        'country' => $visaRec['country'],
                        'visa_type' => $visaRec['type'],
                        'eligibility' => $visaRec['eligibility'],
                        'requirements' => $visaRec['requirements'],
                    ],
                    'action_type' => 'apply_visa',
                    'action_url' => route('visa-application.create'),
                    'relevance_score' => $visaRec['eligibility'],
                    'expires_at' => now()->addDays(180),
                ];
            }
        }

        return $suggestions;
    }

    /**
     * Generate risk mitigation suggestions
     */
    private function generateRiskMitigationSuggestions(User $user, ?ProfileAssessment $assessment): array
    {
        $suggestions = [];

        if (!$assessment) {
            return $suggestions;
        }

        // High risk - urgent actions needed
        if ($assessment->risk_level === 'high') {
            $weakCategories = $this->getWeakCategories($assessment);

            foreach ($weakCategories as $category => $score) {
                $suggestions[] = [
                    'suggestion_type' => 'risk_mitigation',
                    'category' => 'assessment',
                    'priority' => 'urgent',
                    'title' => "Improve {$this->getCategoryName($category)}",
                    'description' => "Your {$this->getCategoryName($category)} score is {$score}/100. " . $this->getCategoryAdvice($category),
                    'data' => [
                        'category' => $category,
                        'current_score' => $score,
                        'target_score' => 70,
                    ],
                    'action_type' => 'improve_category',
                    'action_url' => $this->getCategoryActionUrl($category),
                    'relevance_score' => 100 - $score,
                    'expires_at' => now()->addDays(30),
                ];
            }
        }

        // Missing critical documents
        if ($assessment->document_completeness_score < 60) {
            $suggestions[] = [
                'suggestion_type' => 'risk_mitigation',
                'category' => 'document',
                'priority' => 'high',
                'title' => 'Complete Your Documents',
                'description' => 'Your document completeness score is low. Upload missing documents to strengthen your visa applications.',
                'data' => [
                    'current_score' => $assessment->document_completeness_score,
                    'target_score' => 80,
                ],
                'action_type' => 'upload_documents',
                'action_url' => route('profile.passports.index'),
                'relevance_score' => 90,
                'expires_at' => now()->addDays(30),
            ];
        }

        return $suggestions;
    }

    /**
     * Generate next step suggestions
     */
    private function generateNextStepSuggestions(User $user): array
    {
        $suggestions = [];

        // Check if user has applied for any service
        $hasVisaApplications = $user->visaApplications()->count() > 0;
        $hasJobApplications = $user->jobApplications()->count() > 0;
        $hasInsurance = $user->insuranceBookings()->count() > 0;

        if (!$hasVisaApplications && $user->passports()->count() > 0) {
            $suggestions[] = [
                'suggestion_type' => 'next_step',
                'category' => 'application',
                'priority' => 'medium',
                'title' => 'Ready to Apply for a Visa?',
                'description' => 'Your profile is set up. Explore visa options and start your application process.',
                'data' => ['service' => 'visa'],
                'action_type' => 'browse_visas',
                'action_url' => route('visa-application.create'),
                'relevance_score' => 80,
                'expires_at' => now()->addDays(90),
            ];
        }

        if (!$hasJobApplications && $user->workExperiences()->count() > 0) {
            $suggestions[] = [
                'suggestion_type' => 'next_step',
                'category' => 'application',
                'priority' => 'low',
                'title' => 'Explore Job Opportunities',
                'description' => 'Browse international job opportunities matching your skills and experience.',
                'data' => ['service' => 'jobs'],
                'action_type' => 'browse_jobs',
                'action_url' => route('jobs.index'),
                'relevance_score' => 60,
                'expires_at' => now()->addDays(120),
            ];
        }

        if (!$hasInsurance && $hasVisaApplications) {
            $suggestions[] = [
                'suggestion_type' => 'next_step',
                'category' => 'application',
                'priority' => 'medium',
                'title' => 'Get Travel Insurance',
                'description' => 'Protect your trip with comprehensive travel insurance. Often required for visa applications.',
                'data' => ['service' => 'insurance'],
                'action_type' => 'get_insurance',
                'action_url' => route('travel-insurance.index'),
                'relevance_score' => 75,
                'expires_at' => now()->addDays(60),
            ];
        }

        return $suggestions;
    }

    /**
     * Calculate visa eligibility for different countries
     */
    private function calculateVisaEligibility(User $user, ProfileAssessment $assessment): array
    {
        $recommendations = [];

        // Student Visa recommendations (if education score is good)
        if ($assessment->education_score >= 70) {
            $recommendations[] = [
                'country' => 'Canada',
                'type' => 'Student',
                'eligibility' => min(100, $assessment->education_score + 15),
                'reason' => 'Your strong education background makes you eligible for Canadian student visas.',
                'requirements' => ['IELTS 6.5+', 'University admission letter', 'Financial proof'],
            ];
        }

        // Work Visa recommendations (if work experience is good)
        if ($assessment->work_experience_score >= 70 && $user->workExperiences()->count() >= 2) {
            $recommendations[] = [
                'country' => 'Australia',
                'type' => 'Skilled Worker',
                'eligibility' => min(100, $assessment->work_experience_score + 10),
                'reason' => 'Your work experience qualifies you for skilled migration programs.',
                'requirements' => ['Skills assessment', 'IELTS 7.0+', 'Points test (65+)'],
            ];
        }

        // Tourist Visa (if financial situation is good)
        if ($assessment->financial_credibility_score >= 70) {
            $recommendations[] = [
                'country' => 'United Kingdom',
                'type' => 'Tourist',
                'eligibility' => $assessment->financial_credibility_score,
                'reason' => 'Your financial profile supports tourist visa applications.',
                'requirements' => ['Return ticket', 'Hotel booking', 'Bank statements (6 months)'],
            ];
        }

        // Family Visa (if family ties are documented)
        if ($user->familyMembers()->count() > 0) {
            $recommendations[] = [
                'country' => 'United States',
                'type' => 'Family',
                'eligibility' => 75,
                'reason' => 'Family reunification programs available for qualified applicants.',
                'requirements' => ['Sponsor documents', 'Relationship proof', 'Financial support'],
            ];
        }

        return $recommendations;
    }

    /**
     * Get weak categories from assessment
     */
    private function getWeakCategories(ProfileAssessment $assessment): array
    {
        $categories = [
            'profile_completeness' => $assessment->profile_completeness_score,
            'document_completeness' => $assessment->document_completeness_score,
            'education' => $assessment->education_score,
            'work_experience' => $assessment->work_experience_score,
            'financial_credibility' => $assessment->financial_credibility_score,
            'travel_history' => $assessment->travel_history_score,
            'language_proficiency' => $assessment->language_proficiency_score,
        ];

        return array_filter($categories, fn($score) => $score < 60);
    }

    /**
     * Get category display name
     */
    private function getCategoryName(string $category): string
    {
        return match($category) {
            'profile_completeness' => 'Profile Completeness',
            'document_completeness' => 'Document Completeness',
            'education' => 'Education',
            'work_experience' => 'Work Experience',
            'financial_credibility' => 'Financial Credibility',
            'travel_history' => 'Travel History',
            'language_proficiency' => 'Language Proficiency',
            default => ucwords(str_replace('_', ' ', $category)),
        };
    }

    /**
     * Get advice for improving category
     */
    private function getCategoryAdvice(string $category): string
    {
        return match($category) {
            'profile_completeness' => 'Complete all sections of your profile including education, work experience, and personal details.',
            'document_completeness' => 'Upload all required documents including passport scans, certificates, and bank statements.',
            'education' => 'Add your educational qualifications, certificates, and transcripts.',
            'work_experience' => 'Document your work history with detailed job descriptions and responsibilities.',
            'financial_credibility' => 'Add financial information including income, savings, and bank statements.',
            'travel_history' => 'Add your previous travel history to strengthen your application.',
            'language_proficiency' => 'Take IELTS/TOEFL and add your language test scores.',
            default => 'Improve this section to strengthen your overall profile.',
        };
    }

    /**
     * Get action URL for category
     */
    private function getCategoryActionUrl(string $category): string
    {
        return match($category) {
            'profile_completeness' => route('profile.show'),
            'document_completeness' => route('profile.passports.index'),
            'education' => route('profile.education.index'),
            'work_experience' => route('profile.work-experience.index'),
            'financial_credibility' => route('profile.financial.index'),
            'travel_history' => route('profile.travel-history.index'),
            'language_proficiency' => route('profile.languages.index'),
            default => route('profile.show'),
        };
    }

    /**
     * Clear old completed or expired suggestions
     */
    private function clearOldSuggestions(User $user): void
    {
        // Delete suggestions that are completed for more than 30 days
        SmartSuggestion::where('user_id', $user->id)
            ->where('is_completed', true)
            ->where('completed_at', '<', now()->subDays(30))
            ->delete();

        // Delete expired suggestions
        SmartSuggestion::where('user_id', $user->id)
            ->whereNotNull('expires_at')
            ->where('expires_at', '<', now())
            ->delete();
    }

    /**
     * Get active suggestions for user
     */
    public function getActiveSuggestions(User $user)
    {
        return SmartSuggestion::where('user_id', $user->id)
            ->active()
            ->orderByRaw("FIELD(priority, 'urgent', 'high', 'medium', 'low')")
            ->orderBy('relevance_score', 'desc')
            ->get();
    }

    /**
     * Get suggestions count by priority
     */
    public function getSuggestionsCountByPriority(User $user): array
    {
        $suggestions = SmartSuggestion::where('user_id', $user->id)
            ->active()
            ->get();

        return [
            'urgent' => $suggestions->where('priority', 'urgent')->count(),
            'high' => $suggestions->where('priority', 'high')->count(),
            'medium' => $suggestions->where('priority', 'medium')->count(),
            'low' => $suggestions->where('priority', 'low')->count(),
            'total' => $suggestions->count(),
        ];
    }
}
