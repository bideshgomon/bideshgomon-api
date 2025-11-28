# 🏗️ SYSTEM ARCHITECTURE - TECHNICAL OVERVIEW

**Project**: BideshGomon Multi-Agency SaaS Platform  
**Last Updated**: November 19, 2025

---

## 📐 HIGH-LEVEL ARCHITECTURE

```
┌─────────────────────────────────────────────────────────────┐
│                    CLIENT LAYER (Vue 3)                      │
├─────────────────────────────────────────────────────────────┤
│  GuestLayout  │  AuthLayout  │  AdminLayout  │  AgencyLayout │
│  PublicLayout │  ConsultantLayout                            │
├─────────────────────────────────────────────────────────────┤
│              Inertia.js (SPA without REST API)              │
├─────────────────────────────────────────────────────────────┤
│                  APPLICATION LAYER (Laravel 12)              │
├─────────────────────────────────────────────────────────────┤
│  Controllers (Thin)  →  Services (Business Logic)           │
│  Middleware (Auth, Role, Service Access)                    │
│  Observers (Auto-initialization)                            │
├─────────────────────────────────────────────────────────────┤
│                    DATA LAYER (MySQL)                        │
├─────────────────────────────────────────────────────────────┤
│  90+ Tables │ Eloquent ORM │ Relationships │ Migrations     │
├─────────────────────────────────────────────────────────────┤
│               CACHE & QUEUE LAYER (Redis)                    │
├─────────────────────────────────────────────────────────────┤
│  Config Cache │ Route Cache │ Session Store │ Job Queue     │
├─────────────────────────────────────────────────────────────┤
│            EXTERNAL SERVICES (24+ APIs)                      │
├─────────────────────────────────────────────────────────────┤
│ Gemini (AI) │ Pexels (Photos) │ Amadeus (Flights)           │
│ Bimafy (Insurance) │ SSLCommerz (Payments) │ SendGrid       │
└─────────────────────────────────────────────────────────────┘
```

---

## 🎭 ROLE-BASED ACCESS ARCHITECTURE

```
┌────────────────────────────────────────────────────────────┐
│                     ROLE HIERARCHY                          │
└────────────────────────────────────────────────────────────┘

         Super Admin (Platform Owner)
               │
               ├──> Admin (Official Staff)
               │      │
               │      └──> Staff (Customer Support)
               │
               ├──> Agency (Service Provider)
               │      │
               │      └──> Consultant (Agency's Employee)
               │
               └──> User (End Customer)


┌────────────────────────────────────────────────────────────┐
│                   ACCESS CONTROL FLOW                       │
└────────────────────────────────────────────────────────────┘

Request → EnsureUserHasRole Middleware
           ↓
     Check user->role->slug
           ↓
     Check service_modules.allowed_roles
           ↓
     Check module_role_settings.can_access
           ↓
     Check profile completion >= required %
           ↓
     Allow or Deny (403)
```

---

## 🏢 MULTI-AGENCY ARCHITECTURE

```
┌────────────────────────────────────────────────────────────┐
│                    AGENCY ECOSYSTEM                         │
└────────────────────────────────────────────────────────────┘

Platform (Super Admin)
  │
  ├─ Agency Category 1: Travel Agency
  │   ├─ Agency A (TOAB Member)
  │   │   ├─ Services: Tourist Visa, Flight, Hotel
  │   │   ├─ Country Permissions: Thailand, Malaysia, Singapore
  │   │   └─ Consultants: 3 assigned
  │   │
  │   └─ Agency B (ATAB Member)
  │       ├─ Services: Tourist Visa, Tour Packages
  │       ├─ Country Permissions: India, Nepal, Bhutan
  │       └─ Consultants: 2 assigned
  │
  ├─ Agency Category 2: Education Consultancy
  │   ├─ Agency C
  │   │   ├─ Services: Student Visa, University Application
  │   │   ├─ Country Permissions: USA, UK, Canada, Australia
  │   │   └─ Consultants: 5 assigned
  │   │
  │   └─ Agency D
  │       ├─ Services: Student Visa, Language Test Prep
  │       ├─ Country Permissions: Germany, France, Italy
  │       └─ Consultants: 3 assigned
  │
  ├─ Agency Category 3: Recruitment Agency
  │   └─ Agency E (BOESL Registered)
  │       ├─ Services: Work Visa, Job Posting
  │       ├─ Country Permissions: Saudi Arabia, UAE, Kuwait
  │       └─ Consultants: 4 assigned
  │
  ├─ Agency Category 4: Hajj & Umrah Agency
  │   └─ Agency F (Religious Affairs Approved)
  │       ├─ Services: Hajj Packages, Umrah Packages
  │       ├─ Country Permissions: Saudi Arabia
  │       └─ Consultants: 2 assigned
  │
  └─ Agency Category 5: Other
      └─ Agency G (Medical Services)
          ├─ Services: Medical Visa, Medical Certificate
          ├─ Country Permissions: India, Thailand, Singapore
          └─ Consultants: 2 assigned
```

