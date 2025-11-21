<?php

namespace App\Services;

use App\Models\UserDocument;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\UploadedFile;

class DocumentVerificationService
{
    /**
     * Upload a new document and create record.
     * Wraps storage + model creation in a transaction for consistency.
     *
     * @param int $userId
     * @param UploadedFile $file
     * @param string $documentType
     * @param array $options ['expires_at' => datetime|string|null, 'is_primary' => bool, 'meta' => array]
     */
    public function upload(int $userId, UploadedFile $file, string $documentType, array $options = []): UserDocument
    {
        return DB::transaction(function () use ($userId, $file, $documentType, $options) {
            // Validate type externally; here we assume it's valid.
            $path = $file->store('documents/'.$userId, 'public');
            $doc = new UserDocument();
            $doc->user_id = $userId;
            $doc->document_type = $documentType;
            $doc->original_filename = $file->getClientOriginalName();
            $doc->storage_path = $path;
            $doc->mime_type = $file->getClientMimeType();
            $doc->size_bytes = $file->getSize();
            $doc->status = UserDocument::STATUS_PENDING;
            $doc->expires_at = $options['expires_at'] ?? null;
            $doc->is_primary = (bool)($options['is_primary'] ?? false);
            $doc->meta = $options['meta'] ?? [];
            $doc->confidence_score = $options['confidence_score'] ?? 50;
            $doc->save();

            // If set primary, unset others of same type.
            if ($doc->is_primary) {
                UserDocument::where('user_id', $userId)
                    ->where('document_type', $documentType)
                    ->where('id', '!=', $doc->id)
                    ->update(['is_primary' => false]);
            }

            return $doc;
        });
    }

    public function approve(UserDocument $document, int $adminId): UserDocument
    {
        $document->markApproved($adminId);
        event(new \App\Events\DocumentApproved($document));
        return $document;
    }

    public function reject(UserDocument $document, int $adminId, string $reason): UserDocument
    {
        $document->markRejected($reason, $adminId);
        event(new \App\Events\DocumentRejected($document, $reason));
        return $document;
    }

    public function replace(UserDocument $document, UploadedFile $file, array $options = []): UserDocument
    {
        return DB::transaction(function () use ($document, $file, $options) {
            // Delete old file
            if ($document->storage_path) {
                Storage::disk('public')->delete($document->storage_path);
            }
            // Store new
            $path = $file->store('documents/'.$document->user_id, 'public');
            $document->original_filename = $file->getClientOriginalName();
            $document->storage_path = $path;
            $document->mime_type = $file->getClientMimeType();
            $document->size_bytes = $file->getSize();
            $document->status = UserDocument::STATUS_PENDING; // reset status
            $document->reviewed_by = null;
            $document->reviewed_at = null;
            $document->rejection_reason = null;
            if (array_key_exists('expires_at', $options)) {
                $document->expires_at = $options['expires_at'];
            }
            if (array_key_exists('is_primary', $options)) {
                $document->is_primary = (bool)$options['is_primary'];
            }
            if (array_key_exists('meta', $options)) {
                $document->meta = $options['meta'];
            }
            $document->save();

            if ($document->is_primary) {
                UserDocument::where('user_id', $document->user_id)
                    ->where('document_type', $document->document_type)
                    ->where('id', '!=', $document->id)
                    ->update(['is_primary' => false]);
            }

            return $document;
        });
    }

    public function delete(UserDocument $document): void
    {
        DB::transaction(function () use ($document) {
            if ($document->storage_path) {
                Storage::disk('public')->delete($document->storage_path);
            }
            $document->delete();
        });
    }
}
