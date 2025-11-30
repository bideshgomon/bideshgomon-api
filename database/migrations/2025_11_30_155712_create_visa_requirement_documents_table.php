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
        Schema::create('visa_requirement_documents', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('visa_requirement_id');
            $table->unsignedBigInteger('document_type_id')->nullable();
            $table->string('document_name');
            $table->text('description')->nullable();
            $table->boolean('is_mandatory')->default(true);
            $table->boolean('is_profession_specific')->default(false);
            $table->string('applicable_professions')->nullable();
            $table->text('specific_instructions')->nullable();
            $table->integer('sort_order')->default(0);
            $table->timestamps();
            
            // Indexes
            $table->index('visa_requirement_id');
            $table->index('document_type_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('visa_requirement_documents');
    }
};
