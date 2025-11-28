<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DocumentScan extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'document_type',
        'original_image',
        'processed_image',
        'extracted_data',
        'metadata',
        'confidence_score',
        'processing_time',
        'status',
        'processing_method',
        'error_message',
        'processed_at',
    ];

    protected $casts = [
        'extracted_data' => 'array',
        'metadata' => 'array',
        'confidence_score' => 'decimal:2',
        'processing_time' => 'decimal:2',
        'processed_at' => 'datetime',
    ];

    /**
     * Get the user that owns the document scan.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope for successful scans.
     */
    public function scopeSuccessful($query)
    {
        return $query->where('status', 'completed');
    }

    /**
     * Scope for failed scans.
     */
    public function scopeFailed($query)
    {
        return $query->where('status', 'failed');
    }

    /**
     * Scope for pending scans.
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }
}
