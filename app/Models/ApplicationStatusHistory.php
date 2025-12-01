<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ApplicationStatusHistory extends Model
{
    protected $table = 'application_status_history';

    protected $fillable = [
        'application_id',
        'changed_by',
        'from_status',
        'to_status',
        'notes',
        'metadata',
    ];

    protected $casts = [
        'metadata' => 'array',
    ];

    /**
     * Get the application this history belongs to
     */
    public function application(): BelongsTo
    {
        return $this->belongsTo(ServiceApplication::class, 'application_id');
    }

    /**
     * Get the user who made this change
     */
    public function changer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'changed_by');
    }

    /**
     * Get status label with color
     */
    public function getStatusBadge(string $status): array
    {
        $badges = [
            'draft' => ['label' => 'Draft', 'color' => 'gray'],
            'pending' => ['label' => 'Pending Review', 'color' => 'yellow'],
            'under_review' => ['label' => 'Under Review', 'color' => 'blue'],
            'additional_info' => ['label' => 'Info Needed', 'color' => 'orange'],
            'approved' => ['label' => 'Approved', 'color' => 'green'],
            'rejected' => ['label' => 'Rejected', 'color' => 'red'],
            'cancelled' => ['label' => 'Cancelled', 'color' => 'gray'],
            'completed' => ['label' => 'Completed', 'color' => 'green'],
        ];

        return $badges[$status] ?? ['label' => ucfirst($status), 'color' => 'gray'];
    }
}
