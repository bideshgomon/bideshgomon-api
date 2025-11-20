<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

echo "\n=== USERS TABLE SCHEMA ===\n\n";
$columns = Schema::getColumnListing('users');
echo "Columns: " . implode(', ', $columns) . "\n\n";

echo "=== RAW SQL QUERY ===\n\n";
$result = DB::table('users')->where('id', 1)->first();
echo "Raw user data:\n";
print_r($result);

echo "\n=== ELOQUENT WITH RELATIONSHIP ===\n\n";
$user = \App\Models\User::with('role')->find(1);
echo "Eloquent user:\n";
print_r($user->getAttributes());
echo "\nRole relationship:\n";
print_r($user->getRelation('role'));

echo "\n";
