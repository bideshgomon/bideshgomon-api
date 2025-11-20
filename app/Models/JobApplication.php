<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class JobApplication extends Model
{
    use HasFactory;

    protected $fillable = [
        'job_posting_id',
        'user_id',
        'user_cv_id',
        'cover_letter',
        'cv_file',
        'status',
        'application_fee_paid',
        'payment_status',
        'payment_reference',
        'payment_date',
        'admin_notes',
        'rejection_reason',
        'reviewed_by',
        'reviewed_at',
        'interview_date',
        'interview_location',
        'interview_notes',
        'submitted_at',
    ];

    protected $casts = [
        'application_fee_paid' => 'decimal:2',
        'payment_date' => 'datetime',
        'submitted_at' => 'datetime',
        'reviewed_at' => 'datetime',
        'interview_date' => 'datetime',
    ];

    // Relationships
    public function jobPosting()
    {
        return $this->belongsTo(JobPosting::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function userCv()
    {
        return $this->belongsTo(UserCv::class);
    }

    public function reviewedBy()
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    // Scopes
    public function scopePending(Builder $query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeUnderReview(Builder $query)
    {
        return $query->where('status', 'under_review');
    }

    public function scopeReviewed(Builder $query)
    {
        return $query->whereIn('status', ['under_review', 'shortlisted', 'interviewed']);
    }

    public function scopeShortlisted(Builder $query)
    {
        return $query->where('status', 'shortlisted');
    }

    public function scopeInterviewed(Builder $query)
    {
        return $query->where('status', 'interviewed');
    }

    public function scopeOffered(Builder $query)
    {
        return $query->where('status', 'offered');
    }

    public function scopeAccepted(Builder $query)
    {
        return $query->where('status', 'accepted');
    }

    public function scopeRejected(Builder $query)
    {
        return $query->where('status', 'rejected');
    }

    public function scopeWithdrawn(Builder $query)
    {
        return $query->where('status', 'withdrawn');
    }

    public function scopeHired(Builder $query)
    {
        return $query->where('status', 'accepted');
    }

    public function scopePaid(Builder $query)
    {
        return $query->where('payment_status', 'paid');
    }

    public function scopeForUser(Builder $query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    // Helper Methods
    public function isPending()
    {
        return $this->status === 'pending';
    }

    public function isReviewed()
    {
        return in_array($this->status, ['under_review', 'shortlisted', 'interviewed', 'offered', 'accepted', 'rejected']);
    }

    public function isHired()
    {
        return $this->status === 'accepted';
    }

    public function isPaid()
    {
        return $this->payment_status === 'paid';
    }

    public function getStatusBadgeColor()
    {
        return match($this->status) {
            'pending' => 'bg-yellow-100 text-yellow-800',
            'under_review' => 'bg-blue-100 text-blue-800',
            'shortlisted' => 'bg-indigo-100 text-indigo-800',
            'interviewed' => 'bg-purple-100 text-purple-800',
            'offered' => 'bg-cyan-100 text-cyan-800',
            'accepted' => 'bg-green-100 text-green-800',
            'rejected' => 'bg-red-100 text-red-800',
            'withdrawn' => 'bg-gray-100 text-gray-800',
            default => 'bg-gray-100 text-gray-800',
        };
    }

    public function getStatusLabel()
    {
        return match($this->status) {
            'pending' => 'Pending',
            'under_review' => 'Under Review',
            'shortlisted' => 'Shortlisted',
            'interviewed' => 'Interviewed',
            'offered' => 'Offered',
            'accepted' => 'Accepted',
            'rejected' => 'Rejected',
            'withdrawn' => 'Withdrawn',
            default => 'Unknown',
        };
    }
}