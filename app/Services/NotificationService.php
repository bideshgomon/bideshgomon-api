<?php

namespace App\Services;

use App\Models\UserNotification;
use Illuminate\Support\Facades\DB;

class NotificationService
{
    public function send(int $userId, string $title, string $body, array $options = []): UserNotification
    {
        return DB::transaction(function () use ($userId, $title, $body, $options) {
            return UserNotification::create([
                'user_id' => $userId,
                'title' => $title,
                'body' => $body,
                'type' => $options['type'] ?? null,
                'priority' => $options['priority'] ?? UserNotification::PRIORITY_NORMAL,
                'data' => $options['data'] ?? [],
            ]);
        });
    }

    public function sendBulk(array $userIds, string $title, string $body, array $options = []): int
    {
        $count = 0;
        DB::transaction(function () use ($userIds, $title, $body, $options, &$count) {
            foreach ($userIds as $id) {
                UserNotification::create([
                    'user_id' => $id,
                    'title' => $title,
                    'body' => $body,
                    'type' => $options['type'] ?? null,
                    'priority' => $options['priority'] ?? UserNotification::PRIORITY_NORMAL,
                    'data' => $options['data'] ?? [],
                ]);
                $count++;
            }
        });
        return $count;
    }

    public function markAllRead(int $userId): int
    {
        return UserNotification::where('user_id', $userId)->whereNull('read_at')->update(['read_at' => now()]);
    }
}
