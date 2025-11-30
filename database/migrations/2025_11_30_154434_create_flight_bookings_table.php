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
            $table->string('booking_reference')->unique();
            $table->string('pnr_number')->nullable();
            $table->string('ticket_number')->nullable();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->unsignedBigInteger('flight_route_id')->nullable();
            $table->enum('trip_type', ['one-way', 'round-trip', 'multi-city'])->default('one-way');
            $table->unsignedBigInteger('return_flight_route_id')->nullable();
            $table->date('return_travel_date')->nullable();
            $table->json('multi_city_segments')->nullable();
            $table->decimal('return_base_fare', 10, 2)->nullable();
            $table->decimal('multi_city_total', 10, 2)->nullable();
            $table->date('travel_date');
            $table->enum('flight_class', ['economy', 'premium-economy', 'business', 'first-class'])->default('economy');
            $table->integer('passengers_count')->default(1);
            $table->json('passengers')->nullable();
            $table->string('contact_name');
            $table->string('contact_email');
            $table->string('contact_phone');
            $table->text('special_requests')->nullable();
            $table->json('selected_seats')->nullable();
            $table->decimal('seat_selection_fee', 10, 2)->default(0);
            $table->integer('extra_baggage_count')->default(0);
            $table->decimal('extra_baggage_fee', 10, 2)->default(0);
            $table->json('meal_preferences')->nullable();
            $table->decimal('meal_fee', 10, 2)->default(0);
            $table->decimal('base_fare', 10, 2);
            $table->decimal('taxes_fees', 10, 2)->default(0);
            $table->decimal('booking_fee', 10, 2)->default(0);
            $table->decimal('insurance_fee', 10, 2)->default(0);
            $table->decimal('subtotal', 10, 2);
            $table->decimal('discount_amount', 10, 2)->default(0);
            $table->string('promo_code')->nullable();
            $table->decimal('total_amount', 10, 2);
            $table->enum('payment_status', ['pending', 'paid', 'partial', 'refunded', 'failed'])->default('pending');
            $table->string('payment_method')->nullable();
            $table->string('payment_reference')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->enum('status', ['pending', 'confirmed', 'completed', 'cancelled', 'refunded'])->default('pending');
            $table->timestamp('confirmed_at')->nullable();
            $table->timestamp('cancelled_at')->nullable();
            $table->text('cancellation_reason')->nullable();
            $table->decimal('refund_amount', 10, 2)->nullable();
            $table->boolean('ticket_issued')->default(false);
            $table->timestamp('ticket_issued_at')->nullable();
            $table->string('ticket_file_path')->nullable();
            $table->boolean('checked_in')->default(false);
            $table->timestamp('checked_in_at')->nullable();
            $table->json('boarding_passes')->nullable();
            $table->text('admin_notes')->nullable();
            $table->foreignId('reviewed_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('reviewed_at')->nullable();
            $table->string('booking_source')->nullable();
            $table->string('ip_address')->nullable();
            $table->timestamps();
            $table->softDeletes();
            
            $table->index(['user_id', 'status']);
            $table->index(['payment_status', 'status']);
            $table->index('travel_date');
            $table->index('created_at');
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
