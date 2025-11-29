#!/usr/bin/env php
<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\ServiceModule;

$activeServices = ServiceModule::where('is_active', true)
    ->orderBy('id')
    ->get(['id', 'name', 'slug', 'service_type', 'route_prefix', 'controller']);

echo "\n";
echo "═══════════════════════════════════════════════════════════════\n";
echo "ACTIVE SERVICES ({$activeServices->count()} total)\n";
echo "═══════════════════════════════════════════════════════════════\n\n";

foreach ($activeServices as $service) {
    echo "{$service->id}. {$service->name}\n";
    echo "   Slug: {$service->slug}\n";
    echo "   Type: {$service->service_type}\n";
    echo "   Route: {$service->route_prefix}\n";
    echo "   Controller: {$service->controller}\n\n";
}

echo "═══════════════════════════════════════════════════════════════\n";
