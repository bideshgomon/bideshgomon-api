@echo off
REM Database Relationship Deep Scan
REM Runs comprehensive analysis scripts

echo ========================================
echo BIDESHGOMON DATABASE ANALYSIS
echo ========================================
echo.

echo [1/2] Running basic relationship analysis...
php scripts/analyze-database-relationships.php
echo.

echo [2/2] Checking wallet transaction schema...
php -r "require 'vendor/autoload.php'; $app = require 'bootstrap/app.php'; $app->make('Illuminate\Contracts\Console\Kernel')->bootstrap(); use Illuminate\Support\Facades\Schema; echo 'Wallet Transactions Table Columns: '; print_r(Schema::getColumnListing('wallet_transactions'));"
echo.

echo ========================================
echo ANALYSIS COMPLETE
echo ========================================
echo Full report saved to: docs/DATABASE_RELATIONSHIP_ANALYSIS.md
echo.
pause