---

## 🔄 APPLICATION FLOW ARCHITECTURE

```
┌────────────────────────────────────────────────────────────┐
│              USER APPLICATION WORKFLOW                      │
└────────────────────────────────────────────────────────────┘

1. USER SUBMITS APPLICATION
   User → Tourist Visa Application Form
          ↓
   Validation (required fields, documents)
          ↓
   Create TouristVisa record (status: pending)
          ↓
   Upload documents (passport, photo, etc.)
          ↓
   Store in tourist_visa_documents table

2. AUTO-ASSIGNMENT TO AGENCY
   ApplicationAssignmentService::autoAssign()
          ↓
   Check destination country (Thailand)
          ↓
   Find agencies with:
     - agency_category: Travel Agency
     - service: Tourist Visa
     - country_permission: Thailand + approved
          ↓
   Assign to best agency (load balancing)
          ↓
   Create ServiceApplication record
          ↓
   Update status: agency_assigned

3. AGENCY REVIEWS APPLICATION
   Agency Dashboard → Assigned Applications
          ↓
   Review documents
          ↓
   Request additional documents (if needed)
          ↓
   OR assign to consultant
          ↓
   Update status: document_review

4. CONSULTANT PROCESSES (OPTIONAL)
   Consultant Dashboard → Assigned Clients
          ↓
   ConsultantAssignment created
          ↓
   Consultant accepts assignment
          ↓
   Consults with client
          ↓
   Prepares final application package

5. AGENCY SUBMITS TO EMBASSY
   Agency marks: processing
          ↓
   Submits to embassy/consulate
          ↓
   Tracks visa processing
          ↓
   Updates user with progress

6. FINAL OUTCOME
   Embassy approves/rejects visa
          ↓
   Agency updates status: visa_approved OR visa_rejected
          ↓
   Notification sent to user (email + SMS)
          ↓
   If approved: Schedule document collection
          ↓
   Mark assignment as completed
          ↓
   Consultant rating + performance tracking
          ↓
   Commission distribution (Platform → Agency → Consultant)
```

---

## 💰 FINANCIAL FLOW ARCHITECTURE

```
┌────────────────────────────────────────────────────────────┐
│                   WALLET & PAYMENT FLOW                     │
└────────────────────────────────────────────────────────────┘

USER REGISTRATION
   UserObserver::created()
          ↓
   WalletService::createWallet()
          ↓
   Create Wallet (balance: ৳0.00, currency: BDT)
          ↓
   ReferralService::generateReferralCode()
          ↓
   Generate unique 8-char code
          ↓
   If ?ref=CODE in URL:
     - Track referral
     - Create pending reward


REFERRAL REWARD FLOW
   New user signs up with referral code
          ↓
   Referral record created (is_completed: false)
          ↓
   Reward record created (status: pending, amount: ৳500)
          ↓
   Admin reviews in Rewards dashboard
          ↓
   Admin approves reward
          ↓
   ReferralService::approveReward()
          ↓
   WalletService::creditWallet()
          ↓
   DB::transaction {
     - Get balance_before
     - wallet->balance += amount
     - Get balance_after
     - Create WalletTransaction (audit trail)
     - Update reward (status: approved)
     - Update referral (is_completed: true)
   }
          ↓
   Notification sent to referrer


SERVICE PAYMENT FLOW
   User applies for visa (fee: ৳5,000)
          ↓
   Payment gateway (SSLCommerz / bKash)
          ↓
   Payment success webhook
          ↓
   WalletService::creditWallet() OR direct payment
          ↓
   Commission split:
     - Platform: 10% (৳500)
     - Agency: 70% (৳3,500)
     - Consultant: 20% (৳1,000)
          ↓
   WalletService::debitWallet(user, ৳5,000)
          ↓
   WalletService::creditWallet(agency, ৳3,500)
          ↓
   WalletService::creditWallet(consultant, ৳1,000)
          ↓
   All wrapped in DB::transaction()


CASHOUT FLOW
   User requests cashout (amount: ৳10,000)
          ↓
   Check wallet balance >= amount
          ↓
   Create CashoutRequest (status: pending)
          ↓
   Admin reviews in Cashout dashboard
          ↓
   Admin approves (bank transfer / bKash)
          ↓
   WalletService::debitWallet(user, ৳10,000)
          ↓
   Update CashoutRequest (status: completed, processed_by)
          ↓
   Notification sent to user
```

