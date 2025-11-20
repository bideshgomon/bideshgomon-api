# Migration Guide: bgproject → bgplatform-fresh

## 🎯 Objective
Copy all improvements, new modules, and features from `bgproject` (almost complete) to `bgplatform-fresh` (current workspace).

---

## 📋 Pre-Migration Checklist

### Step 1: Backup Current Project
```powershell
cd C:\xampp\htdocs
# Create backup of bgplatform-fresh
Copy-Item -Path "bgplatform-fresh" -Destination "bgplatform-fresh-backup-$(Get-Date -Format 'yyyyMMdd-HHmmss')" -Recurse
```

### Step 2: Verify bgproject Status
```powershell
cd C:\xampp\htdocs\bgproject
# Check git status
git status

# List all migrations
php artisan migrate:status

# Check routes
php artisan route:list --compact

# List all models
Get-ChildItem app\Models\*.php
```

### Step 3: Document bgproject Structure
Run this in `bgproject` to generate structure report:
```powershell
# Create structure report
$report = @"
=== BGPROJECT STRUCTURE REPORT ===
Generated: $(Get-Date)

MIGRATIONS:
$(Get-ChildItem database\migrations\*.php | Select-Object -ExpandProperty Name | Out-String)

MODELS:
$(Get-ChildItem app\Models\*.php | Select-Object -ExpandProperty Name | Out-String)

CONTROLLERS:
$(Get-ChildItem app\Http\Controllers\*.php -Recurse | Select-Object -ExpandProperty Name | Out-String)

SERVICES:
$(Get-ChildItem app\Services\*.php -ErrorAction SilentlyContinue | Select-Object -ExpandProperty Name | Out-String)

VUE PAGES:
$(Get-ChildItem resources\js\Pages\*.vue -Recurse | Select-Object -ExpandProperty Name | Out-String)

VUE COMPONENTS:
$(Get-ChildItem resources\js\Components\*.vue -Recurse | Select-Object -ExpandProperty Name | Out-String)

SEEDERS:
$(Get-ChildItem database\seeders\*.php | Select-Object -ExpandProperty Name | Out-String)
"@

$report | Out-File "BGPROJECT_STRUCTURE_REPORT.txt"
Write-Host "Report saved to BGPROJECT_STRUCTURE_REPORT.txt"
```

---

## 🔄 Migration Process

### Phase 1: Database Migrations (Priority: CRITICAL)

#### Step 1.1: Copy New Migrations
```powershell
# FROM: C:\xampp\htdocs\bgproject\database\migrations
# TO:   C:\xampp\htdocs\bgplatform-fresh\database\migrations

# List migrations in bgproject that don't exist in bgplatform-fresh
cd C:\xampp\htdocs\bgproject\database\migrations
$bgprojectMigrations = Get-ChildItem *.php | Select-Object -ExpandProperty Name

cd C:\xampp\htdocs\bgplatform-fresh\database\migrations
$currentMigrations = Get-ChildItem *.php | Select-Object -ExpandProperty Name

# Find new migrations
$newMigrations = $bgprojectMigrations | Where-Object { $currentMigrations -notcontains $_ }

# Copy new migrations
foreach ($migration in $newMigrations) {
    Copy-Item "C:\xampp\htdocs\bgproject\database\migrations\$migration" `
              "C:\xampp\htdocs\bgplatform-fresh\database\migrations\$migration"
    Write-Host "✅ Copied: $migration"
}
```

**Expected New Migrations** (from SAAS plan):
- `create_visa_application_templates_table.php`
- `create_visa_applications_table.php`
- `create_visa_application_documents_table.php`
- `create_visa_application_notes_table.php`
- `create_visa_application_status_history_table.php`
- `create_permissions_table.php`
- `create_role_has_permissions_table.php`
- `create_agencies_table.php`
- `create_agency_clients_table.php`
- `create_agency_services_table.php`
- `create_agency_commissions_table.php`
- `create_consultant_profiles_table.php`
- `create_consultant_clients_table.php`
- `create_subscription_plans_table.php`
- `create_user_subscriptions_table.php`
- `create_notifications_table.php` (if not exists)

#### Step 1.2: Run Migrations
```powershell
cd C:\xampp\htdocs\bgplatform-fresh
php artisan migrate
```

---

### Phase 2: Models (Priority: CRITICAL)

#### Step 2.1: Copy New Models
```powershell
# Compare and copy new models
$bgprojectModels = Get-ChildItem "C:\xampp\htdocs\bgproject\app\Models\*.php" | Select-Object -ExpandProperty Name
$currentModels = Get-ChildItem "C:\xampp\htdocs\bgplatform-fresh\app\Models\*.php" | Select-Object -ExpandProperty Name

