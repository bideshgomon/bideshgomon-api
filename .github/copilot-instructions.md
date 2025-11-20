# BideshGomon Platform - AI Coding Agent Instructions

## Project Overview
**BideshGomon** is a Bangladesh-focused visa application and migration platform built with Laravel 12 + Inertia.js + Vue 3. The platform manages comprehensive user profiles, visa applications, referral rewards, and digital wallets—all tailored for Bangladeshi users with localized formatting.

**Tech Stack:** Laravel 12, Inertia.js 2.0, Vue 3, TailwindCSS, SQLite/MySQL, Vite

---

## Critical Architecture Patterns

### 1. Bangladesh Localization (Foundation Layer)
**Everything** in this codebase uses Bangladesh-specific formatting:

```php
// Backend: app/Helpers/bangladesh_helpers.php (auto-loaded via composer.json)
format_bd_currency($amount)     // ৳5,000.00 (NOT $5,000.00)
format_bd_date($date)           // 18/11/2025 (DD/MM/YYYY, NOT MM/DD/YYYY)
format_bd_phone($phone)         // 01712-345678 (NOT +1-555-...)
validate_bd_nid($nid)           // 10 or 17 digits
detect_bd_operator($phone)      // 'Grameenphone', 'Robi', etc.
get_bd_divisions()              // 8 divisions array
get_popular_destinations_bd()   // Popular destinations by purpose
```

```javascript
// Frontend: resources/js/Composables/useBangladeshFormat.js (use in ALL Vue components)
import { useBangladeshFormat } from '@/Composables/useBangladeshFormat'
const { formatCurrency, formatDate, formatTime } = useBangladeshFormat()

// ৳ symbol, comma separators, DD/MM/YYYY dates, 12-hour time
formatCurrency(1000)  // "৳1,000.00"
formatDate(date)      // "18/11/2025"
formatTime(date)      // "9:30 AM"
```

**Never** use generic `number_format()` or `.toLocaleString('en-US')` without Bangladesh wrappers.

### 2. Service Layer Pattern (Business Logic Container)
Controllers are thin—business logic lives in services:

```php
// app/Services/{WalletService, ReferralService}.php
// All wallet operations, transaction logic, reward approvals go through services
// Controllers only validate, call services, return Inertia responses

// Example flow:
Controller -> validate request
          -> call WalletService::creditWallet()
          -> WalletService wraps in DB::transaction()
          -> returns Inertia::render()
```

**Always** use existing services (`WalletService`, `ReferralService`) for their domain logic instead of duplicating in controllers.

### 3. Observer-Driven Automation
`UserObserver` auto-initializes critical records on user creation:

```php
// app/Observers/UserObserver.php + app/Providers/AppServiceProvider.php
User::created -> auto-create wallet (৳0.00)
              -> auto-generate referral code (8-char uppercase)
```

When adding new user-related models, follow this pattern: hook into `UserObserver` or create new observers registered in `AppServiceProvider::boot()`.

### 4. Inertia.js SPA Architecture
**No Blade views** except `resources/views/app.blade.php` (root template). All pages are Vue 3 components in `resources/js/Pages/`.

```javascript
// Controllers return Inertia, not views:
return Inertia::render('Wallet/Index', [
    'balance' => format_bd_currency($wallet->balance),
    'transactions' => $transactions->paginate(20)
]);

// Vue receives props, no axios calls needed for page data
<script setup>
const props = defineProps(['balance', 'transactions'])
</script>
```

**File uploads**: Use `useForm()` with file fields—Inertia handles multipart automatically.

```javascript
import { useForm } from '@inertiajs/vue3'
const form = useForm({
    passport_number: '',
    scan_front: null  // File input
})
form.post(route('profile.passports.store'))  // Handles files + CSRF
```

### 5. Role-Based Access Control
Four roles: `admin`, `user`, `agency`, `consultant` (see `database/seeders/RolesSeeder.php`).

```php
// routes/web.php
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(...)

// User model helpers:
$user->isAdmin()       // Check role
$user->hasRole('consultant')
```

**Admin routes** always use `/admin` prefix + `admin.` name prefix + `role:admin` middleware.

### 6. Comprehensive Profile System (Phase 7-8)
User profiles split across **9 specialized tables**:
- `user_profiles` (basic info)
- `user_passports` (multiple passports, `is_primary` flag)
- `user_visa_history` (rejections tracking)
- `user_travel_history` (required by USA/UK/AU)
- `user_family_members` (spouse, children for family visas)
- `user_financial_information` (bank statements, income)
- `user_security_information` (criminal records, medical)
- `user_educations` (degrees, certificates)
- `user_work_experiences` (employment history)
- `user_languages` (IELTS, TOEFL scores)

