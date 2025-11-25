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

    public function touristVisa(): BelongsTo
    {
        return $this->belongsTo(TouristVisa::class);
    }

    public function documentType(): BelongsTo
    {
        return $this->belongsTo(DocumentType::class);
    }

    public function userDocument(): BelongsTo
    {
        return $this->belongsTo(UserDocument::class);
    }
}
