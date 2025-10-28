<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DegreeLevel extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'is_active', // <-- Ensure this is present
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'is_active' => 'boolean', // <-- Ensure this is present
    ];

    // Define relationships if needed, e.g.:
    // public function courses() {
    //     return $this->hasMany(Course::class);
    // }
    // public function userEducations() {
    //     return $this->hasMany(UserEducation::class);
    // }
}