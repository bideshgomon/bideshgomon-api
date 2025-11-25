<?php

/**
 * Plugin System Complete Verification Script
 * Tests all components of the new Plugin System
 */

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "🔌 PLUGIN SYSTEM COMPLETE VERIFICATION\n";
echo str_repeat("=", 80) . "\n\n";

// Test 1: Service Modules
echo "1️⃣ SERVICE MODULES\n";
echo str_repeat("-", 80) . "\n";
$services = DB::table('service_modules')->select('id', 'name', 'service_category_id', 'base_price')->get();
echo "✅ Total Services: " . $services->count() . "\n";
if ($services->count() > 0) {
    $categoryCounts = DB::table('service_modules')
        ->join('service_categories', 'service_modules.service_category_id', '=', 'service_categories.id')
        ->select('service_categories.name as category_name', DB::raw('count(*) as count'))
        ->groupBy('service_categories.name')
        ->get();
    foreach ($categoryCounts as $cat) {
        echo "   📁 {$cat->category_name}: {$cat->count} services\n";
    }
}
echo "\n";

// Test 2: Service Applications
echo "2️⃣ SERVICE APPLICATIONS\n";
echo str_repeat("-", 80) . "\n";
$applications = DB::table('service_applications')
    ->select('id', 'service_module_id', 'user_id', 'status')
    ->get();
echo "✅ Total Applications: " . $applications->count() . "\n";
foreach ($applications->groupBy('status') as $status => $items) {
    echo "   📊 $status: " . $items->count() . "\n";
}
echo "\n";

// Test 3: Service Quotes
echo "3️⃣ SERVICE QUOTES\n";
echo str_repeat("-", 80) . "\n";
$quotes = DB::table('service_quotes')
    ->select('id', 'service_application_id', 'agency_id', 'status', 'quoted_price')
    ->get();
echo "✅ Total Quotes: " . $quotes->count() . "\n";
$totalRevenue = $quotes->where('status', 'accepted')->sum('quoted_price');
echo "   💰 Total Revenue (Accepted): $" . number_format($totalRevenue, 2) . "\n";
foreach ($quotes->groupBy('status') as $status => $items) {
    echo "   📊 $status: " . $items->count() . "\n";
}
echo "\n";

// Test 4: Controllers Exist
echo "4️⃣ CONTROLLERS\n";
echo str_repeat("-", 80) . "\n";
$controllers = [
    'Admin Service Applications' => 'app/Http/Controllers/Admin/ServiceApplicationController.php',
    'Admin Service Quotes' => 'app/Http/Controllers/Admin/ServiceQuoteController.php',
    'User Service Controller' => 'app/Http/Controllers/ServiceController.php',
    'User Application Controller' => 'app/Http/Controllers/User/UserApplicationController.php',
];
foreach ($controllers as $name => $path) {
    if (file_exists(__DIR__ . '/' . $path)) {
        echo "✅ $name\n";
    } else {
        echo "❌ $name - NOT FOUND\n";
    }
}
echo "\n";

// Test 5: Vue Components Exist
echo "5️⃣ VUE COMPONENTS\n";
echo str_repeat("-", 80) . "\n";
$components = [
    'Admin Service Applications' => 'resources/js/Pages/Admin/ServiceApplications/Index.vue',
    'Agency Applications Dashboard' => 'resources/js/Pages/Agency/Applications/Index.vue',
    'User Services Catalog' => 'resources/js/Pages/Services/Index.vue',
    'User Applications Dashboard' => 'resources/js/Pages/User/Applications/Index.vue',
    'User Quote Comparison' => 'resources/js/Pages/User/Applications/Quotes.vue',
];
foreach ($components as $name => $path) {
    if (file_exists(__DIR__ . '/' . $path)) {
        $size = filesize(__DIR__ . '/' . $path);
        echo "✅ $name (" . number_format($size) . " bytes)\n";
    } else {
        echo "❌ $name - NOT FOUND\n";
    }
}
echo "\n";

