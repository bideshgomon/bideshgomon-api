<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model; // <-- Corrected
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphToMany;

/**
 * @property int $id
 * @property int $user_id
 * @property string $company_name
 * @property string $position
 * @property \Carbon\Carbon $start_date
 * @property \Carbon\Carbon|null $end_date
 * @property string|null $country
 * @property string|null $city
 * @property string|null $job_description
 * @property float|null $salary
 * @property string $currency
 * @property string $salary_period
 * @property string|null $supervisor_name
 * @property string|null $supervisor_phone
 * @property string|null $supervisor_email
 * @property string|null $employment_letter_path
 * @property array|null $payslip_paths
 * @property array|null $tax_return_paths
 * @property string|null $reason_for_leaving
 * @property bool $is_current_employment
 * @property string|null $employment_type
 * @property-read User $user
 */
class UserWorkExperience extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'user_work_experiences';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'user_id',
        'company_name',
        'position',
        'start_date',
        'end_date',
        'country',
        'city',
        'job_description',
        'salary',
        'currency',
        'salary_period',
        'supervisor_name',
        'supervisor_phone',
        'supervisor_email',
        'employment_letter_path',
        'payslip_paths',
        'tax_return_paths',
        'reason_for_leaving',
        'is_current_employment',
        'employment_type',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'is_current_employment' => 'boolean',
        'payslip_paths' => 'array',
        'tax_return_paths' => 'array',
        'salary' => 'decimal:2',
    ];

    /**
     * Get the user that owns the work experience.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
