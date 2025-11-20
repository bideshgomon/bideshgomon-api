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
        Schema::create('translation_quotes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('translation_request_id')->constrained()->onDelete('cascade');
            $table->foreignId('quoted_by')->constrained('users')->onDelete('cascade');
            
            // Quote Details
            $table->decimal('price_per_page', 10, 2);
            $table->decimal('certification_fee', 10, 2)->default(0);
            $table->decimal('urgency_fee', 10, 2)->default(0);
            $table->decimal('total_amount', 10, 2);
            $table->integer('estimated_delivery_days');
            $table->text('notes')->nullable();
            
            // Status
            $table->enum('status', ['pending', 'accepted', 'rejected', 'expired'])->default('pending');
            $table->timestamp('valid_until')->nullable();
            $table->timestamp('accepted_at')->nullable();
            $table->timestamp('rejected_at')->nullable();
            $table->text('rejection_reason')->nullable();
            
            $table->timestamps();
            
            $table->index(['translation_request_id', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('translation_quotes');
    }
};
