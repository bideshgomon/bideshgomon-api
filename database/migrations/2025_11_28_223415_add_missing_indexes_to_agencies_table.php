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
        Schema::table('agencies', function (Blueprint $table) {
            // Add index for user_id foreign key (improves query performance)
            $table->index('user_id');
            
            // Add indexes for common query patterns
            $table->index('verification_status');
            $table->index(['is_active', 'verification_status']);
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('agencies', function (Blueprint $table) {
            $table->dropIndex(['user_id']);
            $table->dropIndex(['verification_status']);
            $table->dropIndex(['is_active', 'verification_status']);
            $table->dropIndex(['created_at']);
        });
    }
};
