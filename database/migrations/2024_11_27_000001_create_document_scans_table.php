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
        Schema::create('document_scans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('document_type'); // passport, national_id, visa, etc.
            $table->string('original_image'); // path to uploaded image
            $table->string('processed_image')->nullable(); // path to processed/enhanced image
            $table->json('extracted_data')->nullable(); // OCR extracted data
            $table->decimal('confidence_score', 5, 2)->nullable(); // 0.00 to 100.00
            $table->enum('status', ['pending', 'processing', 'completed', 'failed'])->default('pending');
            $table->string('processing_method')->nullable(); // tesseract, google_vision, aws_textract, manual
            $table->text('error_message')->nullable();
            $table->timestamp('processed_at')->nullable();
            $table->timestamps();

            $table->index('user_id');
            $table->index('status');
            $table->index('document_type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('document_scans');
    }
};
