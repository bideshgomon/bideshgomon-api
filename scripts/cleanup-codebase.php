<?php

require __DIR__.'/vendor/autoload.php';

echo "\n";
echo "═══════════════════════════════════════════════════════════════\n";
echo "  🧹 PLATFORM CLEANUP UTILITY                                   \n";
echo "═══════════════════════════════════════════════════════════════\n\n";

// Scan for console statements in Vue files
echo "1️⃣ SCANNING FOR DEBUG STATEMENTS\n";
echo "──────────────────────────────────────────────────────────────\n";

$vueFiles = glob('resources/js/**/*.vue', GLOB_BRACE);
$jsFiles = glob('resources/js/**/*.js', GLOB_BRACE);
$allFiles = array_merge($vueFiles, $jsFiles);

$consoleStatements = [];
$todoComments = [];

foreach ($allFiles as $file) {
    $content = file_get_contents($file);
    $lines = explode("\n", $content);
    
    foreach ($lines as $lineNum => $line) {
        // Check for console statements
        if (preg_match('/console\.(log|error|warn|debug|info)\(/', $line)) {
            $consoleStatements[] = [
                'file' => str_replace('resources/js/', '', $file),
                'line' => $lineNum + 1,
                'content' => trim($line)
            ];
        }
        
        // Check for TODO/FIXME comments
        if (preg_match('/(TODO|FIXME|XXX|HACK|TEMP):/i', $line)) {
            $todoComments[] = [
                'file' => str_replace('resources/js/', '', $file),
                'line' => $lineNum + 1,
                'content' => trim($line)
            ];
        }
    }
}

echo "Found " . count($consoleStatements) . " console statements\n";
echo "Found " . count($todoComments) . " TODO/FIXME comments\n\n";

if (count($consoleStatements) > 0) {
    echo "Console Statements (first 10):\n";
    foreach (array_slice($consoleStatements, 0, 10) as $stmt) {
        echo "  • {$stmt['file']}:{$stmt['line']}\n";
        echo "    " . substr($stmt['content'], 0, 80) . "...\n";
    }
    echo "\n";
}

// Scan for temporary/demo files
echo "2️⃣ SCANNING FOR TEMPORARY FILES\n";
echo "──────────────────────────────────────────────────────────────\n";

$tempFiles = [
    'check-countries.php',
    'create-test-data.php',
    'deep-scan-analysis.php',
    'demo-plugin-system.php',
    'verify-plugin-system.php',
    'verify-plugin-frontend.php',
    'verify-improvements.php',
    'add-phase1-services.php',
    'add-priority-bd-services.php',
];

$foundTempFiles = [];
foreach ($tempFiles as $file) {
    if (file_exists($file)) {
        $foundTempFiles[] = $file;
        $size = filesize($file);
        echo "  ✓ Found: {$file} (" . number_format($size / 1024, 2) . " KB)\n";
    }
}

echo "\nFound " . count($foundTempFiles) . " temporary files\n\n";

// Scan for backup files
echo "3️⃣ SCANNING FOR BACKUP FILES\n";
echo "──────────────────────────────────────────────────────────────\n";

$backupPatterns = [
    'resources/js/**/*.backup.vue',
    'resources/js/**/*.backup2.vue',
    'resources/js/**/*.old.vue',
    'resources/js/**/*-old.vue',
];

$backupFiles = [];
foreach ($backupPatterns as $pattern) {
    $files = glob($pattern, GLOB_BRACE);
    foreach ($files as $file) {
        $backupFiles[] = str_replace('resources/js/', '', $file);
        echo "  ✓ Found: " . basename($file) . "\n";
    }
}

echo "\nFound " . count($backupFiles) . " backup files\n\n";

// Scan for TypeError-prone patterns
echo "4️⃣ SCANNING FOR TYPEERROR PATTERNS\n";
echo "──────────────────────────────────────────────────────────────\n";

$typeErrorPatterns = [
    '/\.\w+\.map\(/',  // .data.map()
    '/\.\w+\.filter\(/',  // .data.filter()
    '/\.\w+\.reduce\(/',  // .data.reduce()
    '/\.\w+\.find\(/',  // .data.find()
];

$unsafeAccess = [];
foreach ($vueFiles as $file) {
    $content = file_get_contents($file);
    foreach ($typeErrorPatterns as $pattern) {
        if (preg_match_all($pattern, $content, $matches, PREG_OFFSET_CAPTURE)) {
            foreach ($matches[0] as $match) {
                // Find line number
                $lineNum = substr_count(substr($content, 0, $match[1]), "\n") + 1;
                $unsafeAccess[] = [
                    'file' => str_replace('resources/js/', '', $file),
                    'line' => $lineNum,
                    'pattern' => $match[0]
                ];
            }
        }
    }
}

echo "Found " . count($unsafeAccess) . " potential TypeError locations\n\n";