**Pattern for new profile components:**
1. Controller: `app/Http/Controllers/Profile/{Component}Controller.php`
2. Vue: `resources/js/Components/Profile/{Component}Management.vue`
3. Routes: `Route::prefix('profile/{component}')->name('profile.{component}.')` inside `auth` middleware
4. Always check `user_id` ownership: `$item->user_id === auth()->id()`

See `PHASE_8_PASSPORT_MANAGEMENT_COMPLETE.md` for full CRUD pattern reference.

---

## Development Workflows

### Essential Commands
```powershell
# Start dev server
php artisan serve

# Watch assets with HMR
npm run dev

# Production build (commit public/build/ to git)
npm run build

# CRITICAL: After route changes
php artisan ziggy:generate
```

### Clear Caches (After Config/Route Changes)
```powershell
php artisan config:clear
php artisan route:clear
php artisan cache:clear
php artisan view:clear
```

### Debugging
```powershell
# Tail logs in separate terminal
Get-Content storage/logs/laravel.log -Wait -Tail 50

# Check specific route issues
php artisan route:list | Select-String "wallet"

# Verify middleware
php artisan route:list --columns=Method,URI,Name,Middleware
```

### Database Migrations & Seeding
```powershell
# Fresh database (⚠️ destroys data)
php artisan migrate:fresh --seed

# Add data without wiping
php artisan db:seed --class=RolesSeeder
php artisan db:seed --class=ProfileManagementSeeder

# Create new migration
php artisan make:migration create_table_name --create=table_name
```

### File Storage (Uploads)
```powershell
# Required once before testing file uploads
php artisan storage:link  # Creates public/storage -> storage/app/public symlink
```

Files stored at `storage/app/public/{category}/` (e.g., `passports/scans/`), accessible via `/storage/{category}/filename`.

### Testing
```powershell
# Run tests (currently limited test coverage)
php artisan test

# Verify routes
php artisan route:list

# Check errors
php artisan log:tail
```

---

## Critical Conventions

### 1. Transaction Integrity
**All wallet operations** wrapped in `DB::transaction()`:

```php
use Illuminate\Support\Facades\DB;

DB::transaction(function () {
    $wallet->balance -= $amount;
    $wallet->save();
    WalletTransaction::create([...]);  // Audit trail
});
```

### 2. Balance Snapshots (Audit Trail)
`wallet_transactions` table stores `balance_before` and `balance_after` for every transaction—**never** modify transactions after creation.

### 3. Pagination Standard
**Always paginate** list pages (20 items default):

```php
// Controller
$items = Model::paginate(20);

// Vue (use Inertia Link components)
<Link v-for="link in items.links" :href="link.url" />
```

### 4. Status Badges UI Pattern
Consistent status colors across all pages:

```vue
<span :class="{
  'bg-green-100 text-green-800': status === 'approved',
  'bg-yellow-100 text-yellow-800': status === 'pending',
  'bg-red-100 text-red-800': status === 'rejected'
}" class="px-2 py-1 text-xs font-semibold rounded-full">
  {{ status }}
</span>
```

### 5. Empty States
All list views include empty states with icon + message:

```vue
<div v-if="items.data.length === 0" class="text-center py-12">
  <UserGroupIcon class="mx-auto h-12 w-12 text-gray-400" />
  <h3 class="mt-2 text-sm font-medium text-gray-900">No items yet</h3>
  <p class="mt-1 text-sm text-gray-500">Get started by creating a new item.</p>
</div>
```

### 6. Form Validation (Inertia Forms)
Use Inertia's `useForm()` for auto-CSRF, error handling, file uploads:

```vue
<script setup>
import { useForm } from '@inertiajs/vue3'

const form = useForm({
    name: '',
    file: null
})

function submit() {
    form.post(route('api.submit'), {
        onSuccess: () => form.reset(),
        onError: (errors) => console.log(errors)
    })
}
</script>

<template>
  <input v-model="form.name" />
  <span v-if="form.errors.name">{{ form.errors.name }}</span>
</template>
```

### 7. Eager Loading (N+1 Prevention)
**Always** eager load relationships in controllers:

```php
// BAD (causes N+1 queries)
$posts = BlogPost::paginate(20);

// GOOD
$posts = BlogPost::with(['category', 'author', 'tags'])
    ->latest()
    ->paginate(20);
```

### 8. File Upload Cleanup
**Always delete old files** before uploading new ones:

```php
// In controller update method
if ($request->hasFile('scan_front') && $model->scan_front_upload) {
    Storage::disk('public')->delete($model->scan_front_upload);
}
$model->scan_front_upload = $request->file('scan_front')->store('passports/scans', 'public');
```

