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
        Schema::table('user_profiles', function (Blueprint $table) {
            // Add permanent address fields for Bangladesh
            $table->string('permanent_address_line')->nullable()->after('present_postal_code');
            $table->string('permanent_city', 100)->nullable()->after('permanent_address_line');
            $table->string('permanent_division', 100)->nullable()->after('permanent_city');
            $table->string('permanent_district', 100)->nullable()->after('permanent_division');
            $table->string('permanent_postal_code', 20)->nullable()->after('permanent_district');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('user_profiles', function (Blueprint $table) {
            $table->dropColumn([
                'permanent_address_line',
                'permanent_city',
                'permanent_division',
                'permanent_district',
                'permanent_postal_code',
            ]);
        });
    }
};
