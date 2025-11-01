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
        Schema::create('appointments', function (Blueprint $table) {
            $table->id();

            // Foreign key for the user (client) booking the appointment
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');

            // Foreign key for the consultant
            $table->foreignId('consultant_id')->constrained('users')->onDelete('cascade');

            // Foreign key for the service being booked
            $table->foreignId('consultation_service_id')->constrained('consultation_services')->onDelete('cascade');

            $table->date('preferred_date');
            $table->string('preferred_time_slot'); // e.g., "morning", "afternoon"
            $table->text('notes')->nullable();

            $table->string('status')->default('pending'); // pending, confirmed, completed, cancelled

            // Store the price at the time of booking
            $table->decimal('price', 10, 2);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('appointments');
    }
};
