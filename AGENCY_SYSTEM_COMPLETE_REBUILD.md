# 🏗️ AGENCY SYSTEM - COMPLETE REBUILD ROADMAP

**Date:** November 24, 2025  
**Status:** Ready for Implementation  
**Estimated Time:** 30-40 hours  
**Priority:** CRITICAL - Core SaaS Functionality

---

## 📋 EXECUTIVE SUMMARY

### What We Have
- ✅ **1 Agency** registered (ID: 2, email: agency@bgplatform.com)
- ✅ **39 Service Modules** across 6 categories (7 active, 32 coming soon)
- ✅ **196 Countries** in database
- ✅ **47 Visa Types** seeded
- ✅ **Phase 1 COMPLETE** - Database schema updated with service_module_id support
- ✅ Agency assignments now support ANY service (not just visas)
- ✅ Assignment scopes: global, country_specific, visa_specific
- ✅ Vue form updated with service selection and scope options

### What's Missing (Critical)
- ❌ **No application routing** - User submits tourist visa → goes nowhere
- ❌ **No agency dashboard** - Agencies can't see assigned work
- ❌ **No admin assignment tools** - Cannot manually assign applications
- ❌ **VisaRequirement table EMPTY** - 0 records (needs urgent seeding)
- ❌ **ServiceApplication workflow incomplete** - No status tracking
- ❌ **No commission tracking** - Platform earnings not recorded
- ❌ **No quote system** - Users can't compare agency prices

### The Problem
**User Journey Currently:**
1. User fills tourist visa form ✅
2. TouristVisa record created ✅
3. **DEAD END** - No agency assignment ❌
4. Agency never sees it ❌
5. Admin has no tools to manage it ❌

**User Journey After Rebuild:**
1. User fills tourist visa form ✅
2. TouristVisa record created ✅
3. **Auto-assign to agency** based on destination country ✅
4. **ServiceApplication created** linking user → agency ✅
5. **Agency sees it** in their dashboard ✅
6. **Agency quotes** on the application ✅
7. **User selects quote** and pays ✅
8. **Platform tracks commission** ✅

---

## 🎯 IMPLEMENTATION PHASES

### ✅ PHASE 1: Architecture Fix (COMPLETED - Nov 24, 2025)

**What Was Done:**
- ✅ Created migration `2025_11_24_151811_add_service_module_to_agency_assignments_table.php`
- ✅ Added `service_module_id` (FK to service_modules) - Links assignment to specific service
- ✅ Added `country_id` (FK to countries) - Replaces string country field
- ✅ Added `visa_type_id` (FK to visa_types) - Replaces string visa_type field
- ✅ Added `assignment_scope` ENUM (global, country_specific, visa_specific)
- ✅ Migration executed successfully (164.44ms)
- ✅ Updated `AgencyCountryAssignment` model with new relationships
- ✅ Updated `AgencyAssignmentController` to load service modules
- ✅ Updated `Create.vue` with service selection dropdown
- ✅ Build completed successfully - Vue changes compiled

**Database Schema:**
```php
agency_country_assignments:
    id
    agency_id (FK users) - Who is assigned
    service_module_id (FK service_modules) - WHICH service (tourist visa, translation, etc.)
    country_id (FK countries) - WHICH country (nullable for global)
    country (string) - Backward compatibility
    country_code (string) - Backward compatibility
    visa_type_id (FK visa_types) - WHICH visa type (nullable)
    visa_type (string) - Backward compatibility
    assignment_scope (ENUM) - global/country_specific/visa_specific
    platform_commission_rate (decimal) - 15% default
    commission_type (ENUM) - percentage/fixed
    can_edit_requirements (boolean)
    can_set_fees (boolean)
    can_process_applications (boolean)
    Performance metrics...
```

**Unlocked Capabilities:**
- ✅ Can assign agencies to ANY of 39 services (not just visas)
- ✅ Global assignments (e.g., CV Builder agency serves all countries)
- ✅ Country-specific assignments (e.g., Thailand specialist)
- ✅ Visa-specific assignments (e.g., Thailand Tourist Visa only)
- ✅ Multi-service agencies (one agency handles multiple services)

**Test URL:** `/admin/agency-assignments/create`

---

### 🔨 PHASE 2: Tourist Visa → Agency Connection (6-8 hours)

**Goal:** Make user applications actually reach agencies

#### 2.1. Update TouristVisa Submission Flow

**File:** `app/Http/Controllers/Profile/TouristVisaController.php`

