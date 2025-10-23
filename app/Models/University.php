<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class University extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     * @var string
     */
    protected $table = 'universities';

    /**
     * The attributes that are mass assignable.
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'country_id',
        'city',
        'website_url',
        'description',
        'logo_path',
        'intake_months',
        'ranking',
    ];

    /**
     * The attributes that should be cast.
     * @var array
     */
    protected $casts = [
        'intake_months' => 'array',
    ];

    /**
     * Get the country this university belongs to.
     */
    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class);
    }

    /**
     * Get all courses offered by this university.
     */
    public function courses(): HasMany
    {
        return $this->hasMany(Course::class);
    }
}