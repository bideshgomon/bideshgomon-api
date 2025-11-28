<?php

namespace App\Policies;

use App\Models\DocumentScan;
use App\Models\User;

class DocumentScanPolicy
{
    /**
     * Determine if the user can view the scan.
     */
    public function view(User $user, DocumentScan $scan): bool
    {
        return $user->id === $scan->user_id || $user->hasRole('admin');
    }

    /**
     * Determine if the user can update the scan.
     */
    public function update(User $user, DocumentScan $scan): bool
    {
        return $user->id === $scan->user_id || $user->hasRole('admin');
    }

    /**
     * Determine if the user can delete the scan.
     */
    public function delete(User $user, DocumentScan $scan): bool
    {
        return $user->id === $scan->user_id || $user->hasRole('admin');
    }
}
