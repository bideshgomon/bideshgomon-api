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
        Schema::create('flight_searches', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');
            
            // Search parameters
            $table->enum('trip_type', ['one_way', 'round_trip', 'multi_city'])->default('one_way');
            $table->string('origin_airport_code', 10);
            $table->string('destination_airport_code', 10);
            $table->date('travel_date');
            $table->date('return_date')->nullable();
            $table->json('multi_city_segments')->nullable();
            $table->integer('passengers_count')->default(1);
            $table->enum('flight_class', ['economy', 'business', 'first_class'])->default('economy');
            
            // Tracking
            $table->string('ip_address', 45)->nullable();
            $table->string('user_agent')->nullable();
            $table->integer('search_count')->default(1);
            
            $table->timestamps();
            
            // Indexes for popular routes
            $table->index(['origin_airport_code', 'destination_airport_code', 'created_at'], 'fs_route_date_idx');
            $table->index(['travel_date', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('flight_searches');
    }
};
