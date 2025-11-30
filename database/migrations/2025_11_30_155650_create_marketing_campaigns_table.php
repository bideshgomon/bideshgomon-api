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
            $table->string('type')->default('email'); // email, sms, notification
            $table->string('status')->default('draft'); // draft, scheduled, sending, sent, cancelled
            
            // Email template
            $table->unsignedBigInteger('email_template_id')->nullable();
            $table->string('subject')->nullable();
            $table->text('message')->nullable();
            
            // Audience
            $table->string('audience_type')->default('all'); // all, role, specific, filtered
            $table->json('audience_filters')->nullable();
            $table->json('recipient_ids')->nullable();
            
            // Scheduling
            $table->timestamp('scheduled_at')->nullable();
            $table->timestamp('started_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            
            // Tracking metrics
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
            
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
            $table->softDeletes();
            
            // Indexes
            $table->index('status');
            $table->index('scheduled_at');
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
