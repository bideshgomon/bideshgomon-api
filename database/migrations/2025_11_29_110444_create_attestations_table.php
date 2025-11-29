<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('attestations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('target_country_id')->constrained('countries')->onDelete('cascade');
            $table->string('application_reference')->unique();
            $table->string('document_type');
            $table->string('attestation_type');
            $table->string('purpose');
            $table->integer('document_count')->default(1);
            $table->boolean('is_urgent')->default(false);
            $table->date('required_by_date')->nullable();
            $table->enum('status', ['pending', 'submitted', 'processing', 'completed', 'cancelled'])->default('pending');
            $table->text('user_notes')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'status']);
            $table->index('target_country_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('attestations');
    }
};
