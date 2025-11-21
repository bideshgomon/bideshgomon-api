<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\UserDocument;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Services\DocumentVerificationService;
use App\Models\SystemEvent;

class SystemEventAuditTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->artisan('migrate');
        $this->seed();
    }

    public function test_document_approval_creates_system_event(): void
    {
        $user = User::factory()->create();
        $adminRole = \App\Models\Role::where('slug', 'admin')->first() ?? \App\Models\Role::factory()->create([
            'name' => 'Administrator',
            'slug' => 'admin',
            'description' => 'Admin role'
        ]);
        $admin = User::factory()->create(['role_id' => $adminRole->id]);
        $service = app(DocumentVerificationService::class);

        $doc = UserDocument::factory()->create([
            'user_id' => $user->id,
            'status' => 'pending',
            // document_type already set by factory
        ]);

    $service->approve($doc, $admin->id);

        $this->assertDatabaseHas('system_events', [
            'event_type' => 'document.approved',
            'target_id' => $doc->id,
        ]);

        $event = SystemEvent::where('event_type', 'document.approved')->first();
        $this->assertNotNull($event->data['reviewer_id']);
        $this->assertEquals('approved', $event->data['status']);
    }

    public function test_document_rejection_creates_system_event(): void
    {
        $user = User::factory()->create();
        $adminRole = \App\Models\Role::where('slug', 'admin')->first() ?? \App\Models\Role::factory()->create([
            'name' => 'Administrator',
            'slug' => 'admin',
            'description' => 'Admin role'
        ]);
        $admin = User::factory()->create(['role_id' => $adminRole->id]);
        $service = app(DocumentVerificationService::class);

        $doc = UserDocument::factory()->create([
            'user_id' => $user->id,
            'status' => 'pending',
        ]);

    $service->reject($doc, $admin->id, 'Blurry scan');

        $this->assertDatabaseHas('system_events', [
            'event_type' => 'document.rejected',
            'target_id' => $doc->id,
        ]);

        $event = SystemEvent::where('event_type', 'document.rejected')->first();
        $this->assertEquals('rejected', $event->data['status']);
        $this->assertEquals('Blurry scan', $event->data['rejection_reason']);
    }
}
