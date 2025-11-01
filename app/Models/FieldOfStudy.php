<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FieldOfStudy extends Model
{
    use HasFactory;

    protected $table = 'fields_of_study'; // Matches migration

    protected $fillable = ['name'];

    public $timestamps = false; // Matches migration
}
