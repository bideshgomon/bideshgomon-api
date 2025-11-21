<?php

namespace App\Listeners;

use App\Events\DocumentRejected;
use App\Services\NotificationService;
use App\Services\SmartSuggestionsService;
use App\Models\SmartSuggestion;

class SendNotificationAndSuggestionOnDocumentRejected
{
    public function handle(DocumentRejected $event): void
    {
        $doc = $event->document;
        $reason = $event->reason;

        // Notify user
        app(NotificationService::class)->send(
            $doc->user_id,
            'Document Rejected',
            'Your '.$doc->document_type.' ('.$doc->original_filename.') was rejected. Reason: '.$reason,
            [
                'type' => 'document_rejected',
                'priority' => 'high',
                'data' => [
                    'document_id' => $doc->id,
                    'document_type' => $doc->document_type,
                    'rejection_reason' => $reason,
                ]
            ]
        );

        // Create a targeted smart suggestion to re-upload/fix document if not already present
        SmartSuggestion::firstOrCreate([
            'user_id' => $doc->user_id,
            'suggestion_type' => 'document_required',
            'title' => 'Re-upload '.$doc->document_type,
        ], [
            'category' => 'document',
            'priority' => 'urgent',
            'description' => 'Your '.$doc->document_type.' needs a clear, valid re-upload. Reason given: '.$reason,
            'data' => [
                'document_id' => $doc->id,
                'document_type' => $doc->document_type,
                'rejection_reason' => $reason,
            ],
            'action_type' => 'upload_document',
            'action_url' => route('documents.index'),
            'relevance_score' => 98,
            'expires_at' => now()->addDays(14),
        ]);
    }
}