---

## 🔐 SECURITY ARCHITECTURE

```
┌────────────────────────────────────────────────────────────┐
│                  SECURITY LAYERS                            │
└────────────────────────────────────────────────────────────┘

AUTHENTICATION LAYER
   Laravel Sanctum (API tokens)
   Laravel Breeze (web sessions)
   Email verification required
   Password hashing (bcrypt)

AUTHORIZATION LAYER
   Role-based middleware (role:admin,user)
   Policy classes (can update, can delete)
   Model ownership checks (user_id === auth()->id())
   Service access control (module_role_settings)

INPUT VALIDATION LAYER
   Form Request validation
   CSRF tokens (automatic with Inertia)
   File upload validation (type, size, malware)
   XSS prevention (Vue escaping)
   SQL injection prevention (Eloquent ORM)

DATA PROTECTION LAYER
   Encrypted database fields (sensitive data)
   SSL/TLS (HTTPS everywhere)
   API key masking in UI
   Rate limiting (API endpoints)
   CORS configuration

AUDIT LAYER
   AuditLog model (all admin actions)
   Wallet transactions (immutable audit trail)
   Balance snapshots (before/after)
   IP tracking + user agent
   Failed login attempts logging
```

---

## 📊 DATABASE RELATIONSHIP MAP

```
┌────────────────────────────────────────────────────────────┐
│                  CORE RELATIONSHIPS                         │
└────────────────────────────────────────────────────────────┘

users
  ├─ belongsTo → roles (role_id)
  ├─ hasOne → wallets
  ├─ hasOne → user_profiles
  ├─ hasMany → user_educations
  ├─ hasMany → user_work_experiences
  ├─ hasMany → user_languages
  ├─ hasMany → user_passports
  ├─ hasMany → referrals (as referrer)
  ├─ hasMany → referrals (as referred)
  ├─ hasMany → rewards
  ├─ hasOne → agencies (as owner)
  ├─ hasOne → consultant_profiles
  └─ hasMany → tourist_visas (and 7 other visa types)

agencies
  ├─ belongsTo → users (owner_id)
  ├─ belongsTo → agency_categories
  ├─ belongsToMany → service_categories (pivot: agency_service_category)
  ├─ hasMany → agency_service_permissions (country assignments)
  ├─ hasMany → consultant_profiles
  └─ hasMany → service_applications

consultant_profiles
  ├─ belongsTo → users
  ├─ belongsTo → agencies (nullable)
  └─ hasMany → consultant_assignments

service_applications (polymorphic)
  ├─ morphTo → application (tourist_visa, student_visa, etc.)
  ├─ belongsTo → agencies
  └─ belongsTo → consultant_profiles

tourist_visas (example)
  ├─ belongsTo → users
  ├─ belongsTo → countries (destination)
  ├─ hasMany → tourist_visa_documents
  └─ morphOne → service_applications

wallets
  ├─ belongsTo → users
  └─ hasMany → wallet_transactions

wallet_transactions (immutable audit trail)
  ├─ belongsTo → wallets
  ├─ morphTo → reference (reward, payment, etc.)
  └─ Stores: balance_before, balance_after, amount, type
```

---

## 🔄 SERVICE LAYER ARCHITECTURE

