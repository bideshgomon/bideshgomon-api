#!/usr/bin/env php
<?php
/**
 * ARCHITECTURAL AUDIT SCRIPT
 * Phase 1: Database & Model Synchronization Gap Analysis
 * 
 * This script scans all Models, compares them against migrations and database tables,
 * and generates a comprehensive gap analysis report.
 * 
 * Usage: php scripts/architectural-audit.php
 */

require __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class ArchitecturalAuditor
{
    private array $gaps = [];
    private array $models = [];
    private array $tables = [];
    private array $migrations = [];
    
    public function run(): void
    {
        echo "\n🔍 PHASE 1: DATABASE & MODEL SYNCHRONIZATION AUDIT\n";
        echo str_repeat('=', 70) . "\n\n";
        
        $this->scanModels();
        $this->scanDatabase();
        $this->scanMigrations();
        $this->analyzeGaps();
        $this->generateReport();
        $this->generateFixScript();
    }
    
    private function scanModels(): void
    {
        echo "📦 Scanning Models...\n";
        
        $modelFiles = glob(base_path('app/Models/*.php'));
        
        foreach ($modelFiles as $file) {
            $className = 'App\\Models\\' . basename($file, '.php');
            
            if (!class_exists($className)) {
                continue;
            }
            
            try {
                $reflection = new ReflectionClass($className);
                
                // Skip abstract classes and traits
                if ($reflection->isAbstract() || $reflection->isTrait()) {
                    continue;
                }
                
                $model = new $className;
                $table = $model->getTable();
                
                $this->models[] = [
                    'class' => $className,
                    'file' => basename($file),
                    'table' => $table,
                    'expected_table' => $this->getExpectedTableName($className),
                ];
            } catch (Exception $e) {
                echo "   ⚠️  Warning: Could not instantiate $className: {$e->getMessage()}\n";
            }
        }
        
        echo "   ✅ Found " . count($this->models) . " models\n\n";
    }
    
    private function scanDatabase(): void
    {
        echo "🗄️  Scanning Database Tables...\n";
        
        try {
            $this->tables = DB::select("
                SELECT table_name 
                FROM information_schema.tables 
                WHERE table_schema = DATABASE()
                AND table_type = 'BASE TABLE'
            ");
            
            $this->tables = array_map(fn($t) => $t->table_name ?? $t->TABLE_NAME, $this->tables);
            
            echo "   ✅ Found " . count($this->tables) . " tables\n\n";
        } catch (Exception $e) {
            echo "   ❌ Database connection error: {$e->getMessage()}\n\n";
            exit(1);
        }
    }
    
    private function scanMigrations(): void
    {
        echo "📋 Scanning Migrations...\n";
        
        $migrationFiles = glob(base_path('database/migrations/*.php'));
        
        foreach ($migrationFiles as $file) {
            $content = file_get_contents($file);
            
            // Extract table names from migrations
            if (preg_match("/Schema::create\('([^']+)'/", $content, $matches)) {
                $this->migrations[] = [
                    'file' => basename($file),
                    'table' => $matches[1],
                ];
            }
        }
        
        echo "   ✅ Found " . count($this->migrations) . " migrations\n\n";
    }
    
    private function analyzeGaps(): void
    {
        echo "🔬 Analyzing Gaps...\n\n";
        
        foreach ($this->models as $model) {
            $table = $model['table'];
            $expectedTable = $model['expected_table'];
            $issues = [];
            
            // Check if table exists in database
            if (!in_array($table, $this->tables)) {
                $issues[] = [
                    'type' => 'MISSING_TABLE',
                    'severity' => 'CRITICAL',
                    'message' => "Table '$table' does not exist in database",
                ];
            }
            
            // Check if migration exists
            $migrationExists = array_filter($this->migrations, fn($m) => $m['table'] === $table);
            if (empty($migrationExists)) {
                $issues[] = [
                    'type' => 'MISSING_MIGRATION',
                    'severity' => 'HIGH',
                    'message' => "No migration found for table '$table'",
                ];
            }
            
            // Check naming convention (singular model -> plural table)
            if ($table !== $expectedTable) {
                $issues[] = [
                    'type' => 'NAMING_MISMATCH',
                    'severity' => 'LOW',
                    'message' => "Table name '$table' doesn't match expected '$expectedTable'",
                ];
            }
            
            if (!empty($issues)) {
                $this->gaps[] = [
                    'model' => $model['class'],
                    'file' => $model['file'],
                    'table' => $table,
                    'issues' => $issues,
                ];
            }
        }
        
        // Check for orphaned tables (tables without models)
        $modelTables = array_column($this->models, 'table');
        $systemTables = ['migrations', 'password_reset_tokens', 'sessions', 'cache', 'cache_locks', 'jobs', 'job_batches', 'failed_jobs'];
        
        foreach ($this->tables as $table) {
            if (!in_array($table, $modelTables) && !in_array($table, $systemTables)) {
                $this->gaps[] = [
                    'model' => null,
                    'file' => null,
                    'table' => $table,
                    'issues' => [[
                        'type' => 'ORPHANED_TABLE',
                        'severity' => 'MEDIUM',
                        'message' => "Table '$table' exists but has no corresponding Model",
                    ]],
                ];
            }
        }
    }
    
    private function generateReport(): void
    {
        echo "\n" . str_repeat('=', 70) . "\n";
        echo "📊 GAP ANALYSIS REPORT\n";
        echo str_repeat('=', 70) . "\n\n";
        
        if (empty($this->gaps)) {
            echo "✅ NO GAPS FOUND! Your models and database are perfectly synchronized.\n\n";
            return;
        }
        
        $critical = array_filter($this->gaps, fn($g) => 
            !empty(array_filter($g['issues'], fn($i) => $i['severity'] === 'CRITICAL'))
        );
        $high = array_filter($this->gaps, fn($g) => 
            !empty(array_filter($g['issues'], fn($i) => $i['severity'] === 'HIGH'))
        );
        $medium = array_filter($this->gaps, fn($g) => 
            !empty(array_filter($g['issues'], fn($i) => $i['severity'] === 'MEDIUM'))
        );
        $low = array_filter($this->gaps, fn($g) => 
            !empty(array_filter($g['issues'], fn($i) => $i['severity'] === 'LOW'))
        );
        
        echo "Summary:\n";
        echo "  🔴 CRITICAL: " . count($critical) . " issue(s)\n";
        echo "  🟠 HIGH: " . count($high) . " issue(s)\n";
        echo "  🟡 MEDIUM: " . count($medium) . " issue(s)\n";
        echo "  🟢 LOW: " . count($low) . " issue(s)\n";
        echo "  📊 TOTAL: " . count($this->gaps) . " gap(s) found\n\n";
        
        echo str_repeat('-', 70) . "\n\n";
        
        foreach ($this->gaps as $gap) {
            $severity = $gap['issues'][0]['severity'];
            $icon = match($severity) {
                'CRITICAL' => '🔴',
                'HIGH' => '🟠',
                'MEDIUM' => '🟡',
                'LOW' => '🟢',
                default => '⚪',
            };
            
            echo "$icon " . ($gap['model'] ?? 'ORPHANED') . "\n";
            echo "   Table: {$gap['table']}\n";
            if ($gap['file']) {
                echo "   File: {$gap['file']}\n";
            }
            echo "   Issues:\n";
            
            foreach ($gap['issues'] as $issue) {
                echo "      - [{$issue['severity']}] {$issue['message']}\n";
            }
            echo "\n";
        }
        
        // Save report to file
        $reportPath = base_path('docs/ARCHITECTURAL_AUDIT_REPORT.md');
        $this->saveMarkdownReport($reportPath);
        echo "📄 Full report saved to: docs/ARCHITECTURAL_AUDIT_REPORT.md\n\n";
    }
    
    private function generateFixScript(): void
    {
        echo str_repeat('=', 70) . "\n";
        echo "🔧 GENERATING FIX SCRIPT\n";
        echo str_repeat('=', 70) . "\n\n";
        
        $critical = array_filter($this->gaps, fn($g) => 
            !empty(array_filter($g['issues'], fn($i) => $i['severity'] === 'CRITICAL'))
        );
        
        if (empty($critical)) {
            echo "✅ No critical issues to fix.\n\n";
            return;
        }
        
        $script = "#!/usr/bin/env php\n";
        $script .= "<?php\n";
        $script .= "// AUTO-GENERATED FIX SCRIPT\n";
        $script .= "// Run with: php scripts/fix-database-gaps.php\n\n";
        $script .= "// CRITICAL GAPS REQUIRING MANUAL ATTENTION:\n\n";
        
        foreach ($critical as $gap) {
            foreach ($gap['issues'] as $issue) {
                if ($issue['type'] === 'MISSING_TABLE') {
                    $script .= "// MISSING TABLE: {$gap['table']}\n";
                    $script .= "// Model: {$gap['model']}\n";
                    $script .= "// Action: Create migration:\n";
                    $script .= "//   php artisan make:migration create_{$gap['table']}_table\n\n";
                }
            }
        }
        
        file_put_contents(base_path('scripts/fix-database-gaps.php'), $script);
        echo "📄 Fix script saved to: scripts/fix-database-gaps.php\n\n";
    }
    
    private function saveMarkdownReport(string $path): void
    {
        $md = "# Architectural Audit Report\n\n";
        $md .= "**Generated:** " . date('Y-m-d H:i:s') . "\n";
        $md .= "**Models Scanned:** " . count($this->models) . "\n";
        $md .= "**Database Tables:** " . count($this->tables) . "\n";
        $md .= "**Migrations:** " . count($this->migrations) . "\n\n";
        
        $md .= "## Summary\n\n";
        $md .= "| Severity | Count |\n";
        $md .= "|----------|-------|\n";
        
        $criticalCount = count(array_filter($this->gaps, fn($g) => 
            !empty(array_filter($g['issues'], fn($i) => $i['severity'] === 'CRITICAL'))
        ));
        $highCount = count(array_filter($this->gaps, fn($g) => 
            !empty(array_filter($g['issues'], fn($i) => $i['severity'] === 'HIGH'))
        ));
        $mediumCount = count(array_filter($this->gaps, fn($g) => 
            !empty(array_filter($g['issues'], fn($i) => $i['severity'] === 'MEDIUM'))
        ));
        $lowCount = count(array_filter($this->gaps, fn($g) => 
            !empty(array_filter($g['issues'], fn($i) => $i['severity'] === 'LOW'))
        ));
        
        $md .= "| 🔴 CRITICAL | $criticalCount |\n";
        $md .= "| 🟠 HIGH | $highCount |\n";
        $md .= "| 🟡 MEDIUM | $mediumCount |\n";
        $md .= "| 🟢 LOW | $lowCount |\n\n";
        
        $md .= "## Detailed Findings\n\n";
        
        foreach ($this->gaps as $gap) {
            $md .= "### " . ($gap['model'] ?? 'ORPHANED TABLE') . "\n\n";
            $md .= "- **Table:** `{$gap['table']}`\n";
            if ($gap['file']) {
                $md .= "- **File:** `{$gap['file']}`\n";
            }
            $md .= "\n**Issues:**\n\n";
            
            foreach ($gap['issues'] as $issue) {
                $md .= "- [" . $issue['severity'] . "] " . $issue['message'] . "\n";
            }
            $md .= "\n";
        }
        
        file_put_contents($path, $md);
    }
    
    private function getExpectedTableName(string $className): string
    {
        $shortName = class_basename($className);
        return Str::snake(Str::pluralStudly($shortName));
    }
}

// Run the audit
$auditor = new ArchitecturalAuditor();
$auditor->run();

echo "✅ Audit complete!\n\n";
