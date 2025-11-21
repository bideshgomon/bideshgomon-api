<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    protected $listen = [
        \App\Events\DocumentApproved::class => [
            \App\Listeners\SendNotificationOnDocumentApproved::class,
            \App\Listeners\DocumentEventAuditLogger::class,
        ],
        \App\Events\DocumentRejected::class => [
            \App\Listeners\SendNotificationAndSuggestionOnDocumentRejected::class,
            \App\Listeners\DocumentEventAuditLogger::class,
        ],
    ];
}
