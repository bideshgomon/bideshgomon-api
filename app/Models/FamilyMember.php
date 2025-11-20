<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphToMany;

class FamilyMember extends Model
{
    use HasFactory;

    protected $table = 'user_family_members';

    protected $fillable = [
        'user_id',
        'relationship',
        'relationship_other',
        'full_name',
        'native_name',
        'date_of_birth',
        'place_of_birth',
        'gender',
        'nationality',
        'country_of_residence',
        'city',
        'occupation',
        'employer_name',
        'annual_income',
        'income_currency',
        'education_level',
        'marital_status',
        'is_dependent',
        'lives_with_user',
        'will_accompany',
        'will_accompany_travel',
        'immigration_status',
        'is_traveling_with',
        'passport_number',
        'passport_expiry',
        'is_deceased',
        'deceased_date',
        'phone_number',
        'email',
        'emergency_contact',
        'relationship_proof_path',
        'relationship_proof_uploaded',
        'birth_certificate_path',
        'marriage_certificate_path',
        'death_certificate_path',
        'id_document_path',
        'address',
        'notes',
    ];

    protected $casts = [
        'date_of_birth' => 'date',
        'deceased_date' => 'date',
        'passport_expiry' => 'date',
        'is_dependent' => 'boolean',
        'lives_with_user' => 'boolean',
        'will_accompany' => 'boolean',
        'will_accompany_travel' => 'boolean',
        'is_traveling_with' => 'boolean',
        'is_deceased' => 'boolean',
        'emergency_contact' => 'boolean',
        'relationship_proof_uploaded' => 'boolean',
        'annual_income' => 'decimal:2',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get documents attached to this family member record.
     */
    public function documents(): MorphToMany
    {
        return $this->morphToMany(
            UserDocument::class,
            'documentable',
            'documentables',
            'documentable_id',
            'user_document_id'
        )->withPivot([
            'document_category',
            'is_primary',
            'display_order',
            'notes',
            'verified_at',
        ])->withTimestamps();
    }
}
