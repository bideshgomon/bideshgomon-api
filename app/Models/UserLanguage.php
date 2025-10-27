<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory; // <-- [PATCH]
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo; // <-- [PATCH]

class UserLanguage extends Model
{
    // --- [PATCH START] ---
    use HasFactory;

    protected $fillable = [
        'user_id',
        'language_id',
        'language_test_id',
        'overall_score',
        'reading_score',
        'writing_score',
        'listening_score',
        'speaking_score',
        'test_date',
        'expiry_date',
        'file_path',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
    
    // Add relationships to Language and LanguageTest
    public function language(): BelongsTo
    {
        return $this->belongsTo(Language::class);
    }

    public function languageTest(): BelongsTo
    {
        return $this->belongsTo(LanguageTest::class);
    }
    // --- [PATCH END] ---
}