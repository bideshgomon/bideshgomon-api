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
        Schema::create('flight_quotes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('flight_request_id')->constrained()->onDelete('cascade');
            $table->foreignId('quoted_by')->constrained('users')->onDelete('cascade')->comment('Admin or Agency');
            
            // Quote details
            $table->string('airline_name');
            $table->string('flight_number')->nullable();
            $table->decimal('quoted_price', 10, 2);
            $table->text('price_breakdown')->nullable();
            $table->text('flight_details')->nullable();
            
            // Validity
            $table->timestamp('valid_until');
            $table->boolean('is_valid')->default(true);
            
            // Status
            $table->enum('status', ['pending', 'accepted', 'rejected', 'expired'])->default('pending');
            $table->text('notes')->nullable();
            
            $table->timestamps();
            
            // Indexes
            $table->index(['flight_request_id', 'status']);
            $table->index(['quoted_by', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('flight_quotes');
    }
};
