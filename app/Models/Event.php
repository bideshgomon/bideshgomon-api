<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Event extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'title',
        'slug',
        'description',
        'event_type',
        'event_date',
        'event_time',
        'end_date',
        'end_time',
        'location',
        'is_online',
        'meeting_link',
        'image',
        'max_participants',
        'registration_deadline',
        'is_featured',
        'is_published',
    ];

    protected $casts = [
        'event_date' => 'date',
        'end_date' => 'date',
        'registration_deadline' => 'date',
        'is_online' => 'boolean',
        'is_featured' => 'boolean',
        'is_published' => 'boolean',
        'max_participants' => 'integer',
    ];

    // Automatically generate slug when creating
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($event) {
            if (empty($event->slug)) {
                $event->slug = Str::slug($event->title);
            }
        });
    }

    // Query Scopes
    public function scopePublished($query)
    {
        return $query->where('is_published', true);
    }

    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    public function scopeUpcoming($query)
    {
        return $query->where('event_date', '>=', now()->toDateString())
            ->orderBy('event_date')
            ->orderBy('event_time');
    }

    public function scopePast($query)
    {
        return $query->where('event_date', '<', now()->toDateString())
            ->orderByDesc('event_date')
            ->orderByDesc('event_time');
    }

    public function scopeByType($query, $type)
    {
        return $query->where('event_type', $type);
    }

    public function scopeSearch($query, $search)
    {
        return $query->where(function($q) use ($search) {
            $q->where('title', 'like', "%{$search}%")
              ->orWhere('description', 'like', "%{$search}%")
              ->orWhere('location', 'like', "%{$search}%");
        });
    }

    // Helper Methods
    public function isUpcoming(): bool
    {
        return $this->event_date >= now()->toDateString();
    }

    public function isPast(): bool
    {
        return $this->event_date < now()->toDateString();
    }

    public function hasRegistrationClosed(): bool
    {
        if (!$this->registration_deadline) {
            return false;
        }
        return $this->registration_deadline < now()->toDateString();
    }

    public function isFull(): bool
    {
        if (!$this->max_participants) {
            return false;
        }
        // This would check against actual registrations when that feature is added
        return false;
    }

    public function canRegister(): bool
    {
        return $this->isUpcoming() 
            && !$this->hasRegistrationClosed() 
            && !$this->isFull()
            && $this->is_published;
    }

    public function getEventTypeLabel(): string
    {
        return match($this->event_type) {
            'seminar' => 'Seminar',
            'workshop' => 'Workshop',
            'fair' => 'Fair',
            'consultation' => 'Consultation',
            'webinar' => 'Webinar',
            default => 'Event',
        };
    }
}
