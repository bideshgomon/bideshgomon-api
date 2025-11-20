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
        Schema::create('translation_requests', function (Blueprint $table) {
            $table->id();
            $table->string('request_reference')->unique();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            
            // Translation Details
            $table->string('source_language', 50);
            $table->string('target_language', 50);
            $table->enum('document_type', ['legal', 'academic', 'business', 'medical', 'technical', 'personal', 'certificate', 'other']);
            $table->enum('certification_type', ['standard', 'certified', 'notarized'])->default('standard');
            $table->integer('page_count')->default(1);
            $table->integer('word_count')->nullable();
            
            // Service Details
            $table->enum('urgency', ['standard', 'express', 'urgent'])->default('standard');
            $table->integer('delivery_days')->default(5);
            $table->date('required_by')->nullable();
            $table->text('special_instructions')->nullable();
            
            // Pricing
            $table->decimal('price_per_page', 10, 2)->default(0);
            $table->decimal('certification_fee', 10, 2)->default(0);
            $table->decimal('urgency_fee', 10, 2)->default(0);
            $table->decimal('total_amount', 10, 2)->default(0);
            
            // Status & Assignment
            $table->enum('status', ['draft', 'submitted', 'quote_pending', 'quote_sent', 'quote_accepted', 'in_progress', 'completed', 'delivered', 'cancelled'])->default('draft');
            $table->foreignId('assigned_translator_id')->nullable()->constrained('users')->nullOnDelete();
            $table->enum('priority', ['normal', 'high', 'urgent'])->default('normal');
            
            // Payment
            $table->enum('payment_status', ['pending', 'paid', 'refunded'])->default('pending');
            $table->string('payment_method', 50)->nullable();
            $table->string('payment_transaction_id', 100)->nullable();
            $table->timestamp('paid_at')->nullable();
            
            // Progress Tracking
            $table->timestamp('submitted_at')->nullable();
            $table->timestamp('started_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamp('delivered_at')->nullable();
            $table->integer('progress_percentage')->default(0);
            
            // Admin Notes
            $table->text('admin_notes')->nullable();
            $table->text('translator_notes')->nullable();
            $table->text('rejection_reason')->nullable();
            
            // Metadata
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            
            $table->timestamps();
            $table->softDeletes();
            
            $table->index(['user_id', 'status']);
            $table->index(['assigned_translator_id', 'status']);
            $table->index('status');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('translation_requests');
    }
};
