<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;
use Carbon\Carbon;

class ColumnLengthConsistencyTest extends TestCase
{
    use RefreshDatabase;

    #[\PHPUnit\Framework\Attributes\Test]
    public function travel_history_accepts_full_country_name(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $payload = [
            'country' => 'United Kingdom',
            'city' => 'London',
            'purpose' => 'tourism',
            'entry_date' => Carbon::now()->subDays(10)->format('Y-m-d'),
            'exit_date' => Carbon::now()->subDays(3)->format('Y-m-d'),
            'duration_days' => 7,
            // Optional fields to ensure controller doesn't hit undefined indexes
            'accommodation_type' => 'hotel',
        ];

        $res = $this->post(route('profile.travel-history.store'), $payload);
        $res->assertStatus(201);
        $this->assertDatabaseHas('user_travel_history', [
            'user_id' => $user->id,
            'country_visited' => 'United Kingdom',
        ]);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function passport_accepts_full_nationality(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $payload = [
            'passport_number' => 'P' . rand(100000,999999),
            'full_name_on_passport' => 'TEST USER',
            'issuing_country' => 'Bangladesh',
            'nationality' => 'Bangladeshi',
            'date_of_birth' => '1990-01-01',
            'place_of_birth' => 'Dhaka',
            'issue_date' => Carbon::now()->subYears(4)->format('Y-m-d'),
            'expiry_date' => Carbon::now()->addYears(6)->format('Y-m-d'),
            'passport_type' => 'regular',
            'is_primary' => true,
        ];

        $res = $this->post(route('profile.passports.store'), $payload);
        $res->assertStatus(302); // Redirect on success
        $this->assertDatabaseHas('user_passports', [
            'user_id' => $user->id,
            'nationality' => 'Bangladeshi',
        ]);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function family_member_accepts_full_nationality(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $payload = [
            'relationship' => 'spouse',
            'full_name' => 'TEST SPOUSE',
            'date_of_birth' => '1992-05-05',
            'gender' => 'female',
            'nationality' => 'Bangladeshi',
        ];

        $res = $this->post(route('api.profile.family.store'), $payload);
        $res->assertStatus(201);
        $this->assertDatabaseHas('user_family_members', [
            'user_id' => $user->id,
            'nationality' => 'Bangladeshi',
        ]);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function work_experience_accepts_full_country_name(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $payload = [
            'company_name' => 'Example Corp',
            'position' => 'Engineer',
            'start_date' => Carbon::now()->subYears(2)->format('Y-m-d'),
            'end_date' => Carbon::now()->subMonths(1)->format('Y-m-d'),
            'is_current_employment' => false,
            'country' => 'Bangladesh',
        ];

        $res = $this->post(route('api.profile.work-experience.store'), $payload);
        $res->assertStatus(201);
        $this->assertDatabaseHas('user_work_experiences', [
            'user_id' => $user->id,
            'country' => 'Bangladesh',
        ]);
    }
}
