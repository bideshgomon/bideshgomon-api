<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VisaRequirementDocument extends Model
{
    protected $fillable = [
        'visa_requirement_id',
        'document_name',
        'document_type',
        'description',
        'specifications',
        'is_mandatory',
        'conditions',
        'quantity',
        'format',
        'validation_rules',
        'sample_url',
        'common_mistakes',
        'sort_order',
        'is_active',
    ];

    protected $casts = [
        'is_mandatory' => 'boolean',
        'quantity' => 'integer',
        'validation_rules' => 'array',
        'is_active' => 'boolean',
    ];

    /**
     * Get the visa requirement this document belongs to.
     */
    public function visaRequirement(): BelongsTo
    {
        return $this->belongsTo(VisaRequirement::class);
    }

    /**
     * Scope: Mandatory documents only.
     */
    public function scopeMandatory($query)
    {
        return $query->where('is_mandatory', true);
    }

    /**
     * Scope: Optional documents.
     */
    public function scopeOptional($query)
    {
        return $query->where('is_mandatory', false);
    }

    /**
     * Scope: Active documents.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope: Filter by document type.
     */
    public function scopeByType($query, string $type)
    {
        return $query->where('document_type', $type);
    }

    /**
     * Check if document format is original.
     */
    public function requiresOriginal(): bool
    {
        return str_contains(strtolower($this->format ?? ''), 'original');
    }

    /**
     * Check if document requires notarization.
     */
    public function requiresNotarization(): bool
    {
        return str_contains(strtolower($this->format ?? ''), 'notarized');
    }

    /**
     * Check if document requires translation.
     */
    public function requiresTranslation(): bool
    {
        return str_contains(strtolower($this->format ?? ''), 'translated');
    }

    /**
     * Get formatted document requirement display.
     */
    public function getRequirementSummaryAttribute(): string
    {
        $summary = $this->document_name;
        
        if ($this->quantity > 1) {
            $summary .= " ({$this->quantity} copies)";
        }

        if ($this->format) {
            $summary .= " - {$this->format}";
        }

        return $summary;
    }

    /**
     * Get status badge text.
     */
    public function getStatusBadgeAttribute(): string
    {
        return $this->is_mandatory ? 'Required' : 'Optional';
    }
}
