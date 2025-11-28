<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AgencyVerificationDocument extends Model
{
    use HasFactory;

    protected $fillable = [
        'agency_id',
        'document_type',
        'document_name',
        'file_path',
        'file_type',
        'file_size',
        'status',
        'rejection_reason',
        'reviewed_by',
        'reviewed_at',
        'notes',
    ];

    protected $casts = [
        'file_size' => 'integer',
        'reviewed_at' => 'datetime',
    ];

    /**
     * Get the agency that owns the document.
     */
    public function agency(): BelongsTo
    {
        return $this->belongsTo(Agency::class);
    }

    /**
     * Get the user who reviewed the document.
     */
    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    /**
     * Scope a query to only include pending documents.
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Scope a query to only include approved documents.
     */
    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    /**
     * Scope a query to only include rejected documents.
     */
    public function scopeRejected($query)
    {
        return $query->where('status', 'rejected');
    }

    /**
     * Get the document type label.
     */
    public function getDocumentTypeLabel(): string
    {
        $types = [
            'business_license' => 'Business License',
            'trade_license' => 'Trade License',
            'tax_certificate' => 'Tax Certificate',
            'registration_certificate' => 'Registration Certificate',
            'incorporation_certificate' => 'Incorporation Certificate',
            'professional_certificate' => 'Professional Certificate',
            'insurance_certificate' => 'Insurance Certificate',
            'accreditation' => 'Accreditation Document',
            'other' => 'Other Document',
        ];

        return $types[$this->document_type] ?? $this->document_type;
    }

    /**
     * Get the file size in human readable format.
     */
    public function getFileSizeFormatted(): string
    {
        if (!$this->file_size) {
            return 'Unknown';
        }

        $units = ['B', 'KB', 'MB', 'GB'];
        $size = $this->file_size;
        $unitIndex = 0;

        while ($size >= 1024 && $unitIndex < count($units) - 1) {
            $size /= 1024;
            $unitIndex++;
        }

        return round($size, 2) . ' ' . $units[$unitIndex];
    }
}
