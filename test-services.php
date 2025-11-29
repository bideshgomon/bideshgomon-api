<?php
// Test script to verify database tables and create test data

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "=== DATABASE TABLES CHECK ===\n\n";

// Check if new service tables exist
$tables = ['student_visas', 'work_visas', 'translations', 'attestations', 'hajj_umrahs'];

foreach ($tables as $table) {
    try {
        $count = DB::table($table)->count();
        echo "✅ Table '$table' exists - Records: $count\n";
        
        // Show table structure
        $columns = DB::select("PRAGMA table_info($table)");
        echo "   Columns: " . implode(', ', array_column($columns, 'name')) . "\n\n";
    } catch (Exception $e) {
        echo "❌ Table '$table' does NOT exist or error: " . $e->getMessage() . "\n\n";
    }
}

echo "\n=== SERVICE APPLICATION CHECK ===\n\n";
try {
    $serviceApps = DB::table('service_applications')
        ->whereIn('service_module_slug', ['student-visa', 'work-visa', 'translation', 'attestation', 'hajj-umrah'])
        ->count();
    echo "Service Applications for new services: $serviceApps\n";
} catch (Exception $e) {
    echo "Error checking service_applications: " . $e->getMessage() . "\n";
}

echo "\n=== MODELS CHECK ===\n\n";
$models = [
    'StudentVisa' => App\Models\StudentVisa::class,
    'WorkVisa' => App\Models\WorkVisa::class,
    'Translation' => App\Models\Translation::class,
    'Attestation' => App\Models\Attestation::class,
    'HajjUmrah' => App\Models\HajjUmrah::class,
];

foreach ($models as $name => $class) {
    if (class_exists($class)) {
        try {
            $count = $class::count();
            echo "✅ Model '$name' exists - Records: $count\n";
        } catch (Exception $e) {
            echo "⚠️  Model '$name' exists but error: " . $e->getMessage() . "\n";
        }
    } else {
        echo "❌ Model '$name' does NOT exist\n";
    }
}

echo "\n=== CONTROLLERS CHECK ===\n\n";
$controllers = [
    'StudentVisaController' => 'app/Http/Controllers/Profile/StudentVisaController.php',
    'WorkVisaController' => 'app/Http/Controllers/Profile/WorkVisaController.php',
    'TranslationController' => 'app/Http/Controllers/Profile/TranslationController.php',
    'AttestationController' => 'app/Http/Controllers/Profile/AttestationController.php',
    'HajjUmrahController' => 'app/Http/Controllers/Profile/HajjUmrahController.php',
];

foreach ($controllers as $name => $path) {
    if (file_exists($path)) {
        echo "✅ Controller '$name' exists at $path\n";
    } else {
        echo "❌ Controller '$name' NOT found at $path\n";
    }
}

echo "\n=== VUE PAGES CHECK ===\n\n";
$vuePages = [
    'StudentVisa' => 'resources/js/Pages/Profile/StudentVisa',
    'WorkVisa' => 'resources/js/Pages/Profile/WorkVisa',
    'Translation' => 'resources/js/Pages/Profile/Translation',
    'Attestation' => 'resources/js/Pages/Profile/Attestation',
    'HajjUmrah' => 'resources/js/Pages/Profile/HajjUmrah',
];

foreach ($vuePages as $name => $dir) {
    if (is_dir($dir)) {
        $files = scandir($dir);
        $vueFiles = array_filter($files, fn($f) => str_ends_with($f, '.vue'));
        echo "✅ Vue pages for '$name': " . implode(', ', $vueFiles) . "\n";
    } else {
        echo "❌ Vue pages directory for '$name' NOT found\n";
    }
}

echo "\n=== TEST COMPLETE ===\n";
