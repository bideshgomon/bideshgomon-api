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
        Schema::table('skills', function (Blueprint $table) {
            // Drop index first, then drop column
            $table->dropIndex('skills_category_index');
            $table->dropColumn('category');

            // Add new columns
            $table->foreignId('skill_category_id')->nullable()->after('id')->constrained('skill_categories')->onDelete('set null');
            $table->string('name_bn', 100)->nullable()->after('name');
            $table->boolean('is_active')->default(true)->after('description');

            // Add index
            $table->index('is_active');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('skills', function (Blueprint $table) {
            $table->dropForeign(['skill_category_id']);
            $table->dropColumn(['skill_category_id', 'name_bn', 'is_active']);
            $table->string('category')->nullable();
            $table->index('category');
        });
    }
};
