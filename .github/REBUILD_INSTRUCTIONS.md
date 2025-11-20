# BideshGomon Platform - Complete Rebuild Instructions

## Project Overview
This document provides step-by-step instructions for rebuilding the **BideshGomon** platform from scratch at `C:\xampp\htdocs\bgproject`. The platform is a Bangladesh-focused visa application and migration system with wallet, referral, and comprehensive profile management features.

**Target Location:** `C:\xampp\htdocs\bgproject`  
**Stack:** Laravel 12 + Inertia.js 2.0 + Vue 3 + TailwindCSS + SQLite/MySQL

---

## Phase 0: Foundation Setup (30 minutes)

### Step 1: Create New Laravel Project
```powershell
cd C:\xampp\htdocs
composer create-project laravel/laravel bgproject
cd bgproject
```

### Step 2: Install Core Dependencies
```powershell
# Install Inertia.js + Vue 3 stack
composer require inertiajs/inertia-laravel:"^2.0"
composer require tightenco/ziggy:"^2.0"

# Install Laravel Breeze with Vue
composer require laravel/breeze:"^2.3" --dev
php artisan breeze:install vue
```

### Step 3: Install Frontend Dependencies
```powershell
npm install
npm install @heroicons/vue
```

### Step 4: Database Configuration
Edit `.env`:
```env
APP_NAME="BideshGomon"
APP_URL=http://localhost/bgproject

# SQLite (default for development)
DB_CONNECTION=sqlite

# OR MySQL (production)
# DB_CONNECTION=mysql
# DB_HOST=127.0.0.1
# DB_PORT=3306
# DB_DATABASE=bgplatform
# DB_USERNAME=root
# DB_PASSWORD=
```

Create SQLite database:
```powershell
New-Item -ItemType File -Path database\database.sqlite -Force
```

### Step 5: Initial Build
```powershell
php artisan migrate
php artisan key:generate
npm run build
```

---

## Phase 1: Bangladesh Localization (45 minutes)

### Step 1: Create Bangladesh Config
Create `config/bangladesh.php`:
```php
<?php

return [
    'currency' => [
        'code' => 'BDT',
        'symbol' => '৳',
        'name' => 'Bangladeshi Taka',
        'decimal_separator' => '.',
        'thousand_separator' => ',',
        'decimals' => 2,
        'format' => '৳%s',
    ],
    
    'date_format' => [
        'display' => 'd/m/Y',
        'database' => 'Y-m-d',
        'datetime_display' => 'd/m/Y h:i A',
        'datetime_database' => 'Y-m-d H:i:s',
    ],
    
    'phone' => [
        'country_code' => '+880',
        'format' => '+880 1XXX-XXXXXX',
        'regex' => '/^(?:\+?880|0)?1[3-9]\d{8}$/',
        'placeholder' => '01712-345678',
    ],
    
    'address' => [
        'divisions' => [
            'Dhaka', 'Chittagong', 'Rajshahi', 'Khulna', 
            'Barisal', 'Sylhet', 'Rangpur', 'Mymensingh',
        ],
    ],
    
    'financial' => [
        'min_transaction' => 100,
        'max_transaction' => 1000000,
        'referral_bonus' => 100,
        'signup_bonus' => 50,
    ],
];
```

### Step 2: Create Backend Helpers
Create `app/Helpers/bangladesh_helpers.php`:
```php
<?php

if (! function_exists('format_bd_currency')) {
    function format_bd_currency($amount, $showSymbol = true)
    {
        $formatted = number_format($amount, 2, '.', ',');
        return $showSymbol ? '৳'.$formatted : $formatted;
    }
}

if (! function_exists('format_bd_date')) {
    function format_bd_date($date, $includeTime = false)
    {
        if (! $date) return '';
        $dateTime = $date instanceof DateTime ? $date : new DateTime($date);
        $format = $includeTime ? 'd/m/Y h:i A' : 'd/m/Y';
        return $dateTime->format($format);
    }
}

if (! function_exists('format_bd_phone')) {
    function format_bd_phone($phone)
    {
        $phone = preg_replace('/[^0-9]/', '', $phone);
        $phone = preg_replace('/^(880|0)/', '', $phone);
        if (strlen($phone) === 10 && $phone[0] === '1') {
            return '0'.substr($phone, 0, 4).'-'.substr($phone, 4);
        }
        return $phone;
    }
}

if (! function_exists('validate_bd_phone')) {
    function validate_bd_phone($phone)
    {
        $phone = preg_replace('/[^0-9]/', '', $phone);
        return preg_match('/^(?:880|0)?1[3-9]\d{8}$/', $phone);
    }
}
```

