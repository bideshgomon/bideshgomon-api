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
        Schema::table('visa_requirements', function (Blueprint $table) {
            $table->foreignId('country_id')->nullable()->after('service_module_id')->constrained('countries')->onDelete('cascade');
            $table->string('profession')->nullable()->after('visa_category');
            $table->json('required_documents')->nullable()->after('important_notes');
            $table->json('profession_specific_docs')->nullable()->after('required_documents');
            $table->string('processing_time')->nullable()->after('processing_days_urgent');
            $table->string('validity_period')->nullable()->after('processing_time');
            $table->boolean('is_template')->default(false)->after('is_active');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('visa_requirements', function (Blueprint $table) {
            $table->dropForeign(['country_id']);
            $table->dropColumn(['country_id', 'profession', 'required_documents', 'profession_specific_docs', 'processing_time', 'validity_period', 'is_template']);
        });
    }
};
