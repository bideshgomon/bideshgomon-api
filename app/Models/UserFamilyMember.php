<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserFamilyMember extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'relationship',
        'relationship_other',
        'full_name',
        'native_name',
        'date_of_birth',
        'gender',
        'nationality',
        'place_of_birth',
        'is_deceased',
        'deceased_date',
        'phone_number',
        'email',
        'country_of_residence',
        'city',
        'address',
        'occupation',
        'employer_name',
        'annual_income',
        'income_currency',
        'education_level',
        'marital_status',
        'immigration_status',
        'is_dependent',
        'lives_with_user',
        'will_accompany',
        'will_accompany_travel',
        'is_traveling_with',
        'emergency_contact',
        'passport_number',
        'passport_expiry',
        'birth_certificate_path',
        'marriage_certificate_path',
        'death_certificate_path',
        'id_document_path',
        'relationship_proof_path',
        'relationship_proof_uploaded',
        'notes',
    ];

    protected $casts = [
        'date_of_birth' => 'date',
        'deceased_date' => 'date',
        'passport_expiry' => 'date',
        'is_deceased' => 'boolean',
        'is_dependent' => 'boolean',
        'lives_with_user' => 'boolean',
        'will_accompany' => 'boolean',
        'will_accompany_travel' => 'boolean',
        'is_traveling_with' => 'boolean',
        'emergency_contact' => 'boolean',
        'relationship_proof_uploaded' => 'boolean',
        'annual_income' => 'decimal:2',
    ];

    /**
     * Get the user that owns the family member.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get age of family member.
     */
    public function getAgeAttribute(): ?int
    {
        if (!$this->date_of_birth) {
            return null;
        }
        
        if ($this->is_deceased && $this->deceased_date) {
            return $this->date_of_birth->diffInYears($this->deceased_date);
        }
        
        return $this->date_of_birth->age;
    }

    /**
     * Check if family member is a parent.
     */
    public function isParent(): bool
    {
        return in_array($this->relationship, ['father', 'mother']);
    }

    /**
     * Check if family member is spouse.
     */
    public function isSpouse(): bool
    {
        return $this->relationship === 'spouse';
    }

    /**
     * Check if family member is a child.
     */
    public function isChild(): bool
    {
        return in_array($this->relationship, ['son', 'daughter']);
    }

    /**
     * Check if family member is dependent (child under 18 or spouse).
     */
    public function isDependent(): bool
    {
        if ($this->isSpouse()) {
            return true;
        }
        
        if ($this->isChild() && $this->date_of_birth) {
            return $this->date_of_birth->age < 18;
        }
        
        return false;
    }

    /**
     * Get the country name from ISO code.
     */
    public function getResidenceCountryNameAttribute(): string
    {
        if (!$this->country_of_residence) {
            return '';
        }
        $countries = config('countries');
        return $countries[$this->country_of_residence] ?? $this->country_of_residence;
    }

    /**
     * Scope to get immediate family (parents, spouse, children).
     */
    public function scopeImmediateFamily($query)
    {
        return $query->whereIn('relationship', ['father', 'mother', 'spouse', 'son', 'daughter']);
    }

    /**
     * Scope to get living family members.
     */
    public function scopeLiving($query)
    {
        return $query->where('is_deceased', false);
    }

    /**
     * Scope to get family members traveling together.
     */
    public function scopeTravelingWith($query)
    {
        return $query->where('is_traveling_with', true);
    }
}
