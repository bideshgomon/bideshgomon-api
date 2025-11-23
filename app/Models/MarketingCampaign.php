<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class MarketingCampaign extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'slug',
        'type',
        'status',
        'email_template_id',
        'subject',
        'message',
        'audience_type',
        'audience_filters',
        'recipient_ids',
        'scheduled_at',
        'started_at',
        'completed_at',
        'total_recipients',
        'sent_count',
        'delivered_count',
        'opened_count',
        'clicked_count',
        'bounced_count',
        'unsubscribed_count',
        'is_ab_test',
        'ab_test_config',
        'settings',
        'created_by',
    ];

    protected $casts = [
        'audience_filters' => 'array',
        'recipient_ids' => 'array',
        'ab_test_config' => 'array',
        'settings' => 'array',
        'is_ab_test' => 'boolean',
        'scheduled_at' => 'datetime',
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    /**
     * Email template relationship.
     */
    public function emailTemplate(): BelongsTo
    {
        return $this->belongsTo(EmailTemplate::class);
    }

    /**
     * Creator relationship.
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Calculate open rate.
     */
    public function getOpenRateAttribute(): float
    {
        if ($this->delivered_count === 0) {
            return 0;
        }

        return round(($this->opened_count / $this->delivered_count) * 100, 2);
    }

    /**
     * Calculate click rate.
     */
    public function getClickRateAttribute(): float
    {
        if ($this->delivered_count === 0) {
            return 0;
        }

        return round(($this->clicked_count / $this->delivered_count) * 100, 2);
    }

    /**
     * Calculate bounce rate.
     */
    public function getBounceRateAttribute(): float
    {
        if ($this->sent_count === 0) {
            return 0;
        }

        return round(($this->bounced_count / $this->sent_count) * 100, 2);
    }

    /**
     * Scope for scheduled campaigns.
     */
    public function scopeScheduled($query)
    {
        return $query->where('status', 'scheduled');
    }

    /**
     * Scope for active campaigns.
     */
    public function scopeActive($query)
    {
        return $query->whereIn('status', ['scheduled', 'sending']);
    }

    /**
     * Scope for completed campaigns.
     */
    public function scopeCompleted($query)
    {
        return $query->where('status', 'sent');
    }
}