$newModels = $bgprojectModels | Where-Object { $currentModels -notcontains $_ }

foreach ($model in $newModels) {
    Copy-Item "C:\xampp\htdocs\bgproject\app\Models\$model" `
              "C:\xampp\htdocs\bgplatform-fresh\app\Models\$model"
    Write-Host "✅ Copied Model: $model"
}
```

**Expected New Models**:
- `VisaApplicationTemplate.php`
- `VisaApplication.php`
- `VisaApplicationDocument.php`
- `VisaApplicationNote.php`
- `VisaApplicationStatusHistory.php`
- `Permission.php` (if using Spatie)
- `Agency.php`
- `AgencyClient.php`
- `AgencyService.php`
- `AgencyCommission.php`
- `ConsultantProfile.php`
- `ConsultantClient.php`
- `SubscriptionPlan.php`
- `UserSubscription.php`

#### Step 2.2: Update Existing Models
Check for relationship updates in existing models:
- `User.php` (may have new relationships)
- `Wallet.php` (may have subscription relationships)
- `Role.php` (may have permission relationships)

---

### Phase 3: Services (Priority: HIGH)

```powershell
# Copy all service files
Copy-Item "C:\xampp\htdocs\bgproject\app\Services\*" `
          "C:\xampp\htdocs\bgplatform-fresh\app\Services\" -Recurse -Force

Write-Host "✅ Services copied"
```

**Expected New Services**:
- `VisaApplicationService.php`
- `AgencyService.php`
- `ConsultantService.php`
- `SubscriptionService.php`
- `NotificationService.php`
- `PermissionService.php`

---

### Phase 4: Controllers (Priority: HIGH)

#### Step 4.1: Copy New Controllers
```powershell
# Copy entire controller directory structure
Copy-Item "C:\xampp\htdocs\bgproject\app\Http\Controllers\*" `
          "C:\xampp\htdocs\bgplatform-fresh\app\Http\Controllers\" -Recurse -Force

Write-Host "✅ Controllers copied"
```

**Expected New Controllers**:
```
app/Http/Controllers/
├── VisaApplicationController.php
├── Agency/
│   ├── DashboardController.php
│   ├── ClientController.php
│   ├── ApplicationController.php
│   └── CommissionController.php
├── Consultant/
│   ├── DashboardController.php
│   ├── ClientController.php
│   └── AppointmentController.php
├── Admin/
│   ├── VisaApplicationController.php
│   ├── AgencyController.php
│   ├── PermissionController.php
│   └── SubscriptionController.php
└── Api/
    └── PaymentController.php
```

#### Step 4.2: Verify No Conflicts
```powershell
# Check for controller conflicts
cd C:\xampp\htdocs\bgplatform-fresh
Get-ChildItem app\Http\Controllers\*.php -Recurse | Select-Object Name, LastWriteTime | Sort-Object LastWriteTime -Descending | Out-GridView
```

---

### Phase 5: Middleware (Priority: MEDIUM)

```powershell
# Copy new middleware
$bgprojectMiddleware = Get-ChildItem "C:\xampp\htdocs\bgproject\app\Http\Middleware\*.php" | Select-Object -ExpandProperty Name
$currentMiddleware = Get-ChildItem "C:\xampp\htdocs\bgplatform-fresh\app\Http\Middleware\*.php" | Select-Object -ExpandProperty Name

$newMiddleware = $bgprojectMiddleware | Where-Object { $currentMiddleware -notcontains $_ }

foreach ($mw in $newMiddleware) {
    Copy-Item "C:\xampp\htdocs\bgproject\app\Http\Middleware\$mw" `
              "C:\xampp\htdocs\bgplatform-fresh\app\Http\Middleware\$mw"
    Write-Host "✅ Copied Middleware: $mw"
}
```

**Expected New Middleware**:
- `CheckPermission.php`
- `CheckSubscription.php`
- `CheckAgencyStatus.php`

---

### Phase 6: Routes (Priority: CRITICAL)

**⚠️ MANUAL MERGE REQUIRED**

```powershell
# Open both files for comparison
code C:\xampp\htdocs\bgproject\routes\web.php
code C:\xampp\htdocs\bgplatform-fresh\routes\web.php
```

**Merge Strategy**:
1. Keep existing routes in `bgplatform-fresh`
2. Add new route groups from `bgproject`:
   - Visa application routes
   - Agency routes (`/agency/*`)
   - Consultant routes (`/consultant/*`)
   - Admin visa application routes
   - Subscription routes

**Expected New Route Groups**:
```php
// Visa Applications (User)
Route::middleware('auth')->group(function () {
    Route::resource('visa-applications', VisaApplicationController::class);
    Route::post('visa-applications/{application}/submit', [VisaApplicationController::class, 'submit']);
    Route::post('visa-applications/{application}/documents', [VisaApplicationController::class, 'uploadDocument']);
});

// Agency Routes
Route::middleware(['auth', 'role:agency'])->prefix('agency')->name('agency.')->group(function () {
    Route::get('/dashboard', [Agency\DashboardController::class, 'index'])->name('dashboard');
    Route::resource('clients', Agency\ClientController::class);
    Route::resource('applications', Agency\ApplicationController::class);
    Route::get('commissions', [Agency\CommissionController::class, 'index'])->name('commissions.index');
});

// Consultant Routes
Route::middleware(['auth', 'role:consultant'])->prefix('consultant')->name('consultant.')->group(function () {
    Route::get('/dashboard', [Consultant\DashboardController::class, 'index'])->name('dashboard');
    Route::resource('clients', Consultant\ClientController::class);
    Route::resource('appointments', Consultant\AppointmentController::class);
});

// Admin Visa Applications
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::resource('visa-applications', Admin\VisaApplicationController::class);
    Route::post('visa-applications/{application}/review', [Admin\VisaApplicationController::class, 'review']);
    Route::post('visa-applications/{application}/assign', [Admin\VisaApplicationController::class, 'assign']);
    Route::resource('agencies', Admin\AgencyController::class);
    Route::resource('permissions', Admin\PermissionController::class);
    Route::resource('subscriptions', Admin\SubscriptionController::class);
});
```

---

### Phase 7: Vue Pages (Priority: HIGH)

```powershell
# Copy entire Pages directory
Copy-Item "C:\xampp\htdocs\bgproject\resources\js\Pages\*" `
          "C:\xampp\htdocs\bgplatform-fresh\resources\js\Pages\" -Recurse -Force

Write-Host "✅ Vue Pages copied"
```

**Expected New Pages**:
```
resources/js/Pages/
├── VisaApplication/
│   ├── Index.vue
│   ├── Create.vue
│   ├── Edit.vue
│   ├── Show.vue
│   └── Wizard.vue
├── Agency/
│   ├── Dashboard.vue
│   ├── Clients/
│   │   ├── Index.vue
│   │   └── Show.vue
│   └── Applications/
│       └── Index.vue
├── Consultant/
│   ├── Dashboard.vue
│   └── Clients/
│       └── Index.vue
└── Admin/
    ├── VisaApplications/
    │   ├── Index.vue
    │   └── Show.vue
    ├── Agencies/
    │   └── Index.vue
    └── Permissions/
        └── Index.vue
```

---

### Phase 8: Vue Components (Priority: MEDIUM)

```powershell
# Copy components
Copy-Item "C:\xampp\htdocs\bgproject\resources\js\Components\*" `
          "C:\xampp\htdocs\bgplatform-fresh\resources\js\Components\" -Recurse -Force

Write-Host "✅ Vue Components copied"
```

---

### Phase 9: Seeders (Priority: MEDIUM)

```powershell
# Copy seeders
$bgprojectSeeders = Get-ChildItem "C:\xampp\htdocs\bgproject\database\seeders\*.php" | Select-Object -ExpandProperty Name
$currentSeeders = Get-ChildItem "C:\xampp\htdocs\bgplatform-fresh\database\seeders\*.php" | Select-Object -ExpandProperty Name

$newSeeders = $bgprojectSeeders | Where-Object { $currentSeeders -notcontains $_ }

foreach ($seeder in $newSeeders) {
    Copy-Item "C:\xampp\htdocs\bgproject\database\seeders\$seeder" `
              "C:\xampp\htdocs\bgplatform-fresh\database\seeders\$seeder"
    Write-Host "✅ Copied Seeder: $seeder"
}
```

**Expected New Seeders**:
- `VisaApplicationTemplateSeeder.php`
- `PermissionSeeder.php`
- `AgencySeeder.php`
- `SubscriptionPlanSeeder.php`

**Update DatabaseSeeder.php**:
```powershell
code C:\xampp\htdocs\bgplatform-fresh\database\seeders\DatabaseSeeder.php
```

Add new seeders to the `run()` method.

---

### Phase 10: Config Files (Priority: MEDIUM)

```powershell
# Compare and merge config files
$configFiles = @('permission', 'subscription', 'payment', 'notification')

foreach ($config in $configFiles) {
    $source = "C:\xampp\htdocs\bgproject\config\$config.php"
    $dest = "C:\xampp\htdocs\bgplatform-fresh\config\$config.php"
    
    if (Test-Path $source) {
        Copy-Item $source $dest -Force
        Write-Host "✅ Copied config: $config.php"
    }
}
```

---

### Phase 11: Composer Dependencies (Priority: CRITICAL)

```powershell
cd C:\xampp\htdocs\bgproject

# Export installed packages
composer show --installed > installed-packages.txt

# Open both composer.json files
code composer.json
code C:\xampp\htdocs\bgplatform-fresh\composer.json
```

**Manually merge new packages into bgplatform-fresh `composer.json`**

**Expected New Packages**:
```json
{
    "require": {
        "spatie/laravel-permission": "^6.0",
        "barryvdh/laravel-dompdf": "^3.0",
        "maatwebsite/excel": "^3.1",
        "laravel/cashier": "^15.0"
    }
}
```

Then install:
```powershell
cd C:\xampp\htdocs\bgplatform-fresh
composer install
```

---

### Phase 12: NPM Dependencies (Priority: MEDIUM)

```powershell
# Compare package.json files
code C:\xampp\htdocs\bgproject\package.json
code C:\xampp\htdocs\bgplatform-fresh\package.json
```

**Expected New Packages**:
```json
{
    "dependencies": {
        "apexcharts": "^3.45.0",
        "vue-apexcharts": "^1.6.0",
        "vue-i18n": "^9.8.0"
    }
}
```

Install:
```powershell
cd C:\xampp\htdocs\bgplatform-fresh
npm install
```

---

### Phase 13: Bootstrap & App Config (Priority: MEDIUM)

```powershell
# Copy bootstrap files if updated
Copy-Item "C:\xampp\htdocs\bgproject\bootstrap\app.php" `
          "C:\xampp\htdocs\bgplatform-fresh\bootstrap\app.php" -Force

# Copy AppServiceProvider if updated
Copy-Item "C:\xampp\htdocs\bgproject\app\Providers\AppServiceProvider.php" `
          "C:\xampp\htdocs\bgplatform-fresh\app\Providers\AppServiceProvider.php" -Force
```

---

### Phase 14: Environment Variables (Priority: CRITICAL)

**⚠️ MANUAL MERGE**

```powershell
code C:\xampp\htdocs\bgproject\.env
code C:\xampp\htdocs\bgplatform-fresh\.env
```

**Add new environment variables**:
```env
# Payment Gateway
BKASH_APP_KEY=
BKASH_APP_SECRET=
BKASH_USERNAME=
BKASH_PASSWORD=
BKASH_BASE_URL=https://tokenized.sandbox.bka.sh/v1.2.0-beta

SSLCOMMERZ_STORE_ID=
SSLCOMMERZ_STORE_PASSWORD=

# Subscription
STRIPE_KEY=
STRIPE_SECRET=

# Notifications
PUSHER_APP_ID=
PUSHER_APP_KEY=
PUSHER_APP_SECRET=
PUSHER_APP_CLUSTER=

# SMS Gateway
SMS_USERNAME=
SMS_PASSWORD=
SMS_API_KEY=
```

---

### Phase 15: Public Assets (Priority: LOW)

```powershell
# Copy public assets if any new ones
Copy-Item "C:\xampp\htdocs\bgproject\public\*" `
          "C:\xampp\htdocs\bgplatform-fresh\public\" -Recurse -Force -Exclude @('index.php', 'storage', 'hot')

Write-Host "✅ Public assets copied"
```

---

## 🧪 Post-Migration Testing

### Step 1: Clear Caches
```powershell
cd C:\xampp\htdocs\bgplatform-fresh

php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear
php artisan optimize:clear
```

### Step 2: Regenerate Assets
```powershell
php artisan ziggy:generate
npm run build
```

### Step 3: Run Migrations
```powershell
# Check migration status
php artisan migrate:status

# Run new migrations
php artisan migrate

# Seed new data
php artisan db:seed --class=PermissionSeeder
php artisan db:seed --class=VisaApplicationTemplateSeeder
php artisan db:seed --class=SubscriptionPlanSeeder
```

### Step 4: Test Critical Routes
```powershell
# List all routes
php artisan route:list --compact

# Check for route conflicts
php artisan route:list | Select-String "POST.*visa-applications"
```

### Step 5: Run Tests
```powershell
php artisan test
```

### Step 6: Start Development Server
```powershell
php artisan serve
```

Visit: `http://localhost:8000`

---

## ✅ Verification Checklist

After migration, verify:

### Database
- [ ] All new migrations ran successfully
- [ ] No migration errors
- [ ] All tables exist: `visa_applications`, `agencies`, `permissions`, etc.

### Models
- [ ] All new models present
- [ ] Relationships work (test in tinker)
- [ ] No namespace errors

### Routes
- [ ] All routes load: `php artisan route:list`
- [ ] No duplicate routes
- [ ] Middleware applied correctly

### Controllers
- [ ] All controllers present
- [ ] No syntax errors
- [ ] Methods accessible

### Vue Pages
- [ ] All pages present
- [ ] No missing components
- [ ] Build completes: `npm run build`

### Features
- [ ] Visa application creation works
- [ ] Agency dashboard loads
- [ ] Consultant dashboard loads
- [ ] Admin visa management works
- [ ] Permission system works
- [ ] Subscription plans visible

### Authentication
- [ ] Login works
- [ ] Registration works
- [ ] Role-based access works

### File Uploads
- [ ] Storage link exists
- [ ] Document uploads work
- [ ] Files accessible via `/storage/`

---

## 🚨 Troubleshooting

### Issue: Migration Errors
```powershell
# Rollback last migration
php artisan migrate:rollback --step=1

# Check specific migration
php artisan migrate --path=database/migrations/YYYY_MM_DD_specific_migration.php
```

### Issue: Composer Dependency Conflicts
```powershell
# Update all packages
composer update

# Or update specific package
composer update spatie/laravel-permission
```

### Issue: NPM Build Errors
```powershell
# Clear node_modules and reinstall
Remove-Item -Path node_modules -Recurse -Force
Remove-Item -Path package-lock.json -Force
npm install
npm run build
```

### Issue: Routes Not Found
```powershell
# Clear route cache
php artisan route:clear
php artisan optimize:clear

# Regenerate Ziggy routes
php artisan ziggy:generate
```

### Issue: Permission Errors
```powershell
# Clear permission cache (if using Spatie)
php artisan permission:cache-reset
```

---

## 📊 Migration Completion Report

After completing all phases, generate a report:

```powershell
cd C:\xampp\htdocs\bgplatform-fresh

$report = @"
=== MIGRATION COMPLETION REPORT ===
Date: $(Get-Date)
From: bgproject
To: bgplatform-fresh

MIGRATIONS:
Total: $(Get-ChildItem database\migrations\*.php | Measure-Object | Select-Object -ExpandProperty Count)
$(php artisan migrate:status)

MODELS:
Total: $(Get-ChildItem app\Models\*.php | Measure-Object | Select-Object -ExpandProperty Count)

CONTROLLERS:
Total: $(Get-ChildItem app\Http\Controllers\*.php -Recurse | Measure-Object | Select-Object -ExpandProperty Count)

ROUTES:
Total: $(php artisan route:list --compact | Measure-Object -Line | Select-Object -ExpandProperty Lines)

VUE PAGES:
Total: $(Get-ChildItem resources\js\Pages\*.vue -Recurse | Measure-Object | Select-Object -ExpandProperty Count)

BUILD STATUS:
$(npm run build 2>&1)

TEST STATUS:
$(php artisan test 2>&1)
"@

$report | Out-File "MIGRATION_REPORT_$(Get-Date -Format 'yyyyMMdd').txt"
Write-Host "✅ Migration report saved"
```

---

## 🎯 Next Steps After Migration

1. **Update Documentation**: Update all `.md` files in `.github/`
2. **Git Commit**: Commit all changes with detailed message
3. **Testing Phase**: Thorough testing of all modules
4. **Deployment**: Deploy to staging/production

---

## 📞 Support

If you encounter issues during migration:

1. Check Laravel logs: `storage/logs/laravel.log`
2. Check browser console for JS errors
3. Run `php artisan optimize:clear` to clear all caches
4. Verify file permissions on Windows

---

**Migration Template Version**: 1.0  
**Last Updated**: November 19, 2025  
**Compatible With**: Laravel 12, Vue 3, Inertia 2.0
