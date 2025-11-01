<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TravelInsurance extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'bimafy_policy_reference', 'package_name', 'destination_country_id', 'start_date', 'end_date', 'duration_days', 'premium_paid', 'currency', 'status', 'coverage_details'];

    protected $casts = ['start_date' => 'date', 'end_date' => 'date', 'premium_paid' => 'decimal:2', 'coverage_details' => 'array'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function destinationCountry(): BelongsTo
    {
        return $this->belongsTo(Country::class, 'destination_country_id');
    }
}
