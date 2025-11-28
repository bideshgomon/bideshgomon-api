<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CountryDocumentRequirement extends Model
{
    protected $fillable = [
        'country_id',
        'visa_type',
        'profession_category',
        'document_id',
        'is_mandatory',
        'specific_notes',
        'sort_order',
    ];

    protected $casts = [
        'is_mandatory' => 'boolean',
    ];

    // Relationships
    public function country()
    {
        return $this->belongsTo(Country::class);
    }

    public function document()
    {
        return $this->belongsTo(MasterDocument::class, 'document_id');
    }
}
