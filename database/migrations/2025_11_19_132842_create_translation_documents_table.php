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
        Schema::create('translation_documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('translation_request_id')->constrained()->onDelete('cascade');
            
            // Document Info
            $table->enum('type', ['source', 'translated', 'reference']);
            $table->string('file_name');
            $table->string('file_path');
            $table->string('mime_type', 100);
            $table->integer('file_size');
            
            // Upload Info
            $table->foreignId('uploaded_by')->constrained('users')->onDelete('cascade');
            $table->text('description')->nullable();
            
            // Version Control
            $table->integer('version')->default(1);
            $table->boolean('is_final')->default(false);
            
            $table->timestamps();
            $table->softDeletes();
            
            $table->index(['translation_request_id', 'type']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('translation_documents');
    }
};
