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
            // Only add columns if they don't exist
            if (!Schema::hasColumn('user_profiles', 'blood_group')) {
                $table->enum('blood_group', ['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-'])->nullable()->after('marital_status');
            }
            if (!Schema::hasColumn('user_profiles', 'religion')) {
                $table->string('religion', 50)->nullable()->after('blood_group');
            }
            if (!Schema::hasColumn('user_profiles', 'bkash_number')) {
                $table->string('bkash_number', 20)->nullable()->after('passport_expiry_date');
            }
            if (!Schema::hasColumn('user_profiles', 'nagad_number')) {
                $table->string('nagad_number', 20)->nullable()->after('bkash_number');
            }
            if (!Schema::hasColumn('user_profiles', 'emergency_contact_name')) {
                $table->string('emergency_contact_name')->nullable()->after('nagad_number');
            }
            if (!Schema::hasColumn('user_profiles', 'emergency_contact_phone')) {
                $table->string('emergency_contact_phone', 20)->nullable()->after('emergency_contact_name');
            }
            if (!Schema::hasColumn('user_profiles', 'emergency_contact_relationship')) {
                $table->string('emergency_contact_relationship', 50)->nullable()->after('emergency_contact_phone');
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
                'blood_group',
                'religion',
                'bkash_number',
                'nagad_number',
                'emergency_contact_name',
                'emergency_contact_phone',
                'emergency_contact_relationship',
            ]);
        });
    }
};
