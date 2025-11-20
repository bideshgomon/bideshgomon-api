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
        Schema::create('user_family_members', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            
            // Relationship
            $table->enum('relationship', [
                'father',
                'mother',
                'spouse',
                'son',
                'daughter',
                'brother',
                'sister',
                'grandfather',
                'grandmother',
                'other'
            ]);
            $table->string('relationship_other')->nullable(); // If "other"
            
            // Personal Information
            $table->string('full_name');
            $table->string('native_name')->nullable(); // Name in native script
            $table->date('date_of_birth')->nullable();
            $table->enum('gender', ['male', 'female', 'other'])->nullable();
            $table->string('nationality', 2)->nullable(); // ISO 3166-1 alpha-2
            $table->string('place_of_birth')->nullable();
            
            // Status
            $table->boolean('is_deceased')->default(false);
            $table->date('deceased_date')->nullable();
            
            // Contact Information
            $table->string('phone_number')->nullable();
            $table->string('email')->nullable();
            
            // Residence
            $table->string('country_of_residence', 2)->nullable();
            $table->string('city')->nullable();
            $table->text('address')->nullable();
            
            // Occupation & Financial
            $table->string('occupation')->nullable();
            $table->string('employer_name')->nullable();
            $table->decimal('annual_income', 12, 2)->nullable();
            $table->string('income_currency', 3)->nullable();
            
            // Immigration Status (if in another country)
            $table->enum('immigration_status', [
                'citizen',
                'permanent_resident',
                'temporary_resident',
                'student',
                'refugee',
                'other',
                'not_applicable'
            ])->default('not_applicable');
            
            // Travel Together
            $table->boolean('is_traveling_with')->default(false);
            $table->string('passport_number')->nullable(); // If traveling together
            $table->date('passport_expiry')->nullable();
            
            // Document Storage
            $table->string('birth_certificate_path')->nullable();
            $table->string('marriage_certificate_path')->nullable(); // If spouse
            $table->string('death_certificate_path')->nullable(); // If deceased
            $table->string('id_document_path')->nullable();
            
            // Notes
            $table->text('notes')->nullable();
            
            $table->timestamps();
            
            // Indexes
            $table->index('user_id');
            $table->index('relationship');
            $table->index('country_of_residence');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_family_members');
    }
};
