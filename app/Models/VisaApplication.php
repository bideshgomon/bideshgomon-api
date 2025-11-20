<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class VisaApplication extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id', 'application_reference', 'visa_type', 'destination_country',
        'destination_country_code', 'visa_category', 'duration_days',
        'applicant_name', 'applicant_email', 'applicant_phone', 'applicant_dob',
        'passport_number', 'passport_issue_date', 'passport_expiry_date',
        'passport_issuing_country', 'nationality',
        'intended_travel_date', 'intended_return_date', 'travel_purpose',
        'occupation', 'previous_visa_history',
        'processing_type', 'processing_days', 'status', 'status_notes',
        'submitted_at', 'reviewed_at', 'approved_at', 'rejected_at', 'rejection_reason',
        'service_fee', 'government_fee', 'processing_fee', 'total_amount',
        'payment_status', 'payment_method', 'payment_reference', 'paid_at',
        'assigned_to', 'assigned_at', 'priority',
        'additional_info', 'required_documents', 'special_requests', 'internal_notes',
        'ip_address', 'user_agent'
    ];

    protected $casts = [
        'applicant_dob' => 'date',
        'passport_issue_date' => 'date',
        'passport_expiry_date' => 'date',
        'intended_travel_date' => 'date',
        'intended_return_date' => 'date',
        'submitted_at' => 'datetime',
        'reviewed_at' => 'datetime',
        'approved_at' => 'datetime',
        'rejected_at' => 'datetime',
        'paid_at' => 'datetime',
        'assigned_at' => 'datetime',
        'additional_info' => 'array',
        'required_documents' => 'array',
        'service_fee' => 'decimal:2',
        'government_fee' => 'decimal:2',
        'processing_fee' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'duration_days' => 'integer',
        'processing_days' => 'integer',
        'priority' => 'integer',
    ];

    // Boot method to generate application reference
    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($application) {
            if (empty($application->application_reference)) {
                $application->application_reference = 'VISA-' . strtoupper(uniqid());
            }
        });
    }

    // Relationships
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function assignedTo(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function documents(): HasMany
    {
        return $this->hasMany(VisaDocument::class);
    }

    public function appointments(): HasMany
    {
        return $this->hasMany(VisaAppointment::class);
    }

    // Scopes
    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function scopeByVisaType($query, $type)
    {
        return $query->where('visa_type', $type);
    }

    public function scopeByDestination($query, $country)
    {
        return $query->where('destination_country', $country);
    }

    public function scopePending($query)
    {
        return $query->where('status', 'submitted');
    }

    public function scopeUnderReview($query)
    {
        return $query->where('status', 'under_review');
    }

    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    public function scopeRejected($query)
    {
        return $query->where('status', 'rejected');
    }

    public function scopePaid($query)
    {
        return $query->where('payment_status', 'paid');
    }

    public function scopeUnpaid($query)
    {
        return $query->where('payment_status', 'pending');
    }

    public function scopeAssignedTo($query, $userId)
    {
        return $query->where('assigned_to', $userId);
    }

    public function scopeUnassigned($query)
    {
        return $query->whereNull('assigned_to');
    }

    public function scopeHighPriority($query)
    {
        return $query->where('priority', '>', 0)->orderBy('priority', 'desc');
    }

    public function scopeRecent($query, $days = 30)
    {
        return $query->where('created_at', '>=', now()->subDays($days));
    }

    // Accessors
    public function getStatusBadgeAttribute()
    {
        $badges = [
            'draft' => ['text' => 'Draft', 'color' => 'gray'],
            'submitted' => ['text' => 'Submitted', 'color' => 'blue'],
            'under_review' => ['text' => 'Under Review', 'color' => 'yellow'],
            'documents_requested' => ['text' => 'Documents Requested', 'color' => 'orange'],
            'interview_scheduled' => ['text' => 'Interview Scheduled', 'color' => 'purple'],
            'approved' => ['text' => 'Approved', 'color' => 'green'],
            'rejected' => ['text' => 'Rejected', 'color' => 'red'],
            'cancelled' => ['text' => 'Cancelled', 'color' => 'gray'],
        ];

        return $badges[$this->status] ?? ['text' => ucfirst($this->status), 'color' => 'gray'];
    }

    public function getPaymentBadgeAttribute()
    {
        $badges = [
            'pending' => ['text' => 'Payment Pending', 'color' => 'yellow'],
            'paid' => ['text' => 'Paid', 'color' => 'green'],
            'refunded' => ['text' => 'Refunded', 'color' => 'blue'],
            'failed' => ['text' => 'Failed', 'color' => 'red'],
        ];

        return $badges[$this->payment_status] ?? ['text' => ucfirst($this->payment_status), 'color' => 'gray'];
    }

    public function getVisaTypeDisplayAttribute()
    {
        $types = [
            'tourist' => 'Tourist Visa',
            'business' => 'Business Visa',
            'student' => 'Student Visa',
            'work' => 'Work Visa',
            'medical' => 'Medical Visa',
            'transit' => 'Transit Visa',
        ];

        return $types[$this->visa_type] ?? ucfirst(str_replace('_', ' ', $this->visa_type));
    }

    public function getProcessingTypeDisplayAttribute()
    {
        return ucfirst($this->processing_type) . ' Processing';
    }

    public function getFormattedTotalAttribute()
    {
        return '৳' . number_format((float) $this->total_amount, 2);
    }

    public function getIsPaidAttribute()
    {
        return $this->payment_status === 'paid';
    }

    public function getIsApprovedAttribute()
    {
        return $this->status === 'approved';
    }

    public function getIsRejectedAttribute()
    {
        return $this->status === 'rejected';
    }

    public function getIsPendingAttribute()
    {
        return in_array($this->status, ['submitted', 'under_review', 'documents_requested']);
    }

    public function getIsAssignedAttribute()
    {
        return !is_null($this->assigned_to);
    }

    public function getPassportExpiryStatusAttribute()
    {
        if (!$this->passport_expiry_date) {
            return null;
        }

        $daysUntilExpiry = now()->diffInDays($this->passport_expiry_date, false);

        if ($daysUntilExpiry < 0) {
            return 'expired';
        } elseif ($daysUntilExpiry < 180) { // 6 months
            return 'expiring_soon';
        }

        return 'valid';
    }

    public function getDaysUntilTravelAttribute()
    {
        if (!$this->intended_travel_date) {
            return null;
        }

        return now()->diffInDays($this->intended_travel_date, false);
    }

    // Helper Methods
    public function calculateTotal()
    {
        $total = ((float) $this->service_fee ?? 0) + 
                 ((float) $this->government_fee ?? 0) + 
                 ((float) $this->processing_fee ?? 0);
        $this->total_amount = $total;
        return $total;
    }

    public function markAsPaid($paymentMethod = null, $paymentReference = null)
    {
        $this->update([
            'payment_status' => 'paid',
            'payment_method' => $paymentMethod,
            'payment_reference' => $paymentReference,
            'paid_at' => now(),
        ]);
    }

    public function submit()
    {
        $this->update([
            'status' => 'submitted',
            'submitted_at' => now(),
        ]);
    }

    public function approve($notes = null)
    {
        $this->update([
            'status' => 'approved',
            'approved_at' => now(),
            'status_notes' => $notes,
        ]);
    }

    public function reject($reason)
    {
        $this->update([
            'status' => 'rejected',
            'rejected_at' => now(),
            'rejection_reason' => $reason,
        ]);
    }

    public function assignTo($userId)
    {
        $this->update([
            'assigned_to' => $userId,
            'assigned_at' => now(),
        ]);
    }

    public function requestDocuments($notes = null)
    {
        $this->update([
            'status' => 'documents_requested',
            'status_notes' => $notes,
        ]);
    }

    public function scheduleInterview()
    {
        $this->update([
            'status' => 'interview_scheduled',
        ]);
    }

    public function cancel($reason = null)
    {
        $this->update([
            'status' => 'cancelled',
            'status_notes' => $reason,
        ]);
    }
}