Update `composer.json` to autoload helpers:
```json
"autoload": {
    "psr-4": {
        "App\\": "app/",
        "Database\\Factories\\": "database/factories/",
        "Database\\Seeders\\": "database/seeders/"
    },
    "files": [
        "app/Helpers/bangladesh_helpers.php"
    ]
}
```

Run: `composer dump-autoload`

### Step 3: Create Frontend Composable
Create `resources/js/Composables/useBangladeshFormat.js`:
```javascript
import { computed } from 'vue'

export function useBangladeshFormat() {
  const formatCurrency = (amount, showSymbol = true) => {
    if (amount === null || amount === undefined) return showSymbol ? '৳0.00' : '0.00'
    const formatted = Number(amount).toLocaleString('en-BD', {
      minimumFractionDigits: 2,
      maximumFractionDigits: 2,
    })
    return showSymbol ? `৳${formatted}` : formatted
  }

  const formatDate = (date, includeTime = false) => {
    if (!date) return ''
    const d = new Date(date)
    if (isNaN(d.getTime())) return date
    
    const day = String(d.getDate()).padStart(2, '0')
    const month = String(d.getMonth() + 1).padStart(2, '0')
    const year = d.getFullYear()
    
    let formatted = `${day}/${month}/${year}`
    
    if (includeTime) {
      let hours = d.getHours()
      const minutes = String(d.getMinutes()).padStart(2, '0')
      const ampm = hours >= 12 ? 'PM' : 'AM'
      hours = hours % 12 || 12
      formatted += ` ${hours}:${minutes} ${ampm}`
    }
    
    return formatted
  }

  const formatPhone = (phone) => {
    if (!phone) return ''
    let cleaned = phone.toString().replace(/\D/g, '')
    cleaned = cleaned.replace(/^(880|0)/, '')
    if (cleaned.length === 10 && cleaned[0] === '1') {
      return `0${cleaned.slice(0, 4)}-${cleaned.slice(4)}`
    }
    return phone
  }

  const formatTime = (date) => {
    if (!date) return ''
    const d = new Date(date)
    if (isNaN(d.getTime())) return date
    let hours = d.getHours()
    const minutes = String(d.getMinutes()).padStart(2, '0')
    const ampm = hours >= 12 ? 'PM' : 'AM'
    hours = hours % 12 || 12
    return `${hours}:${minutes} ${ampm}`
  }

  return {
    formatCurrency,
    formatDate,
    formatPhone,
    formatTime,
  }
}
```

---

## Phase 2: Roles & Permissions System (30 minutes)

### Step 1: Create Roles Migration
```powershell
php artisan make:migration create_roles_table --create=roles
```

Migration content:
```php
Schema::create('roles', function (Blueprint $table) {
    $table->id();
    $table->string('name');
    $table->string('slug')->unique();
    $table->text('description')->nullable();
    $table->timestamps();
});
```

Add role to users table:
```powershell
php artisan make:migration add_role_id_to_users_table --table=users
```

```php
Schema::table('users', function (Blueprint $table) {
    $table->foreignId('role_id')->nullable()->constrained()->onDelete('set null');
});
```

### Step 2: Create Role Model
`app/Models/Role.php`:
```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Role extends Model
{
    protected $fillable = ['name', 'slug', 'description'];

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }
}
```

### Step 3: Update User Model
Add to `app/Models/User.php`:
```php
use Illuminate\Database\Eloquent\Relations\BelongsTo;

protected $fillable = [
    'name',
    'email',
    'password',
    'role_id',
];

public function role(): BelongsTo
{
    return $this->belongsTo(Role::class);
}

public function hasRole(string $roleSlug): bool
{
    return $this->role && $this->role->slug === $roleSlug;
}

public function isAdmin(): bool
{
    return $this->hasRole('admin');
}

public function isUser(): bool
{
    return $this->hasRole('user');
}
```

