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
        Schema::create('cv_templates', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // e.g., "Modern Professional", "Classic ATS"
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->string('thumbnail')->nullable(); // Preview image
            $table->enum('category', ['professional', 'modern', 'creative', 'ats-friendly', 'executive'])->default('professional');
            $table->boolean('is_premium')->default(false);
            $table->decimal('price', 10, 2)->default(0); // Price in BDT
            $table->json('color_scheme')->nullable(); // Primary, secondary colors
            $table->json('sections')->nullable(); // Available sections configuration
            $table->integer('download_count')->default(0);
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cv_templates');
    }
};
