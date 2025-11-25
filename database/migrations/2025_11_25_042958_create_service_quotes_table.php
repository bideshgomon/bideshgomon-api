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
        Schema::create('service_quotes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('service_application_id')->constrained('service_applications')->onDelete('cascade');
            $table->unsignedBigInteger('agency_id'); // No FK - agencies in different database
            $table->decimal('quoted_amount', 10, 2);
            $table->decimal('service_fee', 10, 2);
            $table->decimal('platform_commission', 10, 2)->nullable();
            $table->decimal('agency_earnings', 10, 2)->nullable();
            $table->integer('processing_time_days');
            $table->text('quote_details')->nullable();
            $table->text('special_notes')->nullable();
            $table->json('breakdown')->nullable()->comment('Cost breakdown');
            $table->enum('status', ['pending', 'accepted', 'rejected', 'expired'])->default('pending');
            $table->timestamp('valid_until');
            $table->timestamp('accepted_at')->nullable();
            $table->timestamp('rejected_at')->nullable();
            $table->timestamps();
            
            $table->index(['service_application_id', 'agency_id']);
            $table->index(['agency_id', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('service_quotes');
    }
};
