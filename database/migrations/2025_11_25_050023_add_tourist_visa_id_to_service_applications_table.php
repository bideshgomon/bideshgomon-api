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
        Schema::table('service_applications', function (Blueprint $table) {
            $table->foreignId('tourist_visa_id')
                ->nullable()
                ->after('agency_id')
                ->constrained('tourist_visas')
                ->onDelete('cascade')
                ->comment('Link to legacy tourist visa record');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('service_applications', function (Blueprint $table) {
            $table->dropForeign(['tourist_visa_id']);
            $table->dropColumn('tourist_visa_id');
        });
    }
};
