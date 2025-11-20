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
        Schema::create('job_applications', function (Blueprint $table) {
            $table->id();
            
            // Foreign Keys
            $table->foreignId('job_posting_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            
            // Application Details
            $table->text('cover_letter')->nullable();
            $table->string('cv_file')->nullable(); // Path to uploaded CV
            $table->foreignId('user_cv_id')->nullable()->constrained('user_cvs')->nullOnDelete(); // Reference to built CV
            
            // Payment Information
            $table->decimal('application_fee_paid', 8, 2)->default(0);
            $table->enum('payment_status', ['pending', 'paid', 'refunded'])->default('pending');
            $table->string('payment_reference')->nullable();
            $table->timestamp('payment_date')->nullable();
            
            // Application Status
            $table->enum('status', [
                'pending',
                'under_review',
                'shortlisted',
                'interviewed',
                'offered',
                'accepted',
                'rejected',
                'withdrawn'
            ])->default('pending');
            
            // Admin Notes
            $table->text('admin_notes')->nullable();
            $table->text('rejection_reason')->nullable();
            
            // Interview Details
            $table->timestamp('interview_date')->nullable();
            $table->string('interview_location')->nullable();
            $table->text('interview_notes')->nullable();
            
            // Tracking
            $table->timestamp('reviewed_at')->nullable();
            $table->foreignId('reviewed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('submitted_at')->nullable();
            
            $table->timestamps();
            $table->softDeletes();
            
            // Indexes
            $table->index('job_posting_id');
            $table->index('user_id');
            $table->index('status');
            $table->index('payment_status');
            $table->index(['job_posting_id', 'user_id']); // Prevent duplicate applications
            
            // Unique constraint to prevent duplicate applications
            $table->unique(['job_posting_id', 'user_id'], 'unique_job_application');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('job_applications');
    }
};
