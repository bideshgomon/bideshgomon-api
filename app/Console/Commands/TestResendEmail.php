<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class TestResendEmail extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'mail:test-resend {to? : Override recipient email address}';

    /**
     * The console command description.
     */
    protected $description = 'Send a simple test email through the configured Resend mailer.';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $to = $this->argument('to') ?? config('mail.from.address');

        try {
            Mail::raw('Resend integration test: '.now()->toDateTimeString().' (Asia/Dhaka)', function ($m) use ($to) {
                $m->to($to)->subject('Resend Integration Test');
            });
        } catch (\Throwable $e) {
            $this->error('Failed sending test email: '.$e->getMessage());
            return self::FAILURE;
        }

        $this->info('Dispatched test email to '.$to.' via mailer: '.config('mail.default'));
        return self::SUCCESS;
    }
}
