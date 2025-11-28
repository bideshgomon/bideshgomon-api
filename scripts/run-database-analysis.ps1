# Database Relationship Deep Scan (PowerShell)
# Comprehensive analysis with detailed output

Write-Host "========================================" -ForegroundColor Cyan
Write-Host "BIDESHGOMON DATABASE ANALYSIS" -ForegroundColor Cyan
Write-Host "========================================" -ForegroundColor Cyan
Write-Host ""

Write-Host "[1/2] Running basic relationship analysis..." -ForegroundColor Yellow
php scripts/analyze-database-relationships.php
Write-Host ""

Write-Host "[2/2] Checking additional schema details..." -ForegroundColor Yellow

# Check wallet transactions schema
php -r "
require 'vendor/autoload.php';
`$app = require 'bootstrap/app.php';
`$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

echo '\n=== WALLET TRANSACTIONS SCHEMA ===\n';
`$columns = Schema::getColumnListing('wallet_transactions');
foreach (`$columns as `$col) {
    echo '  - ' . `$col . '\n';
}

echo '\n=== RELATIONSHIP COUNTS ===\n';
echo 'Total Users: ' . DB::table('users')->count() . '\n';
echo 'Total Wallets: ' . DB::table('wallets')->count() . '\n';
echo 'Total Transactions: ' . DB::table('wallet_transactions')->count() . '\n';
echo 'Total Referrals: ' . DB::table('referrals')->count() . '\n';
echo 'Total Rewards: ' . DB::table('rewards')->count() . '\n';
echo 'Total Service Applications: ' . DB::table('service_applications')->count() . '\n';
echo '\n=== USER PROFILE COVERAGE ===\n';
echo 'Users with Profiles: ' . DB::table('user_profiles')->count() . '\n';
echo 'Users with Passports: ' . DB::table('user_passports')->distinct('user_id')->count() . '\n';
echo 'Users with Education: ' . DB::table('user_educations')->distinct('user_id')->count() . '\n';
echo 'Users with Work Experience: ' . DB::table('user_work_experiences')->distinct('user_id')->count() . '\n';
"

Write-Host ""
Write-Host "========================================" -ForegroundColor Green
Write-Host "ANALYSIS COMPLETE" -ForegroundColor Green
Write-Host "========================================" -ForegroundColor Green
Write-Host ""
Write-Host "Full report: docs/DATABASE_RELATIONSHIP_ANALYSIS.md" -ForegroundColor White
Write-Host ""
