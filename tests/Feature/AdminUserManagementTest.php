<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;
use App\Models\Role;

class AdminUserManagementTest extends TestCase
{
    use RefreshDatabase;

    #[\PHPUnit\Framework\Attributes\Test]
    public function admin_users_index_loads(): void
    {
        $adminRole = Role::create([
            'name' => 'Admin',
            'slug' => 'admin',
            'description' => 'Administrator'
        ]);
        $user = User::factory()->create(['role_id' => $adminRole->id]);
        $this->actingAs($user);
        $response = $this->get('/admin/users');
        $response->assertStatus(200);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function admin_user_cannot_be_suspended_or_deleted(): void
    {
        $adminRole = Role::create([
            'name' => 'Admin',
            'slug' => 'admin',
            'description' => 'Administrator'
        ]);
        $admin = User::factory()->create(['role_id' => $adminRole->id]);
        $this->actingAs($admin);

        // Attempt suspend
        $suspend = $this->post(route('admin.users.suspend', $admin->id), [
            'reason' => 'Testing'
        ]);
        $suspend->assertSessionHas('error');

        // Attempt delete
        $delete = $this->delete(route('admin.users.destroy', $admin->id));
        $delete->assertSessionHas('error');
        $this->assertNotNull($admin->fresh());
    }
}
