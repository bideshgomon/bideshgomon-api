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
        Schema::create('flight_requests', function (Blueprint $table) {
            $table->id();
            $table->string('request_reference')->unique();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->enum('trip_type', ['one_way', 'round_trip', 'multi_city'])->default('one_way');
            
            // One-way / Round trip fields
            $table->string('origin_airport_code', 10)->nullable();
            $table->string('destination_airport_code', 10)->nullable();
            $table->date('travel_date')->nullable();
            $table->date('return_date')->nullable();
            
            // Multi-city segments
            $table->json('multi_city_segments')->nullable();
            
            // Passenger details
            $table->integer('passengers_count')->default(1);
            $table->enum('flight_class', ['economy', 'business', 'first_class'])->default('economy');
            $table->json('passengers')->nullable()->comment('Passenger names, ages, passport info');
            
            // Contact
            $table->string('contact_name');
            $table->string('contact_email');
            $table->string('contact_phone');
            $table->text('special_requests')->nullable();
            
            // Budget & Preferences
            $table->decimal('budget_min', 10, 2)->nullable();
            $table->decimal('budget_max', 10, 2)->nullable();
            $table->boolean('prefer_direct_flights')->default(false);
            $table->json('preferred_airlines')->nullable();
            $table->enum('preferred_time', ['morning', 'afternoon', 'evening', 'night', 'flexible'])->default('flexible');
            
            // Assignment
            $table->foreignId('assigned_to')->nullable()->constrained('users')->onDelete('set null')->comment('Admin or Agency user');
            $table->timestamp('assigned_at')->nullable();
            
            // Status workflow
            $table->enum('status', ['pending', 'assigned', 'quoted', 'accepted', 'rejected', 'cancelled', 'completed'])->default('pending');
            $table->text('admin_notes')->nullable();
            $table->text('rejection_reason')->nullable();
            
            // Quotes
            $table->integer('quotes_count')->default(0);
            $table->timestamp('quoted_at')->nullable();
            
            // Tracking
            $table->string('ip_address', 45)->nullable();
            $table->string('user_agent')->nullable();
            $table->integer('search_count')->default(1)->comment('How many times this route searched');
            
            $table->timestamps();
            $table->softDeletes();
            
            // Indexes
            $table->index(['user_id', 'status']);
            $table->index(['assigned_to', 'status']);
            $table->index(['status', 'created_at']);
            $table->index(['origin_airport_code', 'destination_airport_code', 'travel_date'], 'fr_route_date_idx');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('flight_requests');
    }
};
