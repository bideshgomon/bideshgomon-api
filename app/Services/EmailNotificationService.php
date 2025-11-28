<?php

namespace App\Services;

use App\Models\EmailLog;
use App\Models\EmailTemplate;
use App\Models\UserNotificationPreference;
use App\Jobs\SendEmailNotificationJob;

class EmailNotificationService
{
    public function sendFromTemplate(
        string $templateSlug,
        string $toEmail,
        array $data = [],
        ?int $userId = null,
        bool $queueEmail = true
    ): ?EmailLog {
        $template = EmailTemplate::where('slug', $templateSlug)
            ->where('is_active', true)
            ->first();

        if (!$template) {
            return null;
        }

        // Check user preferences
        if ($userId) {
            $notificationType = $this->getNotificationTypeFromTemplate($templateSlug);
            if (!UserNotificationPreference::shouldSendEmail($userId, $notificationType)) {
                return null;
            }
        }

        $subject = $template->renderSubject($data);
        $body = $template->render($data);

        return $this->send($toEmail, $subject, $body, $userId, $templateSlug, $queueEmail);
    }

    public function send(
        string $toEmail,
        string $subject,
        string $body,
        ?int $userId = null,
        ?string $templateSlug = null,
        bool $queueEmail = true
    ): EmailLog {
        $emailLog = EmailLog::create([
            'user_id' => $userId,
            'to_email' => $toEmail,
            'subject' => $subject,
            'body' => $body,
            'template_slug' => $templateSlug,
            'status' => 'pending',
        ]);

        if ($queueEmail) {
            SendEmailNotificationJob::dispatch($emailLog->id);
        }

        return $emailLog;
    }

    private function getNotificationTypeFromTemplate(string $templateSlug): string
    {
        $mapping = [
            'verification_approved' => 'verification_approved',
            'verification_rejected' => 'verification_rejected',
            'wallet_credited' => 'wallet_credited',
            'withdrawal_completed' => 'withdrawal_completed',
            'job_application_received' => 'job_application',
            'visa_application_update' => 'visa_application',
        ];

        return $mapping[$templateSlug] ?? 'general';
    }
}
