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
        Schema::create('student_visas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('destination_country_id')->constrained('countries')->onDelete('cascade');
            $table->string('application_reference')->unique();
            
            // Student-specific fields
            $table->string('education_level')->nullable(); // High School, Bachelor's, Master's, PhD
            $table->string('study_field')->nullable(); // Engineering, Computer Science, etc.
            $table->string('institution_name')->nullable(); // Target university/college
            $table->string('course_name')->nullable(); // Specific program/course
            $table->date('intended_start_date')->nullable();
            $table->integer('course_duration_months')->nullable();
            $table->boolean('has_admission_letter')->default(false);
            $table->boolean('has_ielts_toefl')->default(false);
            $table->string('english_test_type')->nullable(); // IELTS, TOEFL, PTE
            $table->decimal('english_test_score', 4, 1)->nullable();
            $table->decimal('previous_education_gpa', 4, 2)->nullable();
            
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
            $table->index('education_level');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('student_visas');
    }
};
