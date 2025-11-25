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
        Schema::create('service_applications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('service_module_id')->constrained('service_modules')->onDelete('cascade');
            $table->unsignedBigInteger('agency_id')->nullable(); // No FK - agencies in different database
            $table->string('application_number')->unique();
            $table->enum('status', ['pending', 'assigned', 'quoted', 'accepted', 'in_progress', 'completed', 'cancelled', 'rejected'])->default('pending');
            $table->json('application_data')->comment('Service-specific form data');
            $table->decimal('quoted_amount', 10, 2)->nullable();
            $table->decimal('service_fee', 10, 2)->nullable();
            $table->decimal('platform_commission', 10, 2)->nullable();
            $table->decimal('agency_earnings', 10, 2)->nullable();
            $table->integer('processing_time_days')->nullable();
            $table->text('special_notes')->nullable();
            $table->text('rejection_reason')->nullable();
            $table->timestamp('assigned_at')->nullable();
            $table->timestamp('quoted_at')->nullable();
            $table->timestamp('accepted_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();
            
            $table->index(['user_id', 'service_module_id']);
            $table->index(['agency_id', 'status']);
            $table->index('application_number');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('service_applications');
    }
};
