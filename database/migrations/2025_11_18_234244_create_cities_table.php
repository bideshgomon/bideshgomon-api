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
        Schema::create('cities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('country_id')->constrained('countries')->cascadeOnDelete();
            $table->string('name', 100); // Dhaka, New York, London
            $table->string('name_bn', 100)->nullable(); // ঢাকা
            $table->string('state_province', 100)->nullable(); // Division/State name
            $table->string('timezone', 50)->nullable(); // Asia/Dhaka, America/New_York
            $table->decimal('latitude', 10, 8)->nullable();
            $table->decimal('longitude', 11, 8)->nullable();
            $table->boolean('is_capital')->default(false);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            
            // Indexes for fast lookup
            $table->index('country_id');
            $table->index(['country_id', 'name']);
            $table->index('is_active');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cities');
    }
};
