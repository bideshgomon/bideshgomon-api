<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Country extends Model
{
    protected $fillable = [
        'name',
        'name_bn',
        'iso_code_2',
        'iso_code_3',
        'phone_code',
        'currency_code',
        'flag_emoji',
        'region',
        'nationality',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];
}