```
┌────────────────────────────────────────────────────────────┐
│                  SERVICE PATTERN                            │
└────────────────────────────────────────────────────────────┘

Controller (Thin)
   ↓
   Validate request
   ↓
   Call Service method
   ↓
   Return Inertia response

Service (Fat Business Logic)
   ↓
   Check authorization
   ↓
   Wrap in DB::transaction()
   ↓
   Create/update models
   ↓
   Call other services if needed
   ↓
   Trigger events/notifications
   ↓
   Return result

Observer (Auto-actions)
   ↓
   Listen to model events (created, updated, deleted)
   ↓
   Perform side effects (create wallet, log audit, etc.)


EXAMPLE: WalletService::creditWallet()

public function creditWallet(
    Wallet $wallet,
    float $amount,
    string $description,
    ?string $referenceType = null,
    ?int $referenceId = null
) {
    return DB::transaction(function () use ($wallet, $amount, $description, $referenceType, $referenceId) {
        // Authorization check
        if (!$wallet->isActive()) {
            throw new Exception('Wallet is not active');
        }

        // Get snapshot before
        $balanceBefore = $wallet->balance;

        // Update wallet
        $wallet->balance += $amount;
        $wallet->save();

        // Get snapshot after
        $balanceAfter = $wallet->balance;

        // Create audit trail
        $transaction = WalletTransaction::create([
            'wallet_id' => $wallet->id,
            'type' => 'credit',
            'amount' => $amount,
            'balance_before' => $balanceBefore,
            'balance_after' => $balanceAfter,
            'description' => $description,
            'reference_type' => $referenceType,
            'reference_id' => $referenceId,
        ]);

        // Trigger event (optional)
        event(new WalletCredited($wallet, $amount));

        return $transaction;
    });
}
```

---

## 🌐 BANGLADESH LOCALIZATION ARCHITECTURE

```
┌────────────────────────────────────────────────────────────┐
│           LOCALIZATION STACK                                │
└────────────────────────────────────────────────────────────┘

CONFIG LAYER
   config/bangladesh.php
     - Timezone: Asia/Dhaka
     - Currency: BDT (৳)
     - Date format: DD/MM/YYYY
     - Phone format: +880 1XXX-XXXXXX
     - Divisions, districts, operators

PHP HELPER LAYER
   app/Helpers/bangladesh_helpers.php (auto-loaded)
     - format_bd_currency($amount)  → ৳5,000.00
     - format_bd_date($date)        → 18/11/2025
     - format_bd_phone($phone)      → 01712-345678
     - validate_bd_nid($nid)        → true/false
     - detect_bd_operator($phone)   → 'Grameenphone'
     - get_bd_divisions()           → ['Dhaka', ...]
     - get_popular_destinations_bd('work') → ['Saudi Arabia', ...]

VUE COMPOSABLE LAYER
   resources/js/Composables/useBangladeshFormat.js
     const { formatCurrency, formatDate, formatPhone } = useBangladeshFormat()
     formatCurrency(5000)  → "৳5,000.00"
     formatDate(new Date()) → "18/11/2025"
     formatTime(new Date()) → "9:30 AM"

USAGE PATTERN
   Backend: Always use format_bd_*() helpers
   Frontend: Always import useBangladeshFormat
   Database: Store raw values (numbers, ISO dates)
   Display: Format on output (views, emails, PDFs)

VALIDATION LAYER
   Bangladesh-specific validation rules:
     - 'nid' => ['required', 'digits_between:10,17']
     - 'phone' => ['required', 'regex:/^01[3-9][0-9]{8}$/']
     - 'division' => ['required', 'in:' . implode(',', get_bd_divisions())]
```

---

## 🚀 DEPLOYMENT ARCHITECTURE

```
┌────────────────────────────────────────────────────────────┐
│              PRODUCTION ENVIRONMENT                         │
└────────────────────────────────────────────────────────────┘

INFRASTRUCTURE
   VPS (DigitalOcean/AWS/Linode)
     - 4GB RAM (minimum), 8GB (recommended)
     - 40GB SSD storage
     - Ubuntu 22.04 LTS

WEB SERVER
   Nginx
     - Reverse proxy to Laravel
     - SSL/TLS (Let's Encrypt)
     - Gzip compression
     - Static file caching

APPLICATION SERVER
   PHP 8.2-FPM
     - Laravel 12 application
     - Opcache enabled
     - Max execution time: 60s

DATABASE SERVER
   MySQL 8.0
     - InnoDB engine
     - Automated daily backups
     - Replication (optional, for HA)

CACHE SERVER
   Redis
     - Session store
     - Config cache
     - Route cache
     - API response cache

QUEUE WORKER
   Supervisor
     - Manages queue:work processes
     - Auto-restart on failure
     - Processes: emails, SMS, notifications

CDN
   CloudFlare (free tier)
     - Static asset caching
     - DDoS protection
     - SSL termination

MONITORING
   Laravel Telescope (dev/staging only)
   Sentry/Bugsnag (error tracking)
   UptimeRobot (uptime monitoring)
   New Relic/Scout (performance monitoring)

BACKUPS
   Database: Daily full backup to S3
   Files: Weekly backup to S3
   Retention: 30 days
   Test restore: Monthly


DEPLOYMENT FLOW
   Developer pushes to GitHub
          ↓
   GitHub webhook triggers deploy script
          ↓
   Server pulls latest code
          ↓
   Composer install --no-dev --optimize-autoloader
          ↓
   npm ci && npm run build
          ↓
   php artisan migrate --force
          ↓
   php artisan config:cache
   php artisan route:cache
   php artisan view:cache
          ↓
   php artisan queue:restart
          ↓
   Zero-downtime deployment complete
```

