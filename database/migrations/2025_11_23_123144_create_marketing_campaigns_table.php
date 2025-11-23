<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('marketing_campaigns', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->enum('type', ['email', 'sms', 'push', 'notification'])->default('email');
            $table->enum('status', ['draft', 'scheduled', 'sending', 'sent', 'paused', 'cancelled'])->default('draft');
            
            // Email template
            $table->foreignId('email_template_id')->nullable()->constrained()->nullOnDelete();
            $table->string('subject')->nullable();
            $table->text('message')->nullable();
            
            // Audience
            $table->enum('audience_type', ['all_users', 'segment', 'custom'])->default('all_users');
            $table->json('audience_filters')->nullable();
            $table->json('recipient_ids')->nullable();
            
            // Scheduling
            $table->timestamp('scheduled_at')->nullable();
            $table->timestamp('started_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            
            // Tracking
            $table->integer('total_recipients')->default(0);
            $table->integer('sent_count')->default(0);
            $table->integer('delivered_count')->default(0);
            $table->integer('opened_count')->default(0);
            $table->integer('clicked_count')->default(0);
            $table->integer('bounced_count')->default(0);
            $table->integer('unsubscribed_count')->default(0);
            
            // A/B Testing
            $table->boolean('is_ab_test')->default(false);
            $table->json('ab_test_config')->nullable();
            
            // Settings
            $table->json('settings')->nullable();
            
            // Creator
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            
            $table->softDeletes();
            $table->timestamps();
            
            $table->index(['status', 'scheduled_at']);
            $table->index('created_by');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('marketing_campaigns');
    }
};
