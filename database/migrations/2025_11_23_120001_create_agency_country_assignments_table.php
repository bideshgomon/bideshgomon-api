<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * Agencies can be assigned to handle one or multiple countries.
     * Each assignment can have custom commission rates.
     */
    public function up(): void
    {
        Schema::create('agency_country_assignments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('agency_id')->constrained('users')->onDelete('cascade');
            
            // Country Information
            $table->string('country'); // Country name
            $table->string('country_code', 3); // ISO code
            $table->string('visa_type')->default('tourist'); // tourist, business, student, etc.
            
            // Commission Structure (Platform takes commission from agency earnings)
            $table->decimal('platform_commission_rate', 5, 2)->default(15.00); // Platform commission percentage (e.g., 15%)
            $table->enum('commission_type', ['percentage', 'fixed'])->default('percentage');
            $table->decimal('fixed_commission_amount', 10, 2)->nullable(); // If commission_type is fixed
            
            // Agency Capabilities
            $table->boolean('can_edit_requirements')->default(true); // Can agency edit visa requirements?
            $table->boolean('can_set_fees')->default(true); // Can agency set their own fees?
            $table->boolean('can_process_applications')->default(true); // Can agency process applications?
            
            // Performance Tracking
            $table->integer('total_applications')->default(0);
            $table->integer('approved_applications')->default(0);
            $table->integer('rejected_applications')->default(0);
            $table->decimal('total_revenue', 12, 2)->default(0); // Total revenue generated
            $table->decimal('platform_earnings', 12, 2)->default(0); // Platform's commission earned
            
            // Status & Assignment
            $table->boolean('is_active')->default(true);
            $table->timestamp('assigned_at')->useCurrent();
            $table->foreignId('assigned_by')->nullable()->constrained('users')->nullOnDelete(); // Admin who assigned
            $table->text('assignment_notes')->nullable();
            
            $table->timestamps();
            
            // Indexes
            $table->index(['agency_id', 'country', 'visa_type']);
            $table->index('country');
            $table->index('is_active');
            
            // Unique constraint: One agency per country per visa type
            $table->unique(['agency_id', 'country', 'visa_type'], 'agency_country_visa_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('agency_country_assignments');
    }
};
