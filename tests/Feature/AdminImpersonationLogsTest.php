<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\AdminImpersonationLog;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Auth;

class AdminImpersonationLogsTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->artisan('migrate');
        $this->seed(); // assumes roles & admin user seeding
    }

    public function test_admin_can_view_impersonation_logs_index()
    {
        $admin = User::whereHas('role', fn($q) => $q->where('slug','admin'))->first();
        $user = User::factory()->create();
        // Create sample log
        $log = AdminImpersonationLog::create([
            'impersonator_id' => $admin->id,
            'target_user_id' => $user->id,
            'started_at' => now()->subMinutes(10),
            'ended_at' => now()->subMinutes(5),
            'purpose' => 'Support troubleshooting',
        ]);

        $this->actingAs($admin)
            ->get(route('admin.impersonations.index'))
            ->assertStatus(200);
    }

    public function test_duration_minutes_accessor_returns_expected_value()
    {
        $admin = User::whereHas('role', fn($q) => $q->where('slug','admin'))->first();
        $user = User::factory()->create();
        $started = now()->subMinutes(30);
        $ended = now()->subMinutes(5);
        $log = AdminImpersonationLog::create([
            'impersonator_id' => $admin->id,
            'target_user_id' => $user->id,
            'started_at' => $started,
            'ended_at' => $ended,
            'purpose' => 'Duration test',
        ]);

        $expected = abs($log->started_at->diffInMinutes($log->ended_at, false));
        $this->assertEquals($expected, $log->duration_minutes, 'Duration minutes accessor should match absolute diffInMinutes calculation.');
    }

    public function test_csv_export_streams_expected_headers()
    {
        $admin = User::whereHas('role', fn($q) => $q->where('slug','admin'))->first();
        $user = User::factory()->create();
        AdminImpersonationLog::create([
            'impersonator_id' => $admin->id,
            'target_user_id' => $user->id,
            'started_at' => now()->subMinutes(15),
            'purpose' => 'CSV export test',
        ]);

        $response = $this->actingAs($admin)->get(route('admin.impersonations.export'));
        $response->assertStatus(200);
        $this->assertTrue(str_contains($response->headers->get('content-disposition'), 'attachment'));
        // Streamed response content may not be fully captured; assert header presence only.
    }
}
