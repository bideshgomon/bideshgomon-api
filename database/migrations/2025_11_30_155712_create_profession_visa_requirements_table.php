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
        Schema::create('profession_visa_requirements', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('visa_requirement_id');
            $table->string('profession');
            $table->json('additional_documents')->nullable();
            $table->text('specific_requirements')->nullable();
            $table->text('eligibility_notes')->nullable();
            $table->decimal('additional_fee', 10, 2)->nullable();
            $table->integer('additional_processing_days')->nullable();
            $table->timestamps();
            
            // Indexes
            $table->index('visa_requirement_id');
            $table->index('profession');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('profession_visa_requirements');
    }
};
