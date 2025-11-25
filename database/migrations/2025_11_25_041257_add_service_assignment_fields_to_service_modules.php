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
        Schema::table('service_modules', function (Blueprint $table) {
            // Service Assignment Model (Plugin System)
            $table->enum('assignment_model', [
                'competitive',          // Multiple agencies compete (Tourist Visa)
                'exclusive_resource',   // First to add resource owns it (Universities)
                'global_single',        // One agency worldwide (Air Tickets)
                'multi_country',        // Multi-country specialists (Jobs)
                'hybrid',               // API + Agency fallback (Hotels)
                'peer_to_peer'          // No agency needed (CV Builder)
            ])->default('competitive')
                ->after('service_type')
                ->comment('How agencies are assigned');
            
            $table->boolean('allows_multiple_agencies')
                ->default(true)
                ->after('assignment_model')
                ->comment('Can multiple agencies handle same scope?');
            
            $table->boolean('requires_admin_approval')
                ->default(false)
                ->after('allows_multiple_agencies')
                ->comment('Admin must approve agency assignments?');
            
            $table->boolean('resource_locking')
                ->default(false)
                ->after('requires_admin_approval')
                ->comment('First agency locks resource (universities)?');
            
            $table->boolean('requires_agency')
                ->default(true)
                ->after('resource_locking')
                ->comment('Does this service need agency processing?');
            
            $table->decimal('platform_commission_rate', 5, 2)
                ->default(15.00)
                ->after('base_price')
                ->comment('Default platform commission %');
            
            $table->integer('quote_timeout_hours')
                ->default(24)
                ->after('platform_commission_rate')
                ->comment('Hours for agencies to respond with quotes');
            
            $table->integer('min_quotes_required')
                ->default(1)
                ->after('quote_timeout_hours')
                ->comment('Minimum quotes before user can proceed');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('service_modules', function (Blueprint $table) {
            $table->dropColumn([
                'assignment_model',
                'allows_multiple_agencies',
                'requires_admin_approval',
                'resource_locking',
                'requires_agency',
                'platform_commission_rate',
                'quote_timeout_hours',
                'min_quotes_required',
            ]);
        });
    }
};
