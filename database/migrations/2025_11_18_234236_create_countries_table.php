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
        Schema::create('countries', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100)->unique(); // Official country name
            $table->string('name_bn', 100)->nullable(); // Bengali name
            $table->string('iso_code_2', 2)->unique(); // ISO 3166-1 alpha-2 (BD, US, IN)
            $table->string('iso_code_3', 3)->unique(); // ISO 3166-1 alpha-3 (BGD, USA, IND)
            $table->string('phone_code', 10); // +880, +1, +91
            $table->string('currency_code', 3); // BDT, USD, INR
            $table->string('flag_emoji', 10)->nullable(); // 🇧🇩
            $table->string('region', 50)->nullable(); // Asia, Europe, Americas
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            
            // Indexes for fast lookup
            $table->index('iso_code_2');
            $table->index('is_active');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('countries');
    }
};
