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
        Schema::table('user_languages', function (Blueprint $table) {
            // Add proficiency level (e.g., 'Beginner', 'Conversational', 'Fluent', 'Native')
            $table->string('proficiency')->nullable()->after('language_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('user_languages', function (Blueprint $table) {
            $table->dropColumn('proficiency');
        });
    }
};
