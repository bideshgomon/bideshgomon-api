<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * This table connects visa requirements to specific professions/occupations.
     * Different professions may have different document requirements and fees.
     */
    public function up(): void
    {
        Schema::create('profession_visa_requirements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('visa_requirement_id')->constrained()->onDelete('cascade');
            
            // Profession Information
            $table->string('profession_category'); // employed, self_employed, student, retired, unemployed, business_owner
            $table->string('profession_title')->nullable(); // Specific job title
            
            // Profession-Specific Requirements (Multiline Support)
            $table->text('additional_requirements'); // Extra requirements for this profession
            $table->text('additional_documents')->nullable(); // Extra documents needed
            
            // Financial Requirements Override
            $table->decimal('min_bank_balance_override', 12, 2)->nullable(); // Profession-specific balance
            $table->text('financial_proof_details')->nullable(); // Specific financial evidence needed
            
            // Fee Adjustments
            $table->decimal('fee_adjustment', 10, 2)->nullable(); // Additional fee for this profession
            $table->string('fee_adjustment_type')->nullable(); // fixed, percentage
            
            // Supporting Documentation
            $table->json('required_documents')->nullable(); // Array of additional document codes
            $table->text('employment_proof_requirements')->nullable(); // Employment verification details
            $table->text('income_proof_requirements')->nullable(); // Income verification details
            
            // Special Conditions
            $table->text('special_conditions')->nullable(); // Any special rules for this profession
            $table->text('rejection_risks')->nullable(); // Common rejection reasons
            $table->text('success_tips')->nullable(); // Tips for this profession
            
            // Priority & Status
            $table->integer('risk_level')->default(1); // 1=low, 2=medium, 3=high risk profession
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);
            
            $table->timestamps();
            
            // Indexes
            $table->index('visa_requirement_id');
            $table->index('profession_category');
            $table->index('risk_level');
            $table->unique(['visa_requirement_id', 'profession_category', 'profession_title'], 'visa_profession_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('profession_visa_requirements');
    }
};
