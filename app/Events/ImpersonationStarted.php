<?php

namespace App\Events;

use App\Models\AdminImpersonationLog;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ImpersonationStarted
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(public AdminImpersonationLog $log)
    {
    }
}
