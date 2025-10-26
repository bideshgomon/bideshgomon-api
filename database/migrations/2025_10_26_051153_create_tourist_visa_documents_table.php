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
        Schema::create('tourist_visa_documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tourist_visa_id')->constrained('tourist_visas')->onDelete('cascade');
            $table->foreignId('document_type_id')->constrained('document_types')->onDelete('cascade');
            // Link to the actual uploaded file in user_documents (optional, can be null if not uploaded yet)
            $table->foreignId('user_document_id')->nullable()->constrained('user_documents')->onDelete('set null');
            $table->string('status')->default('pending'); // e.g., pending, submitted, verified, rejected
            $table->text('admin_notes')->nullable(); // Notes specific to this document
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tourist_visa_documents');
    }
};