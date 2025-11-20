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
        Schema::create('travel_insurance_bookings', function (Blueprint $table) {
            $table->id();
            $table->string('booking_reference')->unique(); // TI20251119001
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('package_id')->constrained('travel_insurance_packages')->onDelete('restrict');
            
            // Trip Details
            $table->foreignId('destination_country_id')->constrained('countries')->onDelete('restrict');
            $table->date('trip_start_date');
            $table->date('trip_end_date');
            $table->integer('duration_days');
            $table->text('trip_purpose')->nullable(); // Tourism, Business, Study, etc.
            
            // Traveler Details
            $table->json('travelers'); // Array of traveler details (name, age, passport, etc.)
            $table->integer('travelers_count');
            
            // Pricing
            $table->decimal('package_price', 10, 2);
            $table->decimal('tax_amount', 10, 2)->default(0);
            $table->decimal('discount_amount', 10, 2)->default(0);
            $table->decimal('total_amount', 10, 2);
            
            // Payment
            $table->enum('payment_status', ['pending', 'paid', 'failed', 'refunded'])->default('pending');
            $table->string('payment_method')->nullable(); // wallet, bkash, card, etc.
            $table->string('payment_reference')->nullable();
            $table->timestamp('paid_at')->nullable();
            
            // Booking Status
            $table->enum('status', ['pending', 'confirmed', 'active', 'expired', 'cancelled'])->default('pending');
            $table->text('cancellation_reason')->nullable();
            $table->timestamp('cancelled_at')->nullable();
            
            // Insurance Policy
            $table->string('policy_number')->nullable()->unique();
            $table->timestamp('policy_issued_at')->nullable();
            $table->string('policy_document_url')->nullable();
            
            // Additional Info
            $table->text('notes')->nullable();
            $table->json('metadata')->nullable();
            
            $table->timestamps();
            $table->softDeletes();

            $table->index('booking_reference');
            $table->index('user_id');
            $table->index('payment_status');
            $table->index('status');
            $table->index(['trip_start_date', 'trip_end_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('travel_insurance_bookings');
    }
};
