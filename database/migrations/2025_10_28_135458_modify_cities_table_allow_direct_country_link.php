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
        Schema::table('cities', function (Blueprint $table) {
            // 1. Make state_id nullable
            // Important: Ensure your DB supports altering foreign keys like this,
            // or you might need to drop and re-add the key.
            // Temporarily drop foreign key if necessary (syntax might vary by DB)
            // $table->dropForeign(['state_id']);
            $table->foreignId('state_id')->nullable()->change();

            // 2. Add nullable country_id after state_id
            $table->foreignId('country_id')->nullable()->after('state_id')->constrained()->onDelete('cascade');

            // Re-add foreign key if dropped (ensure it matches original definition but allows null)
            // $table->foreign('state_id')->references('id')->on('states')->onDelete('cascade');

             // Add a check constraint (Optional but Recommended - SQL syntax varies)
             // Ensures that either state_id OR country_id is set, but not neither.
             // Example for MySQL:
             // DB::statement('ALTER TABLE cities ADD CONSTRAINT chk_city_parent CHECK (state_id IS NOT NULL OR country_id IS NOT NULL)');
             // Example for PostgreSQL:
             // DB::statement('ALTER TABLE cities ADD CONSTRAINT chk_city_parent CHECK (state_id IS NOT NULL OR country_id IS NOT NULL)');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cities', function (Blueprint $table) {
            // Drop check constraint if added
            // DB::statement('ALTER TABLE cities DROP CONSTRAINT chk_city_parent'); // Adjust syntax for your DB

            $table->dropForeign(['country_id']);
            $table->dropColumn('country_id');

            // Revert state_id to non-nullable (assuming it was originally)
            // May require handling existing null values first
             // $table->dropForeign(['state_id']); // If necessary
             // $table->foreignId('state_id')->nullable(false)->change(); // This might fail if nulls exist
             // $table->foreign('state_id')->references('id')->on('states')->onDelete('cascade'); // If necessary
        });
    }
};