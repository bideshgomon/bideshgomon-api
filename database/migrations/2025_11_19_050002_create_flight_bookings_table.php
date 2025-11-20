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
        Schema::create('flight_bookings', function (Blueprint $table) {
            $table->id();
            
            // Booking Reference
            $table->string('booking_reference', 20)->unique(); // FB20251119001
            $table->string('pnr_number', 20)->unique()->nullable(); // Passenger Name Record
            $table->string('ticket_number', 30)->nullable(); // Airline ticket number
            
            // Relations
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('flight_route_id')->constrained()->onDelete('restrict');
            
            // Travel Details
            $table->date('travel_date');
            $table->string('flight_class', 20); // economy, business, first_class
            $table->integer('passengers_count');
            $table->json('passengers'); // [{name, passport, age, seat}]
            
            // Contact Information
            $table->string('contact_name', 100);
            $table->string('contact_email', 100);
            $table->string('contact_phone', 20);
            $table->text('special_requests')->nullable();
            
            // Seat Selection
            $table->json('selected_seats')->nullable(); // ["12A", "12B", "12C"]
            $table->decimal('seat_selection_fee', 8, 2)->default(0);
            
            // Baggage
            $table->integer('extra_baggage_count')->default(0);
            $table->decimal('extra_baggage_fee', 10, 2)->default(0);
            
            // Meal Preferences
            $table->json('meal_preferences')->nullable(); // ["vegetarian", "halal", "normal"]
            $table->decimal('meal_fee', 8, 2)->default(0);
            
            // Pricing Breakdown (in BDT)
            $table->decimal('base_fare', 10, 2); // Per passenger
            $table->decimal('taxes_fees', 10, 2); // Airport tax, fuel surcharge
            $table->decimal('booking_fee', 8, 2);
            $table->decimal('insurance_fee', 8, 2)->default(0);
            $table->decimal('subtotal', 10, 2);
            $table->decimal('discount_amount', 10, 2)->default(0);
            $table->string('promo_code', 50)->nullable();
            $table->decimal('total_amount', 10, 2);
            
            // Payment Information
            $table->string('payment_status', 20)->default('pending'); // pending, paid, failed, refunded
            $table->string('payment_method', 50)->nullable(); // wallet, bkash, card
            $table->string('payment_reference', 100)->nullable();
            $table->timestamp('paid_at')->nullable();
            
            // Booking Status
            $table->string('status', 20)->default('pending'); // pending, confirmed, cancelled, completed, refunded
            $table->timestamp('confirmed_at')->nullable();
            $table->timestamp('cancelled_at')->nullable();
            $table->text('cancellation_reason')->nullable();
            $table->decimal('refund_amount', 10, 2)->nullable();
            
            // Ticket Information
            $table->boolean('ticket_issued')->default(false);
            $table->timestamp('ticket_issued_at')->nullable();
            $table->string('ticket_file_path')->nullable(); // PDF ticket
            
            // Check-in Status
            $table->boolean('checked_in')->default(false);
            $table->timestamp('checked_in_at')->nullable();
            $table->json('boarding_passes')->nullable(); // URLs to boarding pass PDFs
            
            // Admin Notes
            $table->text('admin_notes')->nullable();
            $table->foreignId('reviewed_by')->nullable()->constrained('users');
            $table->timestamp('reviewed_at')->nullable();
            
            // Metadata
            $table->string('booking_source', 50)->default('web'); // web, mobile, admin
            $table->ipAddress('ip_address')->nullable();
            $table->timestamps();
            $table->softDeletes();
            
            // Indexes
            $table->index(['user_id', 'status']);
            $table->index(['travel_date', 'status']);
            $table->index(['payment_status', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('flight_bookings');
    }
};
