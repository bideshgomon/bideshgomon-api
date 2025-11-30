<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('user_languages', function (Blueprint $table) {
            // Add new foreign key columns
            $table->unsignedBigInteger('language_id')->nullable()->after('user_id');
            $table->unsignedBigInteger('language_test_id')->nullable()->after('test_taken');
            
            // Add foreign key constraints
            $table->foreign('language_id')->references('id')->on('languages')->onDelete('cascade');
            $table->foreign('language_test_id')->references('id')->on('language_tests')->onDelete('set null');
            
            // Add indexes for performance
            $table->index('language_id');
            $table->index('language_test_id');
        });
        
        // Migrate existing data: Convert string values to IDs
        DB::statement("
            UPDATE user_languages ul
            LEFT JOIN languages l ON LOWER(ul.language) = LOWER(l.name)
            SET ul.language_id = l.id
            WHERE l.id IS NOT NULL
        ");
        
        // Migrate test_taken enum to language_test_id
        DB::statement("
            UPDATE user_languages ul
            LEFT JOIN language_tests lt ON LOWER(ul.test_taken) = LOWER(lt.name)
            SET ul.language_test_id = lt.id
            WHERE lt.id IS NOT NULL AND ul.test_taken != 'none'
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('user_languages', function (Blueprint $table) {
            // Drop foreign keys first
            $table->dropForeign(['language_id']);
            $table->dropForeign(['language_test_id']);
            
            // Drop columns
            $table->dropColumn(['language_id', 'language_test_id']);
        });
    }
};
