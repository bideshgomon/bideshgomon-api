# BideshGomon Service Architecture Strategy

## Executive Summary

Complete service architecture redesign based on deep analysis of 38 service modules. This strategy defines 4 distinct service types, eliminates redundant dashboard pages, and creates a unified service center approach.

**Date:** November 29, 2025  
**Status:** 🔄 Strategy & Roadmap  
**Impact:** Platform-wide restructuring

---

## Current State Analysis

### Service Modules Inventory (38 Services)

#### ✅ **IMPLEMENTED** (5 services - 13%)
1. **Tourist Visa** - Query-based, competitive model, working
2. **Flight Booking** - Query-based, competitive model, working
3. **Hotel Booking** - Hybrid (API + Agency), working
4. **Travel Insurance** - API-based, global single agency, working
5. **CV Builder** - Premade/Free, peer-to-peer, working

#### 🚧 **COMING SOON** (33 services - 87%)
All other visa, education, employment, document, and financial services

### Service Types Identified

Based on seeder analysis, we have **4 core service delivery models**:

| Service Type | Assignment Model | Example | Agency Required | API Integration |
|-------------|------------------|---------|-----------------|-----------------|
| **query_based** | competitive, exclusive_resource, multi_country | Tourist Visa, Translation | Yes | No |
| **api_based** | global_single, competitive | Travel Insurance, Foreign Exchange | Optional | Yes |
| **premade** | peer_to_peer | CV Builder, Tour Packages | No | No |
| **marketplace** | multi_country | Job Posting | Yes | No |
| **hybrid** | hybrid | Hotel Booking | Optional | Yes + Agency |

---

## Critical Problems Identified

### 1. **Dashboard Redundancy**
Currently have multiple separate pages for what should be unified:
- `/dashboard` - User dashboard with service recommendations
- `/services` - Service listing page
- `/services/tourist-visa` - Individual service page
- `/profile/tourist-visa` - Application management
- Individual sections for each service (duplicated patterns)

**Problem:** Users navigate through 3-4 pages to complete one service journey.

### 2. **Inconsistent Service Implementation**
- Tourist Visa: Has full flow (query → agencies quote → user accepts)
- CV Builder: Self-service, no agency
- Other 33 services: Not implemented, marked "coming soon"

**Problem:** No standard template for implementing new services.

### 3. **Unclear Service Discovery**
- Service categories exist but aren't prominently displayed
- Assignment models (competitive, exclusive_resource, etc.) not explained to users
- No clear indication which services need agency vs self-service

**Problem:** Users don't understand how services work before applying.

### 4. **Missing Service Templates**

Need 4 distinct application forms based on service type:

| Service Type | Form Template | Agency Flow | Payment Flow |
|-------------|--------------|-------------|-------------|
| query_based | Multi-step with docs upload | Quote-based (multiple agencies compete) | Pay after quote acceptance |
| api_based | Simple form + instant API call | Direct booking (fixed price) | Pay immediately |
| premade | Self-service with templates | No agency | Free or one-time fee |
| marketplace | Listing/search interface | Browse + apply | Varies by listing |
| hybrid | Form + API options | API results + agency backup | Instant or quote-based |

**Problem:** Currently only Tourist Visa has proper implementation. Others undefined.

---

## Proposed Solution: Unified Service Center

### Architecture Overview

