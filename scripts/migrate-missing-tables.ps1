# Database Migration Reconciliation Script (PowerShell)
# Run this to sync all pending migrations to database

Write-Host "=========================================" -ForegroundColor Cyan
Write-Host "BideshGomon Database Migration Script" -ForegroundColor Cyan
Write-Host "=========================================" -ForegroundColor Cyan
Write-Host ""
Write-Host "⚠️  WARNING: This will run ALL pending migrations" -ForegroundColor Yellow
Write-Host "📊 Current migrations in DB: 50" -ForegroundColor White
Write-Host "📁 Total migration files: 187" -ForegroundColor White
Write-Host ""
Write-Host "This script will run migrations in phases to avoid dependency issues."
Write-Host ""

$continue = Read-Host "Continue? (y/n)"
if ($continue -ne "y" -and $continue -ne "Y") {
    Write-Host "Migration cancelled." -ForegroundColor Red
    exit
}

Write-Host ""
Write-Host "=========================================" -ForegroundColor Cyan
Write-Host "PHASE 1: Critical Dependencies" -ForegroundColor Cyan
Write-Host "=========================================" -ForegroundColor Cyan

php artisan migrate --path=database/migrations/2025_11_24_065209_create_job_categories_table.php --force
php artisan migrate --path=database/migrations/2025_11_24_070124_create_skill_categories_table.php --force
php artisan migrate --path=database/migrations/2025_11_25_154324_create_application_documents_table.php --force
php artisan migrate --path=database/migrations/2025_11_25_042224_create_agency_resources_table.php --force
php artisan migrate --path=database/migrations/2025_11_25_042958_create_service_quotes_table.php --force
php artisan migrate --path=database/migrations/2025_11_23_000004_create_service_reviews_table.php --force

Write-Host ""
Write-Host "✅ Phase 1 Complete" -ForegroundColor Green
Write-Host ""

Write-Host "=========================================" -ForegroundColor Cyan
Write-Host "PHASE 2: Visa System" -ForegroundColor Cyan
Write-Host "=========================================" -ForegroundColor Cyan

php artisan migrate --path=database/migrations/2025_11_25_162226_create_visa_fees_table.php --force
php artisan migrate --path=database/migrations/2025_11_24_000001_create_tourist_visas_table.php --force
php artisan migrate --path=database/migrations/2025_11_29_105530_create_student_visas_table.php --force
php artisan migrate --path=database/migrations/2025_11_29_105942_create_work_visas_table.php --force
php artisan migrate --path=database/migrations/2025_11_19_122429_create_visa_documents_table.php --force
php artisan migrate --path=database/migrations/2025_11_19_122430_create_visa_appointments_table.php --force

Write-Host ""
Write-Host "✅ Phase 2 Complete" -ForegroundColor Green
Write-Host ""

Write-Host "=========================================" -ForegroundColor Cyan
Write-Host "PHASE 3: Flight System" -ForegroundColor Cyan
Write-Host "=========================================" -ForegroundColor Cyan

php artisan migrate --path=database/migrations/2025_11_19_050001_create_flight_routes_table.php --force
php artisan migrate --path=database/migrations/2025_11_19_070002_create_flight_quotes_table.php --force
php artisan migrate --path=database/migrations/2025_11_19_070003_create_flight_searches_table.php --force

Write-Host ""
Write-Host "✅ Phase 3 Complete" -ForegroundColor Green
Write-Host ""

Write-Host "=========================================" -ForegroundColor Cyan
Write-Host "PHASE 4: Translation System" -ForegroundColor Cyan
Write-Host "=========================================" -ForegroundColor Cyan

php artisan migrate --path=database/migrations/2025_11_29_110434_create_translations_table.php --force
php artisan migrate --path=database/migrations/2025_11_19_132840_create_translation_requests_table.php --force
php artisan migrate --path=database/migrations/2025_11_19_132842_create_translation_documents_table.php --force
php artisan migrate --path=database/migrations/2025_11_19_132844_create_translation_quotes_table.php --force

