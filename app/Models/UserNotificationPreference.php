<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class UserNotificationPreference extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'notification_type',
        'email_enabled',
        'in_app_enabled',
        'push_enabled',
    ];

    protected $casts = [
        'email_enabled' => 'boolean',
        'in_app_enabled' => 'boolean',
        'push_enabled' => 'boolean',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public static function shouldSendEmail(int $userId, string $notificationType): bool
    {
        $preference = static::where('user_id', $userId)
            ->where('notification_type', $notificationType)
            ->first();

        return $preference ? $preference->email_enabled : true;
    }

    public static function shouldSendInApp(int $userId, string $notificationType): bool
    {
        $preference = static::where('user_id', $userId)
            ->where('notification_type', $notificationType)
            ->first();

        return $preference ? $preference->in_app_enabled : true;
    }
}
