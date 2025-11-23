<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class ServiceApplication extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'user_id',
        'service_module_id',
        'application_number',
        'status',
        'form_data',
        'documents',
        'timeline',
        'assigned_to',
        'assigned_role',
        'amount',
        'payment_status',
        'payment_method',
        'payment_date',
        'transaction_id',
        'admin_notes',
        'user_notes',
        'priority',
        'expected_completion_date',
        'completed_at',
        'submitted_at',
        'reviewed_at',
    ];

    protected $casts = [
        'form_data' => 'array',
        'documents' => 'array',
        'timeline' => 'array',
        'amount' => 'decimal:2',
        'payment_date' => 'datetime',
        'expected_completion_date' => 'date',
        'completed_at' => 'datetime',
        'submitted_at' => 'datetime',
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
     * Get the assigned user (agent/consultant)
     */
    public function assignedTo(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    /**
     * Update status with timeline tracking
     */
    public function updateStatus(string $newStatus, string $note = null): void
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
            'changed_by' => auth()->id(),
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
