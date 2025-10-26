<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('travel_insurances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('bimafy_policy_reference')->unique()->comment('Reference ID from Bimafy API');
            $table->string('package_name')->nullable(); // Name of the Bimafy package chosen
            $table->foreignId('destination_country_id')->nullable()->constrained('countries')->onDelete('set null');
            $table->date('start_date');
            $table->date('end_date');
            $table->integer('duration_days');
            $table->decimal('premium_paid', 10, 2);
            $table->string('currency', 3)->default('BDT'); // Or fetched from API
            $table->string('status')->default('active'); // e.g., active, expired, cancelled
            $table->json('coverage_details')->nullable(); // Store basic coverage info fetched from Bimafy
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('travel_insurances');
    }
};