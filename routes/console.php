<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Lightweight production smoke test (public + protected redirect expectations)
Artisan::command('smoke:test {--base-url=}', function () {
    $baseUrl = rtrim($this->option('base-url') ?: env('SMOKE_TEST_BASE_URL', env('APP_URL')), '/');
    if (!$baseUrl) {
        $this->error('No base URL provided. Use --base-url= or set SMOKE_TEST_BASE_URL in .env');
        return 1;
    }

    $this->info('Running smoke tests against: ' . $baseUrl);

    $tests = [
        ['GET','/','expect'=>200,'label'=>'Home'],
        ['GET','/login','expect'=>200,'label'=>'Login page'],
        ['GET','/register','expect'=>200,'label'=>'Register page'],
        ['GET','/robots.txt','expect'=>200,'label'=>'Robots'],
        // Protected (should redirect to login when unauthenticated)
        ['GET','/profile/assessment','expect'=>302,'label'=>'Profile Assessment (redirect)'],
        ['GET','/admin/seo-settings','expect'=>302,'label'=>'Admin SEO Settings (redirect)'],
    ];

    $failures = 0;
    foreach ($tests as $t) {
        [$method,$path,$expect,$label] = [$t[0],$t[1],$t['expect'],$t['label']];
        $url = $baseUrl . $path;
        try {
            $response = Illuminate\Support\Facades\Http::withoutVerifying()->withOptions([
                'allow_redirects' => false,
                'timeout' => 10,
            ])->send($method, $url);
            $status = $response->status();
            $ok = $status === $expect || ($expect === 200 && $status === 204);
            if (!$ok) { $failures++; }
            $this->line(sprintf('%-35s %-20s Status: %3d (expected %3d) %s', $label, $path, $status, $expect, $ok ? '✓' : '✗'));
        } catch (Throwable $e) {
            $failures++;
            $this->line(sprintf('%-35s %-20s ERROR: %s', $label, $path, $e->getMessage()));
        }
    }

    $this->newLine();
    $this->info('Summary');
    $passed = count($tests) - $failures;
    $this->line("Passed: $passed / " . count($tests));
    if ($failures === 0) {
        $this->info('All smoke tests passed.');
    } else {
        $this->error($failures . ' failure(s)');
    }

    return $failures === 0 ? 0 : 2;
})->purpose('Run a quick smoke test against production/public endpoints');
