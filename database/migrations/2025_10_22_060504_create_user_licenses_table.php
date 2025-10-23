<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Stores a user's professional licenses
        Schema::create('user_licenses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('license_type_id')->nullable()->constrained()->onDelete('set null');
            
            $table->string('custom_license_name')->nullable(); // If not in our list
            $table->string('license_number')->nullable();
            
            // --- For expiry notifications ---
            $table->date('issue_date')->nullable();
            $table->date('expiry_date')->nullable();
            
            $table->string('issuing_authority'); // e.g., "BRTA"
            $table->string('file_path')->nullable(); // Optional scan of the license
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_licenses');
    }
};