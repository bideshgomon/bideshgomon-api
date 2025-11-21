<?php

namespace App\Listeners;

use App\Events\DocumentApproved;
use App\Services\NotificationService;

class SendNotificationOnDocumentApproved
{
    public function handle(DocumentApproved $event): void
    {
        $doc = $event->document;
        app(NotificationService::class)->send(
            $doc->user_id,
            'Document Approved',
            'Your '.$doc->document_type.' ('.$doc->original_filename.') has been approved.',
            [
                'type' => 'document_approved',
                'priority' => 'normal',
                'data' => [
                    'document_id' => $doc->id,
                    'document_type' => $doc->document_type,
                ]
            ]
        );
    }
}