### Step 4: Create Role Middleware
```powershell
php artisan make:middleware EnsureUserHasRole
```

`app/Http/Middleware/EnsureUserHasRole.php`:
```php
<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EnsureUserHasRole
{
    public function handle(Request $request, Closure $next, ...$roles)
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $user = Auth::user();
        $user->loadMissing('role');

        if (!$user->role) {
            abort(403, 'No role assigned.');
        }

        $roleSlug = strtolower($user->role->slug ?? '');

        foreach ($roles as $role) {
            if ($roleSlug === strtolower($role)) {
                return $next($request);
            }
            // Allow admins to access user routes
            if (strtolower($role) === 'user' && $roleSlug === 'admin') {
                return $next($request);
            }
        }

        abort(403, 'Insufficient permissions.');
    }
}
```

Register middleware in `bootstrap/app.php`:
```php
->withMiddleware(function (Middleware $middleware): void {
    $middleware->alias([
        'role' => \App\Http\Middleware\EnsureUserHasRole::class,
    ]);
})
```

### Step 5: Create Roles Seeder
```powershell
php artisan make:seeder RolesSeeder
```

```php
<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;

class RolesSeeder extends Seeder
{
    public function run(): void
    {
        $roles = [
            ['name' => 'Admin', 'slug' => 'admin', 'description' => 'Platform administrator'],
            ['name' => 'User', 'slug' => 'user', 'description' => 'Regular user'],
            ['name' => 'Agency', 'slug' => 'agency', 'description' => 'Agency account'],
            ['name' => 'Consultant', 'slug' => 'consultant', 'description' => 'Consultant account'],
        ];

        foreach ($roles as $roleData) {
            Role::firstOrCreate(['slug' => $roleData['slug']], $roleData);
        }
    }
}
```

Run migrations and seed:
```powershell
php artisan migrate
php artisan db:seed --class=RolesSeeder
```

---

## Phase 3: User Profiles System (45 minutes)

### Step 1: Create User Profiles Migration
```powershell
php artisan make:migration create_user_profiles_table --create=user_profiles
```

```php
Schema::create('user_profiles', function (Blueprint $table) {
    $table->id();
    $table->foreignId('user_id')->constrained()->onDelete('cascade');
    $table->text('bio')->nullable();
    $table->string('phone')->nullable();
    $table->date('dob')->nullable();
    $table->enum('gender', ['male', 'female', 'other'])->nullable();
    $table->string('nationality')->default('Bangladeshi');
    $table->string('nid')->nullable();
    $table->string('passport_number')->nullable();
    $table->string('present_address_line')->nullable();
    $table->string('present_city')->nullable();
    $table->string('present_division')->nullable();
    $table->string('present_postal_code')->nullable();
    $table->string('permanent_address_line')->nullable();
    $table->string('permanent_city')->nullable();
    $table->string('permanent_division')->nullable();
    $table->string('permanent_postal_code')->nullable();
    $table->string('profile_picture')->nullable();
    $table->timestamps();
});
```

### Step 2: Create UserProfile Model
```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserProfile extends Model
{
    protected $fillable = [
        'user_id', 'bio', 'phone', 'dob', 'gender', 'nationality',
        'nid', 'passport_number', 'present_address_line', 'present_city',
        'present_division', 'present_postal_code', 'permanent_address_line',
        'permanent_city', 'permanent_division', 'permanent_postal_code',
        'profile_picture',
    ];

    protected $casts = [
        'dob' => 'date',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
```

Add to User model:
```php
use Illuminate\Database\Eloquent\Relations\HasOne;

public function profile(): HasOne
{
    return $this->hasOne(UserProfile::class);
}
```

---

## Phase 4: Wallet System (60 minutes)

### Step 1: Create Wallet Migrations
```powershell
php artisan make:migration create_wallets_table --create=wallets
php artisan make:migration create_wallet_transactions_table --create=wallet_transactions
```

