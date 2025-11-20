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
        Schema::create('visa_appointments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('visa_application_id')->constrained()->onDelete('cascade');
            $table->string('appointment_reference')->unique();
            
            // Appointment Details
            $table->enum('appointment_type', ['biometrics', 'interview', 'document_submission', 'medical_exam', 'other'])->default('interview');
            $table->dateTime('appointment_datetime');
            $table->integer('duration_minutes')->default(30);
            $table->string('location_name');
            $table->text('location_address');
            $table->string('location_city');
            $table->string('location_country');
            
            // Contact & Instructions
            $table->string('contact_person')->nullable();
            $table->string('contact_phone')->nullable();
            $table->string('contact_email')->nullable();
            $table->text('instructions')->nullable();
            $table->json('required_items')->nullable(); // things to bring
            
            // Status & Tracking
            $table->enum('status', ['scheduled', 'confirmed', 'rescheduled', 'completed', 'missed', 'cancelled'])->default('scheduled');
            $table->timestamp('confirmed_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamp('cancelled_at')->nullable();
            $table->text('cancellation_reason')->nullable();
            
            // Rescheduling
            $table->foreignId('rescheduled_from')->nullable()->constrained('visa_appointments')->nullOnDelete();
            $table->dateTime('original_datetime')->nullable();
            $table->text('reschedule_reason')->nullable();
            
            // Reminders
            $table->boolean('reminder_sent')->default(false);
            $table->timestamp('reminder_sent_at')->nullable();
            $table->boolean('sms_reminder')->default(false);
            $table->boolean('email_reminder')->default(true);
            
            // Additional Information
            $table->text('notes')->nullable();
            $table->text('outcome')->nullable(); // result of appointment
            $table->json('metadata')->nullable();
            
            $table->timestamps();
            $table->softDeletes();
            
            // Indexes
            $table->index('visa_application_id');
            $table->index('appointment_datetime');
            $table->index('status');
            $table->index('location_city');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('visa_appointments');
    }
};
