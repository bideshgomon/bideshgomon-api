<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\ServiceModule;

echo "Service Modules with Service Types:\n";
echo str_repeat("=", 80) . "\n\n";

$services = ServiceModule::select('name', 'slug', 'service_type')
    ->orderBy('id')
    ->get();

$grouped = $services->groupBy('service_type');

foreach ($grouped as $type => $typeServices) {
    echo strtoupper(str_replace('_', ' ', $type)) . " ({$typeServices->count()} services):\n";
    echo str_repeat("-", 80) . "\n";
    
    foreach ($typeServices as $service) {
        echo "  ✓ {$service->name} ({$service->slug})\n";
    }
    
    echo "\n";
}

echo "\n" . str_repeat("=", 80) . "\n";
echo "Total Services: " . $services->count() . "\n";
echo "Service Types:\n";
foreach ($grouped as $type => $typeServices) {
    echo "  - " . strtoupper(str_replace('_', ' ', $type)) . ": {$typeServices->count()}\n";
}
