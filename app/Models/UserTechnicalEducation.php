<?php namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory; use Illuminate\Database\Eloquent\Model; use Illuminate\Database\Eloquent\Relations\BelongsTo;
class UserTechnicalEducation extends Model {
    use HasFactory;
    protected $fillable = ['user_id', 'education_type_id', 'institution_name', 'certification_name', 'completion_date', 'description'];
    protected $casts = ['completion_date' => 'date'];
    public function user(): BelongsTo { return $this->belongsTo(User::class); }
    public function educationType(): BelongsTo { return $this->belongsTo(TechnicalEducationType::class, 'education_type_id'); }
}