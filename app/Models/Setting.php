<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache; // Import Cache facade

class Setting extends Model
{
    use HasFactory;

    protected $fillable = [
        'key',
        'value',
        'type',
    ];

    /**
     * The attributes that should be cast.
     * Consider casting based on the 'type' column if implemented further.
     * For now, we'll treat 'value' mostly as a string.
     */
    // protected $casts = [
    //     // Example: 'value' => 'array', // If type is 'json'
    // ];


    /**
     * Override the default retrieval to potentially cast based on type.
     * This is a basic example; more complex casting might be needed.
     */
    public function getValueAttribute($value)
    {
        if ($this->type === 'boolean') {
            return filter_var($value, FILTER_VALIDATE_BOOLEAN);
        }
        if ($this->type === 'integer') {
            return intval($value);
        }
        if ($this->type === 'json') {
            return json_decode($value, true);
        }
        return $value;
    }

    /**
     * Override the default mutator to potentially cast based on type.
     */
    public function setValueAttribute($value)
    {
        if ($this->type === 'json' && is_array($value)) {
            $this->attributes['value'] = json_encode($value);
        } elseif ($this->type === 'boolean') {
            $this->attributes['value'] = filter_var($value, FILTER_VALIDATE_BOOLEAN) ? '1' : '0';
        } else {
            $this->attributes['value'] = $value;
        }
    }

    /**
     * Clear the settings cache whenever a setting is saved or deleted.
     */
    protected static function booted(): void
    {
        static::saved(function ($setting) {
            Cache::forget('app_settings');
        });

        static::deleted(function ($setting) {
            Cache::forget('app_settings');
        });
    }
}