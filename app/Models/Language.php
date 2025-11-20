<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property int $id
 * @property string $name
 * @property string|null $name_bn
 * @property string $code
 * @property string|null $native_name
 * @property bool $is_active
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, UserLanguage> $userLanguages
 * @property-read \Illuminate\Database\Eloquent\Collection<int, LanguageTest> $languageTests
 */
class Language extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'name_bn',
        'code',
        'native_name',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Get all user language entries using this language
     */
    public function userLanguages(): HasMany
    {
        return $this->hasMany(UserLanguage::class);
    }

    /**
     * Get all language tests for this language
     */
    public function languageTests(): HasMany
    {
        return $this->hasMany(LanguageTest::class);
    }

    /**
     * Scope to get only active languages
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
