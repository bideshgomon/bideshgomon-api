<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::table('users', function (Blueprint $table) {
            $table->unsignedBigInteger('role_id')->after('id');
            // REMOVED phone column
            // REMOVED avatar column
            $table->boolean('is_active')->default(true)->after('password'); // Positioned after password
            $table->softDeletes();
            $table->foreign('role_id')->references('id')->on('roles');
        });
    }
    public function down(): void {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['role_id']);
            // ADDED phone and avatar to dropColumn in case of rollback
            $table->dropColumn(['role_id', 'is_active', 'deleted_at', 'phone', 'avatar']);
        });
    }
};