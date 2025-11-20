<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('user_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            
            // Basic Info
            $table->text('bio')->nullable();
            $table->string('avatar')->nullable();
            $table->string('phone', 20)->nullable();
            
            // Personal Details
            $table->date('dob')->nullable();
            $table->enum('gender', ['male', 'female', 'other'])->nullable();
            $table->string('nationality', 100)->default('Bangladesh');
            $table->string('marital_status', 20)->nullable();
            
            // Bangladesh Address
            $table->string('present_address_line')->nullable();
            $table->string('present_city', 100)->nullable();
            $table->string('present_division', 100)->nullable(); // BD specific
            $table->string('present_district', 100)->nullable(); // BD specific
            $table->string('present_postal_code', 20)->nullable();
            
            // Documents (Bangladesh specific)
            $table->string('nid', 20)->nullable(); // Bangladesh National ID
            $table->string('passport_number', 20)->nullable();
            $table->date('passport_issue_date')->nullable();
            $table->date('passport_expiry_date')->nullable();
            
            $table->timestamps();
            
            // Indexes
            $table->index('user_id');
            $table->index('phone');
            $table->index('nid');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_profiles');
    }
};
