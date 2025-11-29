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
        Schema::create('work_visas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('destination_country_id')->constrained('countries')->onDelete('cascade');
            $table->string('application_reference')->unique();
            
            // Employment-specific fields
            $table->string('job_title')->nullable();
            $table->string('job_category')->nullable(); // Skilled Worker, Professional, etc.
            $table->string('employer_name')->nullable();
            $table->string('employer_country')->nullable();
            $table->string('employment_type')->nullable(); // Permanent, Temporary, Seasonal
            $table->string('industry_sector')->nullable();
            $table->decimal('offered_salary', 12, 2)->nullable();
            $table->string('salary_currency', 3)->nullable(); // USD, BDT, SAR, etc.
            $table->string('salary_period')->nullable(); // month, year
            $table->boolean('has_job_offer')->default(false);
            $table->date('intended_start_date')->nullable();
            $table->integer('contract_duration_months')->nullable();
            
            // Experience & Skills
            $table->string('experience_level')->nullable(); // Entry, Mid, Senior, Expert
            $table->integer('years_of_experience')->nullable();
            $table->text('key_skills')->nullable(); // JSON or comma-separated
            $table->string('highest_qualification')->nullable();
            $table->boolean('has_language_certificate')->default(false);
            $table->string('language_certificate_type')->nullable(); // IELTS, TOEFL, etc.
            
            // Visa & Permit Details
            $table->string('work_permit_type')->nullable(); // H-1B, TSS, Iqama, etc.
            $table->boolean('requires_sponsorship')->default(true);
            $table->boolean('has_lmia_approval')->default(false); // Canada LMIA
            
            // Common visa fields
            $table->enum('status', [
                'pending',
                'submitted',
                'processing',
                'approved',
                'rejected',
                'cancelled'
            ])->default('pending');
            $table->text('user_notes')->nullable();
            $table->text('admin_notes')->nullable();
            $table->timestamp('submitted_at')->nullable();
            $table->timestamp('processed_at')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'status']);
            $table->index('destination_country_id');
            $table->index('application_reference');
            $table->index('job_category');
            $table->index('employment_type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('work_visas');
    }
};
