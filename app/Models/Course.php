<?php namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory; use Illuminate\Database\Eloquent\Model; use Illuminate\Database\Eloquent\Relations\BelongsTo;
class Course extends Model {
    use HasFactory;
    protected $fillable = ['university_id', 'name', 'degree_level', 'field_of_study', 'description', 'duration_years', 'tuition_fee', 'application_deadline', 'entry_requirements'];
    protected $casts = ['tuition_fee' => 'decimal:2', 'entry_requirements' => 'array', 'application_deadline' => 'date'];
    public function university(): BelongsTo { return $this->belongsTo(University::class); }
}