```
┌─────────────────────────────────────────────────────────────┐
│                    USER DASHBOARD                            │
│  - Quick stats                                               │
│  - Active applications                                       │
│  - Recommended services (based on profile)                   │
│  - CTA: "Browse All Services"  ────────────────┐            │
└─────────────────────────────────────────────────│────────────┘
                                                  │
                                                  ▼
┌─────────────────────────────────────────────────────────────┐
│                 SERVICE CENTER HUB                           │
│                 /services (main hub)                         │
├─────────────────────────────────────────────────────────────┤
│  Category Tabs:                                              │
│  [Visa] [Travel] [Education] [Employment] [Documents] [More]│
│                                                               │
│  Service Cards (grid layout):                                │
│  ┌────────────┐ ┌────────────┐ ┌────────────┐              │
│  │ Tourist    │ │ Student    │ │ Work Visa  │              │
│  │ Visa       │ │ Visa       │ │            │              │
│  │ [Active]   │ │[Coming Soon│ │[Coming Soon│              │
│  │ Query-based│ │            │ │            │              │
│  └────────────┘ └────────────┘ └────────────┘              │
│                                                               │
│  Each card shows:                                            │
│  - Service name & icon                                       │
│  - Status badge (Active/Coming Soon)                         │
│  - Service type badge (Query/API/Premade/Marketplace)        │
│  - Price range or "Get Quote"                                │
│  - Processing time estimate                                  │
│  - Click → Service Detail Page                               │
└─────────────────────────────────────────────────────────────┘
                                                  │
                                                  ▼
┌─────────────────────────────────────────────────────────────┐
│              SERVICE DETAIL PAGE                             │
│              /services/{slug}                                │
├─────────────────────────────────────────────────────────────┤
│  Hero Section:                                               │
│  - Service name, description, pricing                        │
│  - Service type badge                                        │
│  - Processing time                                           │
│  - CTA: "Apply Now" or "Coming Soon"                        │
│                                                               │
│  How It Works (dynamic based on service_type):               │
│  ┌─ query_based ────────────────────────────────┐           │
│  │ 1. Submit your requirements                   │           │
│  │ 2. Multiple agencies send quotes (24-48h)     │           │
│  │ 3. Compare & select best offer                │           │
│  │ 4. Agency processes your application          │           │
│  └───────────────────────────────────────────────┘           │
│                                                               │
│  │ OR for api_based:                                         │
│  │ 1. Enter details → Instant results from API               │
│  │ 2. Select option → Pay → Confirmed                        │
│                                                               │
│  │ OR for premade:                                           │
│  │ 1. Use our free tool/template                             │
│  │ 2. Self-service, no agency needed                         │
│                                                               │
│  Required Documents:                                         │
│  - List of documents needed                                  │
│                                                               │
│  Agencies Offering This Service: (if applicable)             │
│  - Top-rated agencies with stats                             │
│                                                               │
│  Reviews & Ratings:                                          │
│  - Customer testimonials                                     │
└─────────────────────────────────────────────────────────────┘
                                                  │
                                                  ▼
┌─────────────────────────────────────────────────────────────┐
│              APPLICATION FORM                                │
│              /services/{slug}/apply                          │
│              (Dynamic based on service_type)                 │
├─────────────────────────────────────────────────────────────┤
│  Form Type 1: QUERY_BASED (e.g., Tourist Visa)             │
│  ┌───────────────────────────────────────────────┐          │
│  │ Step 1: Basic Information                      │          │
│  │ - Destination country, purpose, dates          │          │
│  │                                                 │          │
│  │ Step 2: Personal Details                       │          │
│  │ - From user profile (auto-filled)              │          │
│  │                                                 │          │
│  │ Step 3: Document Upload                        │          │
│  │ - Passport scan, photos, etc.                  │          │
│  │                                                 │          │
│  │ Step 4: Additional Information                 │          │
│  │ - Service-specific questions                   │          │
│  │                                                 │          │
│  │ [Submit for Quotes] ─→ Creates ServiceApplication        │
│  └───────────────────────────────────────────────┘          │
│                                                               │
│  Form Type 2: API_BASED (e.g., Travel Insurance)           │
│  ┌───────────────────────────────────────────────┐          │
│  │ - Travel dates, destination, coverage type     │          │
│  │ [Get Quote] ─→ API call ─→ Instant results    │          │
│  │ - Show options with prices                     │          │
│  │ [Buy Now] ─→ Payment ─→ Instant confirmation  │          │
│  └───────────────────────────────────────────────┘          │
│                                                               │
│  Form Type 3: PREMADE (e.g., CV Builder)                   │
│  ┌───────────────────────────────────────────────┐          │
│  │ - Interactive tool/template                    │          │
│  │ - Self-service interface                       │          │
│  │ - Download/save results                        │          │
│  └───────────────────────────────────────────────┘          │
│                                                               │
│  Form Type 4: MARKETPLACE (e.g., Job Posting)              │
│  ┌───────────────────────────────────────────────┐          │
│  │ - Browse listings (countries, jobs, packages)  │          │
│  │ - Filter & search                              │          │
│  │ - Click listing → Details → Apply              │          │
│  └───────────────────────────────────────────────┘          │
└─────────────────────────────────────────────────────────────┘
                                                  │
                                                  ▼
┌─────────────────────────────────────────────────────────────┐
│            MY APPLICATIONS                                   │
│            /profile/applications                             │
│            (Consolidated view of ALL services)               │
├─────────────────────────────────────────────────────────────┤
│  Tabs by Status:                                             │
│  [All] [Pending Quotes] [Active] [Completed] [Cancelled]   │
│                                                               │
│  Application Cards:                                          │
│  ┌─────────────────────────────────────────────┐            │
│  │ Tourist Visa - Thailand                      │            │
│  │ Status: Waiting for Quotes (3 received)      │            │
│  │ Submitted: Nov 29, 2025                      │            │
│  │ [View Details] [View Quotes]                 │            │
│  └─────────────────────────────────────────────┘            │
│                                                               │
│  │ (No more separate /profile/tourist-visa page)            │
│  │ (All services consolidated here)                          │
└─────────────────────────────────────────────────────────────┘
```

---

## Implementation Strategy

### Phase 1: Foundation (Week 1-2) ⚡ PRIORITY

#### 1.1 Database Schema Updates

**Add to `service_modules` table** (already has service_type, assignment_model):
```php
// migration: add_form_template_to_service_modules
$table->enum('form_template', [
    'query_multistep',     // Tourist visa style
    'api_instant',         // Insurance/flight style
    'selfservice_tool',    // CV builder style
    'marketplace_browse'   // Job posting style
])->after('service_type');

$table->json('form_fields')->nullable(); // Dynamic form configuration
$table->json('api_config')->nullable();  // API endpoint, keys, etc.
$table->string('success_page_route')->nullable();
```