**Wallets table:**
```php
Schema::create('wallets', function (Blueprint $table) {
    $table->id();
    $table->foreignId('user_id')->unique()->constrained()->onDelete('cascade');
    $table->decimal('balance', 15, 2)->default(0.00);
    $table->string('currency', 3)->default('BDT');
    $table->enum('status', ['active', 'suspended', 'closed'])->default('active');
    $table->timestamps();
});
```

**Wallet transactions table:**
```php
Schema::create('wallet_transactions', function (Blueprint $table) {
    $table->id();
    $table->foreignId('wallet_id')->constrained()->onDelete('cascade');
    $table->enum('transaction_type', ['credit', 'debit']);
    $table->decimal('amount', 15, 2);
    $table->decimal('balance_before', 15, 2);
    $table->decimal('balance_after', 15, 2);
    $table->string('description');
    $table->string('reference_type', 50)->nullable();
    $table->unsignedBigInteger('reference_id')->nullable();
    $table->enum('status', ['pending', 'completed', 'failed', 'reversed'])->default('pending');
    $table->foreignId('processed_by')->nullable()->constrained('users')->onDelete('set null');
    $table->timestamp('processed_at')->nullable();
    $table->json('metadata')->nullable();
    $table->timestamps();
});
```

### Step 2: Create Wallet Models

**Wallet Model** (`app/Models/Wallet.php`):
```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Wallet extends Model
{
    protected $fillable = ['user_id', 'balance', 'currency', 'status'];
    protected $casts = ['balance' => 'decimal:2'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(WalletTransaction::class)->latest();
    }

    public function credit(float $amount, string $description, ?string $referenceType = null, ?int $referenceId = null, ?array $metadata = null)
    {
        $balanceBefore = $this->balance;
        $this->balance += $amount;
        $balanceAfter = $this->balance;
        $this->save();

        return $this->transactions()->create([
            'transaction_type' => 'credit',
            'amount' => $amount,
            'balance_before' => $balanceBefore,
            'balance_after' => $balanceAfter,
            'description' => $description,
            'reference_type' => $referenceType,
            'reference_id' => $referenceId,
            'status' => 'completed',
            'processed_at' => now(),
            'metadata' => $metadata,
        ]);
    }

    public function debit(float $amount, string $description, ?string $referenceType = null, ?int $referenceId = null, ?array $metadata = null)
    {
        if (!$this->hasBalance($amount)) {
            throw new \Exception('Insufficient balance');
        }

        $balanceBefore = $this->balance;
        $this->balance -= $amount;
        $balanceAfter = $this->balance;
        $this->save();

        return $this->transactions()->create([
            'transaction_type' => 'debit',
            'amount' => $amount,
            'balance_before' => $balanceBefore,
            'balance_after' => $balanceAfter,
            'description' => $description,
            'reference_type' => $referenceType,
            'reference_id' => $referenceId,
            'status' => 'completed',
            'processed_at' => now(),
            'metadata' => $metadata,
        ]);
    }

    public function hasBalance(float $amount): bool
    {
        return $this->balance >= $amount;
    }

    public function isActive(): bool
    {
        return $this->status === 'active';
    }
}
```

**WalletTransaction Model** (`app/Models/WalletTransaction.php`):
```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WalletTransaction extends Model
{
    protected $fillable = [
        'wallet_id', 'transaction_type', 'amount', 'balance_before',
        'balance_after', 'description', 'reference_type', 'reference_id',
        'status', 'processed_by', 'processed_at', 'metadata',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'balance_before' => 'decimal:2',
        'balance_after' => 'decimal:2',
        'metadata' => 'array',
        'processed_at' => 'datetime',
    ];

    public function wallet(): BelongsTo
    {
        return $this->belongsTo(Wallet::class);
    }

    public function processedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'processed_by');
    }
}
```

### Step 3: Create WalletService

