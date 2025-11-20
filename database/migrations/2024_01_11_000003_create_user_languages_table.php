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
        Schema::create('user_languages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            
            // Basic Language Information
            $table->string('language'); // English, Bengali, Hindi, etc.
            $table->enum('proficiency', ['basic', 'intermediate', 'advanced', 'native'])->default('basic');
            
            // Enhanced for Visa Applications
            $table->boolean('can_read')->default(false);
            $table->boolean('can_write')->default(false);
            $table->boolean('can_speak')->default(false);
            $table->boolean('can_understand')->default(false);
            
            // English proficiency test details
            $table->enum('test_taken', ['none', 'ielts', 'toefl', 'pte', 'duolingo', 'cambridge', 'other'])->default('none');
            $table->string('test_other_name')->nullable(); // If "other" selected
            $table->string('test_score')->nullable(); // Overall score (e.g., "7.5")
            $table->date('test_date')->nullable();
            $table->string('test_certificate_path')->nullable();
            
            // Individual skill scores (IELTS bands, TOEFL scores, etc.)
            $table->string('listening_score', 10)->nullable();
            $table->string('reading_score', 10)->nullable();
            $table->string('writing_score', 10)->nullable();
            $table->string('speaking_score', 10)->nullable();
            
            // Certificate details
            $table->string('test_reference_number')->nullable();
            $table->date('certificate_expiry_date')->nullable();
            
            $table->boolean('is_native')->default(false);
            
            $table->timestamps();
            
            // Indexes
            $table->index('user_id');
            $table->index('language');
            $table->index('test_taken');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_languages');
    }
};
