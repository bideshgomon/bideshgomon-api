<?php namespace App\Models;
use Illuminate\Database\Eloquent\Model; use Illuminate\Support\Str; use Illuminate\Database\Eloquent\Relations\BelongsToMany;
class Skill extends Model
{
    protected $fillable = ['name', 'slug', 'category'];
    protected static function booted(): void { static::saving(function ($skill) { $skill->slug = Str::slug($skill->name); }); }
    public function users(): BelongsToMany { return $this->belongsToMany(User::class, 'user_skill'); }
}