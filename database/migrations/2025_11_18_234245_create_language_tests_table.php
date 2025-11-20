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
        Schema::create('language_tests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('language_id')->nullable()->constrained('languages')->nullOnDelete();
            $table->string('name', 100); // IELTS, TOEFL, PTE, Duolingo, JLPT, etc.
            $table->string('name_bn', 100)->nullable(); // Bengali name
            $table->string('code', 20)->unique(); // ielts, toefl, pte, duolingo, jlpt
            $table->decimal('min_score', 5, 2)->nullable(); // Minimum score (e.g., 0)
            $table->decimal('max_score', 5, 2)->nullable(); // Maximum score (e.g., 9.0 for IELTS, 120 for TOEFL)
            $table->string('score_type', 20)->default('band'); // band, score, level
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            
            // Indexes
            $table->index('code');
            $table->index('is_active');
            $table->index('language_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('language_tests');
    }
};
