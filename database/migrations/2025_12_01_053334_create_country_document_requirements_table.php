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
        Schema::create('country_document_requirements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('country_id')->constrained()->onDelete('cascade');
            $table->string('visa_type', 50);
            $table->string('profession_category', 100)->nullable();
            $table->foreignId('document_id')->constrained('master_documents')->onDelete('cascade');
            $table->boolean('is_mandatory')->default(true);
            $table->text('specific_notes')->nullable();
            $table->integer('sort_order')->default(0);
            $table->timestamps();

            $table->index(['country_id', 'visa_type']);
            $table->index('document_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('country_document_requirements');
    }
};
