<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany; // <-- ADDED
use Illuminate\Support\Facades\Storage;
// Added for new/existing relationships
use App\Models\User;
use App\Models\DocumentType;
use App\Models\TouristVisaDocument; 

class UserDocument extends Model
{
    use HasFactory;

    protected $table = 'user_documents';

    /**
     * The attributes that are mass assignable.
     * (MERGED from both versions)
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'document_type_id',
        'file_path',
        'file_name',
        'document_number', // From snippet 1
        'issue_date', // From snippet 1
        'expiry_date', // From snippet 1
        'issuing_country_id', // From snippet 1
        'status', // From snippet 1
        'verified_at', // From snippet 2
        'notes', // From snippet 2
    ];

    /**
     * The attributes that should be cast.
     * (MERGED from both versions)
     *
     * @var array<string, string>
     */
    protected $casts = [
        'issue_date' => 'date',
        'expiry_date' => 'date',
        'verified_at' => 'datetime',
    ];

    /**
     * The accessors to append to the model's array form.
     * (From snippet 1)
     *
     * @var array
     */
    protected $appends = ['url'];

    /**
     * Get the public URL for the document.
     * (From snippet 1)
     */
    public function getUrlAttribute(): string
    {
        return Storage::url($this->file_path);
    }

    // --- Relationships ---

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function documentType(): BelongsTo
    {
        return $this->belongsTo(DocumentType::class);
    }

    /**
     * Get the tourist visa document entries linked to this uploaded file.
     * (MERGED from snippet 2)
     */
    public function touristVisaDocuments(): HasMany
    {
        return $this->hasMany(TouristVisaDocument::class);
    }
}