---

## 📈 SCALING ARCHITECTURE (Future)

```
┌────────────────────────────────────────────────────────────┐
│              HORIZONTAL SCALING PLAN                        │
└────────────────────────────────────────────────────────────┘

STAGE 1: Single Server (0-5,000 users)
   Current architecture

STAGE 2: Vertical Scaling (5,000-20,000 users)
   Upgrade VPS to 8GB → 16GB RAM
   Optimize queries
   Add database indexes
   Increase Redis memory

STAGE 3: Horizontal Scaling (20,000+ users)
   Load Balancer (Nginx)
        ↓
   ┌────────────┬────────────┬────────────┐
   │  App       │  App       │  App       │
   │  Server 1  │  Server 2  │  Server 3  │
   └────────────┴────────────┴────────────┘
        ↓            ↓             ↓
   ┌────────────────────────────────────┐
   │      Shared Database (Master)      │
   └────────────────────────────────────┘
        ↓
   ┌────────────────────────────────────┐
   │    Read Replicas (if needed)       │
   └────────────────────────────────────┘

STAGE 4: Microservices (100,000+ users)
   API Gateway
        ↓
   ┌───────────────┬────────────────┬──────────────┐
   │   Visa        │   Travel       │   Education  │
   │   Service     │   Service      │   Service    │
   └───────────────┴────────────────┴──────────────┘
        ↓                ↓                 ↓
   Individual databases per service

   Benefits:
     - Independent scaling
     - Fault isolation
     - Team autonomy
     - Technology flexibility
```

---

## 🎯 KEY ARCHITECTURAL DECISIONS

### 1. Why Inertia.js (Not REST API)?
✅ **Faster development** (no API versioning, no CORS)  
✅ **Type-safe** (share types between Laravel & Vue)  
✅ **SEO-friendly** (server-side rendering possible)  
✅ **Reduced complexity** (one codebase, not two)  
✅ **Better security** (CSRF automatic, no token management)  

### 2. Why Service Layer?
✅ **Thin controllers** (easier to maintain)  
✅ **Reusable logic** (call from controllers, commands, jobs)  
✅ **Testable** (unit test services independently)  
✅ **Transaction safety** (wrap complex operations in DB::transaction())  

### 3. Why Observers?
✅ **Auto-initialization** (wallet, referral code on user creation)  
✅ **Decoupled** (don't clutter controllers)  
✅ **Consistent** (runs for all user creations, not just registration)  

### 4. Why Bangladesh Helpers?
✅ **Consistency** (same formatting everywhere)  
✅ **DRY** (don't repeat formatting logic)  
✅ **Centralized** (easy to update if format changes)  
✅ **Validated** (proper phone, NID, date validation)  

### 5. Why Multi-Agency SaaS Model?
✅ **Scalability** (unlimited agencies can join)  
✅ **Specialization** (agencies focus on their expertise)  
✅ **Competition** (agencies compete for quality)  
✅ **Revenue** (platform commission on all transactions)  
✅ **Compliance** (agencies handle their own licenses)  

---

## 📚 FURTHER READING

- `ZERO_TO_DEPLOYMENT_MASTER_PLAN.md` - Complete implementation plan
- `QUICK_REFERENCE_SUMMARY.md` - Quick start guide
- `.github/copilot-instructions.md` - AI agent guide
- Laravel 12 Documentation: https://laravel.com/docs/12.x
- Inertia.js Documentation: https://inertiajs.com
- Vue 3 Documentation: https://vuejs.org

---

**Architecture Version**: 1.0  
**Last Updated**: November 19, 2025  
**Status**: Ready for Implementation 🚀