```php
public function store(Request $request)
{
    $validated = $request->validate([
        'destination_country' => 'required|string',
        'travel_date' => 'required|date',
        // ... other fields
    ]);
    
    // Create tourist visa record
    $touristVisa = TouristVisa::create([
        'user_id' => auth()->id(),
        'destination_country' => $validated['destination_country'],
        // ... other fields
        'status' => 'pending',
    ]);
    
    // Upload documents
    // ... document handling code
    
    // **NEW: Auto-assign to agency**
    $this->assignToAgency($touristVisa);
    
    return redirect()->route('profile.tourist-visa.show', $touristVisa)
        ->with('success', 'Application submitted! An agency will review it shortly.');
}

protected function assignToAgency(TouristVisa $touristVisa)
{
    // Get Tourist Visa service module
    $touristVisaModule = ServiceModule::where('slug', 'tourist-visa')->first();
    
    // Get Tourist visa type
    $touristVisaType = VisaType::where('slug', 'tourist')->first();
    
    // Get country
    $country = Country::where('name', $touristVisa->destination_country)->first();
    
    if (!$country || !$touristVisaModule || !$touristVisaType) {
        return; // Cannot auto-assign
    }
    
    // Find matching agency assignment (most specific first)
    $assignment = AgencyCountryAssignment::where('service_module_id', $touristVisaModule->id)
        ->where('is_active', true)
        ->where(function($query) use ($country, $touristVisaType) {
            // Try visa-specific first
            $query->where('assignment_scope', 'visa_specific')
                  ->where('country_id', $country->id)
                  ->where('visa_type_id', $touristVisaType->id);
        })
        ->orWhere(function($query) use ($country) {
            // Then country-specific
            $query->where('assignment_scope', 'country_specific')
                  ->where('country_id', $country->id);
        })
        ->orWhere(function($query) {
            // Finally global
            $query->where('assignment_scope', 'global');
        })
        ->orderByRaw("FIELD(assignment_scope, 'visa_specific', 'country_specific', 'global')")
        ->first();
    
    if ($assignment) {
        // Create ServiceApplication
        ServiceApplication::create([
            'user_id' => $touristVisa->user_id,
            'service_module_id' => $touristVisaModule->id,
            'assigned_to' => $assignment->agency_id,
            'assigned_role' => 'agency',
            'application_number' => 'APP-' . date('Ymd') . '-' . strtoupper(substr(uniqid(), -6)),
            'status' => 'submitted',
            'form_data' => [
                'model_type' => TouristVisa::class,
                'model_id' => $touristVisa->id,
                'destination_country' => $touristVisa->destination_country,
                'travel_date' => $touristVisa->travel_date,
            ],
            'submitted_at' => now(),
        ]);
        
        // Update tourist visa status
        $touristVisa->update(['status' => 'agency_assigned']);
        
        // Increment agency stats
        $assignment->increment('total_applications');
    }
}
```

#### 2.2. Create Agency Dashboard

**File:** `app/Http/Controllers/Agency/DashboardController.php`

```php
<?php

namespace App\Http\Controllers\Agency;

use App\Http\Controllers\Controller;
use App\Models\ServiceApplication;
use App\Models\AgencyCountryAssignment;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function index()
    {
        $agencyId = auth()->id();
        
        // Get assignments
        $assignments = AgencyCountryAssignment::where('agency_id', $agencyId)
            ->where('is_active', true)
            ->with(['serviceModule', 'country', 'visaType'])
            ->get();
        
        // Get applications
        $applications = ServiceApplication::where('assigned_to', $agencyId)
            ->with(['user', 'serviceModule'])
            ->latest()
            ->paginate(20);
        
        // Statistics
        $stats = [
            'total_applications' => ServiceApplication::where('assigned_to', $agencyId)->count(),
            'pending' => ServiceApplication::where('assigned_to', $agencyId)->where('status', 'submitted')->count(),
            'processing' => ServiceApplication::where('assigned_to', $agencyId)->where('status', 'processing')->count(),
            'completed' => ServiceApplication::where('assigned_to', $agencyId)->where('status', 'completed')->count(),
            'revenue_month' => ServiceApplication::where('assigned_to', $agencyId)
                ->where('payment_status', 'paid')
                ->whereMonth('payment_date', now()->month)
                ->sum('amount'),
        ];
        
        return Inertia::render('Agency/Dashboard', [
            'assignments' => $assignments,
            'applications' => $applications,
            'stats' => $stats,
        ]);
    }
    
    public function applications()
    {
        $agencyId = auth()->id();
        
        $applications = ServiceApplication::where('assigned_to', $agencyId)
            ->with(['user', 'serviceModule'])
            ->when(request('status'), function($query, $status) {
                $query->where('status', $status);
            })
            ->when(request('service'), function($query, $service) {
                $query->where('service_module_id', $service);
            })
            ->latest()
            ->paginate(20);
        
        return Inertia::render('Agency/Applications/Index', [
            'applications' => $applications,
        ]);
    }
    
    public function show(ServiceApplication $application)
    {
        // Ensure agency owns this application
        if ($application->assigned_to !== auth()->id()) {
            abort(403);
        }
        
        $application->load(['user.profile', 'serviceModule']);
        
        // Get related tourist visa if exists
        if ($application->form_data['model_type'] === 'App\\Models\\TouristVisa') {
            $touristVisa = TouristVisa::with('documents')->find($application->form_data['model_id']);
            $application->touristVisa = $touristVisa;
        }
        
        return Inertia::render('Agency/Applications/Show', [
            'application' => $application,
        ]);
    }
}
```