`app/Services/WalletService.php`:
```php
<?php

namespace App\Services;

use App\Models\User;
use App\Models\Wallet;
use Illuminate\Support\Facades\DB;

class WalletService
{
    public function createWallet(User $user): Wallet
    {
        return $user->wallet()->create([
            'balance' => 0.00,
            'currency' => 'BDT',
            'status' => 'active',
        ]);
    }

    public function creditWallet(
        Wallet $wallet,
        float $amount,
        string $description,
        ?string $referenceType = null,
        ?int $referenceId = null,
        ?array $metadata = null,
        ?int $processedBy = null
    ) {
        return DB::transaction(function () use ($wallet, $amount, $description, $referenceType, $referenceId, $metadata, $processedBy) {
            if (!$wallet->isActive()) {
                throw new \Exception('Wallet is not active');
            }

            $transaction = $wallet->credit($amount, $description, $referenceType, $referenceId, $metadata);
            
            if ($processedBy) {
                $transaction->update(['processed_by' => $processedBy]);
            }

            return $transaction;
        });
    }

    public function debitWallet(
        Wallet $wallet,
        float $amount,
        string $description,
        ?string $referenceType = null,
        ?int $referenceId = null,
        ?array $metadata = null,
        ?int $processedBy = null
    ) {
        return DB::transaction(function () use ($wallet, $amount, $description, $referenceType, $referenceId, $metadata, $processedBy) {
            if (!$wallet->isActive()) {
                throw new \Exception('Wallet is not active');
            }

            if (!$wallet->hasBalance($amount)) {
                throw new \Exception('Insufficient balance');
            }

            $transaction = $wallet->debit($amount, $description, $referenceType, $referenceId, $metadata);
            
            if ($processedBy) {
                $transaction->update(['processed_by' => $processedBy]);
            }

            return $transaction;
        });
    }
}
```

### Step 4: Create UserObserver (Auto-create Wallet)

```powershell
php artisan make:observer UserObserver --model=User
```

`app/Observers/UserObserver.php`:
```php
<?php

namespace App\Observers;

use App\Models\User;
use App\Services\WalletService;

class UserObserver
{
    protected WalletService $walletService;

    public function __construct(WalletService $walletService)
    {
        $this->walletService = $walletService;
    }

    public function created(User $user): void
    {
        // Auto-create wallet for new users
        $this->walletService->createWallet($user);
    }
}
```

Register observer in `app/Providers/AppServiceProvider.php`:
```php
use App\Models\User;
use App\Observers\UserObserver;

public function boot(): void
{
    User::observe(UserObserver::class);
}
```

Add to User model:
```php
public function wallet(): HasOne
{
    return $this->hasOne(Wallet::class);
}
```

---

## Phase 5: Referral System (60 minutes)

### Step 1: Create Referral Migrations
```powershell
php artisan make:migration add_referral_fields_to_users_table --table=users
php artisan make:migration create_referrals_table --create=referrals
php artisan make:migration create_rewards_table --create=rewards
```

**Add to users:**
```php
Schema::table('users', function (Blueprint $table) {
    $table->string('referral_code', 8)->unique()->nullable();
    $table->foreignId('referred_by')->nullable()->constrained('users')->onDelete('set null');
});
```

**Referrals table:**
```php
Schema::create('referrals', function (Blueprint $table) {
    $table->id();
    $table->foreignId('referrer_id')->constrained('users')->onDelete('cascade');
    $table->foreignId('referred_id')->constrained('users')->onDelete('cascade');
    $table->string('referral_code', 8);
    $table->enum('status', ['pending', 'completed'])->default('pending');
    $table->boolean('reward_paid')->default(false);
    $table->decimal('reward_amount', 10, 2)->default(0.00);
    $table->timestamps();
});
```

**Rewards table:**
```php
Schema::create('rewards', function (Blueprint $table) {
    $table->id();
    $table->foreignId('user_id')->constrained()->onDelete('cascade');
    $table->string('reward_type');
    $table->decimal('amount', 10, 2);
    $table->text('description');
    $table->enum('status', ['pending', 'approved', 'rejected', 'paid'])->default('pending');
    $table->timestamp('approved_at')->nullable();
    $table->foreignId('approved_by')->nullable()->constrained('users')->onDelete('set null');
    $table->foreignId('referral_id')->nullable()->constrained()->onDelete('cascade');
    $table->json('metadata')->nullable();
    $table->timestamps();
});
```

