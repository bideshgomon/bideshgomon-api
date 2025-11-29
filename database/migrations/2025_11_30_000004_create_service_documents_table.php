<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('service_documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('service_booking_id')->constrained()->onDelete('cascade');
            $table->foreignId('uploaded_by')->constrained('users')->onDelete('cascade');
            
            $table->string('document_type'); // 'passport', 'photo', 'bank_statement', 'nid', etc.
            $table->string('title');
            $table->string('file_path');
            $table->string('file_type'); // pdf, jpg, png, doc
            $table->integer('file_size'); // bytes
            $table->text('notes')->nullable();
            
            $table->enum('status', ['pending', 'verified', 'rejected'])->default('pending');
            $table->text('rejection_reason')->nullable();
            $table->foreignId('verified_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('verified_at')->nullable();
            
            $table->timestamps();
            
            $table->index(['service_booking_id', 'status']);
            $table->index('document_type');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('service_documents');
    }
};