**Routes:** `routes/web.php`

```php
// Agency Routes
Route::middleware(['auth', 'role:agency'])->prefix('agency')->name('agency.')->group(function () {
    Route::get('/dashboard', [App\Http\Controllers\Agency\DashboardController::class, 'index'])->name('dashboard');
    Route::get('/applications', [App\Http\Controllers\Agency\DashboardController::class, 'applications'])->name('applications.index');
    Route::get('/applications/{application}', [App\Http\Controllers\Agency\DashboardController::class, 'show'])->name('applications.show');
    Route::post('/applications/{application}/quote', [App\Http\Controllers\Agency\ApplicationController::class, 'submitQuote'])->name('applications.quote');
    Route::post('/applications/{application}/accept', [App\Http\Controllers\Agency\ApplicationController::class, 'accept'])->name('applications.accept');
    Route::post('/applications/{application}/reject', [App\Http\Controllers\Agency\ApplicationController::class, 'reject'])->name('applications.reject');
});
```

#### 2.3. Create Agency Vue Components

**File:** `resources/js/Pages/Agency/Dashboard.vue`

```vue
<template>
    <AgencyLayout title="Dashboard">
        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <h2 class="text-2xl font-bold mb-6">Agency Dashboard</h2>
                
                <!-- Stats -->
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-8">
                    <div class="bg-white p-6 rounded-lg shadow">
                        <div class="text-gray-500 text-sm">Total Applications</div>
                        <div class="text-3xl font-bold">{{ stats.total_applications }}</div>
                    </div>
                    <div class="bg-white p-6 rounded-lg shadow">
                        <div class="text-gray-500 text-sm">Pending</div>
                        <div class="text-3xl font-bold text-yellow-600">{{ stats.pending }}</div>
                    </div>
                    <div class="bg-white p-6 rounded-lg shadow">
                        <div class="text-gray-500 text-sm">Processing</div>
                        <div class="text-3xl font-bold text-blue-600">{{ stats.processing }}</div>
                    </div>
                    <div class="bg-white p-6 rounded-lg shadow">
                        <div class="text-gray-500 text-sm">Month Revenue</div>
                        <div class="text-3xl font-bold text-green-600">৳{{ formatNumber(stats.revenue_month) }}</div>
                    </div>
                </div>
                
                <!-- Assignments -->
                <div class="bg-white rounded-lg shadow p-6 mb-8">
                    <h3 class="text-lg font-bold mb-4">Your Service Assignments</h3>
                    <div class="space-y-4">
                        <div v-for="assignment in assignments" :key="assignment.id" 
                             class="border rounded-lg p-4">
                            <div class="flex justify-between items-start">
                                <div>
                                    <div class="font-semibold">{{ assignment.service_module.name }}</div>
                                    <div class="text-sm text-gray-600">
                                        <span v-if="assignment.assignment_scope === 'global'">Global Service</span>
                                        <span v-else-if="assignment.assignment_scope === 'country_specific'">
                                            {{ assignment.country.name }}
                                        </span>
                                        <span v-else>
                                            {{ assignment.country.name }} - {{ assignment.visa_type.name }}
                                        </span>
                                    </div>
                                    <div class="text-sm text-gray-500 mt-1">
                                        Commission: {{ assignment.platform_commission_rate }}%
                                    </div>
                                </div>
                                <div class="text-right">
                                    <div class="text-sm text-gray-500">Applications</div>
                                    <div class="text-2xl font-bold">{{ assignment.total_applications }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Recent Applications -->
                <div class="bg-white rounded-lg shadow p-6">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-bold">Recent Applications</h3>
                        <Link :href="route('agency.applications.index')" 
                              class="text-indigo-600 hover:text-indigo-800">
                            View All →
                        </Link>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead>
                                <tr>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Application #</th>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Service</th>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">User</th>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200">
                                <tr v-for="app in applications.data" :key="app.id">
                                    <td class="px-4 py-3 text-sm">{{ app.application_number }}</td>
                                    <td class="px-4 py-3 text-sm">{{ app.service_module.name }}</td>
                                    <td class="px-4 py-3 text-sm">{{ app.user.name }}</td>
                                    <td class="px-4 py-3 text-sm">
                                        <span :class="statusClass(app.status)" class="px-2 py-1 rounded text-xs">
                                            {{ app.status }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3 text-sm">{{ formatDate(app.created_at) }}</td>
                                    <td class="px-4 py-3 text-sm">
                                        <Link :href="route('agency.applications.show', app.id)"
                                              class="text-indigo-600 hover:text-indigo-800">
                                            View
                                        </Link>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </AgencyLayout>
</template>

<script setup>
import AgencyLayout from '@/Layouts/AgencyLayout.vue';
import { Link } from '@inertiajs/vue3';

defineProps({
    assignments: Array,
    applications: Object,
    stats: Object,
});

const formatNumber = (num) => {
    return new Intl.NumberFormat('en-US').format(num);
};

const formatDate = (date) => {
    return new Date(date).toLocaleDateString();
};

const statusClass = (status) => {
    const classes = {
        'submitted': 'bg-blue-100 text-blue-800',
        'processing': 'bg-yellow-100 text-yellow-800',
        'completed': 'bg-green-100 text-green-800',
        'rejected': 'bg-red-100 text-red-800',
    };
    return classes[status] || 'bg-gray-100 text-gray-800';
};
</script>
```

