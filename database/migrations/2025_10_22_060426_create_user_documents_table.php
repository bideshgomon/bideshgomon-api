<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Stores an uploaded document for a user (e.g., "My Passport")
        Schema::create('user_documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('document_type_id')->constrained()->onDelete('cascade');
            
            $table->string('file_path'); // Path to the stored file
            $table->string('file_name');
            $table->string('document_number')->nullable(); // e.g., Passport number
            
            // --- Crucial for your notification feature ---
            $table->date('issue_date')->nullable();
            $table->date('expiry_date')->nullable();
            // ---------------------------------------------
            
            $table->string('issuing_country_id')->nullable();
            $table->string('status')->default('pending'); // 'pending', 'verified', 'expired'
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_documents');
    }
};