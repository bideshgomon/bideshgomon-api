<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TranslationRequest extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'request_reference', 'user_id', 'source_language', 'target_language',
        'document_type', 'certification_type', 'page_count', 'word_count',
        'urgency', 'delivery_days', 'required_by', 'special_instructions',
        'price_per_page', 'certification_fee', 'urgency_fee', 'total_amount',
        'status', 'assigned_translator_id', 'priority',
        'payment_status', 'payment_method', 'payment_transaction_id', 'paid_at',
        'submitted_at', 'started_at', 'completed_at', 'delivered_at', 'progress_percentage',
        'admin_notes', 'translator_notes', 'rejection_reason',
        'ip_address', 'user_agent',
    ];

    protected $casts = [
        'required_by' => 'date',
        'paid_at' => 'datetime',
        'submitted_at' => 'datetime',
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
        'delivered_at' => 'datetime',
        'price_per_page' => 'decimal:2',
        'certification_fee' => 'decimal:2',
        'urgency_fee' => 'decimal:2',
        'total_amount' => 'decimal:2',
    ];

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            if (empty($model->request_reference)) {
                $model->request_reference = 'TRANS-' . strtoupper(uniqid());
            }
        });
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function translator()
    {
        return $this->belongsTo(User::class, 'assigned_translator_id');
    }

    public function documents()
    {
        return $this->hasMany(TranslationDocument::class);
    }

    public function quotes()
    {
        return $this->hasMany(TranslationQuote::class);
    }

    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function scopePending($query)
    {
        return $query->whereIn('status', ['draft', 'submitted', 'quote_pending']);
    }

    public function scopeInProgress($query)
    {
        return $query->where('status', 'in_progress');
    }

    public function scopeCompleted($query)
    {
        return $query->whereIn('status', ['completed', 'delivered']);
    }

    public function assignTo($translatorId)
    {
        $this->update(['assigned_translator_id' => $translatorId]);
    }

    public function submit()
    {
        $this->update(['status' => 'submitted', 'submitted_at' => now()]);
    }

    public function complete()
    {
        $this->update(['status' => 'completed', 'completed_at' => now(), 'progress_percentage' => 100]);
    }
}