#### 2.4. Create Admin Assignment Tools

**File:** `app/Http/Controllers/Admin/TouristVisaController.php`

```php
<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TouristVisa;
use App\Models\ServiceApplication;
use App\Models\AgencyCountryAssignment;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;

class TouristVisaController extends Controller
{
    public function index()
    {
        $applications = TouristVisa::with(['user', 'documents'])
            ->when(request('status'), function($query, $status) {
                $query->where('status', $status);
            })
            ->when(request('country'), function($query, $country) {
                $query->where('destination_country', $country);
            })
            ->latest()
            ->paginate(20);
        
        return Inertia::render('Admin/TouristVisa/Index', [
            'applications' => $applications,
        ]);
    }
    
    public function show(TouristVisa $touristVisa)
    {
        $touristVisa->load(['user.profile', 'documents']);
        
        // Get service application if exists
        $serviceApplication = ServiceApplication::where('form_data->model_type', TouristVisa::class)
            ->where('form_data->model_id', $touristVisa->id)
            ->with('assignedTo')
            ->first();
        
        // Get available agencies for this destination
        $country = Country::where('name', $touristVisa->destination_country)->first();
        $touristVisaModule = ServiceModule::where('slug', 'tourist-visa')->first();
        
        $availableAgencies = [];
        if ($country && $touristVisaModule) {
            $availableAgencies = AgencyCountryAssignment::where('service_module_id', $touristVisaModule->id)
                ->where('is_active', true)
                ->where(function($query) use ($country) {
                    $query->where('country_id', $country->id)
                          ->orWhere('assignment_scope', 'global');
                })
                ->with('agency')
                ->get();
        }
        
        return Inertia::render('Admin/TouristVisa/Show', [
            'touristVisa' => $touristVisa,
            'serviceApplication' => $serviceApplication,
            'availableAgencies' => $availableAgencies,
        ]);
    }
    
    public function assignAgency(Request $request, TouristVisa $touristVisa)
    {
        $validated = $request->validate([
            'agency_id' => 'required|exists:users,id',
        ]);
        
        $touristVisaModule = ServiceModule::where('slug', 'tourist-visa')->first();
        
        // Create or update ServiceApplication
        ServiceApplication::updateOrCreate(
            [
                'form_data->model_type' => TouristVisa::class,
                'form_data->model_id' => $touristVisa->id,
            ],
            [
                'user_id' => $touristVisa->user_id,
                'service_module_id' => $touristVisaModule->id,
                'assigned_to' => $validated['agency_id'],
                'assigned_role' => 'agency',
                'status' => 'submitted',
                'form_data' => [
                    'model_type' => TouristVisa::class,
                    'model_id' => $touristVisa->id,
                    'destination_country' => $touristVisa->destination_country,
                ],
            ]
        );
        
        $touristVisa->update(['status' => 'agency_assigned']);
        
        return back()->with('success', 'Agency assigned successfully!');
    }
}
```

**Routes:**

```php
// Admin Tourist Visa Management
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::prefix('tourist-visas')->name('tourist-visas.')->group(function () {
        Route::get('/', [App\Http\Controllers\Admin\TouristVisaController::class, 'index'])->name('index');
        Route::get('/{touristVisa}', [App\Http\Controllers\Admin\TouristVisaController::class, 'show'])->name('show');
        Route::post('/{touristVisa}/assign-agency', [App\Http\Controllers\Admin\TouristVisaController::class, 'assignAgency'])->name('assign-agency');
    });
});
```

**Estimated Time:** 6-8 hours

---

### 📊 PHASE 3: Seed VisaRequirement Data (4-6 hours)

**Goal:** Populate visa requirements for top countries

**File:** `database/seeders/VisaRequirementSeeder.php`

