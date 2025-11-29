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
        Schema::create('document_types', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100); // Passport, NID, Bank Statement, Photo, etc.
            $table->string('name_bn', 100)->nullable(); // Bengali name
            $table->string('slug', 100)->unique();
            $table->text('description')->nullable();
            $table->string('category', 50)->nullable(); // Identity, Financial, Academic, Travel, etc.
            $table->boolean('is_required')->default(false); // Required for visa applications?
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
        Schema::dropIfExists('document_types');
    }
};
