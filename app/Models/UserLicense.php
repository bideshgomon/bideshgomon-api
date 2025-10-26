<?php namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory; use Illuminate\Database\Eloquent\Model; use Illuminate\Database\Eloquent\Relations\BelongsTo;
class UserLicense extends Model {
    use HasFactory;
    protected $fillable = ['user_id', 'license_type_id', 'license_number', 'issuing_authority', 'issue_date', 'expiry_date'];
    protected $casts = ['issue_date' => 'date', 'expiry_date' => 'date'];
    public function user(): BelongsTo { return $this->belongsTo(User::class); }
    public function licenseType(): BelongsTo { return $this->belongsTo(LicenseType::class); }
}