<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('hajj_umrahs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('application_reference')->unique();
            $table->string('package_type'); // Hajj, Umrah, Combined
            $table->integer('number_of_travelers')->default(1);
            $table->date('preferred_travel_date')->nullable();
            $table->string('duration');
            $table->string('accommodation_type');
            $table->string('meal_plan');
            $table->string('transport_type');
            $table->boolean('requires_visa_assistance')->default(true);
            $table->boolean('requires_training')->default(false);
            $table->enum('status', ['pending', 'submitted', 'processing', 'confirmed', 'completed', 'cancelled'])->default('pending');
            $table->text('special_requirements')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'status']);
            $table->index('package_type');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('hajj_umrahs');
    }
};
