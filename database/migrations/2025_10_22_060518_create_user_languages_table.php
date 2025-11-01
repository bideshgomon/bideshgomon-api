<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Stores a user's language test scores
        Schema::create('user_languages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');

            // e.g., Language: English, Test: IELTS
            $table->foreignId('language_id')->constrained()->onDelete('cascade');
            $table->foreignId('language_test_id')->nullable()->constrained()->onDelete('set null');

            $table->string('overall_score')->nullable(); // e.g., "7.5"
            $table->string('reading_score')->nullable();
            $table->string('writing_score')->nullable();
            $table->string('listening_score')->nullable();
            $table->string('speaking_score')->nullable();

            // --- For expiry notifications ---
            $table->date('test_date')->nullable();
            $table->date('expiry_date')->nullable();

            $table->string('file_path')->nullable(); // Optional scan of the score sheet
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_languages');
    }
};
