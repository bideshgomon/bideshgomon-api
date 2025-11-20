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
        Schema::create('job_postings', function (Blueprint $table) {
            $table->id();
            
            // Company/Employer Information
            $table->string('company_name');
            $table->string('company_logo')->nullable();
            $table->text('company_description')->nullable();
            
            // Job Basic Information
            $table->string('title');
            $table->string('slug')->unique();
            $table->text('description');
            $table->text('requirements');
            $table->text('responsibilities')->nullable();
            
            // Job Category & Type
            $table->string('category'); // e.g., 'Healthcare', 'IT', 'Construction', 'Manufacturing'
            $table->enum('job_type', ['full_time', 'part_time', 'contract', 'temporary'])->default('full_time');
            $table->enum('experience_level', ['entry', 'mid', 'senior', 'expert'])->default('entry');
            
            // Location Information
            $table->foreignId('country_id')->constrained()->cascadeOnDelete();
            $table->string('city')->nullable();
            $table->text('address')->nullable();
            
            // Salary Information
            $table->decimal('salary_min', 10, 2)->nullable();
            $table->decimal('salary_max', 10, 2)->nullable();
            $table->string('salary_currency', 3)->default('BDT');
            $table->enum('salary_period', ['hour', 'day', 'week', 'month', 'year'])->default('month');
            $table->boolean('salary_negotiable')->default(false);
            
            // Job Requirements
            $table->string('education_level')->nullable(); // e.g., 'High School', 'Bachelor', 'Master'
            $table->integer('experience_years')->default(0);
            $table->json('skills')->nullable(); // Array of required skills
            $table->json('benefits')->nullable(); // Array of benefits
            $table->enum('gender_preference', ['any', 'male', 'female'])->default('any');
            $table->integer('age_min')->nullable();
            $table->integer('age_max')->nullable();
            
            // Application Details
            $table->integer('positions_available')->default(1);
            $table->decimal('application_fee', 8, 2)->default(0);
            $table->date('application_deadline');
            $table->text('how_to_apply')->nullable();
            $table->string('contact_email')->nullable();
            $table->string('contact_phone')->nullable();
            
            // Status & Visibility
            $table->boolean('is_active')->default(true);
            $table->boolean('is_featured')->default(false);
            $table->boolean('is_urgent')->default(false);
            $table->integer('views')->default(0);
            $table->integer('applications_count')->default(0);
            
            // Metadata
            $table->foreignId('posted_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('published_at')->nullable();
            $table->timestamp('expires_at')->nullable();
            
            $table->timestamps();
            $table->softDeletes();
            
            // Indexes
            $table->index('category');
            $table->index('country_id');
            $table->index('is_active');
            $table->index('is_featured');
            $table->index('application_deadline');
            $table->index('published_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('job_postings');
    }
};