**Create `service_packages` table** (for premade services like Hajj):
```php
Schema::create('service_packages', function (Blueprint $table) {
    $table->id();
    $table->foreignId('service_module_id')->constrained();
    $table->foreignId('agency_id')->constrained();
    $table->string('package_name');
    $table->text('description');
    $table->decimal('price', 10, 2);
    $table->string('currency', 3)->default('BDT');
    $table->json('inclusions'); // What's included
    $table->json('exclusions'); // What's not included
    $table->integer('duration_days');
    $table->date('valid_from');
    $table->date('valid_until');
    $table->integer('max_participants')->nullable();
    $table->integer('current_bookings')->default(0);
    $table->boolean('is_active')->default(true);
    $table->timestamps();
});
```

#### 1.2 Create Base Service Controllers

**BaseServiceController.php:**
```php
abstract class BaseServiceController extends Controller
{
    protected ServiceModule $serviceModule;
    
    abstract public function apply(Request $request);
    abstract public function getFormData();
    
    public function show()
    {
        // Standard service detail page
        return Inertia::render('Services/Show', [
            'service' => $this->serviceModule,
            'formTemplate' => $this->getFormTemplate(),
            'howItWorks' => $this->getHowItWorks(),
        ]);
    }
    
    protected function getFormTemplate()
    {
        return match($this->serviceModule->form_template) {
            'query_multistep' => 'Services/Forms/QueryBased',
            'api_instant' => 'Services/Forms/ApiInstant',
            'selfservice_tool' => 'Services/Forms/SelfService',
            'marketplace_browse' => 'Services/Forms/Marketplace',
        };
    }
}
```

**4 Specialized Controllers:**
1. `QueryBasedServiceController` - extends Base (Tourist Visa, Translation, etc.)
2. `ApiBasedServiceController` - extends Base (Insurance, Flight search, etc.)
3. `PremadeServiceController` - extends Base (CV Builder, Packages)
4. `MarketplaceServiceController` - extends Base (Job Posting, University Browse)

#### 1.3 Unified Service Center UI

**NEW: `resources/js/Pages/Services/Index.vue`**
- Replace current simple `/services` page
- Category tabs (Visa, Travel, Education, etc.)
- Grid of service cards with:
  * Service type badges (Query/API/Premade/Marketplace)
  * Status (Active/Coming Soon)
  * Price indication
  * Quick action buttons
- Search & filter functionality

**ENHANCE: `resources/js/Pages/Services/Show.vue`**
- Dynamic "How It Works" section based on service_type
- Agency listing (if query_based)
- Package listings (if premade with packages)
- API search (if api_based)
- CTA adapts to service type

**NEW: 4 Form Templates**
1. `Services/Forms/QueryBased.vue` - Multi-step wizard (tourist visa pattern)
2. `Services/Forms/ApiInstant.vue` - Simple form + instant results
3. `Services/Forms/SelfService.vue` - Interactive tool interface
4. `Services/Forms/Marketplace.vue` - Browse/search/filter interface

---

### Phase 2: Service Type Implementation (Week 3-4)

#### 2.1 Query-Based Services (Template: Tourist Visa)

**Services to implement:**
- Student Visa
- Work Visa
- Business Visa
- Medical Visa
- Family Reunion Visa
- Translation Services
- Document Attestation
- Notary Services
- Hajj & Umrah (query for custom packages)

**Pattern:**
```php
// Controller: QueryBasedServiceController
public function apply(Request $request, $serviceSlug)
{
    $service = ServiceModule::where('slug', $serviceSlug)->firstOrFail();
    
    // 1. Create ServiceApplication (quote request)
    $application = ServiceApplication::create([
        'user_id' => auth()->id(),
        'service_module_id' => $service->id,
        'status' => 'pending_quotes',
        'form_data' => $request->validated(),
    ]);
    
    // 2. Notify agencies offering this service
    $this->notifyAgencies($application);
    
    // 3. Redirect to application tracking
    return redirect()->route('profile.applications.show', $application);
}
```

**Flow:**
1. User fills multi-step form
2. System creates ServiceApplication with `status: pending_quotes`
3. Agencies receive notifications
4. Agencies submit quotes (existing Quote model)
5. User compares quotes
6. User accepts quote → Application assigned to agency
7. Agency processes → Updates status
8. Completion & payment

#### 2.2 API-Based Services (Real-time data)

**Services to implement:**
- Travel Insurance (existing)
- Flight Booking (enhance with real API)
- Foreign Exchange
- Travel Money Card
- Hotel Booking (enhance existing hybrid)

**API Integration Strategy:**

| Service | Recommended API | Type | Cost |
|---------|----------------|------|------|
| Flights | **Amadeus API** or **Skyscanner** | REST | Free tier available |
| Hotels | **Booking.com Affiliate** or **Agoda** | REST | Commission-based |
| Insurance | **Cover Genius** or local BD providers | REST | Commission |
| Forex | **XE.com API** or **CurrencyLayer** | REST | Free tier |

