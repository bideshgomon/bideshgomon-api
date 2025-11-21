<?php

namespace App\Http\Controllers;

use App\Services\ProfileAssessmentService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Inertia\Inertia;

class ProfileAssessmentController extends Controller
{
    protected $assessmentService;

    public function __construct(ProfileAssessmentService $assessmentService)
    {
        $this->assessmentService = $assessmentService;
    }

    /**
     * Display user's profile assessment.
     */
    public function show()
    {
        $user = auth()->user();
        
        // Try to get cached assessment or generate new one
        $assessment = Cache::remember(
            "profile_assessment_{$user->id}",
            now()->addHours(1),
            function () use ($user) {
                return $this->assessmentService->assessProfile($user);
            }
        );

        return Inertia::render('ProfileAssessment/Show', [
            'assessment' => [
                'overall_score' => $assessment->overall_score,
                'score_color' => $assessment->score_color,
                'profile_completeness' => $assessment->profile_completeness,
                'document_readiness' => $assessment->document_readiness,
                'visa_eligibility' => $assessment->visa_eligibility,
                'strengths' => $assessment->strengths,
                'weaknesses' => $assessment->weaknesses,
                'recommendations' => $assessment->recommendations,
                'missing_documents' => $assessment->missing_documents,
                'risk_level' => $assessment->risk_level,
                'risk_color' => $assessment->risk_color,
                'risk_factors' => $assessment->risk_factors,
                'section_scores' => [
                    'personal_info' => $assessment->personal_info_score,
                    'education' => $assessment->education_score,
                    'work_experience' => $assessment->work_experience_score,
                    'language' => $assessment->language_proficiency_score,
                    'financial' => $assessment->financial_score,
                    'travel_history' => $assessment->travel_history_score,
                    'passport' => $assessment->passport_score,
                ],
                'recommended_visa_types' => $assessment->recommended_visa_types,
                'eligible_countries' => $assessment->eligible_countries,
                'visa_eligibility_breakdown' => $assessment->visa_eligibility_breakdown,
                'ai_summary' => $assessment->ai_summary,
                'assessed_at' => format_bd_date($assessment->assessed_at),
                'is_recent' => $assessment->isRecent(),
            ],
        ]);
    }

    /**
     * Generate or refresh profile assessment.
     */
    public function generate(Request $request)
    {
        $user = auth()->user();
        
        $assessment = $this->assessmentService->assessProfile($user, forceRefresh: true);

        return redirect()->route('profile.assessment.show')
            ->with('success', 'Profile assessment updated successfully!');
    }

    /**
     * Get recommendations for improving profile.
     */
    public function recommendations()
    {
        $user = auth()->user();
        
        $assessment = Cache::remember(
            "profile_assessment_{$user->id}",
            now()->addHours(1),
            function () use ($user) {
                return $this->assessmentService->assessProfile($user);
            }
        );

        \Log::info('ProfileAssessmentController@recommendations invoked', ['user_id' => $user->id]);

        return response()->json([
            'recommendations' => $assessment->recommendations,
            'missing_documents' => $assessment->missing_documents,
            'weaknesses' => $assessment->weaknesses,
        ]);
    }

    /**
     * Get score breakdown by section.
     */
    public function scoreBreakdown()
    {
        $user = auth()->user();
        
        $assessment = Cache::remember(
            "profile_assessment_{$user->id}",
            now()->addHours(1),
            function () use ($user) {
                return $this->assessmentService->assessProfile($user);
            }
        );

        \Log::info('ProfileAssessmentController@scoreBreakdown invoked', ['user_id' => $user->id]);

        return response()->json([
            'sections' => [
                [
                    'name' => 'Personal Information',
                    'score' => $assessment->personal_info_score,
                    'icon' => 'user',
                ],
                [
                    'name' => 'Education',
                    'score' => $assessment->education_score,
                    'icon' => 'academic-cap',
                ],
                [
                    'name' => 'Work Experience',
                    'score' => $assessment->work_experience_score,
                    'icon' => 'briefcase',
                ],
                [
                    'name' => 'Language Proficiency',
                    'score' => $assessment->language_proficiency_score,
                    'icon' => 'chat',
                ],
                [
                    'name' => 'Financial Status',
                    'score' => $assessment->financial_score,
                    'icon' => 'currency-bangladeshi',
                ],
                [
                    'name' => 'Travel History',
                    'score' => $assessment->travel_history_score,
                    'icon' => 'globe',
                ],
                [
                    'name' => 'Passport',
                    'score' => $assessment->passport_score,
                    'icon' => 'identification',
                ],
            ],
            'overall_score' => $assessment->overall_score,
            'profile_completeness' => $assessment->profile_completeness,
            'document_readiness' => $assessment->document_readiness,
            'visa_eligibility' => $assessment->visa_eligibility,
        ]);
    }

    /**
     * Get visa eligibility for specific country.
     */
    public function visaEligibility(Request $request)
    {
        $request->validate([
            'country' => 'required|string|max:3',
        ]);

        $user = auth()->user();
        
        $assessment = Cache::remember(
            "profile_assessment_{$user->id}",
            now()->addHours(1),
            function () use ($user) {
                return $this->assessmentService->assessProfile($user);
            }
        );

        $country = $request->input('country');
        $breakdown = $assessment->visa_eligibility_breakdown[$country] ?? null;

        if (!$breakdown) {
            return response()->json([
                'message' => 'Eligibility data not available for this country',
            ], 404);
        }

        return response()->json([
            'country' => $country,
            'eligibility' => $breakdown,
            'recommended_visa_types' => collect($assessment->recommended_visa_types)
                ->sortByDesc('suitability')
                ->values()
                ->all(),
        ]);
    }
}
