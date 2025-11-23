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
        Schema::table('visa_requirements', function (Blueprint $table) {
            // Agency Assignment
            $table->foreignId('managed_by_agency')->nullable()->after('service_module_id')->constrained('users')->nullOnDelete();
            $table->timestamp('agency_assigned_at')->nullable()->after('managed_by_agency');
            
            // Agency Pricing (Agency sets these)
            $table->decimal('agency_service_fee', 10, 2)->nullable()->after('service_fee')->comment('Fee agency charges for their service');
            $table->decimal('agency_processing_fee', 10, 2)->nullable()->after('agency_service_fee')->comment('Agency processing fee');
            
            // Platform Pricing (Admin sets these)
            $table->decimal('platform_commission', 10, 2)->nullable()->after('agency_processing_fee')->comment('Platform commission amount');
            $table->decimal('platform_commission_rate', 5, 2)->default(15.00)->after('platform_commission')->comment('Platform commission percentage');
            
            // Total Breakdown (Auto-calculated)
            $table->decimal('total_agency_earnings', 10, 2)->nullable()->after('platform_commission_rate')->comment('What agency earns after platform commission');
            $table->decimal('total_applicant_cost', 10, 2)->nullable()->after('total_agency_earnings')->comment('Total cost for applicant');
            
            // Agency Management
            $table->boolean('agency_can_edit')->default(true)->after('is_active')->comment('Can assigned agency edit this?');
            $table->text('admin_notes')->nullable()->after('agency_can_edit')->comment('Admin notes about this requirement');
            
            // Indexes
            $table->index('managed_by_agency');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('visa_requirements', function (Blueprint $table) {
            $table->dropForeign(['managed_by_agency']);
            $table->dropColumn([
                'managed_by_agency',
                'agency_assigned_at',
                'agency_service_fee',
                'agency_processing_fee',
                'platform_commission',
                'platform_commission_rate',
                'total_agency_earnings',
                'total_applicant_cost',
                'agency_can_edit',
                'admin_notes',
            ]);
        });
    }
};
