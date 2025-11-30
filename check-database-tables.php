<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "=== DATABASE TABLE CHECK ===\n\n";

// Get existing tables
$tables = DB::select("SELECT TABLE_NAME FROM information_schema.TABLES WHERE TABLE_SCHEMA = 'bideshgomondb' ORDER BY TABLE_NAME");
$existingTables = array_map(fn($t) => $t->TABLE_NAME, $tables);

echo "Existing tables: " . count($existingTables) . "\n\n";

// Critical models to check
$criticalModels = [
    'User' => App\Models\User::class,
    'UserProfile' => App\Models\UserProfile::class,
    'Wallet' => App\Models\Wallet::class,
    'WalletTransaction' => App\Models\WalletTransaction::class,
    'Role' => App\Models\Role::class,
    'Referral' => App\Models\Referral::class,
    'Reward' => App\Models\Reward::class,
    'Skill' => App\Models\Skill::class,
    'Language' => App\Models\Language::class,
    'LanguageTest' => App\Models\LanguageTest::class,
    'Country' => App\Models\Country::class,
    'City' => App\Models\City::class,
    'Currency' => App\Models\Currency::class,
    'Airport' => App\Models\Airport::class,
    'ServiceApplication' => App\Models\ServiceApplication::class,
    'ServiceCategory' => App\Models\ServiceCategory::class,
    'VisaType' => App\Models\VisaType::class,
    'Agency' => App\Models\Agency::class,
    'AgencyType' => App\Models\AgencyType::class,
    'Degree' => App\Models\Degree::class,
    'InstitutionType' => App\Models\InstitutionType::class,
    'DocumentType' => App\Models\DocumentType::class,
    'Setting' => App\Models\Setting::class,
];

$missing = [];
$existing = [];

foreach ($criticalModels as $name => $class) {
    try {
        $model = new $class;
        $tableName = $model->getTable();
        
        if (!in_array($tableName, $existingTables)) {
            $missing[] = ['model' => $name, 'table' => $tableName, 'class' => $class];
            echo "❌ MISSING: {$name} -> {$tableName}\n";
        } else {
            try {
                $count = $class::count();
                $existing[] = ['model' => $name, 'table' => $tableName, 'count' => $count];
                echo "✅ EXISTS: {$name} -> {$tableName} ({$count} records)\n";
            } catch (Exception $e) {
                echo "⚠️  TABLE EXISTS BUT ERROR: {$name} -> {$tableName} - {$e->getMessage()}\n";
                $missing[] = ['model' => $name, 'table' => $tableName, 'class' => $class, 'error' => $e->getMessage()];
            }
        }
    } catch (Exception $e) {
        echo "❌ ERROR: {$name} - {$e->getMessage()}\n";
    }
}

echo "\n=== SUMMARY ===\n";
echo "Total models checked: " . count($criticalModels) . "\n";
echo "Tables existing: " . count($existing) . "\n";
echo "Tables missing: " . count($missing) . "\n\n";

if (count($missing) > 0) {
    echo "=== MISSING TABLES ===\n";
    foreach ($missing as $item) {
        echo "- {$item['table']} (Model: {$item['model']})\n";
    }
}