**Pattern:**
```php
// Controller: ApiBasedServiceController
public function search(Request $request)
{
    $service = ServiceModule::where('slug', $request->slug)->firstOrFail();
    $apiService = app("Services\\Api\\{$service->api_service_class}");
    
    $results = $apiService->search($request->validated());
    
    return response()->json([
        'results' => $results,
        'cached' => false,
        'timestamp' => now(),
    ]);
}

public function book(Request $request)
{
    // Instant booking with API
    $booking = $apiService->book($request->validated());
    
    // Create ServiceApplication record
    $application = ServiceApplication::create([
        'user_id' => auth()->id(),
        'service_module_id' => $service->id,
        'status' => 'completed',
        'api_reference' => $booking->reference,
        'amount' => $booking->total_price,
    ]);
    
    // Process payment
    return $this->processPayment($application);
}
```

**Flow:**
1. User enters search criteria
2. API call → Instant results displayed
3. User selects option → Redirected to payment
4. Payment processed → API booking confirmed
5. ServiceApplication created with status: completed

#### 2.3 Premade/Self-Service (No agency needed)

**Services to implement:**
- CV Builder (existing - enhance)
- Tour Packages (browsable packages)
- University Browse (marketplace)
- Job Posting (marketplace)

**Pattern A: Free Tools (CV Builder)**
```php
// No backend processing needed
// Pure Vue.js frontend tool
// Optional: Save to user profile
```

**Pattern B: Premade Packages (Hajj/Umrah)**
```php
// Controller: PremadeServiceController
public function packages($serviceSlug)
{
    $service = ServiceModule::where('slug', $serviceSlug)->firstOrFail();
    
    $packages = ServicePackage::where('service_module_id', $service->id)
        ->active()
        ->with('agency')
        ->paginate(12);
    
    return Inertia::render('Services/Packages', [
        'service' => $service,
        'packages' => $packages,
    ]);
}

public function bookPackage(Request $request, ServicePackage $package)
{
    // Direct booking, no quote needed
    $application = ServiceApplication::create([
        'user_id' => auth()->id(),
        'service_module_id' => $package->service_module_id,
        'agency_id' => $package->agency_id,
        'package_id' => $package->id,
        'status' => 'confirmed',
        'amount' => $package->price,
    ]);
    
    return $this->processPayment($application);
}
```

**Flow:**
1. User browses packages (grid view)
2. Clicks package → Details modal
3. "Book Now" → Checkout
4. Payment → Confirmed
5. Agency receives booking notification

#### 2.4 Marketplace Services (Browse & Apply)

**Services to implement:**
- Job Posting & Search
- University Application (browse universities)
- School Application
- Student Accommodation

**Pattern:**
```php
// Controller: MarketplaceServiceController
public function browse($serviceSlug)
{
    $service = ServiceModule::where('slug', $serviceSlug)->firstOrFail();
    
    // Example: Job marketplace
    if ($service->slug === 'job-posting') {
        $listings = AgencyResource::where('service_module_id', $service->id)
            ->where('resource_type', 'job_listing')
            ->active()
            ->with('agency')
            ->latest()
            ->paginate(20);
    }
    
    return Inertia::render('Services/Marketplace', [
        'service' => $service,
        'listings' => $listings,
        'filters' => $this->getFilters($service),
    ]);
}

public function applyToListing(Request $request, AgencyResource $listing)
{
    // User applies to specific listing
    $application = ServiceApplication::create([
        'user_id' => auth()->id(),
        'service_module_id' => $listing->service_module_id,
        'agency_id' => $listing->agency_id,
        'resource_id' => $listing->id,
        'status' => 'submitted',
        'form_data' => $request->validated(),
    ]);
    
    $listing->agency->notify(new NewApplicationReceived($application));
    
    return redirect()->route('profile.applications.show', $application);
}
```

**Flow:**
1. User browses listings (jobs, universities, etc.)
2. Filters by country, field, requirements
3. Clicks listing → Details page
4. "Apply" → Submit application
5. Agency reviews and responds

---

### Phase 3: Dashboard Consolidation (Week 5)

#### 3.1 Pages to REMOVE (Redundant)

❌ **DELETE THESE:**
1. `/profile/tourist-visa/index` - Redundant (consolidate to `/profile/applications`)
2. `/profile/tourist-visa/show/{id}` - Move to `/profile/applications/{id}`
3. Separate index pages for each service type
4. Any service-specific listing pages

✅ **KEEP ONLY:**
1. `/dashboard` - User homepage with quick stats & recommendations
2. `/services` - Service center hub (all services, categorized)
3. `/services/{slug}` - Service detail page (dynamic)
4. `/services/{slug}/apply` - Application form (dynamic based on service type)
5. `/profile/applications` - All applications across ALL services
6. `/profile/applications/{id}` - Application detail (dynamic)

#### 3.2 Unified Application Management