### 9. Primary Flag Pattern (One Primary Per User)
For models with `is_primary` flag (passports, addresses):

```php
// Before setting new primary, unset all others for this user
if ($request->is_primary) {
    Model::where('user_id', $userId)
         ->where('id', '!=', $currentId)
         ->update(['is_primary' => false]);
}
```

---

## Integration Points

### Wallet ↔ Referral System
Reward approval flow:
1. Admin approves `Reward` → calls `ReferralService::approveReward()`
2. Service calls `WalletService::creditWallet()`
3. Wallet transaction created with `reference_type: 'referral_reward'`, `reference_id: {reward_id}`
4. Referral marked `is_completed: true`

### User Registration ↔ Observers
On `User::created`:
1. `UserObserver` auto-creates `Wallet` (৳0.00)
2. Auto-generates `referral_code` (8-char)
3. If registration had `?ref=CODE`, `ReferralService::trackReferral()` creates pending reward

---

## Common Pitfalls & Solutions

1. **Login Session Issues (Known Bug)**
   - Backend `Auth::attempt()` works, but frontend session not persisting
   - Workaround: Test with direct API calls or seed test users
   - Document mentions user continued building despite this

2. **Ziggy Routes Stale**
   - After adding routes, run `php artisan ziggy:generate`
   - Restart Vite dev server if routes not updating

3. **Form Validation Errors Not Showing**
   ```vue
   <!-- Inertia form errors are in form.errors -->
   <input v-model="form.email" :class="{ 'border-red-500': form.errors.email }" />
   <p v-if="form.errors.email" class="text-red-500 text-sm">{{ form.errors.email }}</p>
   ```

4. **File Upload 404s**
   - Ensure `php artisan storage:link` ran
   - Check files saved to `storage/app/public/`, not `storage/app/`

5. **Currency/Date Format Errors**
   - Always use `useBangladeshFormat` composable in Vue
   - Always use `format_bd_*` helpers in PHP
   - Check `config/bangladesh.php` for format specs (if it exists)

6. **Relationship Integrity on Delete**
   - Check foreign keys before delete: `if ($passport->visaHistory()->exists()) return error`
   - Example in `PassportController::destroy()`

---

## Phase Documentation Reference
- `PHASE_5_COMPLETE.md`: Referral system architecture
- `PHASE_8_PASSPORT_MANAGEMENT_COMPLETE.md`: CRUD component pattern
- `PHASE_4_WALLET_PLAN.md`: Wallet system design
- `config/bangladesh.php`: All localization constants

**Current Status:** Phases 1-8 complete (roles, profiles, wallet, referrals, passport management). Platform is production-ready for Bangladesh market.

---

## Testing Approach

### Manual Testing Checklist
- ✅ Test with different roles (admin, user, agency, consultant)
- ✅ Verify Bangladesh formatting (currency, dates, phones)
- ✅ Check form validation with invalid data
- ✅ Test file uploads and deletions
- ✅ Verify transaction atomicity (wallet operations)
- ✅ Check responsive design (mobile/tablet)

### Key Test Scenarios
1. **User Registration** → Wallet created, referral code generated
2. **Referral Flow** → Code tracking, reward pending, admin approval, wallet credit
3. **Profile CRUD** → Create, update, delete with validation
4. **Wallet Operations** → Credit, debit, balance checks, transaction history

---

## Path Aliases & Config

```javascript
// vite.config.js
resolve: {
  alias: {
    '@': path.resolve(__dirname, './resources/js'),
  }
}

// Usage in imports
import Layout from '@/Layouts/AuthenticatedLayout.vue'
import { formatCurrency } from '@/Composables/useBangladeshFormat'
```

---

## Key Files for Onboarding
1. `app/Helpers/bangladesh_helpers.php` - Formatting functions
2. `resources/js/Composables/useBangladeshFormat.js` - Vue formatting
3. `app/Services/{WalletService, ReferralService}.php` - Business logic
4. `app/Observers/UserObserver.php` - Auto-initialization
5. `routes/web.php` - All route patterns
6. `app/Providers/AppServiceProvider.php` - Observer registration
7. `resources/js/Pages/` - Vue page components (organized by feature)
8. `config/bangladesh.php` - Constants and cultural settings (if exists)

---

**Last Updated:** November 2025 | **Laravel:** 12.x | **Vue:** 3.x | **Inertia:** 2.x  
**Market:** 🇧🇩 Bangladesh | **Currency:** ৳ BDT | **Timezone:** Asia/Dhaka (BST +06:00)
