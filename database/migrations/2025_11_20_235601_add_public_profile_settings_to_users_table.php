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
        Schema::table('users', function (Blueprint $table) {
            $table->string('public_profile_slug')->unique()->nullable()->after('referral_code');
            $table->boolean('profile_is_public')->default(false)->after('public_profile_slug');
            $table->json('profile_visibility_settings')->nullable()->after('profile_is_public');
            $table->text('profile_bio')->nullable()->after('profile_visibility_settings');
            $table->string('profile_headline')->nullable()->after('profile_bio');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'public_profile_slug',
                'profile_is_public',
                'profile_visibility_settings',
                'profile_bio',
                'profile_headline'
            ]);
        });
    }
};
