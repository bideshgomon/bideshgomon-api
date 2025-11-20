<?php
require __DIR__.'/vendor/autoload.php';
$app = require __DIR__.'/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

\Mail::raw('Password reset test email from BideshGomon', function($m) {
    $m->to('bideshgomon@gmail.com')->subject('Test Password Reset Delivery');
});

echo "Sent test email script executed\n";