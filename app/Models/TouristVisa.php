<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TouristVisa extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'destination_country_id', 'intended_travel_date', 'duration_days', 'status', 'admin_notes', 'application_reference'];

    protected $casts = ['intended_travel_date' => 'date'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function destinationCountry(): BelongsTo
    {
        return $this->belongsTo(Country::class, 'destination_country_id');
    }

    public function documents(): HasMany
    {
        return $this->hasMany(TouristVisaDocument::class);
    }
}
