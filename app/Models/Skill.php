<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Str;

class Skill extends Model
{
    protected $fillable = [
        'skill_category_id',
        'name',
        'name_bn',
        'slug',
        'description',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'skill_category_id' => 'integer',
    ];

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();

        // Auto-generate slug from name if not provided
        static::creating(function ($skill) {
            if (empty($skill->slug) && !empty($skill->name)) {
                $skill->slug = Str::slug($skill->name);
            }
        });

        static::updating(function ($skill) {
            if (empty($skill->slug) && !empty($skill->name)) {
                $skill->slug = Str::slug($skill->name);
            }
        });
    }

    /**
     * The category this skill belongs to.
     */
    public function skillCategory(): BelongsTo
    {
        return $this->belongsTo(SkillCategory::class);
    }

    /**
     * The users that possess this skill.
     */
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'user_skill')
            ->withPivot('proficiency_level', 'years_of_experience')
            ->withTimestamps();
    }

    /**
     * Scope: Only active skills.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
