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
            // Add missing emergency contact fields
            if (!Schema::hasColumn('user_profiles', 'emergency_contact_email')) {
                $table->string('emergency_contact_email')->nullable()->after('emergency_contact_relationship');
            }
            if (!Schema::hasColumn('user_profiles', 'emergency_contact_address')) {
                $table->string('emergency_contact_address', 500)->nullable()->after('emergency_contact_email');
            }
            
            // Add medical information fields
            if (!Schema::hasColumn('user_profiles', 'allergies')) {
                $table->text('allergies')->nullable()->after('blood_group');
            }
            if (!Schema::hasColumn('user_profiles', 'medical_conditions')) {
                $table->text('medical_conditions')->nullable()->after('allergies');
            }
            if (!Schema::hasColumn('user_profiles', 'vaccinations')) {
                $table->json('vaccinations')->nullable()->after('medical_conditions');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('user_profiles', function (Blueprint $table) {
            $table->dropColumn([
                'emergency_contact_email',
                'emergency_contact_address',
                'allergies',
                'medical_conditions',
                'vaccinations',
            ]);
        });
    }
};
