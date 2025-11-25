<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserDocument extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'title',
        'document_type',
        'file_name',
        'file_path',
        'file_type',
        'file_size',
        'description',
        'issue_date',
        'expiry_date',
        'document_number',
        'issuing_authority',
        'is_verified',
        'verified_at',
        'verified_by',
        'is_shared',
        'shared_with',
        'status',
        'original_filename',
        'storage_path',
        'mime_type',
        'size_bytes',
        'meta',
        'reviewed_by',
        'reviewed_at',
        'rejection_reason',
        'expires_at',
        'is_primary',
        'confidence_score',
    ];

    protected $casts = [
        'meta' => 'array',
        'shared_with' => 'array',
        'reviewed_at' => 'datetime',
        'verified_at' => 'datetime',
        'expires_at' => 'datetime',
        'expiry_date' => 'date',
        'issue_date' => 'date',
        'is_primary' => 'boolean',
        'is_verified' => 'boolean',
        'is_shared' => 'boolean',
    ];

    // Status constants
    public const STATUS_PENDING = 'pending';
    public const STATUS_APPROVED = 'approved';
    public const STATUS_REJECTED = 'rejected';

    // Document type suggestions (not enforced here; controllers/services validate)
    public static function supportedTypes(): array
    {
        return [
            'passport' => '🛂 Passport',
            'visa' => '✈️ Visa',
            'nid' => '🪪 National ID',
            'birth_certificate' => '📄 Birth Certificate',
            'driving_license' => '🚗 Driving License',
            'educational_certificate' => '🎓 Educational Certificate',
            'work_permit' => '💼 Work Permit',
            'bank_statement' => '🏦 Bank Statement',
            'police_clearance' => '👮 Police Clearance',
            'medical_certificate' => '🏥 Medical Certificate',
            'insurance' => '🛡️ Insurance Document',
            'reference_letter' => '📝 Reference Letter',
            'other' => '📎 Other Document',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    // Scope: active (non-expired)
    public function scopeActive($query)
    {
        return $query->where(function ($q) {
            $q->whereNull('expires_at')->orWhere('expires_at', '>', now());
        });
    }

    public function scopeType($query, string $type)
    {
        return $query->where('document_type', $type);
    }

    public function markApproved(?int $adminId = null): void
    {
        $this->status = self::STATUS_APPROVED;
        $this->reviewed_by = $adminId;
        $this->reviewed_at = now();
        $this->rejection_reason = null;
        $this->save();
    }

    public function markRejected(string $reason, ?int $adminId = null): void
    {
        $this->status = self::STATUS_REJECTED;
        $this->reviewed_by = $adminId;
        $this->reviewed_at = now();
        $this->rejection_reason = $reason;
        $this->save();
    }

    public function isApproved(): bool
    {
        return $this->status === self::STATUS_APPROVED;
    }

    public function isPending(): bool
    {
        return $this->status === self::STATUS_PENDING;
    }

    public function isRejected(): bool
    {
        return $this->status === self::STATUS_REJECTED;
    }

    public function isExpired(): bool
    {
        return $this->expires_at !== null && $this->expires_at->isPast();
    }

    public function statusColor(): string
    {
        return match ($this->status) {
            self::STATUS_APPROVED => 'green',
            self::STATUS_PENDING => 'yellow',
            self::STATUS_REJECTED => 'red',
            default => 'gray',
        };
    }
}
