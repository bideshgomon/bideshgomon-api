<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory; // <-- [PATCH]
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo; // <-- [PATCH]

class UserLicense extends Model
{
    // --- [PATCH START] ---
    use HasFactory;

    protected $fillable = [
        'user_id',
        'license_type_id',
        'custom_license_name',
        'license_number',
        'issue_date',
        'expiry_date',
        'issuing_authority',
        'file_path',
    ];

    protected $casts = [
        'issue_date' => 'date',
        'expiry_date' => 'date',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function licenseType(): BelongsTo
    {
        return $this->belongsTo(LicenseType::class);
    }
    // --- [PATCH END] ---
}
