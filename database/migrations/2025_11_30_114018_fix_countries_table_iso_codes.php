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
        Schema::table('countries', function (Blueprint $table) {
            // Rename iso_code to iso_code_2
            $table->renameColumn('iso_code', 'iso_code_2');
            
            // Add iso_code_3 if it doesn't exist
            if (!Schema::hasColumn('countries', 'iso_code_3')) {
                $table->string('iso_code_3', 3)->nullable()->after('iso_code_2');
            }
        });
        
        // Populate iso_code_3 from code column if available
        DB::statement('UPDATE countries SET iso_code_3 = code WHERE iso_code_3 IS NULL AND code IS NOT NULL');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('countries', function (Blueprint $table) {
            // Rename back
            $table->renameColumn('iso_code_2', 'iso_code');
            
            // Drop iso_code_3
            if (Schema::hasColumn('countries', 'iso_code_3')) {
                $table->dropColumn('iso_code_3');
            }
        });
    }
};
