<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany; // Import this

class Airline extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'code'];

    // Add this relationship
    public function flights(): HasMany
    {
        return $this->hasMany(Flight::class);
    }
}