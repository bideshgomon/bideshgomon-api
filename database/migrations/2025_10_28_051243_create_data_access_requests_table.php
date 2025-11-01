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
        Schema::create('data_access_requests', function (Blueprint $table) {
            $table->id();
            // Assuming 'consultant' role users make requests
            $table->foreignId('consultant_id')->constrained('users')->onDelete('cascade');
            // The user whose data is being requested
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            // Status: pending, approved, denied
            $table->string('status')->default('pending');
            $table->timestamp('requested_at')->useCurrent();
            $table->timestamp('responded_at')->nullable();
            $table->timestamps(); // Adds created_at and updated_at

            // Add unique constraint to prevent duplicate pending requests
            $table->unique(['consultant_id', 'user_id', 'status']);
            // Add indexes for faster lookups
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('data_access_requests');
    }
};
