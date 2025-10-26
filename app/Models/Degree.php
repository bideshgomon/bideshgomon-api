<?php namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory; use Illuminate\Database\Eloquent\Model; use Illuminate\Database\Eloquent\Relations\BelongsTo;
class Degree extends Model {
    use HasFactory;
    protected $fillable = ['name', 'degree_level_id', 'field_of_study_id'];
    public $timestamps = false; // Matches migration
    public function degreeLevel(): BelongsTo { return $this->belongsTo(DegreeLevel::class); }
    public function fieldOfStudy(): BelongsTo { return $this->belongsTo(FieldOfStudy::class); }
}