**NEW: `resources/js/Pages/Profile/Applications/Index.vue`**
```vue
<template>
  <div>
    <h1>My Applications</h1>
    
    <!-- Filter by service type, status -->
    <Tabs>
      <Tab name="All" />
      <Tab name="Visa Services" />
      <Tab name="Travel Services" />
      <Tab name="Education" />
      <Tab name="Documents" />
    </Tabs>
    
    <!-- Status filters -->
    <Pills>
      <Pill :active="statusFilter === 'all'">All</Pill>
      <Pill :active="statusFilter === 'pending_quotes'">Pending Quotes</Pill>
      <Pill :active="statusFilter === 'active'">Active</Pill>
      <Pill :active="statusFilter === 'completed'">Completed</Pill>
    </Pills>
    
    <!-- Application cards (unified across all services) -->
    <div v-for="app in applications" :key="app.id" class="card">
      <ServiceTypeBadge :type="app.service.service_type" />
      <h3>{{ app.service.name }} - {{ app.destination }}</h3>
      <StatusBadge :status="app.status" />
      
      <!-- Dynamic content based on service type -->
      <div v-if="app.service_type === 'query_based' && app.quotes_count > 0">
        <p>{{ app.quotes_count }} quotes received</p>
        <Button @click="viewQuotes(app)">Compare Quotes</Button>
      </div>
      
      <div v-if="app.service_type === 'api_based'">
        <p>Booking Reference: {{ app.api_reference }}</p>
        <Button @click="downloadTicket(app)">Download</Button>
      </div>
      
      <Link :href="route('profile.applications.show', app.id)">
        View Details
      </Link>
    </div>
  </div>
</template>
```

**Pattern:**
- Single unified list
- Service-agnostic design
- Dynamic actions based on service_type
- No more separate pages per service

#### 3.3 Updated Dashboard

**ENHANCE: `resources/js/Pages/Dashboard.vue`**
- Remove redundant service shortcuts
- Focus on:
  * Quick stats (active applications, pending quotes)
  * Recent activity
  * Profile completion prompt
  * "Browse Services" CTA
  * Recommended services (based on profile)

---

### Phase 4: Service Templates & Automation (Week 6-7)

#### 4.1 Service Configuration System

**Admin UI: Service Module Editor**

Add to `/admin/service-modules/{id}/edit`:

```vue
<template>
  <div>
    <h2>Configure Service: {{ service.name }}</h2>
    
    <!-- Service Type Selection -->
    <Select v-model="form.service_type">
      <option value="query_based">Query-Based (Agencies Quote)</option>
      <option value="api_based">API-Based (Instant Results)</option>
      <option value="premade">Premade Packages</option>
      <option value="marketplace">Marketplace (Browse Listings)</option>
      <option value="hybrid">Hybrid (API + Agency)</option>
    </Select>
    
    <!-- Dynamic configuration based on service_type -->
    
    <!-- For query_based: -->
    <div v-if="form.service_type === 'query_based'">
      <h3>Quote Settings</h3>
      <Input v-model="form.quote_timeout_hours" label="Quote Timeout (hours)" />
      <Input v-model="form.min_quotes_required" label="Min Quotes Required" />
      
      <h3>Form Fields (drag to reorder)</h3>
      <FormBuilder v-model="form.form_fields" />
    </div>
    
    <!-- For api_based: -->
    <div v-if="form.service_type === 'api_based'">
      <h3>API Configuration</h3>
      <Select v-model="form.api_provider">
        <option value="amadeus">Amadeus (Flights)</option>
        <option value="booking">Booking.com (Hotels)</option>
        <option value="covergenius">Cover Genius (Insurance)</option>
        <option value="custom">Custom API</option>
      </Select>
      
      <Input v-model="form.api_endpoint" label="API Endpoint" />
      <Input v-model="form.api_key" label="API Key" type="password" />
    </div>
    
    <!-- For premade: -->
    <div v-if="form.service_type === 'premade'">
      <Checkbox v-model="form.allow_packages" label="Allow Agency Packages" />
      <Checkbox v-model="form.is_free_tool" label="Free Self-Service Tool" />
    </div>
    
    <!-- For marketplace: -->
    <div v-if="form.service_type === 'marketplace'">
      <h3>Listing Settings</h3>
      <Select v-model="form.resource_type">
        <option value="job_listing">Job Listings</option>
        <option value="university">Universities</option>
        <option value="accommodation">Accommodations</option>
      </Select>
      
      <Input v-model="form.listing_approval_required" label="Require Admin Approval" />
    </div>
  </div>
</template>
```

#### 4.2 Service Launcher Workflow

**For Activating New Services:**

1. Admin goes to `/admin/service-modules`
2. Clicks "Configure" on coming_soon service
3. Selects service_type
4. Fills configuration (form fields, API settings, etc.)
5. Tests service flow
6. Clicks "Activate Service"
7. System automatically:
   - Generates routes
   - Creates form pages
   - Notifies agencies (if applicable)
   - Adds to service center

**No code changes needed** for standard services following patterns.

#### 4.3 Dynamic Form Generator

