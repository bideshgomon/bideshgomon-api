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
            
            // Visa Type & Destination
            $table->string('visa_type'); // tourist, business, student, work, medical, transit
            $table->string('destination_country');
            $table->string('destination_country_code', 3);
            $table->string('visa_category')->nullable(); // single_entry, multiple_entry, long_term
            $table->integer('duration_days')->nullable();
            
            // Applicant Information
            $table->string('applicant_name');
            $table->string('applicant_email');
            $table->string('applicant_phone');
            $table->date('applicant_dob');
            $table->string('passport_number');
            $table->date('passport_issue_date');
            $table->date('passport_expiry_date');
            $table->string('passport_issuing_country');
            $table->string('nationality');
            
            // Travel Details
            $table->date('intended_travel_date')->nullable();
            $table->date('intended_return_date')->nullable();
            $table->text('travel_purpose')->nullable();
            $table->string('occupation')->nullable();
            $table->text('previous_visa_history')->nullable();
            
            // Processing Information
            $table->enum('processing_type', ['standard', 'express', 'urgent'])->default('standard');
            $table->integer('processing_days')->nullable();
            $table->enum('status', ['draft', 'submitted', 'under_review', 'documents_requested', 'interview_scheduled', 'approved', 'rejected', 'cancelled'])->default('submitted');
            $table->text('status_notes')->nullable();
            $table->timestamp('submitted_at')->nullable();
            $table->timestamp('reviewed_at')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->timestamp('rejected_at')->nullable();
            $table->text('rejection_reason')->nullable();
            
            // Financial Information
            $table->decimal('service_fee', 10, 2);
            $table->decimal('government_fee', 10, 2)->nullable();
            $table->decimal('processing_fee', 10, 2)->nullable();
            $table->decimal('total_amount', 10, 2);
            $table->enum('payment_status', ['pending', 'paid', 'refunded', 'failed'])->default('pending');
            $table->string('payment_method')->nullable();
            $table->string('payment_reference')->nullable();
            $table->timestamp('paid_at')->nullable();
            
            // Assignment & Tracking
            $table->foreignId('assigned_to')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('assigned_at')->nullable();
            $table->integer('priority')->default(0);
            
            // Additional Information
            $table->json('additional_info')->nullable();
            $table->json('required_documents')->nullable();
            $table->text('special_requests')->nullable();
            $table->text('internal_notes')->nullable();
            
            // Metadata
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            
            $table->timestamps();
            $table->softDeletes();
            
            // Indexes
            $table->index('user_id');
            $table->index('status');
            $table->index('destination_country');
            $table->index('visa_type');
            $table->index('payment_status');
            $table->index('assigned_to');
            $table->index('submitted_at');
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
