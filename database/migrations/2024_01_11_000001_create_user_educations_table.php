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
        Schema::create('user_educations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            
            // Basic Education Information
            $table->string('institution_name');
            $table->string('degree'); // Bachelor's, Master's, PhD, etc.
            $table->string('field_of_study')->nullable(); // Computer Science, Engineering, etc.
            $table->date('start_date');
            $table->date('end_date')->nullable();
            
            // Enhanced for Visa Applications
            $table->string('country', 2)->nullable(); // Where studied
            $table->string('city')->nullable();
            $table->boolean('is_completed')->default(true);
            $table->string('gpa_or_grade', 50)->nullable();
            $table->string('degree_certificate_path')->nullable();
            $table->string('transcript_path')->nullable();
            $table->string('language_of_instruction', 50)->nullable(); // English, Bengali, etc.
            $table->text('courses_completed')->nullable(); // List of major courses
            $table->text('honors_awards')->nullable(); // Academic achievements
            
            $table->timestamps();
            
            // Indexes
            $table->index('user_id');
            $table->index('start_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_educations');
    }
};