```php
<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\VisaRequirement;
use App\Models\ServiceModule;
use App\Models\Country;
use App\Models\VisaType;

class VisaRequirementSeeder extends Seeder
{
    public function run(): void
    {
        $touristVisaModule = ServiceModule::where('slug', 'tourist-visa')->first();
        $touristVisaType = VisaType::where('slug', 'tourist')->first();
        
        $requirements = [
            // Thailand
            [
                'country' => 'Thailand',
                'general_requirements' => 'Valid passport, recent photos, proof of accommodation',
                'min_bank_balance' => 50000,
                'bank_statement_months' => 3,
                'government_fee' => 5000,
                'service_fee' => 2000,
                'processing_days_standard' => 7,
                'processing_days_express' => 3,
                'interview_required' => false,
                'documents_required' => json_encode([
                    ['name' => 'Passport copy (valid 6 months)', 'mandatory' => true],
                    ['name' => 'Passport-size photos (2 copies)', 'mandatory' => true],
                    ['name' => 'Bank statement (3 months)', 'mandatory' => true],
                    ['name' => 'Hotel booking confirmation', 'mandatory' => false],
                    ['name' => 'Return flight ticket', 'mandatory' => false],
                ]),
            ],
            
            // India
            [
                'country' => 'India',
                'general_requirements' => 'Valid passport, online application, biometric appointment',
                'min_bank_balance' => 80000,
                'bank_statement_months' => 6,
                'government_fee' => 8000,
                'service_fee' => 3000,
                'processing_days_standard' => 10,
                'processing_days_express' => 5,
                'interview_required' => false,
                'biometrics_required' => true,
                'documents_required' => json_encode([
                    ['name' => 'Passport copy', 'mandatory' => true],
                    ['name' => 'Passport-size photos', 'mandatory' => true],
                    ['name' => 'Bank statement (6 months)', 'mandatory' => true],
                    ['name' => 'Hotel booking', 'mandatory' => true],
                    ['name' => 'Travel insurance', 'mandatory' => false],
                ]),
            ],
            
            // Malaysia
            [
                'country' => 'Malaysia',
                'general_requirements' => 'Valid passport, return ticket, proof of funds',
                'min_bank_balance' => 30000,
                'bank_statement_months' => 3,
                'government_fee' => 4000,
                'service_fee' => 1500,
                'processing_days_standard' => 5,
                'processing_days_express' => 2,
                'interview_required' => false,
                'documents_required' => json_encode([
                    ['name' => 'Passport copy', 'mandatory' => true],
                    ['name' => 'Photos', 'mandatory' => true],
                    ['name' => 'Bank statement', 'mandatory' => true],
                    ['name' => 'Return ticket', 'mandatory' => true],
                ]),
            ],
            
            // Saudi Arabia (Umrah/Visit)
            [
                'country' => 'Saudi Arabia',
                'general_requirements' => 'Valid passport, polio vaccination, sponsor letter',
                'min_bank_balance' => 100000,
                'bank_statement_months' => 6,
                'government_fee' => 12000,
                'service_fee' => 5000,
                'processing_days_standard' => 14,
                'processing_days_express' => 7,
                'interview_required' => false,
                'documents_required' => json_encode([
                    ['name' => 'Passport copy', 'mandatory' => true],
                    ['name' => 'Photos', 'mandatory' => true],
                    ['name' => 'Polio vaccination certificate', 'mandatory' => true],
                    ['name' => 'Bank statement', 'mandatory' => true],
                    ['name' => 'Sponsor letter (if applicable)', 'mandatory' => false],
                ]),
            ],
            
            // Singapore
            [
                'country' => 'Singapore',
                'general_requirements' => 'Valid passport, return ticket, hotel booking',
                'min_bank_balance' => 60000,
                'bank_statement_months' => 3,
                'government_fee' => 6000,
                'service_fee' => 2500,
                'processing_days_standard' => 5,
                'processing_days_express' => 2,
                'interview_required' => false,
                'documents_required' => json_encode([
                    ['name' => 'Passport copy', 'mandatory' => true],
                    ['name' => 'Photos', 'mandatory' => true],
                    ['name' => 'Bank statement', 'mandatory' => true],
                    ['name' => 'Hotel booking', 'mandatory' => true],
                    ['name' => 'Return ticket', 'mandatory' => true],
                ]),
            ],
            
            // UAE
            [
                'country' => 'United Arab Emirates',
                'general_requirements' => 'Valid passport, sponsor in UAE or hotel booking',
                'min_bank_balance' => 70000,
                'bank_statement_months' => 3,
                'government_fee' => 10000,
                'service_fee' => 4000,
                'processing_days_standard' => 7,
                'processing_days_express' => 3,
                'interview_required' => false,
                'documents_required' => json_encode([
                    ['name' => 'Passport copy', 'mandatory' => true],
                    ['name' => 'Photos', 'mandatory' => true],
                    ['name' => 'Bank statement', 'mandatory' => true],
                    ['name' => 'Hotel booking or sponsor letter', 'mandatory' => true],
                ]),
            ],
            
            // Canada
            [
                'country' => 'Canada',
                'general_requirements' => 'Valid passport, biometrics, purpose of visit proof',
                'min_bank_balance' => 200000,
                'bank_statement_months' => 6,
                'government_fee' => 18000,
                'service_fee' => 8000,
                'processing_days_standard' => 21,
                'processing_days_express' => 10,
                'interview_required' => true,
                'biometrics_required' => true,
                'documents_required' => json_encode([
                    ['name' => 'Passport copy', 'mandatory' => true],
                    ['name' => 'Photos', 'mandatory' => true],
                    ['name' => 'Bank statement (6 months)', 'mandatory' => true],
                    ['name' => 'Employment letter', 'mandatory' => true],
                    ['name' => 'Travel itinerary', 'mandatory' => true],
                    ['name' => 'Property documents', 'mandatory' => false],
                ]),
            ],
            
            // United Kingdom
            [
                'country' => 'United Kingdom',
                'general_requirements' => 'Valid passport, financial proof, accommodation proof',
                'min_bank_balance' => 250000,
                'bank_statement_months' => 6,
                'government_fee' => 20000,
                'service_fee' => 10000,
                'processing_days_standard' => 21,
                'processing_days_express' => 7,
                'interview_required' => false,
                'biometrics_required' => true,
                'documents_required' => json_encode([
                    ['name' => 'Passport copy', 'mandatory' => true],
                    ['name' => 'Photos', 'mandatory' => true],
                    ['name' => 'Bank statement (6 months)', 'mandatory' => true],
                    ['name' => 'Employment documents', 'mandatory' => true],
                    ['name' => 'Hotel booking', 'mandatory' => true],
                    ['name' => 'Travel insurance', 'mandatory' => true],
                ]),
            ],
            
            // Australia
            [
                'country' => 'Australia',
                'general_requirements' => 'Valid passport, health insurance, financial capacity',
                'min_bank_balance' => 300000,
                'bank_statement_months' => 6,
                'government_fee' => 22000,
                'service_fee' => 12000,
                'processing_days_standard' => 21,
                'processing_days_express' => 10,
                'interview_required' => false,
                'biometrics_required' => true,
                'documents_required' => json_encode([
                    ['name' => 'Passport copy', 'mandatory' => true],
                    ['name' => 'Photos', 'mandatory' => true],
                    ['name' => 'Bank statement', 'mandatory' => true],
                    ['name' => 'Health insurance', 'mandatory' => true],
                    ['name' => 'Employment proof', 'mandatory' => true],
                ]),
            ],
            
            // United States
            [
                'country' => 'United States',
                'general_requirements' => 'Valid passport, DS-160 form, embassy interview',
                'min_bank_balance' => 400000,
                'bank_statement_months' => 6,
                'government_fee' => 28000,
                'service_fee' => 15000,
                'processing_days_standard' => 30,
                'processing_days_express' => 14,
                'interview_required' => true,
                'documents_required' => json_encode([
                    ['name' => 'Passport copy', 'mandatory' => true],
                    ['name' => 'DS-160 confirmation', 'mandatory' => true],
                    ['name' => 'Photos (specific format)', 'mandatory' => true],
                    ['name' => 'Bank statement (6 months)', 'mandatory' => true],
                    ['name' => 'Employment letter', 'mandatory' => true],
                    ['name' => 'Property documents', 'mandatory' => true],
                    ['name' => 'Previous travel history', 'mandatory' => false],
                ]),
            ],
        ];
        
        foreach ($requirements as $data) {
            $country = Country::where('name', $data['country'])->first();
            
            if (!$country) continue;
            
            VisaRequirement::create([
                'service_module_id' => $touristVisaModule->id,
                'country' => $data['country'],
                'country_code' => $country->iso_code_2,
                'visa_type' => 'Tourist Visa',
                'general_requirements' => $data['general_requirements'],
                'min_bank_balance' => $data['min_bank_balance'],
                'bank_statement_months' => $data['bank_statement_months'],
                'government_fee' => $data['government_fee'],
                'service_fee' => $data['service_fee'],
                'processing_days_standard' => $data['processing_days_standard'],
                'processing_days_express' => $data['processing_days_express'] ?? null,
                'interview_required' => $data['interview_required'],
                'biometrics_required' => $data['biometrics_required'] ?? false,
                'documents_required' => $data['documents_required'],
                'is_active' => true,
                'agency_can_edit' => true,
            ]);
        }
        
        $this->command->info('✅ Seeded visa requirements for 10 countries');
    }
}
```

