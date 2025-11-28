<?php

echo "\n";
echo "═══════════════════════════════════════════════════════════════\n";
echo "  🗑️  CLEANUP EXECUTION - REMOVE TEMPORARY FILES                \n";
echo "═══════════════════════════════════════════════════════════════\n\n";

$filesToRemove = [
    'check-countries.php',
    'create-test-data.php',
    'deep-scan-analysis.php',
    'demo-plugin-system.php',
    'verify-plugin-system.php',
    'verify-plugin-frontend.php',
    // 'verify-improvements.php',  // Keep this one, it's useful
];

$removed = 0;
$failed = 0;

foreach ($filesToRemove as $file) {
    if (file_exists($file)) {
        try {
            if (unlink($file)) {
                echo "✓ Removed: {$file}\n";
                $removed++;
            } else {
                echo "✗ Failed: {$file}\n";
                $failed++;
            }
        } catch (Exception $e) {
            echo "✗ Error removing {$file}: " . $e->getMessage() . "\n";
            $failed++;
        }
    } else {
        echo "⊘ Not found: {$file}\n";
    }
}

echo "\n";
echo "═══════════════════════════════════════════════════════════════\n";
echo "Summary: {$removed} removed, {$failed} failed\n";
echo "═══════════════════════════════════════════════════════════════\n\n";
