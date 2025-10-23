<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class UserDocument extends Model
{
    use HasFactory;

    protected $table = 'user_documents';

    protected $fillable = [
        'user_id',
        'document_type_id',
        'file_path',
        'file_name',
        'document_number',
        'issue_date',
        'expiry_date',
        'issuing_country_id',
        'status',
    ];

    protected $casts = [
        'issue_date' => 'date',
        'expiry_date' => 'date',
    ];

    protected $appends = ['url'];

    public function getUrlAttribute(): string
    {
        return Storage::url($this->file_path);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function documentType(): BelongsTo
    {
        return $this->belongsTo(DocumentType::class);
    }
}