<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\UserPhoneNumber;
use App\Services\SmsService;

class TestSmsCommand extends Command
{
    protected $signature = 'sms:test {phoneNumberId? : ID of user_phone_numbers row} {--code= : Override code to send}';

    protected $description = 'Send a test verification SMS using configured gateway (Twilio if available, else log).';

    public function handle(SmsService $smsService): int
    {
        $id = $this->argument('phoneNumberId');
        $codeOverride = $this->option('code');

        $phone = $id
            ? UserPhoneNumber::find($id)
            : UserPhoneNumber::first();

        if (!$phone) {
            $this->error('No phone number found. Add one via profile first.');
            return self::FAILURE;
        }

        $code = $codeOverride ?: '123456';
        $sent = $smsService->sendVerificationCode($phone, $code);

        if ($sent) {
            $this->info('Test SMS dispatched to: '.$phone->country_code.$phone->phone_number);
            if (!$smsService->twilioConfigured()) {
                $this->warn('Twilio not fully configured; message logged only.');
            }
            return self::SUCCESS;
        }

        $this->error('Failed to send test SMS. Check logs.');
        return self::FAILURE;
    }
}
