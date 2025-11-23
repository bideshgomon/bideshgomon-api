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
            $table->string('service_type')->default('query_based')->after('slug'); 
            // Service types:
            // 'query_based' - User submits request, gets quotes from agencies (default for most services)
            // 'api_based' - Real-time data from external APIs (travel insurance, flight search)
            // 'premade' - Pre-built tools/templates (CV Builder, calculators)
            // 'marketplace' - Direct purchase/booking (hotels, flights)
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('service_modules', function (Blueprint $table) {
            $table->dropColumn('service_type');
        });
    }
};