### Step 2: Create Referral Models

**Referral Model:**
```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Referral extends Model
{
    protected $fillable = [
        'referrer_id', 'referred_id', 'referral_code',
        'status', 'reward_paid', 'reward_amount',
    ];

    protected $casts = [
        'reward_paid' => 'boolean',
        'reward_amount' => 'decimal:2',
    ];

    public function referrer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'referrer_id');
    }

    public function referred(): BelongsTo
    {
        return $this->belongsTo(User::class, 'referred_id');
    }

    public function complete(): void
    {
        $this->update(['status' => 'completed']);
    }

    public function markRewardPaid(float $amount): void
    {
        $this->update([
            'reward_paid' => true,
            'reward_amount' => $amount,
        ]);
    }
}
```

**Reward Model:**
```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Reward extends Model
{
    protected $fillable = [
        'user_id', 'reward_type', 'amount', 'description',
        'status', 'approved_at', 'approved_by', 'referral_id', 'metadata',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'approved_at' => 'datetime',
        'metadata' => 'array',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function approvedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function referral(): BelongsTo
    {
        return $this->belongsTo(Referral::class);
    }

    public function approve(int $approvedBy): void
    {
        $this->update([
            'status' => 'approved',
            'approved_at' => now(),
            'approved_by' => $approvedBy,
        ]);
    }

    public function markPaid(): void
    {
        $this->update(['status' => 'paid']);
    }
}
```

### Step 3: Create ReferralService

`app/Services/ReferralService.php`:
```php
<?php

namespace App\Services;

use App\Models\Referral;
use App\Models\Reward;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ReferralService
{
    protected WalletService $walletService;

    public function __construct(WalletService $walletService)
    {
        $this->walletService = $walletService;
    }

    public function generateReferralCode(User $user): string
    {
        do {
            $code = strtoupper(Str::random(8));
        } while (User::where('referral_code', $code)->exists());

        $user->update(['referral_code' => $code]);
        return $code;
    }

    public function trackReferral(User $newUser, string $referralCode): ?Referral
    {
        $referrer = User::where('referral_code', $referralCode)->first();

        if (!$referrer || $referrer->id === $newUser->id) {
            return null;
        }

        $newUser->update(['referred_by' => $referrer->id]);

        $referral = Referral::create([
            'referrer_id' => $referrer->id,
            'referred_id' => $newUser->id,
            'referral_code' => $referralCode,
            'status' => 'pending',
        ]);

        $this->createSignupReward($referrer, $referral);

        return $referral;
    }

    protected function createSignupReward(User $referrer, Referral $referral): Reward
    {
        $rewardAmount = 100.00; // ৳100 signup bonus

        return Reward::create([
            'user_id' => $referrer->id,
            'reward_type' => 'referral_signup',
            'amount' => $rewardAmount,
            'description' => "Referral signup bonus for {$referral->referred->name}",
            'status' => 'pending',
            'referral_id' => $referral->id,
        ]);
    }

    public function approveReward(Reward $reward, int $approvedBy): void
    {
        DB::transaction(function () use ($reward, $approvedBy) {
            $reward->approve($approvedBy);

            $wallet = $reward->user->wallet;
            if ($wallet) {
                $this->walletService->creditWallet(
                    $wallet,
                    (float) $reward->amount,
                    $reward->description,
                    'referral_reward',
                    $reward->id,
                    ['reward_type' => $reward->reward_type],
                    $approvedBy
                );
            }

            $reward->markPaid();

            if ($reward->referral) {
                $reward->referral->complete();
                $reward->referral->markRewardPaid((float) $reward->amount);
            }
        });
    }
}
```

### Step 4: Update UserObserver

Add referral code generation to `UserObserver`:
```php
use App\Services\ReferralService;

protected ReferralService $referralService;

public function __construct(WalletService $walletService, ReferralService $referralService)
{
    $this->walletService = $walletService;
    $this->referralService = $referralService;
}

public function created(User $user): void
{
    $this->walletService->createWallet($user);
    $this->referralService->generateReferralCode($user);
}
```

