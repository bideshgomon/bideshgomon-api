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
        Schema::create('agency_country_assignments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('agency_id')->constrained('users')->onDelete('cascade');
            $table->unsignedBigInteger('service_module_id')->nullable();
            
            // Country & Visa Type
            $table->string('country');
            $table->string('country_code', 3)->nullable();
            $table->unsignedBigInteger('country_id')->nullable();
            $table->string('visa_type')->nullable();
            $table->unsignedBigInteger('visa_type_id')->nullable();
            
            $table->string('assignment_scope')->default('full'); // full, limited, view_only
            
            // Commission settings
            $table->decimal('platform_commission_rate', 5, 2)->default(15.00);
            $table->string('commission_type')->default('percentage'); // percentage, fixed
            $table->decimal('fixed_commission_amount', 10, 2)->nullable();
            
            // Permissions
            $table->boolean('can_edit_requirements')->default(false);
            $table->boolean('can_set_fees')->default(false);
            $table->boolean('can_process_applications')->default(true);
            
            // Statistics
            $table->integer('total_applications')->default(0);
            $table->integer('approved_applications')->default(0);
            $table->integer('rejected_applications')->default(0);
            $table->decimal('total_revenue', 10, 2)->default(0);
            $table->decimal('platform_earnings', 10, 2)->default(0);
            
            $table->boolean('is_active')->default(true);
            $table->timestamp('assigned_at')->nullable();
            $table->unsignedBigInteger('assigned_by')->nullable();
            $table->text('assignment_notes')->nullable();
            
            $table->timestamps();
            
            // Indexes
            $table->index(['agency_id', 'country']);
            $table->index('service_module_id');
            $table->index('is_active');
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
