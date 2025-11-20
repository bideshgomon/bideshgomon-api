<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('user_languages', function (Blueprint $table) {
            // Add new columns for normalized schema
            $table->foreignId('language_id')->nullable()->after('user_id')->constrained('languages')->nullOnDelete();
            $table->foreignId('language_test_id')->nullable()->after('language_id')->constrained('language_tests')->nullOnDelete();
            
            // Add proficiency_level to replace enum proficiency
            $table->string('proficiency_level', 50)->nullable()->after('language');
            
            // Rename/restructure test score columns
            $table->decimal('overall_score', 5, 2)->nullable()->after('test_score');
            
            // Add expiry_date column
            $table->date('expiry_date')->nullable()->after('certificate_expiry_date');
            
            // Rename test_certificate_path to file_path
            $table->string('file_path')->nullable()->after('test_certificate_path');
        });

        // Migrate existing data: copy proficiency enum to proficiency_level string
        DB::statement("UPDATE user_languages SET proficiency_level = proficiency WHERE proficiency_level IS NULL");

        // Make old columns nullable for backward compatibility
        Schema::table('user_languages', function (Blueprint $table) {
            $table->string('language')->nullable()->change();
            $table->enum('proficiency', ['basic', 'intermediate', 'advanced', 'native'])->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('user_languages', function (Blueprint $table) {
            $table->dropForeign(['language_id']);
            $table->dropForeign(['language_test_id']);
            $table->dropColumn([
                'language_id',
                'language_test_id',
                'proficiency_level',
                'overall_score',
                'expiry_date',
                'file_path',
            ]);
            
            // Revert language and proficiency back to NOT NULL
            $table->string('language')->nullable(false)->change();
            $table->enum('proficiency', ['basic', 'intermediate', 'advanced', 'native'])->default('basic')->change();
        });
    }
};
