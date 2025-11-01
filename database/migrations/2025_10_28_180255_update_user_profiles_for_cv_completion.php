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

            // --- Deprecate Old Address Fields ---
            $table->string('address_line_1')->nullable()->change();
            $table->string('address_line_2')->nullable()->change();
            $table->string('city')->nullable()->change();
            $table->string('country')->nullable()->change();

            // --- Personal Details ---
            $table->string('gender')->nullable()->after('passport_number');
            $table->string('marital_status')->nullable()->after('gender');
            $table->string('current_occupation')->nullable()->after('marital_status');
            // 'bio' column is already created in the original migration, so we skip it here.

            // --- Present Address ---
            $table->string('present_address_line')->nullable()->after('bio');
            $table->string('present_city')->nullable()->after('present_address_line');
            $table->string('present_country')->nullable()->after('present_city');
            $table->string('present_postal_code')->nullable()->after('present_country');

            // --- Permanent Address ---
            $table->boolean('is_permanent_same_as_present')->default(false)->after('present_postal_code');
            $table->string('permanent_address_line')->nullable()->after('is_permanent_same_as_present');
            $table->string('permanent_city')->nullable()->after('permanent_address_line');
            $table->string('permanent_country')->nullable()->after('permanent_city');
            $table->string('permanent_postal_code')->nullable()->after('permanent_country');

            // --- Social & Portfolio Links ---
            $table->string('social_linkedin')->nullable()->after('permanent_postal_code');
            $table->string('social_github')->nullable()->after('social_linkedin');
            $table->string('social_website')->nullable()->after('social_github');
            $table->string('portfolio_link')->nullable()->after('social_website');

            // --- Bidesh Gomon Analysis Fields ---
            $table->string('travel_purpose')->nullable()->after('portfolio_link');
            $table->string('funding_source')->nullable()->after('travel_purpose');
            $table->decimal('estimated_funds', 15, 2)->nullable()->after('funding_source');
            $table->json('preferred_countries')->nullable()->after('estimated_funds');
            $table->string('intended_intake')->nullable()->after('preferred_countries');
            $table->boolean('has_dependents')->default(false)->after('intended_intake');
            $table->integer('dependents_count')->nullable()->after('has_dependents');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('user_profiles', function (Blueprint $table) {
            // Revert old address fields
            $table->string('address_line_1')->nullable(false)->change();
            $table->string('address_line_2')->nullable(false)->change();
            $table->string('city')->nullable(false)->change();
            $table->string('country')->nullable(false)->change();

            // Drop all new columns (Note 'bio' is removed from this list)
            $table->dropColumn([
                'gender', 'marital_status', 'current_occupation',
                'present_address_line', 'present_city', 'present_country', 'present_postal_code',
                'is_permanent_same_as_present', 'permanent_address_line', 'permanent_city',
                'permanent_country', 'permanent_postal_code',
                'social_linkedin', 'social_github', 'social_website', 'portfolio_link',
                'travel_purpose', 'funding_source', 'estimated_funds',
                'preferred_countries', 'intended_intake', 'has_dependents', 'dependents_count',
            ]);
        });
    }
};
