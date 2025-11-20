<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Adjust short ISO code columns to store full country/nationality names where validation expects up to 100 chars.
     */
    public function up(): void
    {
        // Work Experiences: country (2 -> 100)
        Schema::table('user_work_experiences', function (Blueprint $table) {
            $table->string('country', 100)->nullable()->change();
        });

        // Travel History: country_visited (2 -> 100)
        Schema::table('user_travel_history', function (Blueprint $table) {
            $table->string('country_visited', 100)->change();
        });

        // Passports: nationality (2 -> 100)
        Schema::table('user_passports', function (Blueprint $table) {
            $table->string('nationality', 100)->nullable()->change();
        });

        // Family Members: nationality (2 -> 100)
        Schema::table('user_family_members', function (Blueprint $table) {
            $table->string('nationality', 100)->nullable()->change();
        });
    }

    public function down(): void
    {
        // Revert changes (may truncate data if longer values exist)
        Schema::table('user_work_experiences', function (Blueprint $table) {
            $table->string('country', 2)->nullable()->change();
        });
        Schema::table('user_travel_history', function (Blueprint $table) {
            $table->string('country_visited', 2)->change();
        });
        Schema::table('user_passports', function (Blueprint $table) {
            $table->string('nationality', 2)->nullable()->change();
        });
        Schema::table('user_family_members', function (Blueprint $table) {
            $table->string('nationality', 2)->nullable()->change();
        });
    }
};