**Component: `FormBuilder.vue`**
- Drag-and-drop form field builder
- Field types: text, select, date, file upload, country picker, etc.
- Conditional logic (show field X if field Y = value)
- Document requirements linked to fields
- Validation rules

**Stored in `service_modules.form_fields` JSON:**
```json
{
  "steps": [
    {
      "title": "Basic Information",
      "fields": [
        {
          "name": "destination_country",
          "type": "country_select",
          "label": "Destination Country",
          "required": true,
          "validation": "required"
        },
        {
          "name": "travel_dates",
          "type": "date_range",
          "label": "Travel Dates",
          "required": true
        }
      ]
    }
  ]
}
```

---

### Phase 5: API Integration Layer (Week 8-9)

#### 5.1 API Service Classes

**Create: `app/Services/Api/` directory**

**FlightSearchService.php:**
```php
class FlightSearchService implements ApiServiceInterface
{
    protected $provider; // Amadeus, Skyscanner, etc.
    
    public function search(array $params): array
    {
        // Call API
        $response = Http::withHeaders([
            'Authorization' => "Bearer {$this->apiKey}"
        ])->get($this->endpoint, $params);
        
        // Transform to standard format
        return $this->transformResults($response->json());
    }
    
    public function book(array $params): object
    {
        // Process booking
    }
    
    protected function transformResults($apiData): array
    {
        // Convert API response to our standard format
        return [
            'results' => array_map(fn($flight) => [
                'provider_id' => $flight['id'],
                'price' => $flight['price']['total'],
                'currency' => $flight['price']['currency'],
                'departure' => $flight['itineraries'][0]['segments'][0]['departure'],
                'arrival' => $flight['itineraries'][0]['segments'][0]['arrival'],
                'airline' => $flight['validatingAirlineCodes'][0],
            ], $apiData['data']),
        ];
    }
}
```

**Similar services for:**
- `HotelSearchService.php`
- `InsuranceQuoteService.php`
- `ForexRateService.php`

#### 5.2 Caching Strategy

**For API calls:**
- Cache flight search results: 15 minutes
- Cache hotel availability: 5 minutes
- Cache forex rates: 1 hour
- Use Redis for fast access

**Pattern:**
```php
$cacheKey = "flights:{$origin}:{$destination}:{$date}";
$results = Cache::remember($cacheKey, 900, function() use ($params) {
    return $this->flightService->search($params);
});
```

#### 5.3 Fallback to Agency

**For hybrid services (hotel booking):**
```php
// Try API first
try {
    $apiResults = $this->hotelService->search($params);
    
    if (empty($apiResults)) {
        // No API results, show agency option
        return $this->showAgencyQuoteOption($params);
    }
    
    return $apiResults;
} catch (ApiException $e) {
    // API failed, fallback to agency
    Log::error("Hotel API failed: " . $e->getMessage());
    return $this->showAgencyQuoteOption($params);
}
```

---

## Service-by-Service Roadmap

### Priority 1: High-Demand Services (Weeks 3-5)

| Service | Type | Implementation | Effort |
|---------|------|----------------|--------|
| **Student Visa** | query_based | Clone Tourist Visa pattern | 3 days |
| **Work Visa** | query_based | Clone Tourist Visa pattern | 3 days |
| **Translation** | query_based | Clone Tourist Visa pattern | 2 days |
| **Document Attestation** | query_based | Clone Tourist Visa pattern | 2 days |
| **Flight Booking** | api_based | Amadeus API integration | 5 days |
| **Hajj & Umrah Packages** | premade | Package listing system | 4 days |

**Total: 19 days (4 weeks with testing)**

### Priority 2: Medium-Demand Services (Weeks 6-8)

| Service | Type | Implementation | Effort |
|---------|------|----------------|--------|
| Business Visa | query_based | Clone pattern | 2 days |
| Medical Visa | query_based | Clone pattern | 2 days |
| Family Reunion | query_based | Clone pattern | 2 days |
| University Application | marketplace | Listing system | 5 days |
| Job Posting | marketplace | Listing system | 4 days |
| Foreign Exchange | api_based | XE.com API | 3 days |

**Total: 18 days (4 weeks)**

### Priority 3: Specialized Services (Weeks 9-10)

| Service | Type | Implementation | Effort |
|---------|------|----------------|--------|
| Language Course | query_based | Clone pattern | 2 days |
| Scholarship Assistance | query_based | Clone pattern | 2 days |
| Education Loan | query_based | Clone pattern | 2 days |
| Interview Prep | query_based | Clone pattern | 2 days |
| Medical Tourism | query_based | Clone pattern | 3 days |
| Relocation Services | query_based | Clone pattern | 3 days |

**Total: 14 days (3 weeks)**

### Priority 4: Low-Demand/Future (Later)

- Airport Transfer
- Car Rental
- Tour Packages
- Student Accommodation
- SIM Card & Internet
- Bank Account Opening
- Travel Money Card
- Certificate Verification
- Police Clearance
- Medical Certificate
- Legal Consultation

**All follow established patterns, can be activated quickly when needed.**

---

