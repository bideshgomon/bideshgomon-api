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
        Schema::create('visa_requirements', function (Blueprint $table) {
            $table->id();
            
            // Service & Country Information
            $table->foreignId('service_module_id')->nullable()->constrained()->onDelete('cascade');
            $table->string('country'); // Destination country
            $table->string('country_code', 3); // ISO code (USA, GBR, CAN, etc.)
            $table->string('visa_type'); // tourist, business, student, work, medical, transit
            $table->string('visa_category')->nullable(); // B-1/B-2, Schengen Type C, etc.
            
            // General Requirements (Multiline Text Support)
            $table->text('general_requirements'); // General requirements for all applicants
            $table->text('eligibility_criteria')->nullable(); // Who can apply
            $table->text('processing_time_info')->nullable(); // Processing duration details
            $table->text('validity_info')->nullable(); // How long visa is valid
            $table->text('important_notes')->nullable(); // Critical information
            
            // Financial Requirements
            $table->decimal('min_bank_balance', 12, 2)->nullable(); // Minimum bank balance required
            $table->integer('bank_statement_months')->nullable(); // How many months of statements needed
            $table->text('financial_requirements')->nullable(); // Detailed financial requirements
            
            // Fee Structure
            $table->decimal('government_fee', 10, 2)->nullable(); // Embassy/Consulate fee
            $table->decimal('service_fee', 10, 2)->nullable(); // Platform service fee
            $table->decimal('processing_fee_standard', 10, 2)->nullable();
            $table->decimal('processing_fee_express', 10, 2)->nullable();
            $table->decimal('processing_fee_urgent', 10, 2)->nullable();
            $table->string('currency', 3)->default('BDT');
            
            // Processing Times
            $table->integer('processing_days_standard')->nullable(); // Standard processing days
            $table->integer('processing_days_express')->nullable(); // Express processing days
            $table->integer('processing_days_urgent')->nullable(); // Urgent processing days
            
            // Interview & Biometrics
            $table->boolean('interview_required')->default(false);
            $table->text('interview_details')->nullable(); // Interview process details
            $table->boolean('biometrics_required')->default(false);
            $table->text('biometrics_details')->nullable(); // Biometric collection details
            
            // Application Details
            $table->string('application_method')->nullable(); // online, in_person, postal
            $table->string('embassy_website')->nullable(); // Official embassy URL
            $table->text('application_process')->nullable(); // Step-by-step process
            
            // Additional Information
            $table->json('specific_conditions')->nullable(); // JSON for special conditions
            $table->json('prohibited_items')->nullable(); // Items not allowed
            $table->json('tips_for_applicants')->nullable(); // Success tips
            
            // Status
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);
            
            $table->timestamps();
            
            // Indexes
            $table->index(['country', 'visa_type']);
            $table->index('country_code');
            $table->index('visa_type');
            $table->index('is_active');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('visa_requirements');
    }
};
