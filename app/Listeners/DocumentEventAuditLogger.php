<?php

namespace App\Listeners;

use App\Events\DocumentApproved;
use App\Events\DocumentRejected;
use App\Models\SystemEvent;
use Illuminate\Contracts\Queue\ShouldQueue;

class DocumentEventAuditLogger implements ShouldQueue
{
    public function handleDocumentApproved(DocumentApproved $event): void
    {
        $doc = $event->document;
        $doc->load('reviewer');
        SystemEvent::create([
            'event_type' => 'document.approved',
            'user_id' => $doc->user_id,
            'target_type' => get_class($doc),
            'target_id' => $doc->id,
            'data' => [
                'status' => $doc->status,
                'reviewer_id' => $doc->reviewer?->id,
                'reviewer_name' => $doc->reviewer?->name,
                'document_type' => $doc->document_type,
                'expires_at' => $doc->expires_at,
            ],
        ]);
    }

    public function handleDocumentRejected(DocumentRejected $event): void
    {
        $doc = $event->document;
        $doc->load('reviewer');
        SystemEvent::create([
            'event_type' => 'document.rejected',
            'user_id' => $doc->user_id,
            'target_type' => get_class($doc),
            'target_id' => $doc->id,
            'data' => [
                'status' => $doc->status,
                'reviewer_id' => $doc->reviewer?->id,
                'reviewer_name' => $doc->reviewer?->name,
                'document_type' => $doc->document_type,
                'expires_at' => $doc->expires_at,
                'rejection_reason' => $event->reason,
            ],
        ]);
    }

    public function handle(object $event): void
    {
        if ($event instanceof DocumentApproved) {
            $this->handleDocumentApproved($event);
        } elseif ($event instanceof DocumentRejected) {
            $this->handleDocumentRejected($event);
        }
    }
}
