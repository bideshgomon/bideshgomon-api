<?php

namespace Tests\Feature;

use App\Models\Role;
use App\Models\User;
use App\Models\UserDocument;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;

class DocumentVerificationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        // Seed roles or create minimal ones
        $this->adminRole = Role::factory()->admin()->create();
        $this->userRole = Role::factory()->user()->create();
    }

    public function test_user_can_upload_document(): void
    {
        Storage::fake('public');
        $user = User::factory()->create(['role_id' => $this->userRole->id]);

        $this->actingAs($user)
            ->post(route('documents.store'), [
                'document_type' => 'passport_scan',
                'file' => UploadedFile::fake()->create('passport.pdf', 120, 'application/pdf'),
                'is_primary' => true,
            ])
            ->assertRedirect(route('documents.index'));

        $this->assertDatabaseCount('user_documents', 1);
        $doc = UserDocument::first();
        $this->assertTrue($doc->is_primary);
        Storage::disk('public')->assertExists($doc->storage_path);
    }

    public function test_admin_can_approve_document(): void
    {
        $admin = User::factory()->create(['role_id' => $this->adminRole->id]);
        $user = User::factory()->create(['role_id' => $this->userRole->id]);
        $doc = UserDocument::factory()->create(['user_id' => $user->id]);

        $this->actingAs($admin)
            ->post(route('admin.documents.approve', $doc->id))
            ->assertRedirect();

        $this->assertTrue($doc->fresh()->isApproved());
        $this->assertNotNull($doc->fresh()->reviewed_at);
        $this->assertDatabaseHas('user_notifications', [
            'user_id' => $user->id,
            'type' => 'document_approved',
            'title' => 'Document Approved'
        ]);
    }

    public function test_admin_can_reject_document_with_reason(): void
    {
        $admin = User::factory()->create(['role_id' => $this->adminRole->id]);
        $user = User::factory()->create(['role_id' => $this->userRole->id]);
        $doc = UserDocument::factory()->create(['user_id' => $user->id]);

        $this->actingAs($admin)
            ->post(route('admin.documents.reject', $doc->id), [
                'reason' => 'Blurry image'
            ])
            ->assertRedirect();

        $doc->refresh();
        $this->assertTrue($doc->isRejected());
        $this->assertEquals('Blurry image', $doc->rejection_reason);
        $this->assertDatabaseHas('user_notifications', [
            'user_id' => $user->id,
            'type' => 'document_rejected',
            'title' => 'Document Rejected'
        ]);
    }

    public function test_setting_primary_unsets_previous_primary(): void
    {
        Storage::fake('public');
        $user = User::factory()->create(['role_id' => $this->userRole->id]);
        $first = UserDocument::factory()->create(['user_id' => $user->id, 'document_type' => 'passport_scan', 'is_primary' => true]);
        $this->assertTrue($first->is_primary);

        $this->actingAs($user)
            ->post(route('documents.store'), [
                'document_type' => 'passport_scan',
                'file' => UploadedFile::fake()->create('passport2.pdf', 120, 'application/pdf'),
                'is_primary' => true,
            ])
            ->assertRedirect(route('documents.index'));

        $first->refresh();
        $newPrimary = UserDocument::where('id', '!=', $first->id)->first();
        $this->assertFalse($first->is_primary);
        $this->assertTrue($newPrimary->is_primary);
    }
}
