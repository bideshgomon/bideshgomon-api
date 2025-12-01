<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ServiceApplication extends Model
{
    protected $fillable = [
        'user_id',
        'service_module_id',
        'agency_id',
        'tourist_visa_id',
        'application_number',
        'status',
        'application_data',
        'notes',
        'quoted_amount',
        'service_fee',
        'platform_commission',
        'agency_earnings',
        'processing_time_days',
        'special_notes',
        'rejection_reason',
        'submitted_at',
        'assigned_at',
        'quoted_at',
        'accepted_at',
        'completed_at',
        'timeline',
        'reviewed_at',
        'form_data',
        'profile_snapshot',
    ];

    protected $casts = [
        'application_data' => 'array',
        'form_data' => 'array',
        'profile_snapshot' => 'array',
        'timeline' => 'array',
        'quoted_amount' => 'decimal:2',
        'service_fee' => 'decimal:2',
        'platform_commission' => 'decimal:2',
        'agency_earnings' => 'decimal:2',
        'submitted_at' => 'datetime',
        'assigned_at' => 'datetime',
        'quoted_at' => 'datetime',
        'accepted_at' => 'datetime',
        'completed_at' => 'datetime',
        'reviewed_at' => 'datetime',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($application) {
            if (empty($application->application_number)) {
                $application->application_number = static::generateApplicationNumber();
            }
        });
    }

    /**
     * Generate unique application number
     */
    protected static function generateApplicationNumber(): string
    {
        do {
            $number = 'APP-' . date('Ymd') . '-' . strtoupper(substr(uniqid(), -6));
        } while (static::where('application_number', $number)->exists());

        return $number;
    }

    /**
     * Get the user who submitted the application
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the service module
     */
    public function serviceModule(): BelongsTo
    {
        return $this->belongsTo(ServiceModule::class);
    }

    /**
     * Get all quotes for this application
     */
    public function quotes()
    {
        return $this->hasMany(ServiceQuote::class);
    }

    /**
     * Get the assigned agency
     */
    public function assignedAgency(): BelongsTo
    {
        return $this->belongsTo(User::class, 'agency_id');
    }

    /**
     * Get the linked tourist visa record (if applicable)
     */
    public function touristVisa(): BelongsTo
    {
        return $this->belongsTo(TouristVisa::class);
    }

    public function documents(): HasMany
    {
        return $this->hasMany(ApplicationDocument::class);
    }

    /**
     * Get application status history
     */
    public function statusHistory(): HasMany
    {
        return $this->hasMany(ApplicationStatusHistory::class, 'application_id')->latest();
    }

    public function pendingDocuments(): HasMany
    {
        return $this->documents()->where('verification_status', 'pending');
    }

    public function verifiedDocuments(): HasMany
    {
        return $this->documents()->where('verification_status', 'verified');
    }

    /**
     * Update status with timeline tracking
     */
    public function updateStatus(string $newStatus, ?string $note = null): void
    {
        $oldStatus = $this->status;
        $this->status = $newStatus;

        // Add to timeline
        $timeline = $this->timeline ?? [];
        $timeline[] = [
            'status' => $newStatus,
            'from' => $oldStatus,
            'note' => $note,
            'changed_at' => now()->toISOString(),
            'changed_by' => auth()->id(), // @phpstan-ignore-line
        ];
        $this->timeline = $timeline;

        // Update special timestamps
        if ($newStatus === 'submitted' && !$this->submitted_at) {
            $this->submitted_at = now();
        }

        if (in_array($newStatus, ['under_review', 'processing']) && !$this->reviewed_at) {
            $this->reviewed_at = now();
        }

        if (in_array($newStatus, ['completed', 'approved']) && !$this->completed_at) {
            $this->completed_at = now();
            $this->serviceModule->incrementCompletions();
        }

        $this->save();
    }

    /**
     * Scope: Filter by status
     */
    public function scopeStatus($query, string $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope: Pending applications
     */
    public function scopePending($query)
    {
        return $query->whereIn('status', ['submitted', 'under_review']);
    }

    /**
     * Scope: Active applications
     */
    public function scopeActive($query)
    {
        return $query->whereNotIn('status', ['completed', 'cancelled', 'rejected']);
    }

    /**
     * Scope: By priority
     */
    public function scopePriority($query, string $priority)
    {
        return $query->where('priority', $priority);
    }

    /**
     * Check if application is editable
     */
    public function isEditable(): bool
    {
        return $this->status === 'draft';
    }

    /**
     * Check if application can be cancelled
     */
    public function isCancellable(): bool
    {
        return !in_array($this->status, ['completed', 'cancelled', 'rejected']);
    }

    /**
     * Get status color for UI
     */
    public function getStatusColorAttribute(): string
    {
        return match($this->status) {
            'draft' => 'gray',
            'submitted' => 'blue',
            'under_review' => 'yellow',
            'documents_required' => 'orange',
            'processing' => 'purple',
            'approved' => 'green',
            'rejected' => 'red',
            'completed' => 'green',
            'cancelled' => 'gray',
            default => 'gray',
        };
    }

    /**
     * Get status label
     */
    public function getStatusLabelAttribute(): string
    {
        return str_replace('_', ' ', ucfirst($this->status));
    }
}
