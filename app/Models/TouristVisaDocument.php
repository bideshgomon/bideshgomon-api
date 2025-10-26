<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TouristVisaDocument extends Model
{
    use HasFactory;

    protected $fillable = [
        'tourist_visa_id',
        'document_type_id',
        'user_document_id',
        'status',
        'admin_notes',
    ];

    /**
     * Get the tourist visa application this document belongs to.
     */
    public function touristVisa(): BelongsTo
    {
        return $this->belongsTo(TouristVisa::class);
    }

    /**
     * Get the type of document required.
     */
    public function documentType(): BelongsTo
    {
        return $this->belongsTo(DocumentType::class);
    }

    /**
     * Get the actual uploaded user document (if linked).
     */
    public function userDocument(): BelongsTo
    {
        // Note: This relationship can be null if user_document_id is null
        return $this->belongsTo(UserDocument::class);
    }
}