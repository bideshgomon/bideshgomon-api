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
        Schema::create('work_visa_applications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade'); // Link to the applicant
            $table->foreignId('destination_country_id')->constrained('countries')->onDelete('cascade'); // Target country
            $table->foreignId('job_category_id')->nullable()->constrained('job_categories')->onDelete('set null'); // Optional: General category of job
            $table->foreignId('job_posting_id')->nullable()->constrained('job_postings')->onDelete('set null'); // Optional: Specific job applied for
            $table->foreignId('agency_id')->nullable()->constrained('agencies')->onDelete('set null'); // Optional: Agency handling the application
            $table->string('status')->default('pending'); // e.g., pending, processing, approved, rejected, document_request
            $table->text('user_notes')->nullable(); // Notes from the applicant
            $table->text('admin_notes')->nullable(); // Notes from admin/agency/consultant
            $table->string('application_reference')->nullable()->unique(); // Optional internal or external reference
            $table->timestamps(); // Includes application_date (created_at)
            $table->softDeletes(); // Optional: If applications can be soft-deleted
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('work_visa_applications');
    }
};
