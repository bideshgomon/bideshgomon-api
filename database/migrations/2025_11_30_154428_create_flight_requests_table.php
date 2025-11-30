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
            $table->enum('trip_type', ['one-way', 'round-trip', 'multi-city'])->default('one-way');
            $table->string('origin_airport_code', 10);
            $table->string('destination_airport_code', 10);
            $table->date('travel_date');
            $table->date('return_date')->nullable();
            $table->json('multi_city_segments')->nullable();
            $table->integer('passengers_count')->default(1);
            $table->enum('flight_class', ['economy', 'premium-economy', 'business', 'first-class'])->default('economy');
            $table->json('passengers')->nullable();
            $table->string('contact_name');
            $table->string('contact_email');
            $table->string('contact_phone');
            $table->text('special_requests')->nullable();
            $table->decimal('budget_min', 10, 2)->nullable();
            $table->decimal('budget_max', 10, 2)->nullable();
            $table->boolean('prefer_direct_flights')->default(false);
            $table->json('preferred_airlines')->nullable();
            $table->string('preferred_time')->nullable();
            $table->foreignId('assigned_to')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('assigned_at')->nullable();
            $table->enum('status', ['pending', 'assigned', 'quoted', 'converted', 'rejected', 'cancelled'])->default('pending');
            $table->text('admin_notes')->nullable();
            $table->text('rejection_reason')->nullable();
            $table->integer('quotes_count')->default(0);
            $table->timestamp('quoted_at')->nullable();
            $table->string('ip_address')->nullable();
            $table->text('user_agent')->nullable();
            $table->integer('search_count')->default(1);
            $table->timestamps();
            $table->softDeletes();
            
            $table->index(['user_id', 'status']);
            $table->index('travel_date');
            $table->index('created_at');
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