Add to User model:
```php
public function referrals(): HasMany
{
    return $this->hasMany(Referral::class, 'referrer_id');
}

public function referredBy(): BelongsTo
{
    return $this->belongsTo(User::class, 'referred_by');
}

public function rewards(): HasMany
{
    return $this->hasMany(Reward::class);
}
```

---

## Phase 6: Profile Components (9 Tables - 120 minutes)

Create migrations for comprehensive profile system:

```powershell
php artisan make:migration create_user_passports_table --create=user_passports
php artisan make:migration create_user_visa_history_table --create=user_visa_history
php artisan make:migration create_user_travel_history_table --create=user_travel_history
php artisan make:migration create_user_family_members_table --create=user_family_members
php artisan make:migration create_user_financial_information_table --create=user_financial_information
php artisan make:migration create_user_security_information_table --create=user_security_information
php artisan make:migration create_user_educations_table --create=user_educations
php artisan make:migration create_user_work_experiences_table --create=user_work_experiences
php artisan make:migration create_user_languages_table --create=user_languages
```

**Copy table schemas from existing migrations in `database/migrations/` directory**

Create corresponding models:
```powershell
php artisan make:model UserPassport
php artisan make:model UserVisaHistory
php artisan make:model UserTravelHistory
php artisan make:model UserFamilyMember
php artisan make:model UserFinancialInformation
php artisan make:model UserSecurityInformation
php artisan make:model UserEducation
php artisan make:model UserWorkExperience
php artisan make:model UserLanguage
```

**Copy model code from existing `app/Models/` files**

Add relationships to User model:
```php
public function passports(): HasMany { return $this->hasMany(UserPassport::class); }
public function visaHistory(): HasMany { return $this->hasMany(UserVisaHistory::class); }
public function travelHistory(): HasMany { return $this->hasMany(UserTravelHistory::class); }
public function familyMembers(): HasMany { return $this->hasMany(UserFamilyMember::class); }
public function financialInformation(): HasOne { return $this->hasOne(UserFinancialInformation::class); }
public function securityInformation(): HasOne { return $this->hasOne(UserSecurityInformation::class); }
public function educations(): HasMany { return $this->hasMany(UserEducation::class); }
public function workExperiences(): HasMany { return $this->hasMany(UserWorkExperience::class); }
public function languages(): HasMany { return $this->hasMany(UserLanguage::class); }
```

---

## Phase 7: Controllers & Routes (Minimal Design)

### Create Basic Controllers

```powershell
# User controllers
php artisan make:controller WalletController
php artisan make:controller ReferralController
php artisan make:controller ProfileController

# Profile section controllers
php artisan make:controller Profile/PassportController
php artisan make:controller Profile/VisaHistoryController
php artisan make:controller Profile/TravelHistoryController
php artisan make:controller Profile/FamilyMemberController
php artisan make:controller Profile/UserEducationController
php artisan make:controller Profile/UserWorkExperienceController

# Admin controllers
php artisan make:controller Admin/WalletController
php artisan make:controller Admin/RewardController
```

### Configure Routes

Copy routes from `routes/web.php` in existing project. Pattern:

```php
Route::middleware('auth')->group(function () {
    // User routes
    Route::get('/wallet', [WalletController::class, 'index'])->name('wallet.index');
    Route::get('/referrals', [ReferralController::class, 'index'])->name('referrals.index');
    
    // Profile routes
    Route::prefix('profile/passports')->name('profile.passports.')->group(function () {
        Route::get('/', [PassportController::class, 'index'])->name('index');
        Route::post('/', [PassportController::class, 'store'])->name('store');
        Route::put('/{id}', [PassportController::class, 'update'])->name('update');
        Route::delete('/{id}', [PassportController::class, 'destroy'])->name('destroy');
    });
});

Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/wallets', [Admin\WalletController::class, 'index'])->name('wallets.index');
    Route::get('/rewards', [Admin\RewardController::class, 'index'])->name('rewards.index');
    Route::post('/rewards/{reward}/approve', [Admin\RewardController::class, 'approve'])->name('rewards.approve');
});
```

---

