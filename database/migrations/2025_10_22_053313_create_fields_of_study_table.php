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
        // Correct table name is 'fields_of_study'
        Schema::create('fields_of_study', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique(); // Added unique constraint
            $table->boolean('is_active')->default(true); // Added is_active column
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fields_of_study'); // Correct table name
    }
};
