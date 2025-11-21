<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Add columns only if they do not already exist (defensive for future schema changes)
        Schema::table('user_educations', function (Blueprint $table) {
            if (!Schema::hasColumn('user_educations', 'degree_level')) {
                $table->string('degree_level', 50)->nullable()->after('degree');
            }
            if (!Schema::hasColumn('user_educations', 'gpa')) {
                // Using string for flexibility (some grading systems use text like 'First Class')
                $table->string('gpa', 20)->nullable()->after('gpa_or_grade');
            }
            if (!Schema::hasColumn('user_educations', 'certificates_upload')) {
                $table->string('certificates_upload')->nullable()->after('degree_certificate_path');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('user_educations', function (Blueprint $table) {
            if (Schema::hasColumn('user_educations', 'degree_level')) {
                $table->dropColumn('degree_level');
            }
            if (Schema::hasColumn('user_educations', 'gpa')) {
                $table->dropColumn('gpa');
            }
            if (Schema::hasColumn('user_educations', 'certificates_upload')) {
                $table->dropColumn('certificates_upload');
            }
        });
    }
};