## Phase 8: Vue Pages (Minimal Design)

### Create Page Structure

```
resources/js/Pages/
├── Dashboard.vue
├── Wallet/
│   ├── Index.vue
│   └── Transactions.vue
├── Referral/
│   ├── Index.vue
│   ├── Referrals.vue
│   └── Rewards.vue
├── Profile/
│   └── Edit.vue
└── Admin/
    ├── Wallets/
    │   ├── Index.vue
    │   └── Show.vue
    └── Rewards/
        └── Index.vue
```

### Basic Vue Component Template

```vue
<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { useBangladeshFormat } from '@/Composables/useBangladeshFormat'
import { Head } from '@inertiajs/vue3'

const props = defineProps({
    // Props from controller
})

const { formatCurrency, formatDate } = useBangladeshFormat()
</script>

<template>
    <Head title="Page Title" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold">Page Title</h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <!-- Page content -->
            </div>
        </div>
    </AuthenticatedLayout>
</template>
```

Copy Vue pages from existing `resources/js/Pages/` directory.

---

## Phase 9: Database Seeding

### Create Seeders

```powershell
php artisan make:seeder ProfileManagementSeeder
php artisan make:seeder SimpleBangladeshiSeeder
```

Copy seeder code from existing `database/seeders/` directory.

Update `DatabaseSeeder.php`:
```php
public function run(): void
{
    $this->call(RolesSeeder::class);
    $this->call(ProfileManagementSeeder::class);
    $this->call(SimpleBangladeshiSeeder::class);
}
```

Run seeders:
```powershell
php artisan migrate:fresh --seed
```

---

## Phase 10: File Storage Setup

```powershell
php artisan storage:link
```

---

## Phase 11: Generate Ziggy Routes

```powershell
php artisan ziggy:generate
```

---

## Phase 12: Build & Test

```powershell
npm run build
php artisan serve
```

Visit: `http://localhost:8000`

Test accounts (after seeding):
- Admin: admin@bgplatform.com / password
- User: user@bgplatform.com / password

---

## Verification Checklist

- [ ] Bangladesh currency formatting (৳) works
- [ ] Date format is DD/MM/YYYY
- [ ] Phone format is 01XXX-XXXXXX
- [ ] User registration auto-creates wallet (৳0.00)
- [ ] User registration auto-generates referral code
- [ ] Wallet transactions show balance before/after
- [ ] Referral system tracks signups with ?ref=CODE
- [ ] Admin can approve rewards → credits wallet
- [ ] File uploads work for passport scans
- [ ] Role middleware blocks unauthorized access
- [ ] All profile sections load without errors

---

## Known Issues & Workarounds

1. **Login Session Bug**: Backend auth works but frontend session may not persist. Workaround: Use seeded users or test API directly.
2. **Ziggy Routes Stale**: Run `php artisan ziggy:generate` after route changes.
3. **File Upload 404s**: Ensure `php artisan storage:link` was executed.

---

## Deployment to Production

```powershell
# Set environment
APP_ENV=production
APP_DEBUG=false

# Build assets
npm run build

# Optimize Laravel
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Run migrations
php artisan migrate --force
php artisan db:seed --class=RolesSeeder
```

---

## Future Modern Template Integration

Once minimal version is tested, modern template can be integrated:

1. **Choose template** (AdminLTE, Tailwind UI, Vuetify)
2. **Replace layouts** (`AuthenticatedLayout.vue`, `GuestLayout.vue`)
3. **Update components** with template's design system
4. **Keep logic unchanged** - only update UI layer
5. **Test all features** after template integration

The modular architecture allows easy template swapping without breaking business logic.

---

## Reference Documentation

- **Original project**: `C:\xampp\htdocs\bgplatform-fresh`
- **Copilot instructions**: `.github/copilot-instructions.md`
- **Phase docs**: `PHASE_*.md` files
- **Bangladesh config**: `config/bangladesh.php`
- **Service layer**: `app/Services/`
- **Observers**: `app/Observers/UserObserver.php`

---

**Built with ❤️ for BideshGomon Platform**  
**Total Rebuild Time Estimate**: 6-8 hours (all phases)
