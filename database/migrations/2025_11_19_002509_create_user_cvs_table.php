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
        Schema::create('user_cvs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('cv_template_id')->constrained()->onDelete('restrict');
            $table->string('title'); // e.g., "Software Developer CV", "My Marketing CV"
            
            // Personal Information (from user_profiles)
            $table->string('full_name');
            $table->string('email');
            $table->string('phone');
            $table->string('city')->nullable();
            $table->foreignId('country_id')->nullable()->constrained()->onDelete('set null');
            $table->string('address')->nullable();
            $table->string('linkedin_url')->nullable();
            $table->string('website_url')->nullable();
            $table->text('professional_summary')->nullable();
            
            // CV Data (JSON format for flexibility)
            $table->json('education')->nullable(); // Array of education entries
            $table->json('experience')->nullable(); // Array of work experience
            $table->json('skills')->nullable(); // Array of skills with proficiency
            $table->json('languages')->nullable(); // Array of languages with proficiency
            $table->json('certifications')->nullable(); // Array of certifications
            $table->json('projects')->nullable(); // Array of projects
            $table->json('references')->nullable(); // Array of references
            
            // Customization
            $table->json('custom_sections')->nullable(); // User-added sections
            $table->json('section_order')->nullable(); // Order of sections
            
            // File & Status
            $table->string('pdf_path')->nullable(); // Generated PDF path
            $table->timestamp('last_generated_at')->nullable();
            $table->boolean('is_public')->default(false); // Share with public link
            $table->string('public_token', 32)->nullable()->unique();
            $table->integer('view_count')->default(0);
            $table->integer('download_count')->default(0);
            
            $table->timestamps();
            
            // Indexes
            $table->index(['user_id', 'created_at']);
            $table->index('public_token');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_cvs');
    }
};
