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
            // Only add missing health insurance columns (blood_group and vaccinations already exist)
            $table->string('health_insurance_provider')->nullable()->after('vaccinations');
            $table->string('health_insurance_policy_number')->nullable()->after('health_insurance_provider');
            $table->date('health_insurance_expiry_date')->nullable()->after('health_insurance_policy_number');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('user_profiles', function (Blueprint $table) {
            $table->dropColumn([
                'health_insurance_provider',
                'health_insurance_policy_number',
                'health_insurance_expiry_date',
            ]);
        });
    }
};
