<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\UserEducation;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Carbon\Carbon;

class UserEducationTest extends TestCase
{
    use RefreshDatabase;

    #[\PHPUnit\Framework\Attributes\Test]
    public function user_can_create_education_with_full_country_name(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $payload = [
            'institution_name' => 'University of Dhaka',
            'degree' => 'Bachelor of Science (B.Sc.)',
            'field_of_study' => 'Computer Science',
            'start_date' => Carbon::now()->subYears(4)->format('Y-m-d'),
            'end_date' => Carbon::now()->subYear()->format('Y-m-d'),
            'country' => 'Bangladesh', // full name (previously causing length error under strict mode)
            'city' => 'Dhaka',
            'is_completed' => true,
            'gpa_or_grade' => '3.85',
            'language_of_instruction' => 'English',
            'courses_completed' => 'Algorithms, Data Structures',
            'honors_awards' => 'Dean List',
        ];

        $response = $this->post(route('profile.education.store'), $payload);
        $response->assertStatus(201);

        $this->assertDatabaseHas('user_educations', [
            'user_id' => $user->id,
            'institution_name' => 'University of Dhaka',
            'country' => 'Bangladesh',
        ]);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function validation_blocks_missing_required_fields(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $response = $this->post(route('profile.education.store'), [
            // Missing institution_name & degree & start_date
        ]);

        $response->assertSessionHasErrors(['institution_name', 'degree', 'start_date']);
    }
}
