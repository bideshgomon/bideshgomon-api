<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $user_id
 * @property int|null $language_id
 * @property int|null $language_test_id
 * @property string|null $language (legacy)
 * @property string|null $proficiency (legacy)
 * @property string|null $proficiency_level
 * @property bool $can_read
 * @property bool $can_write
 * @property bool $can_speak
 * @property bool $can_understand
 * @property string $test_taken
 * @property string|null $test_other_name
 * @property string|null $test_score
 * @property float|null $overall_score
 * @property \Carbon\Carbon|null $test_date
 * @property string|null $test_certificate_path (legacy)
 * @property string|null $file_path
 * @property float|null $listening_score
 * @property float|null $reading_score
 * @property float|null $writing_score
 * @property float|null $speaking_score
 * @property string|null $test_reference_number
 * @property \Carbon\Carbon|null $certificate_expiry_date (legacy)
 * @property \Carbon\Carbon|null $expiry_date
 * @property bool $is_native
 * @property-read User $user
 * @property-read Language|null $language
 * @property-read LanguageTest|null $languageTest
 */
class UserLanguage extends Model
{
    use HasFactory;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'user_id',
        // New normalized fields
        'language_id',
        'language_test_id',
        'proficiency_level',
        'overall_score',
        'expiry_date',
        'file_path',
        // Legacy fields (kept for backward compatibility)
        'language',
        'proficiency',
        'test_certificate_path',
        'certificate_expiry_date',
        // Common fields
        'can_read',
        'can_write',
        'can_speak',
        'can_understand',
        'test_taken',
        'test_other_name',
        'test_score',
        'test_date',
        'listening_score',
        'reading_score',
        'writing_score',
        'speaking_score',
        'test_reference_number',
        'is_native',
    ];

    protected $casts = [
        'test_date' => 'date',
        'certificate_expiry_date' => 'date',
        'expiry_date' => 'date',
        'can_read' => 'boolean',
        'can_write' => 'boolean',
        'can_speak' => 'boolean',
        'can_understand' => 'boolean',
        'is_native' => 'boolean',
        'overall_score' => 'decimal:2',
        'reading_score' => 'decimal:2',
        'writing_score' => 'decimal:2',
        'listening_score' => 'decimal:2',
        'speaking_score' => 'decimal:2',
    ];

    /**
     * Get the user that owns this language entry
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the language (from normalized languages table)
     */
    public function language(): BelongsTo
    {
        return $this->belongsTo(Language::class);
    }

    /**
     * Get the language test type
     */
    public function languageTest(): BelongsTo
    {
        return $this->belongsTo(LanguageTest::class);
    }
}