// Scan for button text wrapping issues
echo "5️⃣ SCANNING FOR BUTTON TEXT ISSUES\n";
echo "──────────────────────────────────────────────────────────────\n";

$longButtonTexts = [];
foreach ($vueFiles as $file) {
    $content = file_get_contents($file);
    if (preg_match_all('/>(Service Applications|Admin Panel)</i', $content, $matches, PREG_OFFSET_CAPTURE)) {
        foreach ($matches[0] as $match) {
            $lineNum = substr_count(substr($content, 0, $match[1]), "\n") + 1;
            $longButtonTexts[] = [
                'file' => str_replace('resources/js/', '', $file),
                'line' => $lineNum,
                'text' => $match[0]
            ];
        }
    }
}

echo "Found " . count($longButtonTexts) . " button text wrapping issues\n";
foreach ($longButtonTexts as $btn) {
    echo "  • {$btn['file']}:{$btn['line']} - {$btn['text']}\n";
}
echo "\n";

// Summary Report
echo "═══════════════════════════════════════════════════════════════\n";
echo "  📊 CLEANUP SUMMARY                                            \n";
echo "═══════════════════════════════════════════════════════════════\n\n";

echo "Issues Found:\n";
echo "  • Console Statements: " . count($consoleStatements) . "\n";
echo "  • TODO/FIXME Comments: " . count($todoComments) . "\n";
echo "  • Temporary Files: " . count($foundTempFiles) . "\n";
echo "  • Backup Files: " . count($backupFiles) . "\n";
echo "  • TypeError Patterns: " . count($unsafeAccess) . "\n";
echo "  • Button Text Issues: " . count($longButtonTexts) . "\n\n";

echo "Priority Actions:\n";
echo "  1. Remove " . count($consoleStatements) . " console statements\n";
echo "  2. Delete " . count($foundTempFiles) . " temporary files\n";
echo "  3. Delete " . count($backupFiles) . " backup files\n";
echo "  4. Fix " . count($unsafeAccess) . " TypeError patterns\n";
echo "  5. Fix " . count($longButtonTexts) . " button text issues\n\n";

// Check mobile responsiveness
echo "6️⃣ MOBILE RESPONSIVE CHECK\n";
echo "──────────────────────────────────────────────────────────────\n";

$dashboards = [
    'resources/js/Pages/Dashboard.vue',
    'resources/js/Pages/Admin/Dashboard.vue',
    'resources/js/Pages/Agency/Dashboard.vue',
];

foreach ($dashboards as $dashboard) {
    if (file_exists($dashboard)) {
        $content = file_get_contents($dashboard);
        
        // Check for responsive classes
        $hasResponsive = preg_match('/\b(sm:|md:|lg:|xl:)/', $content);
        $hasGrid = preg_match('/\bgrid(-cols)?/', $content);
        $hasFlex = preg_match('/\bflex\b/', $content);
        
        echo "  " . basename($dashboard) . ":\n";
        echo "    Responsive Classes: " . ($hasResponsive ? '✓' : '✗') . "\n";
        echo "    Grid Layout: " . ($hasGrid ? '✓' : '✗') . "\n";
        echo "    Flex Layout: " . ($hasFlex ? '✓' : '✗') . "\n";
    }
}

echo "\n";
echo "═══════════════════════════════════════════════════════════════\n";
echo "  ✅ SCAN COMPLETE                                              \n";
echo "═══════════════════════════════════════════════════════════════\n\n";

echo "Next Steps:\n";
echo "  1. Review cleanup-report.txt\n";
echo "  2. Run: php cleanup-execute.php (to remove files)\n";
echo "  3. Fix console statements manually\n";
echo "  4. Add null checks for TypeError patterns\n";
echo "  5. Fix button text wrapping\n";
echo "  6. Test mobile responsive\n\n";

// Save detailed report
$report = "# Platform Cleanup Report\n\n";
$report .= "Generated: " . date('Y-m-d H:i:s') . "\n\n";

$report .= "## Console Statements (" . count($consoleStatements) . ")\n\n";
foreach ($consoleStatements as $stmt) {
    $report .= "- {$stmt['file']}:{$stmt['line']}\n";
    $report .= "  ```\n  {$stmt['content']}\n  ```\n\n";
}

$report .= "\n## TODO/FIXME Comments (" . count($todoComments) . ")\n\n";
foreach ($todoComments as $todo) {
    $report .= "- {$todo['file']}:{$todo['line']}\n";
    $report .= "  ```\n  {$todo['content']}\n  ```\n\n";
}

$report .= "\n## Button Text Issues (" . count($longButtonTexts) . ")\n\n";
foreach ($longButtonTexts as $btn) {
    $report .= "- {$btn['file']}:{$btn['line']}: {$btn['text']}\n";
}

file_put_contents('cleanup-report.txt', $report);
echo "Detailed report saved to: cleanup-report.txt\n\n";
