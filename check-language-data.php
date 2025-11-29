<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "Languages count: " . App\Models\Language::count() . PHP_EOL;
echo "Language Tests count: " . App\Models\LanguageTest::count() . PHP_EOL;

if (App\Models\Language::count() > 0) {
    echo "\nSample Languages:" . PHP_EOL;
    foreach (App\Models\Language::take(5)->get() as $lang) {
        echo "  ID: {$lang->id}, Name: {$lang->name}" . PHP_EOL;
    }
}

if (App\Models\LanguageTest::count() > 0) {
    echo "\nSample Language Tests:" . PHP_EOL;
    foreach (App\Models\LanguageTest::take(5)->get() as $test) {
        echo "  ID: {$test->id}, Name: {$test->name}" . PHP_EOL;
    }
} else {
    echo "\n⚠️  NO LANGUAGE DATA FOUND - Need to run seeders!" . PHP_EOL;
}
