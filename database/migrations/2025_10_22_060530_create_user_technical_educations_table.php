<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Stores a user's technical/vocational training
        Schema::create('user_technical_educations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('technical_education_type_id')->nullable()->constrained()->onDelete('set null');
            
            $table->string('course_name');
            $table->string('institution_name');
            
            $table->date('start_date');
            $table->date('end_date')->nullable();
            $table->string('file_path')->nullable(); // Optional certificate scan
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_technical_educations');
    }
};