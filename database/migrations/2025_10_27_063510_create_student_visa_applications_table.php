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
        Schema::create('student_visa_applications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade'); // Student applicant
            $table->foreignId('destination_country_id')->constrained('countries')->onDelete('cascade'); // Study destination
            $table->foreignId('university_id')->nullable()->constrained('universities')->onDelete('set null'); // Chosen university
            $table->foreignId('course_id')->nullable()->constrained('courses')->onDelete('set null'); // Chosen course
            $table->foreignId('agency_id')->nullable()->constrained('agencies')->onDelete('set null'); // Optional: Agency assisting

            $table->string('intended_intake_month')->nullable(); // e.g., "September"
            $table->year('intended_intake_year')->nullable(); // e.g., 2026
            $table->string('current_education_level')->nullable(); // e.g., HSC, Bachelor's
            $table->string('english_proficiency_test')->nullable(); // e.g., IELTS, TOEFL, PTE
            $table->string('english_proficiency_score')->nullable(); // e.g., "7.5", "100"

            $table->string('status')->default('pending'); // e.g., pending, documents_required, submitted_to_uni, offer_received, visa_processing, visa_approved, rejected
            $table->text('user_notes')->nullable(); // Notes from student
            $table->text('admin_notes')->nullable(); // Notes from admin/agency/consultant
            $table->string('application_reference')->nullable()->unique(); // Internal reference

            $table->timestamps(); // Includes application date (created_at)
            $table->softDeletes(); // Optional
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('student_visa_applications');
    }
};
