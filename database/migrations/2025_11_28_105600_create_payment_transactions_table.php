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
        Schema::create('payment_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('wallet_id')->nullable()->constrained()->onDelete('set null');
            
            // Transaction identifiers
            $table->string('transaction_id')->unique();
            $table->string('gateway')->index(); // sslcommerz, bkash, nagad
            $table->string('gateway_transaction_id')->nullable()->index();
            $table->string('payment_reference_id')->nullable();
            
            // Payment details
            $table->decimal('amount', 15, 2);
            $table->string('currency', 3)->default('BDT');
            $table->decimal('gateway_fee', 15, 2)->default(0);
            $table->decimal('net_amount', 15, 2);
            
            // Payment status
            $table->enum('status', ['pending', 'processing', 'completed', 'failed', 'cancelled', 'refunded'])
                ->default('pending')
                ->index();
            $table->string('payment_method')->nullable(); // card, mobile_banking, internet_banking
            
            // Customer information
            $table->string('customer_name');
            $table->string('customer_email');
            $table->string('customer_phone', 20);
            
            // Additional metadata
            $table->string('product_name')->nullable();
            $table->text('description')->nullable();
            $table->json('gateway_response')->nullable();
            $table->json('metadata')->nullable();
            
            // URLs
            $table->string('callback_url')->nullable();
            $table->string('redirect_url')->nullable();
            
            // Error tracking
            $table->string('error_code')->nullable();
            $table->text('error_message')->nullable();
            
            // Refund information
            $table->decimal('refund_amount', 15, 2)->default(0);
            $table->string('refund_reference')->nullable();
            $table->timestamp('refunded_at')->nullable();
            
            // Timestamps
            $table->timestamp('paid_at')->nullable();
            $table->timestamp('failed_at')->nullable();
            $table->timestamp('cancelled_at')->nullable();
            $table->timestamps();
            
            // Indexes
            $table->index('status');
            $table->index('gateway');
            $table->index('created_at');
            $table->index(['user_id', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payment_transactions');
    }
};