Write-Host ""
Write-Host "✅ Phase 4 Complete" -ForegroundColor Green
Write-Host ""

Write-Host "=========================================" -ForegroundColor Cyan
Write-Host "PHASE 5: User Features" -ForegroundColor Cyan
Write-Host "=========================================" -ForegroundColor Cyan

php artisan migrate --path=database/migrations/2025_11_21_000000_create_phone_verification_codes_table.php --force
php artisan migrate --path=database/migrations/2025_11_21_001517_create_smart_suggestions_table.php --force
php artisan migrate --path=database/migrations/2025_11_21_002439_create_user_documents_table.php --force
php artisan migrate --path=database/migrations/2025_11_28_125548_create_user_notification_preferences_table.php --force

Write-Host ""
Write-Host "✅ Phase 5 Complete" -ForegroundColor Green
Write-Host ""

Write-Host "=========================================" -ForegroundColor Cyan
Write-Host "PHASE 6: System/Admin" -ForegroundColor Cyan
Write-Host "=========================================" -ForegroundColor Cyan

php artisan migrate --path=database/migrations/2025_11_21_090000_create_system_events_table.php --force
php artisan migrate --path=database/migrations/2025_11_20_232401_create_seo_settings_table.php --force
php artisan migrate --path=database/migrations/2025_11_27_100325_create_site_settings_table.php --force
php artisan migrate --path=database/migrations/2025_11_28_095819_create_notifications_table.php --force
php artisan migrate --path=database/migrations/2025_11_28_102153_create_transactions_table.php --force

Write-Host ""
Write-Host "✅ Phase 6 Complete" -ForegroundColor Green
Write-Host ""

Write-Host "=========================================" -ForegroundColor Cyan
Write-Host "PHASE 7: Marketing/Content" -ForegroundColor Cyan
Write-Host "=========================================" -ForegroundColor Cyan

php artisan migrate --path=database/migrations/2025_11_23_124805_create_email_templates_table.php --force
php artisan migrate --path=database/migrations/2025_11_27_112626_create_testimonials_table.php --force
php artisan migrate --path=database/migrations/2025_11_27_081135_create_pages_table.php --force
php artisan migrate --path=database/migrations/2025_11_27_081135_create_partners_table.php --force

Write-Host ""
Write-Host "✅ Phase 7 Complete" -ForegroundColor Green
Write-Host ""

Write-Host "=========================================" -ForegroundColor Cyan
Write-Host "PHASE 8: Lookup Tables" -ForegroundColor Cyan
Write-Host "=========================================" -ForegroundColor Cyan

php artisan migrate --path=database/migrations/2025_11_29_012730_create_relationship_types_table.php --force
php artisan migrate --path=database/migrations/2025_11_29_012734_create_bank_names_table.php --force
php artisan migrate --path=database/migrations/2025_11_29_110444_create_attestations_table.php --force
php artisan migrate --path=database/migrations/2025_11_29_110523_create_hajj_umrahs_table.php --force

Write-Host ""
Write-Host "✅ Phase 8 Complete" -ForegroundColor Green
Write-Host ""

Write-Host "=========================================" -ForegroundColor Cyan
Write-Host "🎉 ALL MIGRATIONS COMPLETE!" -ForegroundColor Green
Write-Host "=========================================" -ForegroundColor Cyan
Write-Host ""

Write-Host "📊 Verifying table count..." -ForegroundColor Yellow
mysql -u root bideshgomondb -e "SELECT COUNT(*) as total_tables FROM information_schema.tables WHERE table_schema = 'bideshgomondb';"

Write-Host ""
Write-Host "✅ Migration reconciliation complete!" -ForegroundColor Green
Write-Host ""
Write-Host "Next steps:" -ForegroundColor Cyan
Write-Host "1. Test critical models: php artisan tinker" -ForegroundColor White
Write-Host "2. Run seeders if needed: php artisan db:seed" -ForegroundColor White
Write-Host "3. Check application for remaining errors" -ForegroundColor White
Write-Host ""
