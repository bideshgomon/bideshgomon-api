<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\DB;

class ServiceQuote extends Model
{
    protected $fillable = [
        'service_application_id',
        'agency_id',
        'quoted_amount',
        'service_fee',
        'platform_commission',
        'agency_earnings',
        'processing_time_days',
        'quote_details',
        'quote_notes',
        'special_notes',
        'breakdown',
        'status',
        'valid_until',
        'accepted_at',
        'rejected_at',
    ];

    protected $casts = [
        'quoted_amount' => 'decimal:2',
        'service_fee' => 'decimal:2',
        'platform_commission' => 'decimal:2',
        'agency_earnings' => 'decimal:2',
        'breakdown' => 'array',
        'valid_until' => 'datetime',
        'accepted_at' => 'datetime',
        'rejected_at' => 'datetime',
    ];

    public function serviceApplication(): BelongsTo
    {
        return $this->belongsTo(ServiceApplication::class);
    }

    public function agency(): BelongsTo
    {
        return $this->belongsTo(User::class, 'agency_id');
    }

    public function accept(): void
    {
        DB::transaction(function () {
            // Update this quote to accepted
            $this->update([
                'status' => 'accepted',
                'accepted_at' => now(),
            ]);

            // Update the service application
            $this->serviceApplication->update([
                'status' => 'accepted',
                'agency_id' => $this->agency_id,
                'quoted_amount' => $this->quoted_amount,
                'service_fee' => $this->service_fee,
                'platform_commission' => $this->platform_commission,
                'agency_earnings' => $this->agency_earnings,
                'processing_time_days' => $this->processing_time_days,
                'accepted_at' => now(),
            ]);

            // Reject all other quotes for this application
            ServiceQuote::where('service_application_id', $this->service_application_id)
                ->where('id', '!=', $this->id)
                ->where('status', 'pending')
                ->update([
                    'status' => 'rejected',
                    'rejected_at' => now(),
                ]);
        });
    }

    public function reject(): void
    {
        $this->update([
            'status' => 'rejected',
            'rejected_at' => now(),
        ]);
    }

    public function isExpired(): bool
    {
        return $this->valid_until < now();
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending')
                    ->where('valid_until', '>', now());
    }

    public function scopeAccepted($query)
    {
        return $query->where('status', 'accepted');
    }
}
