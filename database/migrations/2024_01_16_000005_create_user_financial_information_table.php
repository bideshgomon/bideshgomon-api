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
        Schema::create('user_financial_information', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained()->onDelete('cascade');
            
            // Income Information
            $table->decimal('annual_income', 15, 2)->nullable();
            $table->decimal('monthly_income', 12, 2)->nullable();
            $table->string('currency', 3)->default('BDT');
            $table->enum('source_of_income', [
                'salary',
                'business',
                'investment',
                'rental',
                'pension',
                'family_support',
                'multiple_sources',
                'other'
            ])->nullable();
            $table->text('income_details')->nullable();
            
            // Employment (if salaried)
            $table->string('employer_name')->nullable();
            $table->string('employer_address')->nullable();
            $table->string('employer_phone')->nullable();
            $table->string('job_title')->nullable();
            $table->date('employment_start_date')->nullable();
            
            // Banking Information
            $table->string('primary_bank_name')->nullable();
            $table->string('bank_account_number')->nullable();
            $table->string('bank_branch')->nullable();
            $table->string('bank_address')->nullable();
            $table->decimal('current_balance', 15, 2)->nullable();
            $table->string('balance_currency', 3)->nullable();
            
            // Tax Information
            $table->string('tax_id_number')->nullable(); // TIN in Bangladesh
            $table->string('tax_id_country', 2)->nullable();
            $table->boolean('files_tax_returns')->default(false);
            
            // Assets
            $table->enum('property_ownership', [
                'own_home',
                'renting',
                'family_home',
                'multiple_properties',
                'no_property'
            ])->nullable();
            $table->decimal('property_value', 15, 2)->nullable();
            $table->string('property_value_currency', 3)->nullable();
            $table->text('property_address')->nullable();
            
            $table->boolean('owns_vehicle')->default(false);
            $table->string('vehicle_details')->nullable();
            $table->decimal('vehicle_value', 12, 2)->nullable();
            
            $table->decimal('investment_value', 15, 2)->nullable();
            $table->text('investment_details')->nullable(); // Stocks, bonds, savings
            
            $table->decimal('total_assets', 15, 2)->nullable();
            $table->decimal('total_liabilities', 15, 2)->nullable();
            
            // Sponsorship (if applicable)
            $table->boolean('has_sponsor')->default(false);
            $table->string('sponsor_name')->nullable();
            $table->enum('sponsor_relationship', [
                'spouse',
                'parent',
                'sibling',
                'child',
                'friend',
                'employer',
                'organization',
                'other'
            ])->nullable();
            $table->string('sponsor_country', 2)->nullable();
            $table->string('sponsor_phone')->nullable();
            $table->string('sponsor_email')->nullable();
            $table->text('sponsor_address')->nullable();
            $table->decimal('sponsor_annual_income', 15, 2)->nullable();
            $table->string('sponsor_income_currency', 3)->nullable();
            $table->text('sponsor_occupation')->nullable();
            
            // Document Storage
            $table->json('bank_statement_paths')->nullable(); // Array of file paths (last 6 months)
            $table->json('tax_return_paths')->nullable(); // Last 3 years
            $table->string('employment_letter_path')->nullable();
            $table->string('salary_certificate_path')->nullable();
            $table->json('payslip_paths')->nullable(); // Last 3-6 months
            $table->json('property_document_paths')->nullable(); // Property deeds
            $table->json('investment_document_paths')->nullable();
            $table->json('sponsor_document_paths')->nullable(); // Sponsor's financial docs
            $table->string('sponsor_affidavit_path')->nullable();
            
            // Proof of Funds
            $table->decimal('available_funds', 15, 2)->nullable(); // Liquid funds available
            $table->string('funds_currency', 3)->nullable();
            $table->date('funds_verified_date')->nullable();
            
            // Notes
            $table->text('notes')->nullable();
            
            $table->timestamps();
            
            // Indexes
            $table->index('user_id');
            $table->index('has_sponsor');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_financial_information');
    }
};
