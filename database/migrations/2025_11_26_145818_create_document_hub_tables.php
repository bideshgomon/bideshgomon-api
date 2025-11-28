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
        // Document Categories Table
        Schema::create('document_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100);
            $table->text('description')->nullable();
            $table->integer('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Master Documents Library Table
        Schema::create('master_documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained('document_categories')->onDelete('cascade');
            $table->string('document_name');
            $table->text('description')->nullable();
            $table->text('specifications')->nullable(); // Format, size, validity requirements
            $table->boolean('translation_required')->default(false);
            $table->boolean('notarization_required')->default(false);
            $table->integer('typical_validity_days')->nullable(); // How long document stays valid
            $table->string('international_standard', 50)->nullable(); // ISO, ICAO, etc.
            $table->string('example_url', 500)->nullable();
            $table->integer('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Country Document Requirements (Many-to-Many)
        Schema::create('country_document_requirements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('country_id')->constrained()->onDelete('cascade');
            $table->string('visa_type', 50); // tourist, business, student, work, medical, transit, family
            $table->string('profession_category', 50)->nullable(); // Job Holder, Business Person, Student
            $table->foreignId('document_id')->constrained('master_documents')->onDelete('cascade');
            $table->boolean('is_mandatory')->default(true);
            $table->text('specific_notes')->nullable(); // Country-specific variations
            $table->integer('sort_order')->default(0);
            $table->timestamps();
            
            // Unique constraint to prevent duplicates
            $table->unique(['country_id', 'visa_type', 'profession_category', 'document_id'], 'unique_country_doc_req');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('country_document_requirements');
        Schema::dropIfExists('master_documents');
        Schema::dropIfExists('document_categories');
    }
};
