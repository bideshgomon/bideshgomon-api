<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Stores a user's education history
        Schema::create('user_educations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            
            // Links to the tables we created in Step 1.9
            $table->foreignId('degree_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('university_id')->nullable()->constrained()->onDelete('set null');
            
            // If their university/degree isn't in our list, they can type it here
            $table->string('custom_degree')->nullable();
            $table->string('custom_university')->nullable();
            
            $table->date('start_date');
            $table->date('end_date')->nullable();
            $table->boolean('is_current')->default(false);
            $table->string('result')->nullable(); // e.g., "CGPA: 3.8/4.0"
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_educations');
    }
};