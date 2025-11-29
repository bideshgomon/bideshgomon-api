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
        Schema::create('bank_names', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100); // Dutch-Bangla Bank, BRAC Bank, etc.
            $table->string('name_bn', 100)->nullable(); // Bengali name
            $table->string('slug', 100)->unique();
            $table->string('short_name', 50)->nullable(); // DBBL, BRAC, etc.
            $table->string('swift_code', 20)->nullable(); // For international transfers
            $table->string('routing_number', 20)->nullable(); // Bangladesh specific
            $table->string('type', 30)->default('commercial'); // commercial, Islamic, specialized
            $table->string('website')->nullable();
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
            
            $table->index('type');
            $table->index('is_active');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bank_names');
    }
};
