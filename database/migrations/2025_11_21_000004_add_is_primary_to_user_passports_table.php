<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('user_passports', 'is_primary')) {
            Schema::table('user_passports', function (Blueprint $table) {
                $table->boolean('is_primary')->default(false)->after('notes');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('user_passports', 'is_primary')) {
            Schema::table('user_passports', function (Blueprint $table) {
                $table->dropColumn('is_primary');
            });
        }
    }
};
