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
        Schema::create('flight_routes', function (Blueprint $table) {
            $table->id();
            
            // Route Information
            $table->string('route_code', 20)->unique(); // e.g., DAC-DXB-001
            $table->string('airline_name', 100);
            $table->string('airline_code', 10); // e.g., EK, QR, TG
            $table->string('flight_number', 20); // e.g., EK585
            $table->string('aircraft_type', 50)->nullable(); // Boeing 777, Airbus A380
            
            // Origin & Destination
            $table->foreignId('origin_country_id')->constrained('countries');
            $table->string('origin_city', 100);
            $table->string('origin_airport', 100);
            $table->string('origin_airport_code', 10); // DAC, CGP
            $table->foreignId('destination_country_id')->constrained('countries');
            $table->string('destination_city', 100);
            $table->string('destination_airport', 100);
            $table->string('destination_airport_code', 10); // DXB, RUH, KUL
            
            // Schedule
            $table->time('departure_time');
            $table->time('arrival_time');
            $table->string('flight_duration', 20); // "4h 30m"
            $table->json('available_days')->nullable(); // ["monday", "wednesday", "friday"]
            $table->boolean('is_direct')->default(true);
            $table->integer('stops')->default(0);
            $table->json('stopover_cities')->nullable(); // ["Dubai", "Doha"]
            
            // Pricing (in BDT)
            $table->decimal('economy_price', 10, 2);
            $table->decimal('business_price', 10, 2)->nullable();
            $table->decimal('first_class_price', 10, 2)->nullable();
            $table->foreignId('currency_id')->constrained('currencies');
            
            // Capacity & Availability
            $table->integer('total_seats');
            $table->integer('economy_seats');
            $table->integer('business_seats')->nullable();
            $table->integer('first_class_seats')->nullable();
            
            // Baggage Allowance
            $table->string('cabin_baggage', 20); // "7kg"
            $table->string('checked_baggage', 20); // "30kg"
            $table->string('extra_baggage_price', 20)->nullable(); // "৳2000 per 5kg"
            
            // Amenities & Features
            $table->json('amenities')->nullable(); // ["in-flight-meal", "wifi", "entertainment"]
            $table->string('meal_type', 50)->nullable(); // "Complimentary", "Paid"
            $table->boolean('has_wifi')->default(false);
            $table->boolean('has_entertainment')->default(true);
            
            // Booking Settings
            $table->integer('booking_deadline_hours')->default(24); // Book 24h before departure
            $table->decimal('booking_fee', 8, 2)->default(500); // ৳500 service fee
            $table->decimal('cancellation_fee_percentage', 5, 2)->default(20); // 20% cancellation fee
            
            // Status & Popularity
            $table->boolean('is_active')->default(true);
            $table->boolean('is_popular')->default(false);
            $table->integer('total_bookings')->default(0);
            $table->decimal('average_rating', 3, 2)->default(0);
            $table->integer('display_order')->default(0);
            
            // Metadata
            $table->text('description')->nullable();
            $table->json('tags')->nullable(); // ["worker-friendly", "budget", "premium"]
            $table->timestamps();
            $table->softDeletes();
            
            // Indexes
            $table->index(['origin_airport_code', 'destination_airport_code']);
            $table->index(['airline_code', 'is_active']);
            $table->index(['is_popular', 'display_order']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('flight_routes');
    }
};
