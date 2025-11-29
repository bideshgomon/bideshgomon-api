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
        Schema::table('agency_team_members', function (Blueprint $table) {
            // Link to User account (NULL if just display profile, e.g. founder without separate login)
            $table->foreignId('user_id')->nullable()->after('agency_id')->constrained()->onDelete('cascade');
            
            // Role within agency: manager (full access), consultant (limited), support (read-only)
            $table->enum('role', ['manager', 'consultant', 'support'])->default('consultant')->after('user_id');
            
            // Permissions (JSON for flexibility)
            $table->json('permissions')->nullable()->after('role')->comment('can_view_applications, can_submit_quotes, can_view_financials, can_manage_team, etc.');
            
            // Status
            $table->enum('status', ['active', 'inactive', 'suspended'])->default('active')->after('permissions');
            
            // Invited vs Direct Add
            $table->string('invitation_token')->nullable()->after('status')->comment('Token for email invitation acceptance');
            $table->timestamp('invited_at')->nullable()->after('invitation_token');
            $table->timestamp('joined_at')->nullable()->after('invited_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('agency_team_members', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropColumn([
                'user_id',
                'role',
                'permissions',
                'status',
                'invitation_token',
                'invited_at',
                'joined_at',
            ]);
        });
    }
};
