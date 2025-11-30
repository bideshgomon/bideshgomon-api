<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

$databaseName = config('database.connections.mysql.database');

echo "🔬 DEEP SCHEMA MISMATCH SCANNER\n";
echo "═══════════════════════════════════════════════════════════════\n";
echo "Scanning for: Model-Database Schema Inconsistencies\n";
echo "Error Type: SQLSTATE[42S22] - Column not found\n";
echo "Database: {$databaseName}\n";
echo "═══════════════════════════════════════════════════════════════\n\n";

$issues = [];
$warnings = [];
$stats = [
    'total_models' => 0,
    'models_with_soft_deletes' => 0,
    'missing_deleted_at' => 0,
    'total_scope_checks' => 0,
    'scope_column_issues' => 0,
];

// Get all model files
$modelPath = app_path('Models');
$modelFiles = array_filter(glob($modelPath . '/*.php'), function($file) {
    return !str_contains($file, '_backup') && !str_contains($file, '.backup.');
});

echo "📋 Phase 1: Scanning Models for SoftDeletes Trait\n";
echo "──────────────────────────────────────────────────────────────\n";

foreach ($modelFiles as $file) {
    $fileName = basename($file, '.php');
    $stats['total_models']++;
    
    $content = file_get_contents($file);
    
    // Check if model uses SoftDeletes
    if (preg_match('/use\s+SoftDeletes;/i', $content)) {
        $stats['models_with_soft_deletes']++;
        
        // Get table name from model
        $className = "App\\Models\\{$fileName}";
        
        if (!class_exists($className)) {
            $warnings[] = "⚠️  Model class not found: {$className}";
            continue;
        }
        
        try {
            $model = new $className();
            $table = $model->getTable();
            
            // Check if deleted_at column exists
            if (Schema::hasTable($table)) {
                $columns = Schema::getColumnListing($table);
                
                if (!in_array('deleted_at', $columns)) {
                    $stats['missing_deleted_at']++;
                    $issues[] = [
                        'severity' => 'CRITICAL',
                        'type' => 'Missing SoftDelete Column',
                        'model' => $fileName,
                        'table' => $table,
                        'issue' => "Model uses SoftDeletes but table '{$table}' is missing 'deleted_at' column",
                        'fix' => "ALTER TABLE {$table} ADD COLUMN deleted_at TIMESTAMP NULL;",
                    ];
                    echo "❌ {$fileName} → {$table}: MISSING deleted_at\n";
                } else {
                    echo "✅ {$fileName} → {$table}: Has deleted_at\n";
                }
            } else {
                $warnings[] = "⚠️  Table not found: {$table} (for model {$fileName})";
            }
        } catch (Exception $e) {
            $warnings[] = "⚠️  Error checking {$fileName}: " . $e->getMessage();
        }
    }
}

echo "\n📋 Phase 2: Scanning for Query Scope Column References\n";
echo "──────────────────────────────────────────────────────────────\n";