**Run:**
```bash
php artisan db:seed --class=VisaRequirementSeeder
```

**Estimated Time:** 4-6 hours (research + data entry)

---

### 🔄 PHASE 4: ServiceApplication Workflow (8-10 hours)

**Goal:** Complete application lifecycle management

#### 4.1. Quote System

**File:** `app/Http/Controllers/Agency/ApplicationController.php`

```php
public function submitQuote(Request $request, ServiceApplication $application)
{
    // Ensure agency owns this application
    if ($application->assigned_to !== auth()->id()) {
        abort(403);
    }
    
    $validated = $request->validate([
        'quoted_amount' => 'required|numeric|min:0',
        'processing_days' => 'required|integer|min:1',
        'notes' => 'nullable|string|max:1000',
    ]);
    
    // Get agency assignment to calculate commission
    $assignment = AgencyCountryAssignment::where('agency_id', auth()->id())
        ->where('service_module_id', $application->service_module_id)
        ->where('is_active', true)
        ->first();
    
    $platformCommission = 0;
    if ($assignment) {
        $platformCommission = $assignment->calculatePlatformCommission($validated['quoted_amount']);
    }
    
    $application->update([
        'status' => 'quoted',
        'amount' => $validated['quoted_amount'],
        'form_data' => array_merge($application->form_data, [
            'quote' => [
                'amount' => $validated['quoted_amount'],
                'platform_commission' => $platformCommission,
                'agency_earnings' => $validated['quoted_amount'] - $platformCommission,
                'processing_days' => $validated['processing_days'],
                'notes' => $validated['notes'],
                'quoted_at' => now()->toISOString(),
            ],
        ]),
    ]);
    
    // Add to timeline
    $application->updateStatus('quoted', 'Agency submitted quote: ৳' . $validated['quoted_amount']);
    
    // Notify user
    // ... notification logic
    
    return back()->with('success', 'Quote submitted successfully!');
}

public function accept(Request $request, ServiceApplication $application)
{
    if ($application->assigned_to !== auth()->id()) {
        abort(403);
    }
    
    $application->updateStatus('processing', 'Agency accepted the application');
    
    return back()->with('success', 'Application accepted!');
}

public function reject(Request $request, ServiceApplication $application)
{
    if ($application->assigned_to !== auth()->id()) {
        abort(403);
    }
    
    $validated = $request->validate([
        'reason' => 'required|string|max:500',
    ]);
    
    $application->updateStatus('rejected', 'Agency rejected: ' . $validated['reason']);
    
    return back()->with('success', 'Application rejected.');
}
```

