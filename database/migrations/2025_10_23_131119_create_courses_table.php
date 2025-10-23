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
        Schema::create('courses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('university_id')->constrained()->onDelete('cascade'); // Link to universities table

            $table->string('name');
            $table->string('degree_level'); // e.g., 'Bachelors', 'Masters'
            $table->string('field_of_study'); // e.g., 'Computer Science'

            $table->decimal('tuition_fee', 10, 2)->nullable(); // Optional tuition fee
            $table->float('duration_years')->nullable(); // Optional duration

            $table->text('description')->nullable(); // Optional description
            $table->date('application_deadline')->nullable(); // Optional deadline

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('courses');
    }
};