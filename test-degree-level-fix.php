<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\User;

echo "Testing the ProfileAssessmentService query that was failing...\n\n";

try {
    $user = User::first();
    
    if (!$user) {
        echo "❌ No user found\n";
        exit(1);
    }
    
    echo "User: {$user->name} (ID: {$user->id})\n";
    
    // Test the exact query from ProfileAssessmentService line 427
    $hasHigherEducation = $user->educations()->whereIn('degree_level', ['bachelors', 'masters', 'phd'])->exists();
    
    echo "✅ Query executed successfully!\n";
    echo "Has higher education: " . ($hasHigherEducation ? 'Yes' : 'No') . "\n";
    
    // Test the other queries too
    $hasMastersOrPhd = $user->educations()->whereIn('degree_level', ['masters', 'phd'])->exists();
    echo "Has masters/phd: " . ($hasMastersOrPhd ? 'Yes' : 'No') . "\n";
    
    $hasBachelorsOrMasters = $user->educations()->whereIn('degree_level', ['bachelors', 'masters'])->exists();
    echo "Has bachelors/masters: " . ($hasBachelorsOrMasters ? 'Yes' : 'No') . "\n";
    
    echo "\n✅ All ProfileAssessmentService degree_level queries work correctly!\n";
    
} catch (\Illuminate\Database\QueryException $e) {
    echo "❌ QueryException occurred:\n";
    echo "Error: " . $e->getMessage() . "\n";
    echo "SQL: " . $e->getSql() . "\n";
    exit(1);
} catch (\Exception $e) {
    echo "❌ Exception occurred:\n";
    echo "Error: " . $e->getMessage() . "\n";
    exit(1);
}
