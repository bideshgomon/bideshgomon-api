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
        Schema::create('user_passports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            
            // Passport Core Information
            $table->string('passport_number')->unique();
            $table->enum('passport_type', ['regular', 'diplomatic', 'official', 'service', 'emergency'])->default('regular');
            $table->string('issuing_country', 2); // ISO 3166-1 alpha-2 (BD, US, UK, etc.)
            $table->string('issuing_authority')->nullable(); // e.g., "Department of Immigration & Passports, Bangladesh"
            $table->date('issue_date');
            $table->date('expiry_date');
            $table->string('place_of_issue')->nullable(); // City/office where issued
            
            // Status
            $table->boolean('is_current_passport')->default(true);
            $table->boolean('is_lost_or_stolen')->default(false);
            $table->date('reported_lost_date')->nullable();
            
            // Personal Details on Passport (may differ from profile)
            $table->string('surname')->nullable();
            $table->string('given_names')->nullable();
            $table->string('nationality', 2)->nullable(); // May have multiple nationalities
            $table->enum('sex', ['M', 'F', 'X'])->nullable();
            $table->date('date_of_birth')->nullable();
            $table->string('place_of_birth')->nullable();
            
            // Document Storage
            $table->string('passport_scan_path')->nullable(); // Biographical page
            $table->string('additional_pages_path')->nullable(); // Other pages with visas
            
            // Notes
            $table->text('notes')->nullable();
            
            $table->timestamps();
            
            // Indexes
            $table->index('user_id');
            $table->index('passport_number');
            $table->index('expiry_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_passports');
    }
};
