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
            // Emergency Contact Information
            $table->string('emergency_contact_name')->nullable()->after('social_links');
            $table->string('emergency_contact_relationship', 50)->nullable()->after('emergency_contact_name');
            $table->string('emergency_contact_phone', 20)->nullable()->after('emergency_contact_relationship');
            $table->string('emergency_contact_email')->nullable()->after('emergency_contact_phone');
            $table->text('emergency_contact_address')->nullable()->after('emergency_contact_email');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('user_profiles', function (Blueprint $table) {
            $table->dropColumn([
                'emergency_contact_name',
                'emergency_contact_relationship',
                'emergency_contact_phone',
                'emergency_contact_email',
                'emergency_contact_address',
            ]);
        });
    }
};