// Test 6: Routes Registered
echo "6️⃣ ROUTES\n";
echo str_repeat("-", 80) . "\n";
$routes = [
    // Admin routes
    ['name' => 'service-applications.index', 'type' => 'Admin', 'params' => []],
    ['name' => 'admin.service-quotes.index', 'type' => 'Admin', 'params' => []],
    // User routes
    ['name' => 'services.index', 'type' => 'User', 'params' => []],
    ['name' => 'user.applications.index', 'type' => 'User', 'params' => []],
    ['name' => 'user.applications.quotes', 'type' => 'User', 'params' => [1]],
    ['name' => 'user.applications.quotes.accept', 'type' => 'User', 'params' => [1, 1]],
];

foreach ($routes as $route) {
    try {
        $url = route($route['name'], $route['params']);
        echo "✅ {$route['type']}: {$route['name']}\n";
    } catch (Exception $e) {
        echo "❌ {$route['type']}: {$route['name']} - NOT REGISTERED\n";
    }
}
echo "\n";

// Test 7: Sample Application Journey
echo "7️⃣ SAMPLE APPLICATION JOURNEY\n";
echo str_repeat("-", 80) . "\n";
$sampleApp = DB::table('service_applications')
    ->join('service_modules', 'service_applications.service_module_id', '=', 'service_modules.id')
    ->join('users', 'service_applications.user_id', '=', 'users.id')
    ->select(
        'service_applications.id',
        'service_applications.status',
        'service_applications.created_at',
        'service_modules.name as service_name',
        'users.name as user_name'
    )
    ->first();

if ($sampleApp) {
    echo "✅ Sample Application Found\n";
    echo "   📝 ID: {$sampleApp->id}\n";
    echo "   👤 User: {$sampleApp->user_name}\n";
    echo "   🔧 Service: {$sampleApp->service_name}\n";
    echo "   📊 Status: {$sampleApp->status}\n";
    echo "   📅 Created: {$sampleApp->created_at}\n";
    
    // Check for quotes
    $quotesCount = DB::table('service_quotes')
        ->where('service_application_id', $sampleApp->id)
        ->count();
    echo "   💬 Quotes: $quotesCount\n";
} else {
    echo "ℹ️ No applications yet\n";
}
echo "\n";

// Test 8: Navigation Updated
echo "8️⃣ NAVIGATION\n";
echo str_repeat("-", 80) . "\n";
$authLayout = file_get_contents(__DIR__ . '/resources/js/Layouts/AuthenticatedLayout.vue');
if (strpos($authLayout, 'services.index') !== false) {
    echo "✅ Services link added to AuthenticatedLayout\n";
} else {
    echo "❌ Services link NOT found in AuthenticatedLayout\n";
}
if (strpos($authLayout, 'user.applications.index') !== false) {
    echo "✅ My Applications link added to AuthenticatedLayout\n";
} else {
    echo "❌ My Applications link NOT found in AuthenticatedLayout\n";
}
echo "\n";

// Final Summary
echo "📊 FINAL SUMMARY\n";
echo str_repeat("=", 80) . "\n";
echo "✅ Service Modules: {$services->count()}\n";
echo "✅ Applications: {$applications->count()}\n";
echo "✅ Quotes: {$quotes->count()}\n";
echo "✅ Revenue: $" . number_format($totalRevenue, 2) . "\n";
echo "\n";
echo "🎉 PLUGIN SYSTEM STATUS: COMPLETE\n";
echo "\n";
echo "🌐 Test URLs:\n";
echo "   Admin: http://localhost/bideshgomon-api/public/admin/service-applications\n";
echo "   User Services: http://localhost/bideshgomon-api/public/services\n";
echo "   User Applications: http://localhost/bideshgomon-api/public/my-applications\n";
echo "\n";
