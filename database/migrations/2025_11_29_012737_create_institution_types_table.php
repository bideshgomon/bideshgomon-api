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
        Schema::create('institution_types', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100); // University, College, Institute, etc.
            $table->string('name_bn', 100)->nullable(); // Bengali name
            $table->string('slug', 100)->unique();
            $table->text('description')->nullable();
            $table->string('category', 50)->default('academic'); // academic, vocational, language, professional
            $table->string('level', 30)->nullable(); // primary, secondary, higher, vocational
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
            
            $table->index('category');
            $table->index('is_active');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('institution_types');
    }
};
