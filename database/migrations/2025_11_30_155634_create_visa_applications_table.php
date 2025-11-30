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
        Schema::create('visa_applications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('application_reference')->unique();
            
            // Visa details
            $table->string('visa_type');
            $table->string('destination_country');
            $table->string('destination_country_code', 3)->nullable();
            $table->string('visa_category')->nullable();
            $table->integer('duration_days')->nullable();
            
            // Applicant details
            $table->string('applicant_name');
            $table->string('applicant_email');
            $table->string('applicant_phone');
            $table->date('applicant_dob')->nullable();
            
            // Passport details
            $table->string('passport_number');
            $table->date('passport_issue_date')->nullable();
            $table->date('passport_expiry_date')->nullable();
            $table->string('passport_issuing_country')->nullable();
            $table->string('nationality');
            
            // Travel details
            $table->date('intended_travel_date')->nullable();
            $table->date('intended_return_date')->nullable();
            $table->string('travel_purpose')->nullable();
            $table->string('occupation')->nullable();
            $table->text('previous_visa_history')->nullable();
            
            // Processing details
            $table->string('processing_type')->default('standard'); // standard, express, urgent
            $table->integer('processing_days')->nullable();
            $table->string('status')->default('pending'); // pending, submitted, under_review, approved, rejected, cancelled
            $table->text('status_notes')->nullable();
            
            // Timeline
            $table->timestamp('submitted_at')->nullable();
            $table->timestamp('reviewed_at')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->timestamp('rejected_at')->nullable();
            $table->text('rejection_reason')->nullable();
            
            // Pricing
            $table->decimal('service_fee', 10, 2)->default(0);
            $table->decimal('government_fee', 10, 2)->default(0);
            $table->decimal('processing_fee', 10, 2)->default(0);
            $table->decimal('total_amount', 10, 2)->default(0);
            
            // Payment tracking
            $table->string('payment_status')->default('pending'); // pending, paid, partial, refunded, failed
            $table->string('payment_method')->nullable();
            $table->string('payment_reference')->nullable();
            $table->timestamp('paid_at')->nullable();
            
            // Assignment
            $table->unsignedBigInteger('assigned_to')->nullable();
            $table->timestamp('assigned_at')->nullable();
            $table->integer('priority')->default(0); // 0=normal, 1=high, 2=urgent
            
            // Additional information
            $table->json('additional_info')->nullable();
            $table->json('required_documents')->nullable();
            $table->text('special_requests')->nullable();
            $table->text('internal_notes')->nullable();
            
            // Tracking
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            
            $table->timestamps();
            $table->softDeletes();
            
            // Indexes
            $table->index(['user_id', 'status']);
            $table->index(['status', 'payment_status']);
            $table->index('assigned_to');
            $table->index('destination_country');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('visa_applications');
    }
};
