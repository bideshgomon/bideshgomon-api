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
        Schema::create('visa_requirement_documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('visa_requirement_id')->constrained()->onDelete('cascade');
            
            // Document Information
            $table->string('document_name'); // e.g., "Passport Copy", "Bank Statement"
            $table->string('document_type')->nullable(); // passport, financial, employment, education, etc.
            $table->text('description'); // Detailed multiline description of requirements
            $table->text('specifications')->nullable(); // Specific format/size/type requirements
            
            // Requirements
            $table->boolean('is_mandatory')->default(true);
            $table->text('conditions')->nullable(); // When this document is required
            $table->integer('quantity')->default(1); // Number of copies needed
            $table->string('format')->nullable(); // original, notarized, translated, etc.
            
            // Validation Rules
            $table->json('validation_rules')->nullable(); // File type, size, age requirements
            $table->text('sample_url')->nullable(); // Link to sample document
            $table->text('common_mistakes')->nullable(); // Common errors to avoid
            
            // Organization
            $table->integer('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            
            $table->timestamps();
            
            // Indexes
            $table->index('visa_requirement_id');
            $table->index('document_type');
            $table->index('is_mandatory');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('visa_requirement_documents');
    }
};
