<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Appointment extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'consultant_id',
        'consultation_service_id',
        'preferred_date',
        'preferred_time_slot',
        'notes',
        'status',
        'price',
        // --- ADD THIS NEW FIELD ---
        'stripe_checkout_session_id',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'preferred_date' => 'date',
        'price' => 'decimal:2',
    ];

    /**
     * Get the user (client) who booked the appointment.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Get the consultant assigned to the appointment.
     */
    public function consultant(): BelongsTo
    {
        return $this->belongsTo(User::class, 'consultant_id');
    }

    /**
     * Get the service that was booked.
     */
    public function service(): BelongsTo
    {
        return $this->belongsTo(ConsultationService::class, 'consultation_service_id');
    }
}