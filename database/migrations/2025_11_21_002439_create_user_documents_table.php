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
        Schema::create('user_documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('document_type'); // passport_scan, bank_statement, language_certificate, education_certificate, work_reference, medical_report
            $table->string('status')->default('pending'); // pending, approved, rejected
            $table->string('original_filename');
            $table->string('storage_path');
            $table->string('mime_type')->nullable();
            $table->unsignedBigInteger('size_bytes')->nullable();
            $table->json('meta')->nullable(); // extra parsed data (e.g., expiry date, issuing country)
            $table->foreignId('reviewed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('reviewed_at')->nullable();
            $table->text('rejection_reason')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->boolean('is_primary')->default(false); // for multiple of same type (e.g., passports)
            $table->integer('confidence_score')->default(50); // future AI extraction reliability
            $table->timestamps();

            // Indexes
            $table->index(['user_id', 'document_type']);
            $table->index(['status', 'document_type']);
            $table->index('expires_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_documents');
    }
};
