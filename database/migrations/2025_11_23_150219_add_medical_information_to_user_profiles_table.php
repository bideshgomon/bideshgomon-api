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
            // Medical Information
            $table->string('blood_group', 10)->nullable()->after('emergency_contact_address');
            $table->text('allergies')->nullable()->after('blood_group');
            $table->text('medical_conditions')->nullable()->after('allergies');
            $table->json('vaccinations')->nullable()->after('medical_conditions'); // COVID, Yellow Fever, etc.
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
                'blood_group',
                'allergies',
                'medical_conditions',
                'vaccinations',
                'health_insurance_provider',
                'health_insurance_policy_number',
                'health_insurance_expiry_date',
            ]);
        });
    }
};
