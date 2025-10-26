<?php namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory; use Illuminate\Database\Eloquent\Model; use Illuminate\Database\Eloquent\Relations\BelongsTo; use Illuminate\Database\Eloquent\Relations\HasMany;
class University extends Model {
    use HasFactory;
    protected $fillable = ['name', 'country_id', 'city', 'ranking', 'website_url', 'description', 'logo_path', 'intake_months'];
    protected $casts = ['intake_months' => 'array'];
    public function country(): BelongsTo { return $this->belongsTo(Country::class); }
    public function courses(): HasMany { return $this->hasMany(Course::class); }
}