## Technical Specifications

### Route Structure

**OLD (Current - Inconsistent):**
```php
// Tourist visa has special routes
Route::prefix('services/tourist-visa')->group(...);
Route::prefix('profile/tourist-visa')->group(...);

// CV builder has different routes
Route::prefix('services/cv-builder')->group(...);

// Other services: ???
```

**NEW (Unified):**
```php
// All services follow same pattern
Route::prefix('services')->group(function() {
    Route::get('/', [ServiceController::class, 'index'])->name('services.index');
    Route::get('/{slug}', [ServiceController::class, 'show'])->name('services.show');
    Route::post('/{slug}/apply', [ServiceController::class, 'apply'])->name('services.apply');
    
    // API-based services
    Route::post('/{slug}/search', [ApiServiceController::class, 'search'])->name('services.search');
    Route::post('/{slug}/book', [ApiServiceController::class, 'book'])->name('services.book');
    
    // Marketplace services
    Route::get('/{slug}/browse', [MarketplaceController::class, 'browse'])->name('services.browse');
    Route::post('/{slug}/listings/{listing}/apply', [MarketplaceController::class, 'apply'])->name('services.listings.apply');
    
    // Package services
    Route::get('/{slug}/packages', [PackageController::class, 'index'])->name('services.packages');
    Route::post('/{slug}/packages/{package}/book', [PackageController::class, 'book'])->name('services.packages.book');
});

// Unified application management
Route::prefix('profile/applications')->group(function() {
    Route::get('/', [ApplicationController::class, 'index'])->name('profile.applications.index');
    Route::get('/{application}', [ApplicationController::class, 'show'])->name('profile.applications.show');
    Route::put('/{application}', [ApplicationController::class, 'update'])->name('profile.applications.update');
    Route::delete('/{application}', [ApplicationController::class, 'destroy'])->name('profile.applications.destroy');
    
    // Quote management
    Route::get('/{application}/quotes', [QuoteController::class, 'index'])->name('profile.applications.quotes');
    Route::post('/{application}/quotes/{quote}/accept', [QuoteController::class, 'accept'])->name('profile.applications.quotes.accept');
});
```

### Database Schema Changes

**migrations needed:**

1. `add_form_template_to_service_modules.php`
2. `create_service_packages_table.php`
3. `add_api_config_to_service_modules.php`
4. `add_marketplace_fields_to_agency_resources.php`

### Component Library

**New reusable components:**

1. `ServiceCard.vue` - Standard service card for grid
2. `ServiceTypeBadge.vue` - Visual badge (Query/API/Premade/Marketplace)
3. `HowItWorks.vue` - Dynamic explainer based on service_type
4. `QuoteComparison.vue` - Side-by-side agency quote comparison
5. `PackageCard.vue` - Package display card
6. `ListingCard.vue` - Marketplace listing card
7. `ApplicationTimeline.vue` - Status progression tracker
8. `FormStepWizard.vue` - Multi-step form container
9. `ApiSearchResults.vue` - API result display (flights, hotels, etc.)
10. `ServiceFilter.vue` - Category/status/price filtering

---

## User Experience Flow Examples

### Example 1: Student Visa (Query-Based)

**Before (Proposed):**
1. Dashboard → "Student Visa" card (not implemented)
2. Would need separate implementation

**After (Unified):**
1. Dashboard → "Browse Services" or direct link from recommendation
2. Service Center → Education tab → "Student Visa" card
3. Student Visa detail page:
   - Explains process (Submit → Get Quotes → Compare → Select)
   - Shows required documents
   - Lists agencies offering this service with ratings
   - "Apply Now" button
4. Application form (4 steps):
   - Step 1: University & Country selection
   - Step 2: Personal details (auto-filled from profile)
   - Step 3: Document upload (passport, transcripts, etc.)
   - Step 4: Additional info
5. Submit → Redirected to "My Applications"
6. Application card shows "Waiting for Quotes"
7. Notifications when quotes arrive
8. User clicks "View Quotes" → Quote comparison page
9. Selects best quote → Agency assigned
10. Agency processes → Status updates
11. Completion → Payment → Done

**Time: 15 minutes for initial application. Quotes arrive in 24-48 hours.**

### Example 2: Flight Booking (API-Based)

**Before (Current):**
1. Flight booking exists but maybe not fully functional with real API

**After (Unified):**
1. Service Center → Travel tab → "Flight Booking"
2. Flight detail page → "Search Flights" form:
   - Origin, Destination, Dates, Passengers
3. Click "Search" → API call to Amadeus
4. Results page (within 3 seconds):
   - List of flights with prices
   - Filter by airline, stops, time, price
   - Sort options
5. Select flight → Review details
6. "Book Now" → Passenger details form
7. Submit → Payment page
8. Pay → Instant booking confirmation
9. Download e-ticket
10. Application auto-created with status: completed

**Time: 5-10 minutes total.**

### Example 3: Hajj Package (Premade)

**Before:**
1. Not implemented

