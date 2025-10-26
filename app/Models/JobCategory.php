<?php namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory; use Illuminate\Database\Eloquent\Model; use Illuminate\Support\Str; use Illuminate\Database\Eloquent\Relations\HasMany;
class JobCategory extends Model {
    use HasFactory;
    protected $table = 'job_categories';
    protected $fillable = ['name', 'slug', 'description', 'is_active'];
    protected $casts = ['is_active' => 'boolean'];
    protected static function boot() { parent::boot(); static::saving(function ($c) { $c->slug = Str::slug($c->name); }); }
    public function jobPostings(): HasMany { return $this->hasMany(JobPosting::class); }
}