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
        Schema::create('hotel_bookings', function (Blueprint $table) {
            $table->id();
            $table->string('booking_reference')->unique();
            
            // Relationships
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('hotel_id')->constrained()->onDelete('cascade');
            $table->foreignId('hotel_room_id')->constrained()->onDelete('cascade');
            
            // Booking Details
            $table->date('check_in_date');
            $table->date('check_out_date');
            $table->integer('nights'); // Auto-calculated
            $table->integer('rooms_count')->default(1); // Number of rooms booked
            
            // Guest Information
            $table->integer('adults_count')->default(1);
            $table->integer('children_count')->default(0);
            $table->json('guests'); // Array of guest details [{name, age, passport_number}]
            
            // Primary Guest/Contact
            $table->string('guest_name');
            $table->string('guest_email');
            $table->string('guest_phone');
            $table->text('special_requests')->nullable();
            
            // Pricing
            $table->decimal('room_price_per_night', 10, 2);
            $table->decimal('subtotal', 10, 2); // rooms_count * nights * price_per_night
            $table->decimal('tax_amount', 10, 2)->default(0);
            $table->decimal('service_charge', 10, 2)->default(0);
            $table->decimal('discount_amount', 10, 2)->default(0);
            $table->decimal('total_amount', 10, 2);
            $table->string('currency')->default('BDT');
            
            // Payment
            $table->string('payment_method')->default('wallet'); // wallet, card, cash
            $table->string('payment_status')->default('pending'); // pending, paid, refunded, failed
            $table->timestamp('paid_at')->nullable();
            $table->string('transaction_id')->nullable();
            
            // Status
            $table->string('status')->default('pending'); // pending, confirmed, checked_in, checked_out, cancelled, no_show
            $table->timestamp('confirmed_at')->nullable();
            $table->timestamp('checked_in_at')->nullable();
            $table->timestamp('checked_out_at')->nullable();
            $table->timestamp('cancelled_at')->nullable();
            
            // Cancellation
            $table->text('cancellation_reason')->nullable();
            $table->decimal('refund_amount', 10, 2)->nullable();
            $table->timestamp('refunded_at')->nullable();
            
            // Notes
            $table->text('admin_notes')->nullable();
            $table->text('hotel_notes')->nullable();
            
            // Tracking
            $table->string('ip_address')->nullable();
            $table->text('user_agent')->nullable();
            
            $table->timestamps();
            $table->softDeletes();
            
            // Indexes
            $table->index('booking_reference');
            $table->index('user_id');
            $table->index('hotel_id');
            $table->index('status');
            $table->index('payment_status');
            $table->index('check_in_date');
            $table->index(['user_id', 'status']);
            $table->index(['hotel_id', 'check_in_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hotel_bookings');
    }
};
