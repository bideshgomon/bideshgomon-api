<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MasterDocument extends Model
{
    protected $fillable = [
        'category_id',
        'document_name',
        'description',
        'specifications',
        'translation_required',
        'notarization_required',
        'typical_validity_days',
        'international_standard',
        'example_url',
        'sort_order',
        'is_active',
    ];

    protected $casts = [
        'translation_required' => 'boolean',
        'notarization_required' => 'boolean',
        'is_active' => 'boolean',
    ];

    // Relationships
    public function category()
    {
        return $this->belongsTo(DocumentCategory::class, 'category_id');
    }

    public function countryRequirements()
    {
        return $this->hasMany(CountryDocumentRequirement::class, 'document_id');
    }
}