foreach ($modelFiles as $file) {
    $fileName = basename($file, '.php');
    $content = file_get_contents($file);
    
    // Find all scope methods
    preg_match_all('/public\s+function\s+scope(\w+)\s*\([^)]*\$query[^)]*\)\s*{([^}]+)}/s', $content, $matches, PREG_SET_ORDER);
    
    foreach ($matches as $match) {
        $scopeName = $match[1];
        $scopeBody = $match[2];
        $stats['total_scope_checks']++;
        
        // Extract column names from where clauses
        preg_match_all('/->where\([\'"]([^\'"]+)[\'"]/', $scopeBody, $columnMatches);
        preg_match_all('/->whereIn\([\'"]([^\'"]+)[\'"]/', $scopeBody, $whereInMatches);
        preg_match_all('/->orderBy\([\'"]([^\'"]+)[\'"]/', $scopeBody, $orderByMatches);
        
        $referencedColumns = array_merge(
            $columnMatches[1] ?? [],
            $whereInMatches[1] ?? [],
            $orderByMatches[1] ?? []
        );
        
        if (!empty($referencedColumns)) {
            $className = "App\\Models\\{$fileName}";
            
            if (class_exists($className)) {
                try {
                    $model = new $className();
                    $table = $model->getTable();
                    
                    if (Schema::hasTable($table)) {
                        $columns = Schema::getColumnListing($table);
                        
                        foreach ($referencedColumns as $col) {
                            if (!in_array($col, $columns)) {
                                $stats['scope_column_issues']++;
                                $issues[] = [
                                    'severity' => 'CRITICAL',
                                    'type' => 'Scope References Missing Column',
                                    'model' => $fileName,
                                    'table' => $table,
                                    'scope' => "scope{$scopeName}",
                                    'column' => $col,
                                    'issue' => "Scope 'scope{$scopeName}()' references column '{$col}' which doesn't exist in table '{$table}'",
                                    'fix' => "Check column name or add column to table",
                                ];
                                echo "❌ {$fileName}::scope{$scopeName}() → '{$col}' NOT FOUND in {$table}\n";
                            }
                        }
                    }
                } catch (Exception $e) {
                    // Skip
                }
            }
        }
    }
}

echo "\n📋 Phase 3: Scanning for Fillable/Guarded Column Mismatches\n";
echo "──────────────────────────────────────────────────────────────\n";

foreach ($modelFiles as $file) {
    $fileName = basename($file, '.php');
    $content = file_get_contents($file);
    
    // Extract fillable columns
    if (preg_match('/protected\s+\$fillable\s*=\s*\[(.*?)\];/s', $content, $fillableMatch)) {
        $fillableStr = $fillableMatch[1];
        preg_match_all('/[\'"]([^\'"]+)[\'"]/', $fillableStr, $fillableColumns);
        
        $className = "App\\Models\\{$fileName}";
        
        if (class_exists($className)) {
            try {
                $model = new $className();
                $table = $model->getTable();
                
                if (Schema::hasTable($table)) {
                    $columns = Schema::getColumnListing($table);
                    
                    foreach ($fillableColumns[1] as $fillable) {
                        if (!in_array($fillable, $columns) && $fillable !== '*') {
                            $issues[] = [
                                'severity' => 'HIGH',
                                'type' => 'Fillable Column Missing',
                                'model' => $fileName,
                                'table' => $table,
                                'column' => $fillable,
                                'issue' => "Model has '{$fillable}' in \$fillable but column doesn't exist in table '{$table}'",
                                'fix' => "Remove from \$fillable or add column to table",
                            ];
                            echo "⚠️  {$fileName}: fillable '{$fillable}' NOT in {$table}\n";
                        }
                    }
                }
            } catch (Exception $e) {
                // Skip
            }
        }
    }
}

echo "\n📋 Phase 4: Scanning Controllers for Direct Column References\n";
echo "──────────────────────────────────────────────────────────────\n";

$controllerPaths = [
    app_path('Http/Controllers'),
    app_path('Http/Controllers/Admin'),
    app_path('Http/Controllers/User'),
];

$criticalPatterns = [
    '/->where\([\'"]([^\'"]+)[\'"]/' => 'where',
    '/->whereIn\([\'"]([^\'"]+)[\'"]/' => 'whereIn',
    '/->orderBy\([\'"]([^\'"]+)[\'"]/' => 'orderBy',
    '/->select\(\[?[\'"]([^\'"]+)[\'"]/' => 'select',
];

$controllerIssues = 0;

