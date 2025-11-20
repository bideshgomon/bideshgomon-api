<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphToMany;

/**
 * @property int $id
 * @property int $user_id
 * @property string|null $institution_name
 * @property string|null $degree
 * @property string|null $field_of_study
 * @property \Carbon\Carbon|null $start_date
 * @property \Carbon\Carbon|null $end_date
 * @property string|null $country
 * @property string|null $city
 * @property bool $is_completed
 * @property string|null $gpa_or_grade
 * @property string|null $degree_certificate_path
 * @property string|null $transcript_path
 * @property string|null $language_of_instruction
 * @property string|null $courses_completed
 * @property string|null $honors_awards
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property-read User $user
 */
class UserEducation extends Model
{
    use HasFactory;

    /**
     * Explicitly tell the model to use the 'user_educations' (plural) table.
     */
    protected $table = 'user_educations';

    /**
     * @var list<string>
     */
    protected $fillable = [
        'user_id',
        'institution_name',
        'degree',
        'field_of_study',
        'start_date',
        'end_date',
        'country',
        'city',
        'is_completed',
        'gpa_or_grade',
        'degree_certificate_path',
        'transcript_path',
        'language_of_instruction',
        'courses_completed',
        'honors_awards',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'is_completed' => 'boolean',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
