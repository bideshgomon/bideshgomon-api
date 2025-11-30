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
            $table->unsignedBigInteger('service_module_id')->nullable();
            $table->unsignedBigInteger('country_id')->nullable();
            $table->unsignedBigInteger('managed_by_agency')->nullable();
            $table->timestamp('agency_assigned_at')->nullable();
            
            // Country & Visa Type
            $table->string('country');
            $table->string('country_code', 3)->nullable();
            $table->string('profession')->nullable();
            $table->string('visa_type');
            $table->string('visa_category')->nullable();
            
            // Requirements
            $table->json('required_documents')->nullable();
            $table->json('profession_specific_docs')->nullable();
            $table->string('processing_time')->nullable();
            $table->string('validity_period')->nullable();
            $table->boolean('is_template')->default(false);
            
            // Details
            $table->text('general_requirements')->nullable();
            $table->text('eligibility_criteria')->nullable();
            $table->text('processing_time_info')->nullable();
            $table->text('validity_info')->nullable();
            $table->text('important_notes')->nullable();
            
            // Financial requirements
            $table->decimal('min_bank_balance', 12, 2)->nullable();
            $table->integer('bank_statement_months')->nullable();
            $table->text('financial_requirements')->nullable();
            
            // Fees
            $table->decimal('government_fee', 10, 2)->default(0);
            $table->decimal('service_fee', 10, 2)->default(0);
            $table->decimal('agency_service_fee', 10, 2)->nullable();
            $table->decimal('agency_processing_fee', 10, 2)->nullable();
            $table->decimal('platform_commission', 10, 2)->nullable();
            $table->decimal('platform_commission_rate', 5, 2)->nullable();
            $table->decimal('total_agency_earnings', 10, 2)->nullable();
            $table->decimal('total_applicant_cost', 10, 2)->nullable();
            
            // Processing fees by type
            $table->decimal('processing_fee_standard', 10, 2)->default(0);
            $table->decimal('processing_fee_express', 10, 2)->nullable();
            $table->decimal('processing_fee_urgent', 10, 2)->nullable();
            $table->string('currency', 3)->default('BDT');
            
            // Processing days
            $table->integer('processing_days_standard')->nullable();
            $table->integer('processing_days_express')->nullable();
            $table->integer('processing_days_urgent')->nullable();
            
            // Additional requirements
            $table->boolean('interview_required')->default(false);
            $table->text('interview_details')->nullable();
            $table->boolean('biometrics_required')->default(false);
            $table->text('biometrics_details')->nullable();
            
            // Application info
            $table->string('application_method')->nullable(); // online, in-person, mail
            $table->string('embassy_website')->nullable();
            $table->text('application_process')->nullable();
            
            // Additional details
            $table->json('specific_conditions')->nullable();
            $table->json('prohibited_items')->nullable();
            $table->json('tips_for_applicants')->nullable();
            
            // Flags
            $table->boolean('is_active')->default(true);
            $table->boolean('agency_can_edit')->default(false);
            $table->text('admin_notes')->nullable();
            $table->integer('sort_order')->default(0);
            
            $table->timestamps();
            $table->softDeletes();
            
            // Indexes
            $table->index(['country', 'visa_type']);
            $table->index('service_module_id');
            $table->index('managed_by_agency');
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
