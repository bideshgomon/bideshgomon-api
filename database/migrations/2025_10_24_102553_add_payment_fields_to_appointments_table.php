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
        Schema::table('appointments', function (Blueprint $table) {
            // Store the Stripe Checkout Session ID after the 'price' column
            $table->string('stripe_checkout_session_id')->nullable()->after('price');
            // You could add a more granular payment status if needed
            // $table->string('payment_status')->default('unpaid')->after('stripe_checkout_session_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('appointments', function (Blueprint $table) {
            $table->dropColumn('stripe_checkout_session_id');
            // $table->dropColumn('payment_status');
        });
    }
};