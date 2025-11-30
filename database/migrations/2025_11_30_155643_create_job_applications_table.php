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
            $table->unsignedBigInteger('job_posting_id')->nullable();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->unsignedBigInteger('user_cv_id')->nullable();
            
            $table->text('cover_letter')->nullable();
            $table->string('cv_file')->nullable();
            
            $table->string('status')->default('pending'); // pending, under_review, shortlisted, interviewed, offered, accepted, rejected, withdrawn
            
            // Payment
            $table->decimal('application_fee_paid', 10, 2)->default(0);
            $table->string('payment_status')->default('pending'); // pending, paid, refunded, waived
            $table->string('payment_reference')->nullable();
            $table->timestamp('payment_date')->nullable();
            
            // Admin review
            $table->text('admin_notes')->nullable();
            $table->text('rejection_reason')->nullable();
            $table->unsignedBigInteger('reviewed_by')->nullable();
            $table->timestamp('reviewed_at')->nullable();
            
            // Interview details
            $table->timestamp('interview_date')->nullable();
            $table->string('interview_location')->nullable();
            $table->text('interview_notes')->nullable();
            
            $table->timestamp('submitted_at')->nullable();
            $table->timestamps();
            $table->softDeletes();
            
            // Indexes
            $table->index(['user_id', 'status']);
            $table->index('job_posting_id');
            $table->index('status');
            $table->index('created_at');
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
