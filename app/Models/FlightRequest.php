<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class FlightRequest extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'request_reference',
        'user_id',
        'trip_type',
        'origin_airport_code',
        'destination_airport_code',
        'travel_date',
        'return_date',
        'multi_city_segments',
        'passengers_count',
        'flight_class',
        'passengers',
        'contact_name',
        'contact_email',
        'contact_phone',
        'special_requests',
        'budget_min',
        'budget_max',
        'prefer_direct_flights',
        'preferred_airlines',
        'preferred_time',
        'assigned_to',
        'assigned_at',
        'status',
        'admin_notes',
        'rejection_reason',
        'quotes_count',
        'quoted_at',
        'ip_address',
        'user_agent',
        'search_count',
    ];

    protected $casts = [
        'travel_date' => 'date',
        'return_date' => 'date',
        'multi_city_segments' => 'array',
        'passengers' => 'array',
        'preferred_airlines' => 'array',
        'assigned_at' => 'datetime',
        'quoted_at' => 'datetime',
        'budget_min' => 'decimal:2',
        'budget_max' => 'decimal:2',
        'prefer_direct_flights' => 'boolean',
    ];

    /**
     * Boot method to generate request reference
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($request) {
            if (empty($request->request_reference)) {
                $request->request_reference = 'FR' . date('Ymd') . str_pad(
                    static::whereDate('created_at', today())->count() + 1,
                    3,
                    '0',
                    STR_PAD_LEFT
                );
            }
        });
    }

    /**
     * Relationships
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function assignedTo(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function quotes(): HasMany
    {
        return $this->hasMany(FlightQuote::class);
    }

    /**
     * Scopes
     */
    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeAssigned($query)
    {
        return $query->where('status', 'assigned');
    }

    public function scopeQuoted($query)
    {
        return $query->where('status', 'quoted');
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    public function scopeAssignedToUser($query, $userId)
    {
        return $query->where('assigned_to', $userId);
    }

    /**
     * Helpers
     */
    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function isAssigned(): bool
    {
        return $this->status === 'assigned';
    }

    public function isQuoted(): bool
    {
        return $this->status === 'quoted';
    }

    public function canReceiveQuote(): bool
    {
        return in_array($this->status, ['pending', 'assigned', 'quoted']);
    }

    /**
     * Formatted attributes
     */
    public function getFormattedBudgetAttribute(): string
    {
        if ($this->budget_min && $this->budget_max) {
            return '৳ ' . number_format((float)$this->budget_min) . ' - ৳ ' . number_format((float)$this->budget_max);
        } elseif ($this->budget_max) {
            return 'Up to ৳ ' . number_format((float)$this->budget_max);
        }
        return 'Flexible';
    }

    public function getRouteNameAttribute(): string
    {
        if ($this->trip_type === 'multi_city') {
            return 'Multi-City Trip';
        }
        return $this->origin_airport_code . ' → ' . $this->destination_airport_code;
    }

    public function getStatusColorAttribute(): string
    {
        return match($this->status) {
            'pending' => 'bg-yellow-100 text-yellow-800 border-yellow-200',
            'assigned' => 'bg-blue-100 text-blue-800 border-blue-200',
            'quoted' => 'bg-purple-100 text-purple-800 border-purple-200',
            'accepted' => 'bg-green-100 text-green-800 border-green-200',
            'rejected' => 'bg-red-100 text-red-800 border-red-200',
            'cancelled' => 'bg-gray-100 text-gray-800 border-gray-200',
            'completed' => 'bg-emerald-100 text-emerald-800 border-emerald-200',
            default => 'bg-gray-100 text-gray-800 border-gray-200',
        };
    }
}
