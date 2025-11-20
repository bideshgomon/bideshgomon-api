<?php

namespace Tests\Feature;

use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminImpersonationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->artisan('migrate');
        Role::factory()->create(['name' => 'Admin', 'slug' => 'admin']);
        Role::factory()->create(['name' => 'User', 'slug' => 'user']);
    }

    public function test_admin_can_impersonate_regular_user(): void
    {
        $adminRole = Role::where('slug', 'admin')->first();
        $userRole = Role::where('slug', 'user')->first();

        $admin = User::factory()->create(['role_id' => $adminRole->id]);
        $target = User::factory()->create(['role_id' => $userRole->id]);

        $this->actingAs($admin)
            ->post(route('admin.users.impersonate', $target->id), [
                'purpose' => 'Support: verify user dashboard layout'
            ])
            ->assertRedirect();

        // After impersonation, current authenticated user should be target
        $this->assertEquals($target->id, auth()->id());
        $this->assertTrue(session()->has('impersonator_id'));
        $this->assertEquals($admin->id, session('impersonator_id'));
        // Audit log created
        $this->assertDatabaseHas('admin_impersonation_logs', [
            'impersonator_id' => $admin->id,
            'target_user_id' => $target->id,
            'ended_at' => null,
        ]);
    }

    public function test_admin_cannot_impersonate_other_admin(): void
    {
        $adminRole = Role::where('slug', 'admin')->first();

        $adminA = User::factory()->create(['role_id' => $adminRole->id]);
        $adminB = User::factory()->create(['role_id' => $adminRole->id]);

        $this->actingAs($adminA)
            ->post(route('admin.users.impersonate', $adminB->id), [
                'purpose' => 'Attempt should fail'
            ])
            ->assertStatus(403);

        $this->assertEquals($adminA->id, auth()->id()); // Still original admin
        $this->assertFalse(session()->has('impersonator_id'));
    }

    public function test_leave_impersonation_restores_admin_identity(): void
    {
        $adminRole = Role::where('slug', 'admin')->first();
        $userRole = Role::where('slug', 'user')->first();

        $admin = User::factory()->create(['role_id' => $adminRole->id]);
        $target = User::factory()->create(['role_id' => $userRole->id]);

        $this->actingAs($admin)
            ->post(route('admin.users.impersonate', $target->id), [
                'purpose' => 'Troubleshoot user issue'
            ])
            ->assertRedirect();

        $this->assertEquals($target->id, auth()->id());

        $this->post(route('admin.impersonation.leave'))
            ->assertRedirect();

        $this->assertEquals($admin->id, auth()->id());
        $this->assertFalse(session()->has('impersonator_id'));
        // Audit log ended
        $this->assertDatabaseHas('admin_impersonation_logs', [
            'impersonator_id' => $admin->id,
            'target_user_id' => $target->id,
        ]);
        $this->assertNotNull(\App\Models\AdminImpersonationLog::first()->ended_at);
    }
}