**After (Unified):**
1. Service Center → Other tab → "Hajj & Umrah"
2. Hajj detail page → "Browse Packages" button
3. Package gallery (grid view):
   - Packages from different agencies
   - Each shows: Price, Duration, Inclusions, Agency rating
   - Filter by: Price range, Duration, Departure city, Date
4. Click package → Package details modal:
   - Full itinerary
   - What's included/excluded
   - Agency info
   - Reviews
   - Availability calendar
5. "Book This Package" → Checkout
   - Select dates
   - Number of pilgrims
   - Add-ons (extra services)
6. Payment → Confirmation
7. Agency receives booking notification
8. Application created with status: confirmed

**Time: 10-15 minutes.**

---

## Migration Plan

### Step-by-Step Migration (Avoid Breaking Changes)

**Week 1: Foundation**
- Create new routes (parallel to existing)
- Build ServiceCenterController (new)
- Create unified Services/Index.vue (new page)
- Add form_template field to service_modules table
- No deletions yet

**Week 2: Service Templates**
- Build 4 form template components
- Create BaseServiceController
- Implement QueryBasedServiceController
- Test with Tourist Visa (dual system - old + new)
- Add service type badges to UI

**Week 3-4: Migrate Tourist Visa**
- Point tourist-visa routes to new controllers
- Update TouristVisa pages to use new templates
- Keep old routes as aliases (redirects)
- Test thoroughly

**Week 5: Consolidate Applications**
- Build unified profile/applications page
- Migrate tourist visa applications to new UI
- Add breadcrumbs back to old URLs
- Start phasing out old pages

**Week 6: New Services**
- Launch Student Visa (new)
- Launch Work Visa (new)
- Launch Translation (new)
- All use new system from day 1

**Week 7: API Services**
- Integrate Flight API
- Test hybrid hotel system
- Launch insurance improvements

**Week 8: Cleanup**
- Remove old tourist-visa specific routes
- Delete redundant pages
- Update all links
- Final testing

**Week 9-10: Documentation & Training**
- Admin documentation for service configuration
- Agency guide for new quote system
- User guide for new service center
- Video tutorials

---

## Success Metrics

### Platform Health
- **Service Activation Rate:** 80% of 38 services active within 3 months
- **Code Reuse:** 90% of services use standard templates (minimal custom code)
- **Development Speed:** New service launch in <2 days after configuration

### User Experience
- **Service Discovery:** 50% of users browse Service Center within first session
- **Application Completion:** 70% of started applications completed (up from current)
- **Time to Apply:** Average <10 minutes for standard services
- **Quote Response Time:** 80% of quote requests get ≥2 quotes within 24 hours

### Business Impact
- **Service Utilization:** Average user applies for 2.5 services (up from 1.2)
- **Revenue per User:** Increase by 60% due to more service consumption
- **Agency Satisfaction:** 85% of agencies active on ≥3 service types
- **Platform Commission:** Clear tracking per service type

---

## Risks & Mitigation

| Risk | Impact | Mitigation |
|------|--------|------------|
| **Breaking existing Tourist Visa flow** | High | Parallel implementation, gradual migration, keep old routes as fallback |
| **API costs exceed budget** | Medium | Use free tiers initially, cache aggressively, set request limits |
| **Agencies resist new quote system** | Medium | Training, onboarding bonus, highlight benefits (more leads) |
| **Users confused by new structure** | Medium | Clear onboarding, help tooltips, video tutorials |
| **Technical debt from rapid implementation** | High | Strict adherence to templates, code reviews, refactor sprints |
| **Service type classification errors** | Low | Admin can reconfigure service type anytime |

---

## Next Steps (Immediate Actions)

### This Week:
1. ✅ **Get stakeholder approval** on this strategy
2. 🔨 **Create database migrations** (form_template, service_packages)
3. 🎨 **Design mockups** for:
   - Service Center hub
   - Service detail page variations (4 types)
   - Unified My Applications page
4. 🏗️ **Start Phase 1 implementation:**
   - BaseServiceController
   - Service Center Index page
   - Service type badges

### Next Week:
1. 🧪 **Test framework** for service templates
2. 📝 **Admin service configuration UI**
3. 🔄 **Migrate Tourist Visa** to new pattern (pilot)
4. 📊 **Analytics setup** for tracking metrics

---

## Conclusion

This unified service architecture will:

✅ **Eliminate redundancy** - Single pattern for all 38 services  
✅ **Accelerate development** - New services launch in days, not weeks  
✅ **Improve UX** - Consistent interface, clear processes  
✅ **Enable scaling** - Easy to add 50+ more services  
✅ **Reduce maintenance** - One codebase, not 38 separate implementations  
✅ **Increase revenue** - More services = more opportunities  

**Status:** Ready for implementation approval  
**Timeline:** 10 weeks for full implementation  
**ROI:** 3x faster service launches, 60% increase in revenue per user

**Recommended Decision:** ✅ Approve and proceed with Phase 1

---

**Document Version:** 1.0  
**Author:** AI System Architect  
**Review Date:** November 29, 2025  
**Next Review:** After Phase 1 completion
