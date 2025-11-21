<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SmartSuggestion extends Model
{
    protected $fillable = [
        'user_id',
        'suggestion_type',
        'category',
        'priority',
        'title',
        'description',
        'data',
        'action_type',
        'action_url',
        'is_completed',
        'completed_at',
        'is_dismissed',
        'dismissed_at',
        'expires_at',
        'relevance_score',
    ];

    protected $casts = [
        'data' => 'array',
        'is_completed' => 'boolean',
        'is_dismissed' => 'boolean',
        'completed_at' => 'datetime',
        'dismissed_at' => 'datetime',
        'expires_at' => 'datetime',
        'relevance_score' => 'integer',
    ];

    /**
     * Get the user that owns the suggestion
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope for active suggestions (not completed, not dismissed, not expired)
     */
    public function scopeActive($query)
    {
        return $query->where('is_completed', false)
            ->where('is_dismissed', false)
            ->where(function ($q) {
                $q->whereNull('expires_at')
                  ->orWhere('expires_at', '>', now());
            });
    }

    /**
     * Scope for high priority suggestions
     */
    public function scopeHighPriority($query)
    {
        return $query->whereIn('priority', ['high', 'urgent']);
    }

    /**
     * Scope by category
     */
    public function scopeByCategory($query, $category)
    {
        return $query->where('category', $category);
    }

    /**
     * Mark suggestion as completed
     */
    public function markAsCompleted()
    {
        $this->update([
            'is_completed' => true,
            'completed_at' => now(),
        ]);
    }

    /**
     * Mark suggestion as dismissed
     */
    public function dismiss()
    {
        $this->update([
            'is_dismissed' => true,
            'dismissed_at' => now(),
        ]);
    }

    /**
     * Check if suggestion is active
     */
    public function isActive()
    {
        return !$this->is_completed 
            && !$this->is_dismissed 
            && (is_null($this->expires_at) || $this->expires_at->isFuture());
    }

    /**
     * Get priority badge color
     */
    public function getPriorityColorAttribute()
    {
        return match($this->priority) {
            'urgent' => 'red',
            'high' => 'orange',
            'medium' => 'yellow',
            'low' => 'blue',
            default => 'gray',
        };
    }

    /**
     * Get category icon
     */
    public function getCategoryIconAttribute()
    {
        return match($this->category) {
            'visa' => '🛂',
            'profile' => '👤',
            'document' => '📄',
            'application' => '📝',
            'assessment' => '⭐',
            default => '💡',
        };
    }
}
