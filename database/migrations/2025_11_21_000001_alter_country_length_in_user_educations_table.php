<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Increase country column length to store full country names instead of 2-letter codes.
     * Root cause fix for 500 error when saving education with full country name under strict mode.
     */
    public function up(): void
    {
        Schema::table('user_educations', function (Blueprint $table) {
            // Change column length from 2 to 100 (matches validation rule max:100)
            $table->string('country', 100)->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('user_educations', function (Blueprint $table) {
            // Revert to 2-character code length
            $table->string('country', 2)->nullable()->change();
        });
    }
};
