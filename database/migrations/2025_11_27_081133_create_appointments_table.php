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
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('appointment_type'); // office_visit, online_meeting
            $table->date('appointment_date');
            $table->time('appointment_time');
            $table->string('duration')->default('30'); // minutes
            $table->string('purpose'); // consultation, document_submission, general_inquiry
            $table->text('message')->nullable();
            $table->string('status')->default('pending'); // pending, confirmed, completed, cancelled, rescheduled
            $table->string('meeting_link')->nullable();
            $table->string('assigned_to')->nullable(); // staff member
            $table->text('admin_notes')->nullable();
            $table->timestamp('confirmed_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamp('cancelled_at')->nullable();
            $table->string('cancellation_reason')->nullable();
            $table->json('reminder_sent')->nullable(); // track email/SMS reminders
            $table->timestamps();
            $table->softDeletes();
            
            $table->index(['user_id', 'appointment_date']);
            $table->index('status');
            $table->index('appointment_type');
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
