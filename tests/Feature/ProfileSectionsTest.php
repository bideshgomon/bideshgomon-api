<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\UserFamilyMember;
use App\Models\UserWorkExperience;
use App\Models\UserProfile;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProfileSectionsTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->user = User::factory()->create();
        $this->user->profile()->create([]);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function family_member_can_be_created_with_all_new_fields(): void
    {
        $response = $this->actingAs($this->user)
            ->postJson('/api/profile/family-members', [
                'relationship' => 'spouse',
                'full_name' => 'Jane Doe',
                'date_of_birth' => '1990-05-15',
                'gender' => 'female',
                'nationality' => 'Bangladesh',
                'country_of_residence' => 'USA',
                'city' => 'New York',
                'employer_name' => 'Tech Corp',
                'annual_income' => 50000,
                'income_currency' => 'USD',
                'education_level' => 'masters',
                'marital_status' => 'married',
                'is_dependent' => true,
                'lives_with_user' => false,
                'will_accompany' => true,
                'will_accompany_travel' => true,
                'immigration_status' => 'permanent_resident',
                'is_deceased' => false,
                'phone_number' => '+1234567890',
                'email' => 'jane@example.com',
                'emergency_contact' => true,
            ]);

        $response->assertStatus(201);
        
        $this->assertDatabaseHas('user_family_members', [
            'user_id' => $this->user->id,
            'full_name' => 'Jane Doe',
            'country_of_residence' => 'USA',
            'city' => 'New York',
            'employer_name' => 'Tech Corp',
            'annual_income' => 50000,
            'education_level' => 'masters',
            'marital_status' => 'married',
            'is_dependent' => true,
            'lives_with_user' => false,
            'will_accompany' => true,
            'will_accompany_travel' => true,
            'immigration_status' => 'permanent_resident',
            'is_deceased' => false,
            'phone_number' => '+1234567890',
            'email' => 'jane@example.com',
            'emergency_contact' => true,
        ]);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function family_member_can_be_updated_with_correct_fields(): void
    {
        $familyMember = UserFamilyMember::create([
            'user_id' => $this->user->id,
            'relationship' => 'child',
            'full_name' => 'John Doe Jr',
            'date_of_birth' => '2010-01-01',
            'gender' => 'male',
            'nationality' => 'Bangladesh',
        ]);

        $response = $this->actingAs($this->user)
            ->putJson("/api/profile/family-members/{$familyMember->id}", [
                'relationship' => 'child',
                'full_name' => 'John Doe Jr',
                'date_of_birth' => '2010-01-01',
                'gender' => 'male',
                'nationality' => 'Bangladesh',
                'education_level' => 'secondary',
                'is_dependent' => true,
                'lives_with_user' => true,
            ]);

        $response->assertStatus(200);
        
        $familyMember->refresh();
        $this->assertEquals('secondary', $familyMember->education_level);
        $this->assertTrue($familyMember->is_dependent);
        $this->assertTrue($familyMember->lives_with_user);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function work_experience_uses_correct_field_name_is_current_employment(): void
    {
        $response = $this->actingAs($this->user)
            ->postJson('/api/profile/work-experience', [
                'company_name' => 'Tech Company',
                'position' => 'Software Engineer',
                'start_date' => '2020-01-01',
                'is_current_employment' => true,
                'city' => 'Dhaka',
            ]);

        $response->assertStatus(201);
        
        $this->assertDatabaseHas('user_work_experiences', [
            'user_id' => $this->user->id,
            'company_name' => 'Tech Company',
            'is_current_employment' => true,
        ]);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function work_experience_can_be_updated_with_is_current_employment(): void
    {
        $experience = UserWorkExperience::create([
            'user_id' => $this->user->id,
            'company_name' => 'Old Company',
            'position' => 'Developer',
            'start_date' => '2018-01-01',
            'end_date' => '2020-12-31',
            'is_current_employment' => false,
        ]);

        $response = $this->actingAs($this->user)
            ->putJson("/api/profile/work-experience/{$experience->id}", [
                'company_name' => 'Old Company',
                'position' => 'Senior Developer',
                'start_date' => '2018-01-01',
                'is_current_employment' => true,
            ]);

        $response->assertStatus(200);
        
        $experience->refresh();
        $this->assertTrue($experience->is_current_employment);
        $this->assertEquals('Senior Developer', $experience->position);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function all_profile_api_routes_are_accessible(): void
    {
        $routes = [
            ['GET', '/api/profile/family-members'],
            ['GET', '/api/profile/languages'],
            ['GET', '/api/profile/security'],
            ['GET', '/api/profile/education'],
            ['GET', '/api/profile/work-experience'],
            ['GET', '/api/profile/skills'],
        ];

        foreach ($routes as [$method, $url]) {
            $response = $this->actingAs($this->user)->json($method, $url);
            $response->assertSuccessful();
        }
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function family_member_boolean_fields_cast_correctly(): void
    {
        $member = UserFamilyMember::create([
            'user_id' => $this->user->id,
            'relationship' => 'parent',
            'full_name' => 'Parent Name',
            'date_of_birth' => '1960-01-01',
            'gender' => 'male',
            'nationality' => 'Bangladesh',
            'is_dependent' => '1',
            'lives_with_user' => '0',
            'will_accompany' => '1',
            'will_accompany_travel' => '0',
            'emergency_contact' => '1',
            'is_deceased' => '0',
        ]);

        $this->assertTrue($member->is_dependent);
        $this->assertFalse($member->lives_with_user);
        $this->assertTrue($member->will_accompany);
        $this->assertFalse($member->will_accompany_travel);
        $this->assertTrue($member->emergency_contact);
        $this->assertFalse($member->is_deceased);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function profile_completion_calculates_correctly(): void
    {
        $completion = $this->user->calculateProfileCompletion();
        
    $this->assertIsInt($completion);
        $this->assertGreaterThanOrEqual(0, $completion);
        $this->assertLessThanOrEqual(100, $completion);
    }
}
