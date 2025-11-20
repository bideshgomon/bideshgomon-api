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
        Schema::table('user_profiles', function (Blueprint $table) {
            // Employment Information
            $table->string('employer_name')->nullable();
            $table->string('employer_address')->nullable();
            $table->date('employment_start_date')->nullable();
            $table->decimal('monthly_income_bdt', 15, 2)->nullable();
            $table->decimal('annual_income_bdt', 15, 2)->nullable();
            
            // Bank Information
            $table->string('bank_name')->nullable();
            $table->string('bank_branch')->nullable();
            $table->string('bank_account_number')->nullable();
            $table->string('bank_account_type')->nullable(); // savings, current, fixed
            $table->decimal('bank_balance_bdt', 15, 2)->nullable();
            $table->string('bank_statement_path')->nullable();
            
            // Property Ownership
            $table->boolean('owns_property')->default(false);
            $table->string('property_type')->nullable(); // house, apartment, land
            $table->text('property_address')->nullable();
            $table->decimal('property_value_bdt', 15, 2)->nullable();
            $table->string('property_documents_path')->nullable();
            
            // Vehicle Ownership
            $table->boolean('owns_vehicle')->default(false);
            $table->string('vehicle_type')->nullable(); // car, motorcycle, other
            $table->string('vehicle_make_model')->nullable();
            $table->integer('vehicle_year')->nullable();
            $table->decimal('vehicle_value_bdt', 15, 2)->nullable();
            
            // Investments & Assets
            $table->boolean('has_investments')->default(false);
            $table->string('investment_types')->nullable(); // stocks, bonds, mutual funds, FDR
            $table->decimal('investment_value_bdt', 15, 2)->nullable();
            
            // Liabilities
            $table->boolean('has_liabilities')->default(false);
            $table->string('liability_types')->nullable(); // loan, mortgage, credit card
            $table->decimal('liabilities_amount_bdt', 15, 2)->nullable();
            
            // Financial Summary
            $table->decimal('total_assets_bdt', 15, 2)->nullable();
            $table->decimal('net_worth_bdt', 15, 2)->nullable();
            
            // Supporting Documents
            $table->string('tax_return_path')->nullable();
            $table->string('salary_certificate_path')->nullable();
            $table->string('financial_sponsor_info')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('user_profiles', function (Blueprint $table) {
            $table->dropColumn([
                'employer_name',
                'employer_address',
                'employment_start_date',
                'monthly_income_bdt',
                'annual_income_bdt',
                'bank_name',
                'bank_branch',
                'bank_account_number',
                'bank_account_type',
                'bank_balance_bdt',
                'bank_statement_path',
                'owns_property',
                'property_type',
                'property_address',
                'property_value_bdt',
                'property_documents_path',
                'owns_vehicle',
                'vehicle_type',
                'vehicle_make_model',
                'vehicle_year',
                'vehicle_value_bdt',
                'has_investments',
                'investment_types',
                'investment_value_bdt',
                'has_liabilities',
                'liability_types',
                'liabilities_amount_bdt',
                'total_assets_bdt',
                'net_worth_bdt',
                'tax_return_path',
                'salary_certificate_path',
                'financial_sponsor_info',
            ]);
        });
    }
};
