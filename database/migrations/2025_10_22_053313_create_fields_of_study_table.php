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
        // CHANGE 'field_of_study' to 'field_of_studies'
        Schema::create('field_of_studies', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // e.g., 'Computer Science', 'Mechanical Engineering', 'Business'
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // CHANGE 'field_of_study' to 'field_of_studies'
        Schema::dropIfExists('field_of_studies');
    }
};