<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role_id',
        'is_active',
        'skills',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'is_active' => 'boolean',
        'skills' => 'array',
    ];

    /**
     * Get the role associated with the user.
     */
    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class);
    }

    /**
     * Get the user profile associated with the user.
     * (Only for 'user' role)
     */
    public function userProfile(): HasOne
    {
        return $this->hasOne(UserProfile::class);
    }

    /**
     * Get the agency this user owns.
     * (Only for 'agency' role)
     */
    public function ownedAgency(): HasOne
    {
        return $this->hasOne(Agency::class, 'owner_id');
    }

    /**
     * The agencies this user works for as a consultant.
     * (Only for 'consultant' role)
     */
    public function agenciesAsConsultant(): BelongsToMany
    {
        return $this->belongsToMany(
            Agency::class,
            'agency_consultant',
            'consultant_id',
            'agency_id'
        );
    }

    /**
     * The clients (users) this consultant manages.
     * (Only for 'consultant' role)
     */
    public function clients(): BelongsToMany
    {
        return $this->belongsToMany(
            User::class,
            'consultant_user',
            'consultant_id',
            'user_id'
        );
    }

    /**
     * The consultant managing this user.
     * (Only for 'user' role)
     */
    public function consultant(): BelongsToMany
    {
        return $this->belongsToMany(
            User::class,
            'consultant_user',
            'user_id',
            'consultant_id'
        );
    }

    /**
     * Get the education history for the user.
     */
    public function educations(): HasMany
    {
        return $this->hasMany(UserEducation::class);
    }

    /**
     * Get the work experience for the user.
     */
    public function experiences(): HasMany
    {
        return $this->hasMany(UserExperience::class);
    }

    /**
     * Get the portfolio items for the user.
     */
    public function portfolios(): HasMany
    {
        return $this->hasMany(UserPortfolio::class);
    }

    /**
     * Get the uploaded documents for the user.
     */
    public function documents(): HasMany
    {
        return $this->hasMany(UserDocument::class);
    }
}
