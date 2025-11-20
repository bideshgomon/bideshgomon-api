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
        Schema::table('user_educations', function (Blueprint $table) {
            $table->string('field_of_study')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('user_educations', function (Blueprint $table) {
            $table->string('field_of_study')->nullable(false)->change();
        });
    }
};
