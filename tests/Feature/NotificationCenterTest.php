<?php

namespace Tests\Feature;

use App\Models\Role;
use App\Models\User;
use App\Models\UserNotification;
use App\Services\NotificationService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class NotificationCenterTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->adminRole = Role::factory()->admin()->create();
        $this->userRole = Role::factory()->user()->create();
    }

    public function test_user_sees_notifications_and_can_mark_read(): void
    {
        $user = User::factory()->create(['role_id' => $this->userRole->id]);
        $service = new NotificationService();
        $n = $service->send($user->id, 'Test Title', 'Test Body', ['priority' => 'high']);

        $response = $this->actingAs($user)->get(route('notifications.index'));
        $response->assertStatus(200);
        // Inertia shared props should include unread_notifications = 1
        $response->assertSee('"unread_notifications":1');
        $response->assertSee('Test Title');

        $this->actingAs($user)
            ->post(route('notifications.read', $n->id))
            ->assertRedirect();

        $this->assertNotNull($n->fresh()->read_at);
        // After marking read, dashboard should not show badge number (still may show 0 but not pulse class)
    $after = $this->actingAs($user)->get(route('notifications.index'));
    $after->assertStatus(200);
    $after->assertSee('"unread_notifications":0');
    }

    public function test_admin_can_broadcast_notifications(): void
    {
        $admin = User::factory()->create(['role_id' => $this->adminRole->id]);
        $users = User::factory()->count(3)->create(['role_id' => $this->userRole->id]);

        $payload = [
            'title' => 'Maintenance Notice',
            'body' => 'System maintenance tonight 11 PM BST',
            'priority' => 'critical',
            'scope' => 'all'
        ];

        $this->actingAs($admin)
            ->post(route('admin.notifications.broadcast'), $payload)
            ->assertRedirect();

        $this->assertEquals(4, UserNotification::count()); // 3 users + admin itself
        $critical = UserNotification::where('priority', 'critical')->count();
        $this->assertEquals(4, $critical);
    }
}
