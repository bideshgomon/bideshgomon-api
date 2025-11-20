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
        Schema::create('user_visa_history', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_passport_id')->nullable()->constrained()->onDelete('set null');
            
            // Visa Information
            $table->string('country', 2); // ISO 3166-1 alpha-2 country code
            $table->enum('visa_type', [
                'tourist',
                'business',
                'student',
                'work',
                'transit',
                'family_visit',
                'medical',
                'conference',
                'other'
            ]);
            $table->string('visa_category')->nullable(); // e.g., "B1/B2", "Tier 4", "Subclass 500"
            $table->string('visa_number')->nullable();
            
            // Dates
            $table->date('application_date')->nullable();
            $table->date('issue_date')->nullable();
            $table->date('expiry_date')->nullable();
            $table->date('entry_date')->nullable(); // When actually entered country
            $table->date('exit_date')->nullable(); // When actually left country
            
            // Duration & Purpose
            $table->integer('duration_of_stay')->nullable(); // Days stayed
            $table->text('purpose_of_visit')->nullable();
            
            // Status
            $table->enum('status', ['approved', 'rejected', 'pending', 'cancelled', 'expired'])->default('approved');
            $table->boolean('was_visa_rejected')->default(false);
            $table->text('rejection_reason')->nullable();
            $table->boolean('overstay_occurred')->default(false);
            $table->integer('overstay_days')->nullable();
            
            // Application Details
            $table->string('application_reference')->nullable();
            $table->string('embassy_location')->nullable(); // Where applied (Dhaka, Delhi, etc.)
            $table->decimal('visa_fee', 10, 2)->nullable();
            $table->string('currency', 3)->default('BDT');
            
            // Document Storage
            $table->string('visa_scan_path')->nullable();
            $table->string('approval_letter_path')->nullable();
            $table->string('rejection_letter_path')->nullable();
            
            // Notes
            $table->text('notes')->nullable();
            
            $table->timestamps();
            
            // Indexes
            $table->index('user_id');
            $table->index('country');
            $table->index(['was_visa_rejected', 'country']); // Important for checking refusal history
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_visa_history');
    }
};
