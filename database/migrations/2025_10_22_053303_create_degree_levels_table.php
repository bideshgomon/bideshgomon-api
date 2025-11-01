<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('degree_levels', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->boolean('is_active')->default(true); // <-- Ensure this line is present
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('degree_levels');
    }
};
