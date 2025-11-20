<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class UserCv extends Model
{
    protected $fillable = [
        'user_id',
        'cv_template_id',
        'title',
        'full_name',
        'email',
        'phone',
        'city',
        'country_id',
        'address',
        'linkedin_url',
        'website_url',
        'professional_summary',
        'education',
        'experience',
        'skills',
        'languages',
        'certifications',
        'projects',
        'references',
        'custom_sections',
        'section_order',
        'pdf_path',
        'last_generated_at',
        'is_public',
        'public_token',
        'view_count',
        'download_count',
    ];

    protected $casts = [
        'country_id' => 'integer',
        'education' => 'array',
        'experience' => 'array',
        'skills' => 'array',
        'languages' => 'array',
        'certifications' => 'array',
        'projects' => 'array',
        'references' => 'array',
        'custom_sections' => 'array',
        'section_order' => 'array',
        'last_generated_at' => 'datetime',
        'is_public' => 'boolean',
        'view_count' => 'integer',
        'download_count' => 'integer',
    ];

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function cvTemplate()
    {
        return $this->belongsTo(CvTemplate::class);
    }

    public function country()
    {
        return $this->belongsTo(Country::class);
    }

    // Scopes
    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopePublic($query)
    {
        return $query->where('is_public', true);
    }

    public function scopeByTemplate($query, $templateId)
    {
        return $query->where('cv_template_id', $templateId);
    }

    // Helpers
    public function getPublicUrlAttribute()
    {
        if (!$this->is_public || !$this->public_token) {
            return null;
        }
        return route('cv.public', $this->public_token);
    }

    public function hasGeneratedPdf()
    {
        return !empty($this->pdf_path) && file_exists(storage_path('app/' . $this->pdf_path));
    }

    public function incrementViewCount()
    {
        $this->increment('view_count');
    }

    public function incrementDownloadCount()
    {
        $this->increment('download_count');
    }

    // Generate public token
    public function generatePublicToken()
    {
        $this->public_token = Str::random(32);
        $this->is_public = true;
        $this->save();
        return $this->public_token;
    }

    // Auto-generate public token on creation if public
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($cv) {
            if ($cv->is_public && empty($cv->public_token)) {
                $cv->public_token = Str::random(32);
            }
        });
    }
}
