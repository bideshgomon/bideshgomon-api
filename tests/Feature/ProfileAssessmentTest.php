<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Role;
use App\Models\ProfileAssessment;
use App\Models\UserProfile;
use App\Models\Education;
use App\Models\WorkExperience;
use App\Models\Language;
use App\Services\ProfileAssessmentService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProfileAssessmentTest extends TestCase
{
    use RefreshDatabase;

    protected $user;
    protected $assessmentService;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->assessmentService = app(ProfileAssessmentService::class);
        
        // Create roles
        Role::create(['name' => 'admin', 'slug' => 'admin', 'description' => 'Administrator']);
        $userRole = Role::create(['name' => 'user', 'slug' => 'user', 'description' => 'User']);
        Role::create(['name' => 'agency', 'slug' => 'agency', 'description' => 'Agency']);
        Role::create(['name' => 'consultant', 'slug' => 'consultant', 'description' => 'Consultant']);
        
        // Create a test user with basic profile
        $this->user = User::factory()->create([
            'role_id' => $userRole->id
        ]);
    }

    /** @test */
    public function user_can_view_their_profile_assessment()
    {
        $assessment = ProfileAssessment::create([
            'user_id' => $this->user->id,
            'overall_score' => 75.50,
            'profile_completeness' => 80.00,
            'document_readiness' => 70.00,
            'visa_eligibility' => 76.50,
            'strengths' => ['Strong education background'],
            'weaknesses' => ['Missing travel history'],
            'recommendations' => [['action' => 'Add passport', 'priority' => 'high']],
            'missing_documents' => ['Passport copy'],
            'visa_eligibility_breakdown' => [],
            'risk_level' => 'low',
            'risk_factors' => [],
            'personal_info_score' => 85.00,
            'education_score' => 80.00,
            'work_experience_score' => 70.00,
            'language_proficiency_score' => 75.00,
            'financial_score' => 65.00,
            'travel_history_score' => 50.00,
            'passport_score' => 60.00,
            'recommended_visa_types' => [],
            'eligible_countries' => [],
            'ai_summary' => 'Good progress',
            'ai_metadata' => [],
            'assessed_at' => now(),
            'assessment_version' => '1.0',
        ]);

        $response = $this->actingAs($this->user)
            ->get(route('profile.assessment.show'));

        $response->assertStatus(200);
        $response->assertInertia(fn ($page) => $page
            ->component('ProfileAssessment/Show')
            ->has('assessment')
        );
    }

    /** @test */
    public function assessment_is_generated_for_new_user()
    {
        UserProfile::create([
            'user_id' => $this->user->id,
            'full_name' => 'Test User',
            'email' => $this->user->email,
            'phone' => '01712345678',
        ]);

        $assessment = $this->assessmentService->assessProfile($this->user);

        $this->assertInstanceOf(ProfileAssessment::class, $assessment);
        $this->assertEquals($this->user->id, $assessment->user_id);
        $this->assertNotNull($assessment->overall_score);
        $this->assertIsArray($assessment->strengths);
        $this->assertIsArray($assessment->weaknesses);
    }

    /** @test */
    public function assessment_scores_improve_with_complete_profile()
    {
        // Create basic profile
        UserProfile::create([
            'user_id' => $this->user->id,
            'full_name' => 'Test User',
            'email' => $this->user->email,
            'phone' => '01712345678',
        ]);

        $basicAssessment = $this->assessmentService->assessProfile($this->user);

        // Add education
        Education::create([
            'user_id' => $this->user->id,
            'degree_level' => 'masters',
            'institution_name' => 'Test University',
            'field_of_study' => 'Computer Science',
            'start_date' => now()->subYears(4),
            'end_date' => now()->subYears(2),
            'gpa' => 3.8,
        ]);

        // Add work experience
        WorkExperience::create([
            'user_id' => $this->user->id,
            'job_title' => 'Software Engineer',
            'company_name' => 'Test Company',
            'start_date' => now()->subYears(3),
            'currently_working' => true,
        ]);

        // Add language
        Language::create([
            'user_id' => $this->user->id,
            'language_name' => 'English',
            'proficiency_level' => 'fluent',
            'ielts_score' => 7.5,
        ]);

        $completeAssessment = $this->assessmentService->assessProfile($this->user, forceRefresh: true);

        // Score should improve with complete profile
        $this->assertGreaterThan($basicAssessment->overall_score, $completeAssessment->overall_score);
        $this->assertGreaterThan($basicAssessment->education_score, $completeAssessment->education_score);
        $this->assertGreaterThan($basicAssessment->work_experience_score, $completeAssessment->work_experience_score);
        $this->assertGreaterThan($basicAssessment->language_proficiency_score, $completeAssessment->language_proficiency_score);
    }

    /** @test */
    public function user_can_refresh_their_assessment()
    {
        UserProfile::create([
            'user_id' => $this->user->id,
            'full_name' => 'Test User',
            'email' => $this->user->email,
        ]);

        $response = $this->actingAs($this->user)
            ->post(route('profile.assessment.generate'));

        $response->assertRedirect(route('profile.assessment.show'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('profile_assessments', [
            'user_id' => $this->user->id,
        ]);
    }

    /** @test */
    public function assessment_identifies_missing_documents()
    {
        UserProfile::create([
            'user_id' => $this->user->id,
            'full_name' => 'Test User',
        ]);

        $assessment = $this->assessmentService->assessProfile($this->user);

        $this->assertIsArray($assessment->missing_documents);
        $this->assertNotEmpty($assessment->missing_documents);
        $this->assertContains('Passport copy (front & back)', $assessment->missing_documents);
    }

    /** @test */
    public function assessment_calculates_risk_level_correctly()
    {
        UserProfile::create([
            'user_id' => $this->user->id,
            'full_name' => 'Test User',
        ]);

        $assessment = $this->assessmentService->assessProfile($this->user);

        $this->assertContains($assessment->risk_level, ['low', 'medium', 'high']);
        
        // Low overall score should result in higher risk
        if ($assessment->overall_score < 50) {
            $this->assertEquals('high', $assessment->risk_level);
        }
    }

    /** @test */
    public function assessment_generates_recommendations()
    {
        UserProfile::create([
            'user_id' => $this->user->id,
            'full_name' => 'Test User',
        ]);

        $assessment = $this->assessmentService->assessProfile($this->user);

        $this->assertIsArray($assessment->recommendations);
        $this->assertNotEmpty($assessment->recommendations);
        
        // Each recommendation should have required fields
        foreach ($assessment->recommendations as $rec) {
            $this->assertArrayHasKey('priority', $rec);
            $this->assertArrayHasKey('action', $rec);
            $this->assertArrayHasKey('benefit', $rec);
        }
    }

    /** @test */
    public function assessment_caches_results()
    {
        UserProfile::create([
            'user_id' => $this->user->id,
            'full_name' => 'Test User',
        ]);

        // First assessment
        $assessment1 = $this->assessmentService->assessProfile($this->user);
        
        // Second call should return same assessment (not force refresh)
        $assessment2 = $this->assessmentService->assessProfile($this->user);

        $this->assertEquals($assessment1->id, $assessment2->id);
        $this->assertEquals($assessment1->assessed_at, $assessment2->assessed_at);
    }

    /** @test */
    public function user_can_get_recommendations_via_api()
    {
        UserProfile::create([
            'user_id' => $this->user->id,
            'full_name' => 'Test User',
        ]);

        $this->assessmentService->assessProfile($this->user);

        $response = $this->actingAs($this->user)
            ->get(route('profile.assessment.recommendations'));

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'recommendations',
            'missing_documents',
            'weaknesses',
        ]);
    }

    /** @test */
    public function user_can_get_score_breakdown()
    {
        UserProfile::create([
            'user_id' => $this->user->id,
            'full_name' => 'Test User',
        ]);

        $this->assessmentService->assessProfile($this->user);

        $response = $this->actingAs($this->user)
            ->get(route('profile.assessment.score-breakdown'));

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'sections' => [
                '*' => ['name', 'score', 'icon']
            ],
            'overall_score',
            'profile_completeness',
            'document_readiness',
            'visa_eligibility',
        ]);
    }

    /** @test */
    public function assessment_includes_visa_eligibility_for_countries()
    {
        UserProfile::create([
            'user_id' => $this->user->id,
            'full_name' => 'Test User',
        ]);

        $assessment = $this->assessmentService->assessProfile($this->user);

        $this->assertIsArray($assessment->visa_eligibility_breakdown);
        $this->assertNotEmpty($assessment->visa_eligibility_breakdown);
        $this->assertArrayHasKey('USA', $assessment->visa_eligibility_breakdown);
    }

    /** @test */
    public function assessment_recommends_visa_types()
    {
        UserProfile::create([
            'user_id' => $this->user->id,
            'full_name' => 'Test User',
        ]);

        Education::create([
            'user_id' => $this->user->id,
            'degree_level' => 'bachelors',
            'institution_name' => 'Test University',
            'field_of_study' => 'Engineering',
        ]);

        $assessment = $this->assessmentService->assessProfile($this->user);

        $this->assertIsArray($assessment->recommended_visa_types);
        $this->assertNotEmpty($assessment->recommended_visa_types);
        
        // Check structure
        foreach ($assessment->recommended_visa_types as $visa) {
            $this->assertArrayHasKey('type', $visa);
            $this->assertArrayHasKey('suitability', $visa);
            $this->assertArrayHasKey('reason', $visa);
        }
    }

    /** @test */
    public function guest_cannot_access_assessment()
    {
        $response = $this->get(route('profile.assessment.show'));

        $response->assertRedirect(route('login'));
    }

    /** @test */
    public function assessment_tracks_version_and_metadata()
    {
        UserProfile::create([
            'user_id' => $this->user->id,
            'full_name' => 'Test User',
        ]);

        $assessment = $this->assessmentService->assessProfile($this->user);

        $this->assertNotNull($assessment->assessment_version);
        $this->assertEquals('1.0', $assessment->assessment_version);
        $this->assertIsArray($assessment->ai_metadata);
        $this->assertArrayHasKey('algorithm_version', $assessment->ai_metadata);
        $this->assertArrayHasKey('confidence_score', $assessment->ai_metadata);
    }
}

