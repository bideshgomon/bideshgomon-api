<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AgencyVerificationRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'agency_id',
        'status',
        'message',
        'reviewed_by',
        'admin_notes',
        'rejection_reason',
        'submitted_at',
        'reviewed_at',
        'approved_at',
        'required_documents',
        'submitted_documents',
    ];

    protected $casts = [
        'submitted_at' => 'datetime',
        'reviewed_at' => 'datetime',
        'approved_at' => 'datetime',
        'required_documents' => 'array',
        'submitted_documents' => 'array',
    ];

    /**
     * Get the agency that owns the verification request.
     */
    public function agency(): BelongsTo
    {
        return $this->belongsTo(Agency::class);
    }

    /**
     * Get the user who reviewed the request.
     */
    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    /**
     * Scope a query to only include pending requests.
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Scope a query to only include under review requests.
     */
    public function scopeUnderReview($query)
    {
        return $query->where('status', 'under_review');
    }

    /**
     * Scope a query to only include approved requests.
     */
    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    /**
     * Scope a query to only include rejected requests.
     */
    public function scopeRejected($query)
    {
        return $query->where('status', 'rejected');
    }

    /**
     * Scope a query to only include recent requests.
     */
    public function scopeRecent($query)
    {
        return $query->orderBy('submitted_at', 'desc');
    }

    /**
     * Get the status label.
     */
    public function getStatusLabel(): string
    {
        $statuses = [
            'pending' => 'Pending Review',
            'under_review' => 'Under Review',
            'approved' => 'Approved',
            'rejected' => 'Rejected',
            'requires_changes' => 'Requires Changes',
        ];

        return $statuses[$this->status] ?? $this->status;
    }

    /**
     * Get the status color for UI.
     */
    public function getStatusColor(): string
    {
        $colors = [
            'pending' => 'yellow',
            'under_review' => 'blue',
            'approved' => 'green',
            'rejected' => 'red',
            'requires_changes' => 'orange',
        ];

        return $colors[$this->status] ?? 'gray';
    }

    /**
     * Check if the request is pending.
     */
    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    /**
     * Check if the request is approved.
     */
    public function isApproved(): bool
    {
        return $this->status === 'approved';
    }

    /**
     * Check if the request is rejected.
     */
    public function isRejected(): bool
    {
        return $this->status === 'rejected';
    }
}
