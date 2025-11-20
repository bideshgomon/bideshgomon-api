<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;

class VisaDocument extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'visa_application_id', 'uploaded_by', 'document_type', 'document_name',
        'original_filename', 'file_path', 'file_extension', 'file_size', 'mime_type',
        'status', 'verification_notes', 'verified_by', 'verified_at',
        'is_required', 'is_translated', 'is_notarized', 'document_expiry_date',
        'description', 'version', 'metadata'
    ];

    protected $casts = [
        'verified_at' => 'datetime',
        'document_expiry_date' => 'date',
        'is_required' => 'boolean',
        'is_translated' => 'boolean',
        'is_notarized' => 'boolean',
        'version' => 'integer',
        'file_size' => 'integer',
        'metadata' => 'array',
    ];

    // Relationships
    public function application(): BelongsTo
    {
        return $this->belongsTo(VisaApplication::class, 'visa_application_id');
    }

    public function uploader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    public function verifier(): BelongsTo
    {
        return $this->belongsTo(User::class, 'verified_by');
    }

    // Scopes
    public function scopeForApplication($query, $applicationId)
    {
        return $query->where('visa_application_id', $applicationId);
    }

    public function scopeByType($query, $type)
    {
        return $query->where('document_type', $type);
    }

    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeVerified($query)
    {
        return $query->where('status', 'verified');
    }

    public function scopeRejected($query)
    {
        return $query->where('status', 'rejected');
    }

    public function scopeRequired($query)
    {
        return $query->where('is_required', true);
    }

    public function scopeExpiringSoon($query, $days = 30)
    {
        return $query->where('document_expiry_date', '<=', now()->addDays($days))
                    ->where('document_expiry_date', '>', now());
    }

    // Accessors
    public function getStatusBadgeAttribute()
    {
        $badges = [
            'pending' => ['text' => 'Pending Review', 'color' => 'yellow'],
            'verified' => ['text' => 'Verified', 'color' => 'green'],
            'rejected' => ['text' => 'Rejected', 'color' => 'red'],
            'reupload_required' => ['text' => 'Reupload Required', 'color' => 'orange'],
        ];

        return $badges[$this->status] ?? ['text' => ucfirst($this->status), 'color' => 'gray'];
    }

    public function getFileSizeFormattedAttribute()
    {
        $bytes = $this->file_size;
        if ($bytes >= 1073741824) {
            return number_format($bytes / 1073741824, 2) . ' GB';
        } elseif ($bytes >= 1048576) {
            return number_format($bytes / 1048576, 2) . ' MB';
        } elseif ($bytes >= 1024) {
            return number_format($bytes / 1024, 2) . ' KB';
        }
        return $bytes . ' bytes';
    }

    public function getFileUrlAttribute()
    {
        return Storage::url($this->file_path);
    }

    public function getIsVerifiedAttribute()
    {
        return $this->status === 'verified';
    }

    public function getIsPendingAttribute()
    {
        return $this->status === 'pending';
    }

    public function getIsExpiredAttribute()
    {
        return $this->document_expiry_date && $this->document_expiry_date < now();
    }

    public function getDocumentTypeDisplayAttribute()
    {
        $types = [
            'passport' => 'Passport Copy',
            'photo' => 'Passport Photo',
            'bank_statement' => 'Bank Statement',
            'invitation_letter' => 'Invitation Letter',
            'employment_letter' => 'Employment Letter',
            'hotel_booking' => 'Hotel Booking Confirmation',
            'flight_ticket' => 'Flight Ticket',
            'insurance' => 'Travel Insurance',
            'birth_certificate' => 'Birth Certificate',
            'marriage_certificate' => 'Marriage Certificate',
            'educational_certificate' => 'Educational Certificate',
            'other' => 'Other Document',
        ];

        return $types[$this->document_type] ?? ucfirst(str_replace('_', ' ', $this->document_type));
    }

    // Helper Methods
    public function verify($verifiedBy, $notes = null)
    {
        $this->update([
            'status' => 'verified',
            'verified_by' => $verifiedBy,
            'verified_at' => now(),
            'verification_notes' => $notes,
        ]);
    }

    public function reject($notes)
    {
        $this->update([
            'status' => 'rejected',
            'verification_notes' => $notes,
        ]);
    }

    public function requestReupload($notes)
    {
        $this->update([
            'status' => 'reupload_required',
            'verification_notes' => $notes,
        ]);
    }

    public function deleteFile()
    {
        if (Storage::exists($this->file_path)) {
            Storage::delete($this->file_path);
        }
    }

    protected static function boot()
    {
        parent::boot();

        static::deleting(function ($document) {
            $document->deleteFile();
        });
    }
}
