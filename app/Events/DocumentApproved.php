<?php

namespace App\Events;

use App\Models\UserDocument;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class DocumentApproved
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(public UserDocument $document) {}
}
