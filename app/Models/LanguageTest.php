<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property int $id
 * @property int|null $language_id
 * @property string $name
 * @property string|null $name_bn
 * @property string $code
 * @property float|null $min_score
 * @property float|null $max_score
 * @property string $score_type
 * @property string|null $description
 * @property bool $is_active
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property-read Language|null $language
 * @property-read \Illuminate\Database\Eloquent\Collection<int, UserLanguage> $userLanguages
 */
class LanguageTest extends Model
{
    use HasFactory;

    protected $fillable = [
        'language_id',
        'name',
        'name_bn',
        'code',
        'min_score',
        'max_score',
        'score_type',
        'description',
        'is_active',
    ];

    protected $casts = [
        'min_score' => 'decimal:2',
        'max_score' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    /**
     * Get the language this test is for
     */
    public function language(): BelongsTo
    {
        return $this->belongsTo(Language::class);
    }

    /**
     * Get all user language entries using this test
     */
    public function userLanguages(): HasMany
    {
        return $this->hasMany(UserLanguage::class);
    }

    /**
     * Scope to get only active tests
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
