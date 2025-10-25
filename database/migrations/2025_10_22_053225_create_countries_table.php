<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('countries', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            // --- FIX: Change 'iso_code' to 'code' and make it nullable ---
            $table->string('code', 10)->nullable()->unique();
            
            // --- FIX: Remove or make other non-seeded columns nullable ---
            $table->string('iso_code_3', 3)->nullable()->unique(); // Make nullable
            $table->string('country_code', 10)->nullable();
            $table->string('continent')->nullable();
            $table->string('nationality')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }
    public function down(): void {
        Schema::dropIfExists('countries');
    }
};