foreach ($controllerPaths as $controllerPath) {
    if (!is_dir($controllerPath)) continue;
    
    $controllerFiles = glob($controllerPath . '/*.php');
    
    foreach ($controllerFiles as $file) {
        $content = file_get_contents($file);
        $fileName = basename($file);
        
        // Try to detect which model is being used
        preg_match_all('/use\s+App\\\\Models\\\\(\w+);/', $content, $modelUses);
        
        foreach ($modelUses[1] as $modelName) {
            $className = "App\\Models\\{$modelName}";
            
            if (class_exists($className)) {
                try {
                    $model = new $className();
                    $table = $model->getTable();
                    
                    if (Schema::hasTable($table)) {
                        $columns = Schema::getColumnListing($table);
                        
                        foreach ($criticalPatterns as $pattern => $type) {
                            preg_match_all($pattern, $content, $matches);
                            
                            foreach ($matches[1] ?? [] as $col) {
                                if (!in_array($col, $columns)) {
                                    $controllerIssues++;
                                    $issues[] = [
                                        'severity' => 'HIGH',
                                        'type' => 'Controller Query Column Missing',
                                        'file' => $fileName,
                                        'model' => $modelName,
                                        'table' => $table,
                                        'column' => $col,
                                        'query_type' => $type,
                                        'issue' => "Controller '{$fileName}' queries column '{$col}' which doesn't exist in table '{$table}'",
                                        'fix' => "Fix column name or add column to table",
                                    ];
                                }
                            }
                        }
                    }
                } catch (Exception $e) {
                    // Skip
                }
            }
        }
    }
}

if ($controllerIssues > 0) {
    echo "❌ Found {$controllerIssues} controller query issues\n";
} else {
    echo "✅ No controller query issues found\n";
}

// Print Summary
echo "\n\n";
echo "═══════════════════════════════════════════════════════════════\n";
echo "📊 SCAN SUMMARY\n";
echo "═══════════════════════════════════════════════════════════════\n";
echo "Total Models Scanned: " . $stats['total_models'] . "\n";
echo "Models with SoftDeletes: " . $stats['models_with_soft_deletes'] . "\n";
echo "Missing deleted_at columns: " . $stats['missing_deleted_at'] . "\n";
echo "Total Scope Methods Checked: " . $stats['total_scope_checks'] . "\n";
echo "Scope Column Issues: " . $stats['scope_column_issues'] . "\n";
echo "Total Issues Found: " . count($issues) . "\n";
echo "Warnings: " . count($warnings) . "\n";
echo "═══════════════════════════════════════════════════════════════\n\n";

if (!empty($warnings)) {
    echo "⚠️  WARNINGS:\n";
    foreach ($warnings as $warning) {
        echo "   {$warning}\n";
    }
    echo "\n";
}

if (!empty($issues)) {
    echo "🚨 CRITICAL ISSUES FOUND:\n";
    echo "═══════════════════════════════════════════════════════════════\n\n";
    
    $grouped = [];
    foreach ($issues as $issue) {
        $grouped[$issue['type']][] = $issue;
    }
    
    foreach ($grouped as $type => $typeIssues) {
        echo "📌 {$type} (" . count($typeIssues) . " issues)\n";
        echo "───────────────────────────────────────────────────────────────\n";
        
        foreach ($typeIssues as $issue) {
            echo "❌ {$issue['severity']}: {$issue['issue']}\n";
            if (isset($issue['fix'])) {
                echo "   💡 Fix: {$issue['fix']}\n";
            }
            echo "\n";
        }
    }
    
    // Generate SQL fix script
    echo "\n═══════════════════════════════════════════════════════════════\n";
    echo "🔧 AUTO-GENERATED SQL FIXES\n";
    echo "═══════════════════════════════════════════════════════════════\n\n";
    
    foreach ($issues as $issue) {
        if ($issue['type'] === 'Missing SoftDelete Column' && isset($issue['fix'])) {
            echo $issue['fix'] . "\n";
        }
    }
    
} else {
    echo "✅ NO CRITICAL ISSUES FOUND!\n";
    echo "   All models are consistent with database schema.\n";
}

echo "\n✅ Deep scan complete!\n";
