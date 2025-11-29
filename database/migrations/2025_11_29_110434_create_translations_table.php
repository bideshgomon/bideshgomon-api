<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('translations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('application_reference')->unique();
            $table->string('document_type');
            $table->string('source_language');
            $table->string('target_language');
            $table->integer('page_count')->default(1);
            $table->string('certification_type')->default('Standard Translation');
            $table->boolean('is_urgent')->default(false);
            $table->date('required_by_date')->nullable();
            $table->enum('status', ['pending', 'submitted', 'processing', 'completed', 'cancelled'])->default('pending');
            $table->text('user_notes')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('translations');
    }
};
