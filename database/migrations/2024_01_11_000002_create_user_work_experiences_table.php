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
        Schema::create('user_work_experiences', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            
            // Basic Work Information
            $table->string('company_name');
            $table->string('position');
            $table->date('start_date');
            $table->date('end_date')->nullable();
            
            // Enhanced for Visa Applications
            $table->string('country', 2)->nullable(); // Where worked
            $table->string('city')->nullable();
            $table->text('job_description')->nullable(); // Detailed duties
            $table->decimal('salary', 12, 2)->nullable(); // Monthly/yearly
            $table->string('currency', 3)->default('BDT');
            $table->enum('salary_period', ['monthly', 'yearly'])->default('monthly');
            $table->string('supervisor_name')->nullable();
            $table->string('supervisor_phone')->nullable();
            $table->string('supervisor_email')->nullable();
            $table->string('employment_letter_path')->nullable();
            $table->json('payslip_paths')->nullable(); // Array of payslip files
            $table->json('tax_return_paths')->nullable(); // Tax documents
            $table->text('reason_for_leaving')->nullable();
            $table->boolean('is_current_employment')->default(false);
            $table->enum('employment_type', ['full_time', 'part_time', 'contract', 'freelance', 'internship'])->nullable();
            
            $table->timestamps();
            
            // Indexes
            $table->index('user_id');
            $table->index('start_date');
            $table->index('is_current_employment');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_work_experiences');
    }
};
