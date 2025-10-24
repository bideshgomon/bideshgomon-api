<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::table('appointments', function (Blueprint $table) {
            // Check if column exists before dropping
            if (Schema::hasColumn('appointments', 'stripe_checkout_session_id')) {
                $table->dropColumn('stripe_checkout_session_id');
            }
        });
    }
    public function down(): void {
        Schema::table('appointments', function (Blueprint $table) {
            // Add it back if rolling back
            $table->string('stripe_checkout_session_id')->nullable()->after('price');
        });
    }
};