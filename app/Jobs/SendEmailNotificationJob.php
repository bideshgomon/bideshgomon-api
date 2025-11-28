<?php

namespace App\Jobs;

use App\Models\EmailLog;
use App\Models\EmailTemplate;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class SendEmailNotificationJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 3;
    public $timeout = 60;

    public function __construct(
        public int $emailLogId
    ) {}

    public function handle(): void
    {
        $emailLog = EmailLog::find($this->emailLogId);

        if (!$emailLog || $emailLog->status === 'sent') {
            return;
        }

        try {
            Mail::send([], [], function ($message) use ($emailLog) {
                $message->to($emailLog->to_email)
                    ->subject($emailLog->subject)
                    ->html($emailLog->body);
            });

            $emailLog->markAsSent();
        } catch (\Exception $e) {
            $emailLog->markAsFailed($e->getMessage());
            
            if ($this->attempts() >= $this->tries) {
                // Log final failure
                \Log::error('Email sending failed after ' . $this->tries . ' attempts', [
                    'email_log_id' => $emailLog->id,
                    'error' => $e->getMessage(),
                ]);
            }
        }
    }
}
