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
        Schema::create('smart_suggestions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('suggestion_type'); // 'visa_recommendation', 'document_required', 'profile_improvement', 'next_step', 'risk_mitigation'
            $table->string('category'); // 'visa', 'profile', 'document', 'application', 'assessment'
            $table->string('priority')->default('medium'); // 'low', 'medium', 'high', 'urgent'
            $table->string('title');
            $table->text('description');
            $table->json('data')->nullable(); // Additional structured data
            $table->string('action_type')->nullable(); // 'complete_profile', 'upload_document', 'apply_visa', etc.
            $table->string('action_url')->nullable(); // Route to take action
            $table->boolean('is_completed')->default(false);
            $table->timestamp('completed_at')->nullable();
            $table->boolean('is_dismissed')->default(false);
            $table->timestamp('dismissed_at')->nullable();
            $table->timestamp('expires_at')->nullable(); // Suggestions can expire
            $table->integer('relevance_score')->default(50); // 0-100, how relevant is this suggestion
            $table->timestamps();

            // Indexes
            $table->index(['user_id', 'is_completed', 'is_dismissed']);
            $table->index(['suggestion_type', 'priority']);
            $table->index('expires_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('smart_suggestions');
    }
};
