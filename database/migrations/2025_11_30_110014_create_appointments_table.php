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
        Schema::create('appointments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('appointment_type')->default('consultation'); // consultation, document_review, interview_prep
            $table->string('appointment_number')->unique();
            $table->date('preferred_date');
            $table->time('preferred_time');
            $table->string('duration')->default('30'); // minutes
            $table->string('status')->default('pending'); // pending, confirmed, completed, cancelled
            $table->text('purpose')->nullable();
            $table->text('notes')->nullable();
            $table->text('admin_notes')->nullable();
            $table->boolean('is_online')->default(true);
            $table->string('meeting_link')->nullable();
            $table->string('location')->nullable();
            $table->foreignId('assigned_to')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('confirmed_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('appointments');
    }
};
