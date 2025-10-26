<?php namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory; use Illuminate\Database\Eloquent\Model; use Illuminate\Database\Eloquent\Relations\BelongsTo;
class UserLanguage extends Model {
    use HasFactory;
    protected $fillable = ['user_id', 'language_id', 'proficiency_level', 'test_taken', 'test_score', 'test_date'];
    protected $casts = ['test_date' => 'date'];
    public function user(): BelongsTo { return $this->belongsTo(User::class); }
    public function language(): BelongsTo { return $this->belongsTo(Language::class); }
}