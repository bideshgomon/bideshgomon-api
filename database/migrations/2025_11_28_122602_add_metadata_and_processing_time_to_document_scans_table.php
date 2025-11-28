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
        Schema::table('document_scans', function (Blueprint $table) {
            $table->json('metadata')->nullable()->after('extracted_data'); // Document metadata for fraud detection
            $table->decimal('processing_time', 8, 2)->nullable()->after('confidence_score'); // Processing time in seconds
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('document_scans', function (Blueprint $table) {
            $table->dropColumn(['metadata', 'processing_time']);
        });
    }
};