#### 4.2. Payment Integration

**File:** `app/Http/Controllers/User/ApplicationPaymentController.php`

```php
<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\ServiceApplication;
use App\Models\WalletTransaction;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ApplicationPaymentController extends Controller
{
    public function show(ServiceApplication $application)
    {
        // Ensure user owns this application
        if ($application->user_id !== auth()->id()) {
            abort(403);
        }
        
        $application->load('serviceModule');
        
        return Inertia::render('User/Applications/Payment', [
            'application' => $application,
        ]);
    }
    
    public function process(Request $request, ServiceApplication $application)
    {
        if ($application->user_id !== auth()->id()) {
            abort(403);
        }
        
        if ($application->payment_status === 'paid') {
            return back()->with('error', 'Already paid!');
        }
        
        $validated = $request->validate([
            'payment_method' => 'required|in:wallet,bkash,nagad,card',
        ]);
        
        $amount = $application->amount;
        
        // Process payment based on method
        if ($validated['payment_method'] === 'wallet') {
            $wallet = auth()->user()->wallet;
            
            if ($wallet->balance < $amount) {
                return back()->with('error', 'Insufficient wallet balance!');
            }
            
            // Deduct from wallet
            $wallet->decrement('balance', $amount);
            
            // Create transaction
            WalletTransaction::create([
                'wallet_id' => $wallet->id,
                'type' => 'debit',
                'amount' => $amount,
                'description' => 'Payment for ' . $application->application_number,
                'reference_type' => ServiceApplication::class,
                'reference_id' => $application->id,
            ]);
        }
        
        // Update application
        $application->update([
            'payment_status' => 'paid',
            'payment_method' => $validated['payment_method'],
            'payment_date' => now(),
            'transaction_id' => 'TXN-' . strtoupper(uniqid()),
        ]);
        
        $application->updateStatus('processing', 'Payment received');
        
        // Update agency stats and earnings
        $assignment = AgencyCountryAssignment::where('agency_id', $application->assigned_to)
            ->where('service_module_id', $application->service_module_id)
            ->first();
        
        if ($assignment) {
            $platformCommission = $assignment->calculatePlatformCommission($amount);
            $assignment->addRevenue($amount);
            
            // Credit agency wallet
            $agencyWallet = $application->assignedTo->wallet;
            $agencyEarnings = $amount - $platformCommission;
            $agencyWallet->increment('balance', $agencyEarnings);
            
            WalletTransaction::create([
                'wallet_id' => $agencyWallet->id,
                'type' => 'credit',
                'amount' => $agencyEarnings,
                'description' => 'Earnings from ' . $application->application_number,
                'reference_type' => ServiceApplication::class,
                'reference_id' => $application->id,
            ]);
        }
        
        return redirect()->route('user.applications.show', $application)
            ->with('success', 'Payment successful!');
    }
}
```

**Estimated Time:** 8-10 hours

---

### 🚀 PHASE 5: Extend to Other Services (2-3 hours per service)

Once tourist visa flow works end-to-end:

1. **Flight Booking** - Query-based with agency quotes
2. **Hotel Booking** - API + agency quotes hybrid
3. **Translation Services** - Document upload + agency quotes
4. **Student Visa** - Similar to tourist visa
5. **Work Visa** - Similar to tourist visa

**Pattern:**
- User submits request
- System finds assigned agency
- Creates ServiceApplication
- Agency quotes/processes
- User pays
- Commission tracked

---

## ✅ VALIDATION CHECKLIST

### Phase 2 Validation:
- [ ] User submits tourist visa application
- [ ] ServiceApplication auto-created
- [ ] Application assigned to correct agency (based on destination country)
- [ ] Agency sees application in dashboard
- [ ] Agency can view application details
- [ ] Admin can view all tourist visa applications
- [ ] Admin can manually assign/reassign to different agency
- [ ] Application number generated correctly

