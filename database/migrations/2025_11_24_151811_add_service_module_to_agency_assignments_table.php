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
        Schema::table('agency_country_assignments', function (Blueprint $table) {
            // Add service module reference (CRITICAL for multi-service support)
            $table->foreignId('service_module_id')->nullable()->after('agency_id')->constrained('service_modules')->nullOnDelete();
            
            // Convert country string to country_id (FK)
            $table->foreignId('country_id')->nullable()->after('service_module_id')->constrained('countries')->nullOnDelete();
            
            // Convert visa_type string to visa_type_id (FK)
            $table->foreignId('visa_type_id')->nullable()->after('country_id')->constrained('visa_types')->nullOnDelete();
            
            // Add assignment scope for flexibility
            $table->enum('assignment_scope', ['global', 'country_specific', 'visa_specific'])->default('visa_specific')->after('visa_type_id');
            
            // Add indexes for performance
            $table->index(['service_module_id', 'country_id', 'visa_type_id'], 'service_country_visa_idx');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('agency_country_assignments', function (Blueprint $table) {
            $table->dropForeign(['service_module_id']);
            $table->dropForeign(['country_id']);
            $table->dropForeign(['visa_type_id']);
            $table->dropIndex('service_country_visa_idx');
            $table->dropColumn(['service_module_id', 'country_id', 'visa_type_id', 'assignment_scope']);
        });
    }
};
