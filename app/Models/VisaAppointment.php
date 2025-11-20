<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class VisaAppointment extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'visa_application_id', 'appointment_reference', 'appointment_type',
        'appointment_datetime', 'duration_minutes', 'location_name',
        'location_address', 'location_city', 'location_country',
        'contact_person', 'contact_phone', 'contact_email', 'instructions',
        'required_items', 'status', 'confirmed_at', 'completed_at',
        'cancelled_at', 'cancellation_reason', 'rescheduled_from',
        'original_datetime', 'reschedule_reason', 'reminder_sent',
        'reminder_sent_at', 'sms_reminder', 'email_reminder', 'notes',
        'outcome', 'metadata'
    ];

    protected $casts = [
        'appointment_datetime' => 'datetime',
        'original_datetime' => 'datetime',
        'confirmed_at' => 'datetime',
        'completed_at' => 'datetime',
        'cancelled_at' => 'datetime',
        'reminder_sent_at' => 'datetime',
        'duration_minutes' => 'integer',
        'reminder_sent' => 'boolean',
        'sms_reminder' => 'boolean',
        'email_reminder' => 'boolean',
        'required_items' => 'array',
        'metadata' => 'array',
    ];

    // Boot method
    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($appointment) {
            if (empty($appointment->appointment_reference)) {
                $appointment->appointment_reference = 'APT-' . strtoupper(uniqid());
            }
        });
    }

    // Relationships
    public function application(): BelongsTo
    {
        return $this->belongsTo(VisaApplication::class, 'visa_application_id');
    }

    public function originalAppointment(): BelongsTo
    {
        return $this->belongsTo(VisaAppointment::class, 'rescheduled_from');
    }

    // Scopes
    public function scopeForApplication($query, $applicationId)
    {
        return $query->where('visa_application_id', $applicationId);
    }

    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function scopeByType($query, $type)
    {
        return $query->where('appointment_type', $type);
    }

    public function scopeScheduled($query)
    {
        return $query->where('status', 'scheduled');
    }

    public function scopeConfirmed($query)
    {
        return $query->where('status', 'confirmed');
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    public function scopeUpcoming($query)
    {
        return $query->whereIn('status', ['scheduled', 'confirmed'])
                    ->where('appointment_datetime', '>', now());
    }

    public function scopePast($query)
    {
        return $query->where('appointment_datetime', '<', now());
    }

    public function scopeToday($query)
    {
        return $query->whereDate('appointment_datetime', today());
    }

    public function scopeThisWeek($query)
    {
        return $query->whereBetween('appointment_datetime', [
            now()->startOfWeek(),
            now()->endOfWeek()
        ]);
    }

    public function scopeNeedingReminder($query)
    {
        return $query->whereIn('status', ['scheduled', 'confirmed'])
                    ->where('reminder_sent', false)
                    ->where('appointment_datetime', '<=', now()->addDay())
                    ->where('appointment_datetime', '>', now());
    }

    // Accessors
    public function getStatusBadgeAttribute()
    {
        $badges = [
            'scheduled' => ['text' => 'Scheduled', 'color' => 'blue'],
            'confirmed' => ['text' => 'Confirmed', 'color' => 'green'],
            'rescheduled' => ['text' => 'Rescheduled', 'color' => 'yellow'],
            'completed' => ['text' => 'Completed', 'color' => 'gray'],
            'missed' => ['text' => 'Missed', 'color' => 'red'],
            'cancelled' => ['text' => 'Cancelled', 'color' => 'gray'],
        ];

        return $badges[$this->status] ?? ['text' => ucfirst($this->status), 'color' => 'gray'];
    }

    public function getAppointmentTypeDisplayAttribute()
    {
        $types = [
            'biometrics' => 'Biometrics',
            'interview' => 'Visa Interview',
            'document_submission' => 'Document Submission',
            'medical_exam' => 'Medical Examination',
            'other' => 'Other',
        ];

        return $types[$this->appointment_type] ?? ucfirst(str_replace('_', ' ', $this->appointment_type));
    }

    public function getIsUpcomingAttribute()
    {
        return in_array($this->status, ['scheduled', 'confirmed']) && 
               $this->appointment_datetime > now();
    }

    public function getIsPastAttribute()
    {
        return $this->appointment_datetime < now();
    }

    public function getIsTodayAttribute()
    {
        return $this->appointment_datetime->isToday();
    }

    public function getIsCompletedAttribute()
    {
        return $this->status === 'completed';
    }

    public function getIsCancelledAttribute()
    {
        return $this->status === 'cancelled';
    }

    public function getTimeUntilAppointmentAttribute()
    {
        if ($this->appointment_datetime < now()) {
            return null;
        }

        $diff = now()->diff($this->appointment_datetime);
        
        if ($diff->days > 0) {
            return $diff->days . ' day' . ($diff->days > 1 ? 's' : '');
        } elseif ($diff->h > 0) {
            return $diff->h . ' hour' . ($diff->h > 1 ? 's' : '');
        }
        
        return $diff->i . ' minute' . ($diff->i > 1 ? 's' : '');
    }

    public function getFormattedDatetimeAttribute()
    {
        return $this->appointment_datetime->format('F j, Y \a\t g:i A');
    }

    // Helper Methods
    public function confirm()
    {
        $this->update([
            'status' => 'confirmed',
            'confirmed_at' => now(),
        ]);
    }

    public function complete($outcome = null)
    {
        $this->update([
            'status' => 'completed',
            'completed_at' => now(),
            'outcome' => $outcome,
        ]);
    }

    public function cancel($reason = null)
    {
        $this->update([
            'status' => 'cancelled',
            'cancelled_at' => now(),
            'cancellation_reason' => $reason,
        ]);
    }

    public function reschedule($newDatetime, $reason = null)
    {
        // Create new appointment
        $newAppointment = $this->replicate();
        $newAppointment->appointment_datetime = $newDatetime;
        $newAppointment->rescheduled_from = $this->id;
        $newAppointment->original_datetime = $this->appointment_datetime;
        $newAppointment->reschedule_reason = $reason;
        $newAppointment->status = 'scheduled';
        $newAppointment->confirmed_at = null;
        $newAppointment->reminder_sent = false;
        $newAppointment->reminder_sent_at = null;
        $newAppointment->save();

        // Mark current as rescheduled
        $this->update([
            'status' => 'rescheduled',
        ]);

        return $newAppointment;
    }

    public function markReminderSent()
    {
        $this->update([
            'reminder_sent' => true,
            'reminder_sent_at' => now(),
        ]);
    }
}
