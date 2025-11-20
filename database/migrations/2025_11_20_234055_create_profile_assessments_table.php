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
        Schema::create('profile_assessments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            
            // Overall Scores (0-100)
            $table->decimal('overall_score', 5, 2)->default(0);
            $table->decimal('profile_completeness', 5, 2)->default(0);
            $table->decimal('document_readiness', 5, 2)->default(0);
            $table->decimal('visa_eligibility', 5, 2)->default(0);
            
            // Detailed Analysis (JSON)
            $table->json('strengths')->nullable();              // Array of strength points
            $table->json('weaknesses')->nullable();             // Array of weakness points
            $table->json('recommendations')->nullable();        // Array of actionable recommendations
            $table->json('missing_documents')->nullable();      // Array of missing docs
            $table->json('visa_eligibility_breakdown')->nullable(); // Per-country eligibility
            
            // Risk Assessment
            $table->enum('risk_level', ['low', 'medium', 'high'])->default('medium');
            $table->json('risk_factors')->nullable();           // Array of risk factors
            
            // Profile Sections Completion (0-100 each)
            $table->decimal('personal_info_score', 5, 2)->default(0);
            $table->decimal('education_score', 5, 2)->default(0);
            $table->decimal('work_experience_score', 5, 2)->default(0);
            $table->decimal('language_proficiency_score', 5, 2)->default(0);
            $table->decimal('financial_score', 5, 2)->default(0);
            $table->decimal('travel_history_score', 5, 2)->default(0);
            $table->decimal('passport_score', 5, 2)->default(0);
            
            // Visa Type Recommendations
            $table->json('recommended_visa_types')->nullable(); // Array of recommended visa types
            $table->json('eligible_countries')->nullable();     // Array of countries user may qualify for
            
            // AI Insights
            $table->text('ai_summary')->nullable();             // Human-readable summary
            $table->json('ai_metadata')->nullable();            // ML model metadata, confidence scores
            
            // Assessment Metadata
            $table->timestamp('assessed_at')->nullable();
            $table->string('assessment_version')->default('1.0'); // Track algorithm version
            $table->timestamps();
            
            // Indexes
            $table->index('user_id');
            $table->index('overall_score');
            $table->index('risk_level');
            $table->index('assessed_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('profile_assessments');
    }
};
