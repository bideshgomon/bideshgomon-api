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
            $table->string('title');
            $table->string('slug')->unique();
            
            // Company
            $table->string('company_name');
            $table->string('company_logo')->nullable();
            $table->text('company_description')->nullable();
            
            // Location
            $table->foreignId('country_id')->nullable()->constrained()->onDelete('set null');
            $table->string('city')->nullable();
            $table->string('address')->nullable();
            
            // Job details
            $table->string('job_type')->default('full-time'); // full-time, part-time, contract, internship
            $table->string('category')->nullable();
            $table->unsignedBigInteger('job_category_id')->nullable();
            
            $table->text('description')->nullable();
            $table->text('responsibilities')->nullable();
            $table->text('requirements')->nullable();
            $table->json('skills')->nullable();
            $table->text('benefits')->nullable();
            
            // Salary
            $table->decimal('salary_min', 10, 2)->nullable();
            $table->decimal('salary_max', 10, 2)->nullable();
            $table->string('salary_currency', 3)->default('BDT');
            $table->string('salary_period')->default('monthly'); // hourly, monthly, yearly
            $table->boolean('salary_negotiable')->default(false);
            
            // Requirements
            $table->integer('positions_available')->default(1);
            $table->integer('experience_years')->nullable();
            $table->string('experience_level')->nullable(); // entry, mid, senior, executive
            $table->string('education_level')->nullable();
            $table->string('gender_preference')->nullable();
            $table->integer('age_min')->nullable();
            $table->integer('age_max')->nullable();
            
            // Fees
            $table->decimal('application_fee', 10, 2)->default(0);
            $table->decimal('agency_posted_fee', 10, 2)->nullable();
            $table->decimal('admin_approved_fee', 10, 2)->nullable();
            $table->decimal('processing_fee', 10, 2)->nullable();
            
            // Approval
            $table->string('approval_status')->default('pending'); // pending, approved, rejected
            $table->timestamp('approved_at')->nullable();
            $table->unsignedBigInteger('approved_by')->nullable();
            
            // Application
            $table->date('application_deadline')->nullable();
            $table->string('contact_email')->nullable();
            $table->string('contact_phone')->nullable();
            
            // Flags
            $table->boolean('is_featured')->default(false);
            $table->boolean('is_active')->default(true);
            $table->boolean('is_urgent')->default(false);
            
            // Meta
            $table->unsignedBigInteger('posted_by')->nullable();
            $table->timestamp('published_at')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->integer('views')->default(0);
            $table->integer('applications_count')->default(0);
            
            $table->timestamps();
            $table->softDeletes();
            
            // Indexes
            $table->index(['is_active', 'approval_status']);
            $table->index('country_id');
            $table->index('job_category_id');
            $table->index('posted_by');
            $table->index('application_deadline');
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
