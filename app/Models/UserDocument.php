<?php namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory; use Illuminate\Database\Eloquent\Model; use Illuminate\Database\Eloquent\Relations\BelongsTo; use Illuminate\Database\Eloquent\Relations\HasMany;
class UserDocument extends Model {
    use HasFactory;
    protected $fillable = ['user_id', 'document_type_id', 'file_path', 'file_name', 'verified_at', 'notes', 'expiry_date'];
    protected $casts = ['verified_at' => 'datetime', 'expiry_date' => 'date'];
    public function user(): BelongsTo { return $this->belongsTo(User::class); }
    public function documentType(): BelongsTo { return $this->belongsTo(DocumentType::class); }
    public function touristVisaDocuments(): HasMany { return $this->hasMany(TouristVisaDocument::class); }
}