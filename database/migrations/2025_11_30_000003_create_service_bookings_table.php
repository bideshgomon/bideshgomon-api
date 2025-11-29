<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('service_bookings', function (Blueprint $table) {
            $table->id();
            $table->string('booking_number')->unique(); // BK-2025-001234
            $table->foreignId('service_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // Customer
            $table->foreignId('provider_id')->constrained('users')->onDelete('cascade'); // Agency/Consultant
            
            // Pricing
            $table->decimal('service_price', 10, 2);
            $table->decimal('processing_fee', 10, 2)->default(0);
            $table->decimal('total_amount', 10, 2);
            $table->string('currency', 3)->default('BDT');
            
            // Payment
            $table->enum('payment_status', ['pending', 'partial', 'paid', 'refunded'])->default('pending');
            $table->decimal('paid_amount', 10, 2)->default(0);
            $table->enum('payment_method', ['wallet', 'bkash', 'nagad', 'card', 'bank'])->nullable();
            $table->foreignId('wallet_transaction_id')->nullable()->constrained('wallet_transactions')->onDelete('set null');
            
            // Booking Details
            $table->enum('status', [
                'pending',
                'confirmed',
                'in_progress',
                'documents_pending',
                'documents_submitted',
                'under_review',
                'approved',
                'completed',
                'cancelled',
                'refunded'
            ])->default('pending');
            
            $table->text('customer_notes')->nullable();
            $table->text('provider_notes')->nullable();
            $table->text('admin_notes')->nullable();
            
            // Schedule
            $table->dateTime('scheduled_at')->nullable();
            $table->dateTime('confirmed_at')->nullable();
            $table->dateTime('started_at')->nullable();
            $table->dateTime('completed_at')->nullable();
            $table->dateTime('cancelled_at')->nullable();
            
            // Documents
            $table->json('documents_uploaded')->nullable(); // Customer uploaded documents
            $table->json('documents_required')->nullable(); // Copy from service
            $table->integer('documents_count')->default(0);
            
            // Assignment
            $table->foreignId('assigned_to')->nullable()->constrained('users')->onDelete('set null'); // Specific consultant
            $table->timestamp('assigned_at')->nullable();
            
            // Review
            $table->integer('rating')->nullable(); // 1-5
            $table->text('review')->nullable();
            $table->timestamp('reviewed_at')->nullable();
            
            $table->timestamps();
            $table->softDeletes();
            
            // Indexes
            $table->index('booking_number');
            $table->index(['user_id', 'status']);
            $table->index(['provider_id', 'status']);
            $table->index('payment_status');
            $table->index('scheduled_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('service_bookings');
    }
};
