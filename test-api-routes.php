#!/usr/bin/env php
<?php

/**
 * API Endpoint Testing Script
 * Tests the REST API for all 5 new services
 */

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\User;
use App\Models\StudentVisa;
use App\Models\WorkVisa;
use App\Models\Translation;
use Illuminate\Support\Facades\Route;

echo "\n";
echo "╔════════════════════════════════════════════════════════════════╗\n";
echo "║              API ENDPOINT TESTING - ROUTE CHECK                 ║\n";
echo "╚════════════════════════════════════════════════════════════════╝\n";
echo "\n";

// Get test user
$user = User::where('email', 'testuser@bideshgomon.com')->first();

if (!$user) {
    echo "❌ ERROR: Test user not found. Run test-live-applications.php first!\n";
    exit(1);
}

echo "✅ Test User: {$user->name} (ID: {$user->id})\n\n";

// Get existing applications
$studentVisa = StudentVisa::where('user_id', $user->id)->first();
$workVisa = WorkVisa::where('user_id', $user->id)->first();
$translation = Translation::where('user_id', $user->id)->first();

if (!$studentVisa || !$workVisa || !$translation) {
    echo "❌ ERROR: Test applications not found. Run test-live-applications.php first!\n";
    exit(1);
}

echo "═══════════════════════════════════════════════════════════════\n";
echo "📍 API ROUTES VERIFICATION\n";
echo "═══════════════════════════════════════════════════════════════\n\n";

// Check if API routes exist
$services = [
    'student-visa-applications' => [
        'name' => 'Student Visa',
        'model_id' => $studentVisa->id,
    ],
    'work-visa-applications' => [
        'name' => 'Work Visa',
        'model_id' => $workVisa->id,
    ],
    'translation-applications' => [
        'name' => 'Translation',
        'model_id' => $translation->id,
    ],
    'attestation-applications' => [
        'name' => 'Attestation',
        'model_id' => 1,
    ],
    'hajj-umrah-applications' => [
        'name' => 'Hajj & Umrah',
        'model_id' => 1,
    ],
];

$allRoutes = Route::getRoutes();
$apiRoutesFound = [];

foreach ($services as $slug => $info) {
    echo "🔍 Checking {$info['name']} API routes...\n";
    
    $routes = [
        "api.{$slug}.index" => "GET /api/{$slug}",
        "api.{$slug}.store" => "POST /api/{$slug}",
        "api.{$slug}.show" => "GET /api/{$slug}/{id}",
        "api.{$slug}.update" => "PUT /api/{$slug}/{id}",
        "api.{$slug}.destroy" => "DELETE /api/{$slug}/{id}",
    ];
    
    $foundCount = 0;
    foreach ($routes as $name => $uri) {
        if ($allRoutes->hasNamedRoute($name)) {
            echo "   ✅ {$uri}\n";
            $foundCount++;
            $apiRoutesFound[$slug][] = $name;
        } else {
            echo "   ❌ {$uri} - NOT FOUND\n";
        }
    }
    
    echo "   Status: {$foundCount}/5 routes found\n\n";
}

echo "═══════════════════════════════════════════════════════════════\n";
echo "📊 SUMMARY\n";
echo "═══════════════════════════════════════════════════════════════\n\n";

$totalServices = count($services);
$servicesWithRoutes = count($apiRoutesFound);

echo "Services Tested: {$totalServices}\n";
echo "Services with API Routes: {$servicesWithRoutes}\n\n";

if ($servicesWithRoutes === $totalServices) {
    echo "✅ SUCCESS: All API routes are registered!\n";
} else {
    echo "⚠️  WARNING: Some API routes are missing!\n";
}

echo "\n";
echo "═══════════════════════════════════════════════════════════════\n";
echo "📋 TEST DATA AVAILABLE\n";
echo "═══════════════════════════════════════════════════════════════\n\n";

echo "Student Visa Application ID: {$studentVisa->id}\n";
echo "Work Visa Application ID: {$workVisa->id}\n";
echo "Translation Application ID: {$translation->id}\n\n";

echo "To test API endpoints manually:\n";
echo "1. Use Postman or curl\n";
echo "2. Authenticate as user ID {$user->id}\n";
echo "3. Test endpoints:\n";
echo "   - GET  /api/student-visa-applications\n";
echo "   - GET  /api/student-visa-applications/{$studentVisa->id}\n";
echo "   - PUT  /api/student-visa-applications/{$studentVisa->id}\n";
echo "   - DELETE /api/student-visa-applications/{$studentVisa->id}\n\n";

echo "═══════════════════════════════════════════════════════════════\n";
echo "🎉 TEST COMPLETE!\n";
echo "═══════════════════════════════════════════════════════════════\n\n";
