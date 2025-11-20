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
        Schema::create('visa_documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('visa_application_id')->constrained()->onDelete('cascade');
            $table->foreignId('uploaded_by')->constrained('users')->onDelete('cascade');
            
            // Document Information
            $table->string('document_type'); // passport, photo, bank_statement, invitation_letter, etc.
            $table->string('document_name');
            $table->string('original_filename');
            $table->string('file_path');
            $table->string('file_extension', 10);
            $table->integer('file_size'); // in bytes
            $table->string('mime_type');
            
            // Verification & Status
            $table->enum('status', ['pending', 'verified', 'rejected', 'reupload_required'])->default('pending');
            $table->text('verification_notes')->nullable();
            $table->foreignId('verified_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('verified_at')->nullable();
            
            // Document Specifications
            $table->boolean('is_required')->default(false);
            $table->boolean('is_translated')->default(false);
            $table->boolean('is_notarized')->default(false);
            $table->date('document_expiry_date')->nullable();
            
            // Additional Information
            $table->text('description')->nullable();
            $table->integer('version')->default(1); // for reupload tracking
            $table->json('metadata')->nullable(); // additional document metadata
            
            $table->timestamps();
            $table->softDeletes();
            
            // Indexes
            $table->index('visa_application_id');
            $table->index('document_type');
            $table->index('status');
            $table->index('uploaded_by');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('visa_documents');
    }
};
