<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('admin_impersonation_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('impersonator_id');
            $table->unsignedBigInteger('target_user_id');
            $table->timestamp('started_at')->useCurrent();
            $table->timestamp('ended_at')->nullable();
            $table->string('purpose')->nullable();
            $table->timestamps();

            $table->foreign('impersonator_id')->references('id')->on('users')->cascadeOnDelete();
            $table->foreign('target_user_id')->references('id')->on('users')->cascadeOnDelete();
            $table->index(['impersonator_id']);
            $table->index(['target_user_id']);
            $table->index(['started_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('admin_impersonation_logs');
    }
};
