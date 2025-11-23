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
        Schema::create('service_applications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('service_module_id')->constrained()->onDelete('cascade');
            $table->string('application_number')->unique();
            
            // Status tracking
            $table->enum('status', [
                'draft',
                'submitted',
                'under_review',
                'documents_required',
                'processing',
                'approved',
                'rejected',
                'completed',
                'cancelled'
            ])->default('draft');
            
            // Application data
            $table->json('form_data')->nullable(); // Service-specific form data
            $table->json('documents')->nullable(); // Uploaded documents
            $table->json('timeline')->nullable(); // Status change history
            
            // Assignment
            $table->foreignId('assigned_to')->nullable()->constrained('users')->nullOnDelete();
            $table->string('assigned_role')->nullable(); // agency, consultant, staff
            
            // Payment
            $table->decimal('amount', 10, 2)->default(0);
            $table->string('payment_status')->default('pending'); // pending, paid, refunded
            $table->string('payment_method')->nullable();
            $table->timestamp('payment_date')->nullable();
            $table->string('transaction_id')->nullable();
            
            // Processing
            $table->text('admin_notes')->nullable();
            $table->text('user_notes')->nullable();
            $table->string('priority')->default('normal'); // low, normal, high, urgent
            $table->date('expected_completion_date')->nullable();
            $table->timestamp('completed_at')->nullable();
            
            // Timestamps
            $table->timestamp('submitted_at')->nullable();
            $table->timestamp('reviewed_at')->nullable();
            $table->timestamps();
            $table->softDeletes();
            
            // Indexes
            $table->index('application_number');
            $table->index('status');
            $table->index(['user_id', 'status']);
            $table->index(['service_module_id', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('service_applications');
    }
};
