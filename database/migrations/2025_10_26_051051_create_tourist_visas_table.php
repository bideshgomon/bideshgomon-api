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
        Schema::create('tourist_visas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('destination_country_id')->constrained('countries')->onDelete('cascade');
            $table->date('intended_travel_date')->nullable();
            $table->integer('duration_days')->nullable(); // How long they plan to stay
            $table->string('status')->default('pending'); // e.g., pending, submitted, approved, rejected, cancelled
            $table->text('admin_notes')->nullable(); // Notes from admin/agency
            $table->string('application_reference')->nullable()->unique(); // Optional reference number
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tourist_visas');
    }
};