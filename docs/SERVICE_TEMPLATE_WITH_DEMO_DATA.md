# Service Creation Template (2025 Standard)

## 🎯 Every New Service Must Follow This Template

When you create ANY service (Visa Application, Flight Booking, Insurance, etc.), you MUST:
1. ✅ Create database migration
2. ✅ Create Eloquent model with relationships
3. ✅ Create service class (business logic)
4. ✅ Create controller (thin layer)
5. ✅ Create API routes
6. ✅ Create Vue.js pages/components
7. ✅ Create demo data seeder
8. ✅ Create feature tests
9. ✅ Update documentation

---

## 📁 Folder Structure Example

For a new service called **"Travel Insurance"**:

```
app/
├── Models/
│   └── TravelInsurance.php
├── Services/
│   └── TravelInsuranceService.php
├── Http/
│   └── Controllers/
│       ├── User/
│       │   └── TravelInsuranceController.php
│       ├── Agency/
│       │   └── TravelInsuranceController.php
│       └── Admin/
│           └── TravelInsuranceController.php
database/
├── migrations/
│   └── 2024_01_10_000001_create_travel_insurances_table.php
└── seeders/
    ├── TravelInsuranceDemoSeeder.php
    └── TravelInsuranceProviderSeeder.php (reference data)
resources/
└── js/
    └── Pages/
        ├── User/
        │   ├── TravelInsurance/
        │   │   ├── Index.vue
        │   │   ├── Create.vue
        │   │   ├── Show.vue
        │   │   └── Components/
        │   │       ├── QuoteForm.vue
        │   │       └── PolicyCard.vue
        ├── Agency/
        │   └── TravelInsurance/
        │       ├── Index.vue
        │       └── Manage.vue
        └── Admin/
            └── TravelInsurance/
                ├── Index.vue
                └── Settings.vue
routes/
├── web.php (user routes)
├── agency.php (agency routes)
└── admin.php (admin routes)
tests/
└── Feature/
    └── TravelInsuranceTest.php
docs/
└── services/
    └── TRAVEL_INSURANCE_SERVICE.md
```

---

## 🗄️ Step 1: Database Migration

