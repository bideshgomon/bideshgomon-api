<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VisaFee extends Model
{
    use HasFactory;

    protected $fillable = [
        'country_id',
        'agency_id',
        'profession',
        'visa_type',
        'embassy_fee',
        'service_fee',
        'processing_fee',
        'total_fee',
        'currency',
        'urgent_processing_fee',
        'is_active',
    ];

    protected $casts = [
        'embassy_fee' => 'decimal:2',
        'service_fee' => 'decimal:2',
        'processing_fee' => 'decimal:2',
        'total_fee' => 'decimal:2',
        'urgent_processing_fee' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    public function country()
    {
        return $this->belongsTo(Country::class);
    }

    public function agency()
    {
        return $this->belongsTo(Agency::class);
    }
}
