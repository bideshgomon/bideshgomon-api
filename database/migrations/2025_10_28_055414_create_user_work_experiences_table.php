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
        Schema::create('user_work_experiences', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('company_name');
            $table->string('position'); // e.g., Job Title
            $table->date('start_date');
            $table->date('end_date')->nullable(); // Nullable if currently working here
            $table->text('description')->nullable();
            $table->foreignId('country_id')->nullable()->constrained('countries')->onDelete('set null'); // Optional country
            $table->string('city')->nullable(); // City name as string
            $table->timestamps(); // created_at, updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_work_experiences');
    }
};