**File**: `database/migrations/2024_01_10_000001_create_travel_insurances_table.php`

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('travel_insurances', function (Blueprint $table) {
            $table->id();
            
            // User & Agency Relations (ALWAYS required for services)
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('agency_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('consultant_id')->nullable()
                  ->constrained('consultant_profiles')->nullOnDelete();
            
            // Service-Specific Data (Use FK to reference tables!)
            $table->string('policy_number')->unique()->index();
            $table->foreignId('destination_country_id')->constrained('countries');
            $table->foreignId('insurance_provider_id')
                  ->constrained('insurance_providers'); // Central reference!
            
            // Trip Details
            $table->date('coverage_start_date');
            $table->date('coverage_end_date');
            $table->integer('trip_duration_days');
            $table->string('trip_type'); // single, annual
            
            // Coverage Details
            $table->decimal('coverage_amount', 12, 2);
            $table->foreignId('currency_id')->constrained();
            $table->json('coverage_details'); // Store structured data
            
            // Traveler Details (How many people covered)
            $table->integer('traveler_count')->default(1);
            $table->json('traveler_ages'); // [25, 30] for 2 adults
            
            // Financial
            $table->decimal('premium_amount', 10, 2);
            $table->foreignId('premium_currency_id')
                  ->constrained('currencies');
            $table->decimal('commission_amount', 10, 2)->nullable();
            
            // Status Tracking
            $table->enum('status', [
                'draft',
                'quote_requested',
                'quote_received',
                'payment_pending',
                'active',
                'expired',
                'cancelled',
                'claimed'
            ])->default('draft')->index();
            
            // Timestamps for status changes
            $table->timestamp('quote_requested_at')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->timestamp('activated_at')->nullable();
            $table->timestamp('expired_at')->nullable();
            $table->timestamp('cancelled_at')->nullable();
            
            // Document Storage
            $table->string('policy_document_path')->nullable();
            $table->string('invoice_path')->nullable();
            
            // Metadata
            $table->json('provider_response')->nullable(); // API response
            $table->text('notes')->nullable();
            
            $table->timestamps();
            $table->softDeletes();
            
            // Indexes for common queries
            $table->index(['user_id', 'status']);
            $table->index(['agency_id', 'status']);
            $table->index('coverage_start_date');
        });
        
        // Create pivot table for additional destinations (multi-country coverage)
        Schema::create('travel_insurance_countries', function (Blueprint $table) {
            $table->foreignId('travel_insurance_id')
                  ->constrained()->cascadeOnDelete();
            $table->foreignId('country_id')->constrained();
            $table->primary(['travel_insurance_id', 'country_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('travel_insurance_countries');
        Schema::dropIfExists('travel_insurances');
    }
};
```

---

## 📦 Step 2: Eloquent Model

**File**: `app/Models/TravelInsurance.php`

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class TravelInsurance extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'agency_id',
        'consultant_id',
        'policy_number',
        'destination_country_id',
        'insurance_provider_id',
        'coverage_start_date',
        'coverage_end_date',
        'trip_duration_days',
        'trip_type',
        'coverage_amount',
        'currency_id',
        'coverage_details',
        'traveler_count',
        'traveler_ages',
        'premium_amount',
        'premium_currency_id',
        'commission_amount',
        'status',
        'quote_requested_at',
        'paid_at',
        'activated_at',
        'expired_at',
        'cancelled_at',
        'policy_document_path',
        'invoice_path',
        'provider_response',
        'notes',
    ];

    protected $casts = [
        'coverage_details' => 'array',
        'traveler_ages' => 'array',
        'provider_response' => 'array',
        'coverage_start_date' => 'date',
        'coverage_end_date' => 'date',
        'quote_requested_at' => 'datetime',
        'paid_at' => 'datetime',
        'activated_at' => 'datetime',
        'expired_at' => 'datetime',
        'cancelled_at' => 'datetime',
        'coverage_amount' => 'decimal:2',
        'premium_amount' => 'decimal:2',
        'commission_amount' => 'decimal:2',
    ];

    // RELATIONSHIPS (Always define these!)
    
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function agency(): BelongsTo
    {
        return $this->belongsTo(Agency::class);
    }

    public function consultant(): BelongsTo
    {
        return $this->belongsTo(ConsultantProfile::class, 'consultant_id');
    }

    public function destinationCountry(): BelongsTo
    {
        return $this->belongsTo(Country::class, 'destination_country_id');
    }

    public function provider(): BelongsTo
    {
        return $this->belongsTo(InsuranceProvider::class, 'insurance_provider_id');
    }

    public function currency(): BelongsTo
    {
        return $this->belongsTo(Currency::class);
    }

    public function premiumCurrency(): BelongsTo
    {
        return $this->belongsTo(Currency::class, 'premium_currency_id');
    }

    public function countries(): BelongsToMany
    {
        return $this->belongsToMany(Country::class, 'travel_insurance_countries');
    }

    // QUERY SCOPES
    
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopePending($query)
    {
        return $query->whereIn('status', ['draft', 'quote_requested', 'payment_pending']);
    }

    public function scopeForUser($query, int $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeForAgency($query, int $agencyId)
    {
        return $query->where('agency_id', $agencyId);
    }

    // HELPER METHODS
    
    public function isPaid(): bool
    {
        return !is_null($this->paid_at);
    }

    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    public function isExpired(): bool
    {
        return $this->coverage_end_date < now();
    }

    public function canCancel(): bool
    {
        return in_array($this->status, ['draft', 'quote_requested', 'payment_pending']);
    }

    public function daysUntilExpiry(): int
    {
        return now()->diffInDays($this->coverage_end_date, false);
    }

    // FORMAT HELPERS (for display)
    
    public function getFormattedPremiumAttribute(): string
    {
        return format_bd_currency($this->premium_amount, $this->premiumCurrency->code);
    }

    public function getFormattedCoverageAmountAttribute(): string
    {
        return format_bd_currency($this->coverage_amount, $this->currency->code);
    }
}
```

---

## ⚙️ Step 3: Service Class (Business Logic)

**File**: `app/Services/TravelInsuranceService.php`

```php
<?php

namespace App\Services;

use App\Models\TravelInsurance;
use App\Models\User;
use App\Models\Agency;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class TravelInsuranceService
{
    /**
     * Create a new travel insurance quote request
     */
    public function createQuoteRequest(User $user, array $data): TravelInsurance
    {
        return DB::transaction(function () use ($user, $data) {
            // Generate unique policy number
            $policyNumber = $this->generatePolicyNumber();
            
            // Calculate trip duration
            $startDate = \Carbon\Carbon::parse($data['coverage_start_date']);
            $endDate = \Carbon\Carbon::parse($data['coverage_end_date']);
            $duration = $startDate->diffInDays($endDate);
            
            // Auto-assign to agency (if needed)
            $agency = $this->findBestAgency($data['destination_country_id']);
            
            $insurance = TravelInsurance::create([
                'user_id' => $user->id,
                'agency_id' => $agency?->id,
                'policy_number' => $policyNumber,
                'destination_country_id' => $data['destination_country_id'],
                'insurance_provider_id' => $data['insurance_provider_id'],
                'coverage_start_date' => $data['coverage_start_date'],
                'coverage_end_date' => $data['coverage_end_date'],
                'trip_duration_days' => $duration,
                'trip_type' => $data['trip_type'],
                'coverage_amount' => $data['coverage_amount'],
                'currency_id' => $data['currency_id'],
                'traveler_count' => $data['traveler_count'] ?? 1,
                'traveler_ages' => $data['traveler_ages'] ?? [$user->age],
                'status' => 'quote_requested',
                'quote_requested_at' => now(),
            ]);
            
            // Attach additional countries if multi-destination
            if (isset($data['additional_countries'])) {
                $insurance->countries()->attach($data['additional_countries']);
            }
            
            // Trigger notification to agency
            // event(new QuoteRequestCreated($insurance));
            
            return $insurance->load(['destinationCountry', 'provider', 'agency']);
        });
    }

    /**
     * Update quote with provider's response
     */
    public function updateQuote(TravelInsurance $insurance, array $quoteData): TravelInsurance
    {
        $insurance->update([
            'premium_amount' => $quoteData['premium_amount'],
            'premium_currency_id' => $quoteData['currency_id'],
            'coverage_details' => $quoteData['coverage_details'],
            'commission_amount' => $quoteData['premium_amount'] * 0.15, // 15% commission
            'status' => 'quote_received',
            'provider_response' => $quoteData['api_response'] ?? null,
        ]);

        return $insurance->fresh();
    }

    /**
     * Process payment and activate policy
     */
    public function processPayment(TravelInsurance $insurance, string $transactionId): TravelInsurance
    {
        return DB::transaction(function () use ($insurance, $transactionId) {
            // Deduct from wallet
            app(WalletService::class)->debitWallet(
                $insurance->user->wallet,
                $insurance->premium_amount,
                "Travel Insurance Payment - {$insurance->policy_number}",
                'travel_insurance',
                $insurance->id
            );
            
            // Update insurance status
            $insurance->update([
                'status' => 'active',
                'paid_at' => now(),
                'activated_at' => now(),
            ]);
            
            // Credit agency commission
            if ($insurance->agency) {
                app(WalletService::class)->creditWallet(
                    $insurance->agency->wallet,
                    $insurance->commission_amount,
                    "Commission - Policy {$insurance->policy_number}",
                    'travel_insurance_commission',
                    $insurance->id
                );
            }
            
            // Generate PDF policy document
            // $this->generatePolicyDocument($insurance);
            
            return $insurance->fresh();
        });
    }

    /**
     * Cancel insurance policy
     */
    public function cancelPolicy(TravelInsurance $insurance, string $reason): TravelInsurance
    {
        if (!$insurance->canCancel()) {
            throw new \Exception('This policy cannot be cancelled.');
        }

        $insurance->update([
            'status' => 'cancelled',
            'cancelled_at' => now(),
            'notes' => $reason,
        ]);

        return $insurance;
    }

    /**
     * Auto-assign to agency with country permission
     */
    private function findBestAgency(int $countryId): ?Agency
    {
        return Agency::query()
            ->whereHas('serviceCategories', function ($q) {
                $q->where('code', 'travel_insurance');
            })
            ->whereHas('countries', function ($q) use ($countryId) {
                $q->where('country_id', $countryId)
                  ->where('is_approved', true);
            })
            ->where('is_active', true)
            ->inRandomOrder()
            ->first();
    }

    /**
     * Generate unique policy number
     */
    private function generatePolicyNumber(): string
    {
        do {
            $number = 'TI-' . strtoupper(Str::random(8));
        } while (TravelInsurance::where('policy_number', $number)->exists());

        return $number;
    }
}
```

---

## 🎮 Step 4: Controller (Thin Layer)

**File**: `app/Http/Controllers/User/TravelInsuranceController.php`

```php
<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\TravelInsurance;
use App\Models\Country;
use App\Models\InsuranceProvider;
use App\Services\TravelInsuranceService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class TravelInsuranceController extends Controller
{
    public function __construct(
        private TravelInsuranceService $service
    ) {}

    public function index(Request $request)
    {
        $insurances = TravelInsurance::query()
            ->forUser($request->user()->id)
            ->with(['destinationCountry', 'provider', 'agency'])
            ->latest()
            ->paginate(10);

        return Inertia::render('User/TravelInsurance/Index', [
            'insurances' => $insurances,
        ]);
    }

    public function create()
    {
        return Inertia::render('User/TravelInsurance/Create', [
            'countries' => Country::active()->orderBy('name')->get(),
            'providers' => InsuranceProvider::active()->get(),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'destination_country_id' => 'required|exists:countries,id',
            'insurance_provider_id' => 'required|exists:insurance_providers,id',
            'coverage_start_date' => 'required|date|after:today',
            'coverage_end_date' => 'required|date|after:coverage_start_date',
            'trip_type' => 'required|in:single,annual',
            'coverage_amount' => 'required|numeric|min:1000',
            'currency_id' => 'required|exists:currencies,id',
            'traveler_count' => 'required|integer|min:1|max:10',
            'traveler_ages' => 'required|array',
            'traveler_ages.*' => 'integer|min:1|max:120',
        ]);

        $insurance = $this->service->createQuoteRequest(
            $request->user(),
            $validated
        );

        return redirect()
            ->route('user.travel-insurance.show', $insurance)
            ->with('success', 'Quote request submitted successfully!');
    }

    public function show(TravelInsurance $insurance)
    {
        $this->authorize('view', $insurance);

        $insurance->load([
            'destinationCountry',
            'provider',
            'agency',
            'consultant',
            'countries'
        ]);

        return Inertia::render('User/TravelInsurance/Show', [
            'insurance' => $insurance,
        ]);
    }

    public function pay(TravelInsurance $insurance, Request $request)
    {
        $this->authorize('update', $insurance);

        $validated = $request->validate([
            'transaction_id' => 'required|string',
        ]);

        $insurance = $this->service->processPayment(
            $insurance,
            $validated['transaction_id']
        );

        return back()->with('success', 'Payment processed successfully!');
    }

    public function cancel(TravelInsurance $insurance, Request $request)
    {
        $this->authorize('delete', $insurance);

        $validated = $request->validate([
            'reason' => 'required|string|max:500',
        ]);

        $this->service->cancelPolicy($insurance, $validated['reason']);

        return back()->with('success', 'Insurance policy cancelled.');
    }
}
```

---

## 🌱 Step 5: Demo Data Seeder

**File**: `database/seeders/TravelInsuranceDemoSeeder.php`

```php
<?php

namespace Database\Seeders;

use App\Models\TravelInsurance;
use App\Models\User;
use App\Models\Agency;
use App\Models\Country;
use App\Models\InsuranceProvider;
use App\Models\Currency;
use Illuminate\Database\Seeder;

class TravelInsuranceDemoSeeder extends Seeder
{
    public function run(): void
    {
        $users = User::where('role_id', 6)->limit(20)->get(); // Regular users
        $agencies = Agency::whereHas('serviceCategories', function ($q) {
            $q->where('code', 'travel_insurance');
        })->get();

        $countries = Country::whereIn('iso_code_2', [
            'US', 'GB', 'CA', 'AU', 'AE', 'SA', 'IN', 'SG', 'MY', 'TH'
        ])->get();

        $providers = InsuranceProvider::all();
        $bdt = Currency::where('code', 'BDT')->first();
        $usd = Currency::where('code', 'USD')->first();

        $statuses = [
            'quote_received' => 30,  // 30 quotes
            'active' => 50,          // 50 active policies
            'expired' => 15,         // 15 expired
            'cancelled' => 5,        // 5 cancelled
        ];

        foreach ($statuses as $status => $count) {
            for ($i = 0; $i < $count; $i++) {
                $user = $users->random();
                $agency = $agencies->random();
                $country = $countries->random();
                $provider = $providers->random();
                
                $startDate = now()->addDays(rand(1, 60));
                $endDate = $startDate->copy()->addDays(rand(7, 30));
                
                $coverageAmount = rand(50000, 200000);
                $premiumAmount = $coverageAmount * 0.03; // 3% of coverage

                TravelInsurance::create([
                    'user_id' => $user->id,
                    'agency_id' => $agency->id,
                    'policy_number' => 'TI-' . strtoupper(\Str::random(8)),
                    'destination_country_id' => $country->id,
                    'insurance_provider_id' => $provider->id,
                    'coverage_start_date' => $startDate,
                    'coverage_end_date' => $endDate,
                    'trip_duration_days' => $startDate->diffInDays($endDate),
                    'trip_type' => rand(0, 1) ? 'single' : 'annual',
                    'coverage_amount' => $coverageAmount,
                    'currency_id' => $usd->id,
                    'traveler_count' => rand(1, 4),
                    'traveler_ages' => [rand(25, 65)],
                    'premium_amount' => $premiumAmount,
                    'premium_currency_id' => $bdt->id,
                    'commission_amount' => $premiumAmount * 0.15,
                    'status' => $status,
                    'coverage_details' => [
                        'medical_coverage' => $coverageAmount * 0.7,
                        'trip_cancellation' => $coverageAmount * 0.2,
                        'baggage_loss' => $coverageAmount * 0.1,
                    ],
                    'quote_requested_at' => now()->subDays(rand(1, 30)),
                    'paid_at' => $status === 'active' ? now()->subDays(rand(1, 20)) : null,
                    'activated_at' => $status === 'active' ? now()->subDays(rand(1, 20)) : null,
                ]);
            }
        }

        $this->command->info('✅ Created 100 demo travel insurance records');
    }
}
```

---

## ✅ Step 6: Feature Test

**File**: `tests/Feature/TravelInsuranceTest.php`

```php
<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\TravelInsurance;
use App\Models\Country;
use App\Models\InsuranceProvider;
use Illuminate\Foundation\Testing\RefreshDatabase;

class TravelInsuranceTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_create_quote_request(): void
    {
        $user = User::factory()->create();
        $country = Country::factory()->create();
        $provider = InsuranceProvider::factory()->create();

        $response = $this->actingAs($user)->post(route('user.travel-insurance.store'), [
            'destination_country_id' => $country->id,
            'insurance_provider_id' => $provider->id,
            'coverage_start_date' => now()->addDays(10)->format('Y-m-d'),
            'coverage_end_date' => now()->addDays(20)->format('Y-m-d'),
            'trip_type' => 'single',
            'coverage_amount' => 100000,
            'currency_id' => 1,
            'traveler_count' => 1,
            'traveler_ages' => [30],
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('travel_insurances', [
            'user_id' => $user->id,
            'status' => 'quote_requested',
        ]);
    }

    public function test_insurance_can_be_paid(): void
    {
        $insurance = TravelInsurance::factory()->create([
            'status' => 'quote_received',
            'premium_amount' => 5000,
        ]);

        $response = $this->actingAs($insurance->user)
            ->post(route('user.travel-insurance.pay', $insurance), [
                'transaction_id' => 'TXN-123456',
            ]);

        $insurance->refresh();
        $this->assertEquals('active', $insurance->status);
        $this->assertNotNull($insurance->paid_at);
    }
}
```

---

## 📚 Key Principles

### ✅ DO's
1. **Use foreign keys** for all reference data (countries, currencies, etc.)
2. **Create demo data** for every service (minimum 100 records)
3. **Index common queries** (user_id, status, dates)
4. **Use transactions** for financial operations
5. **Soft delete** important records
6. **Eager load** relationships to avoid N+1 queries
7. **Validate at controller** level, business logic in service
8. **Test everything** - write feature tests

### ❌ DON'Ts
1. **Never store country/city names** as strings - use FK!
2. **Never skip demo data** seeder
3. **Never put business logic** in controllers
4. **Never forget indexes** on foreign keys
5. **Never hardcode** currency symbols or formats
6. **Never skip validation**
7. **Never forget to eager load** relationships

---

## 🚀 Quick Checklist

When creating a new service, copy this checklist:

```markdown
## Travel Insurance Service - Implementation Checklist

- [ ] Migration created with proper foreign keys
- [ ] Model created with relationships defined
- [ ] Service class created with business logic
- [ ] User controller created (thin layer)
- [ ] Agency controller created
- [ ] Admin controller created
- [ ] Routes registered in web.php, agency.php, admin.php
- [ ] Vue components created (Index, Create, Show)
- [ ] Demo data seeder created (minimum 100 records)
- [ ] Feature tests written (minimum 5 tests)
- [ ] Documentation written in docs/services/
- [ ] Added to main README service list
- [ ] Manual testing completed
- [ ] Deployed to staging
```

This ensures **zero errors** and **complete functionality**!