### Phase 3 Validation:
- [ ] VisaRequirement table has 10+ countries
- [ ] Requirements display on tourist visa form
- [ ] Fees calculated correctly
- [ ] Processing time shown to user

### Phase 4 Validation:
- [ ] Agency can submit quote
- [ ] User receives quote notification
- [ ] User can pay via wallet
- [ ] Payment deducted from user wallet
- [ ] Agency earnings credited to agency wallet
- [ ] Platform commission calculated and tracked
- [ ] Application status updates correctly
- [ ] Timeline shows all status changes

### End-to-End Validation:
- [ ] New user registers
- [ ] User fills tourist visa form for Thailand
- [ ] System auto-assigns to agency (if exists)
- [ ] Agency receives notification
- [ ] Agency submits quote: ৳7,000
- [ ] User pays ৳7,000 from wallet
- [ ] Agency receives ৳5,950 (৳7,000 - 15% = ৳1,050)
- [ ] Platform earns ৳1,050
- [ ] Agency marks as completed
- [ ] User receives notification
- [ ] Stats updated correctly

---

## 📊 SUCCESS METRICS

After complete rebuild:

**Agency Metrics:**
- Total applications received
- Applications by status
- Revenue generated
- Commission paid to platform
- Approval rate
- Average processing time

**Platform Metrics:**
- Total applications processed
- Total revenue
- Total commission earned
- Applications by service
- Applications by country
- Conversion rate (submitted → paid)

**User Metrics:**
- Applications submitted
- Applications completed
- Total spent
- Services used
- Satisfaction rating

---

## 🔧 TECHNICAL REQUIREMENTS

### Database:
- ✅ AgencyCountryAssignment with service_module_id
- ✅ ServiceApplication table
- ✅ VisaRequirement table (needs seeding)
- ✅ Wallets and transactions
- ✅ TouristVisa table

### Models:
- ✅ AgencyCountryAssignment (Phase 1)
- ✅ ServiceApplication
- ✅ ServiceModule
- ✅ TouristVisa
- ✅ VisaRequirement
- ✅ Wallet
- ✅ WalletTransaction

### Controllers:
- ❌ Agency/DashboardController
- ❌ Agency/ApplicationController
- ❌ Admin/TouristVisaController
- ❌ User/ApplicationPaymentController
- ✅ Profile/TouristVisaController (needs update)
- ✅ Admin/AgencyAssignmentController (Phase 1)

### Vue Components:
- ❌ Agency/Dashboard.vue
- ❌ Agency/Applications/Index.vue
- ❌ Agency/Applications/Show.vue
- ❌ Admin/TouristVisa/Index.vue
- ❌ Admin/TouristVisa/Show.vue
- ❌ User/Applications/Payment.vue
- ✅ Admin/AgencyAssignments/Create.vue (Phase 1)

### Routes:
- ✅ Admin agency assignment routes (Phase 1)
- ❌ Agency dashboard routes
- ❌ Admin tourist visa management routes
- ❌ User application payment routes

---

## 🎯 NEXT SESSION START POINT

**When you return, start here:**

1. **Test Phase 1** - Verify agency assignment form works:
   ```
   URL: /admin/agency-assignments/create
   - Select service module: Tourist Visa
   - Select agency: Agency User (ID: 2)
   - Select scope: country_specific
   - Select country: Thailand
   - Set commission: 15%
   - Submit
   ```

2. **Begin Phase 2** - Update TouristVisaController:
   - File: `app/Http/Controllers/Profile/TouristVisaController.php`
   - Add `assignToAgency()` method
   - Test tourist visa submission
   - Verify ServiceApplication created

3. **Create Agency Dashboard** - Build agency views:
   - Controller: `Agency/DashboardController.php`
   - View: `Agency/Dashboard.vue`
   - Login as agency@bgplatform.com
   - Verify dashboard loads

---

## 💾 BACKUP BEFORE STARTING

```bash
# Backup database
php artisan db:backup

# Or export SQLite
cp database/database.sqlite database/database.backup.sqlite

# Commit current state
git add .
git commit -m "Phase 1 Complete - Before Phase 2 implementation"
git push
```

---

## 📝 NOTES

- **Agency Email:** agency@bgplatform.com (User ID: 2, Role ID: 3)
- **Test User:** Create one if needed via `/register`
- **Database:** SQLite at `database/database.sqlite`
- **Service Module IDs:** Run `ServiceModule::where('slug', 'tourist-visa')->first()->id`
- **Build Assets:** `npm run build` from `bideshgomon-api` directory

---

**Document Version:** 1.0  
**Last Updated:** November 24, 2025  
**Status:** Ready for Phase 2 Implementation  
**Estimated Total Time:** 30-40 hours  
**Current Progress:** Phase 1 (100%), Phase 2 (0%), Phase 3 (0%), Phase 4 (0%), Phase 5 (0%)
