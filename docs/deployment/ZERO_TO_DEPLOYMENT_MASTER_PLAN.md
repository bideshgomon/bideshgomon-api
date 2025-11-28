# 🚀 BideshGomon SaaS Platform - ZERO TO DEPLOYMENT MASTER PLAN

**Project**: Multi-Agency SaaS Platform for International Travel & Migration Services  
**Architecture**: Laravel 12 + Inertia.js + Vue 3 + TailwindCSS  
**Market**: 🇧🇩 Bangladesh (Fully Localized)  
**Plan Created**: November 19, 2025  
**Estimated Timeline**: 12-16 weeks (3-4 months)  
**Team Size**: 1-3 developers

---

## 📋 EXECUTIVE SUMMARY

### Project Vision
Build a **100% error-free, production-ready SaaS platform** that connects Bangladeshi users with multiple specialized agencies for international travel, education abroad, visa processing, and employment services. The platform will support:

- **Multiple Agencies** under the main platform with category-based specialization
- **Agency-Consultant Hierarchy** where agencies can assign consultants to clients
- **Admin Super Control** with official staff, customer support agents, and full CRUD operations
- **End Users** receiving services from different agencies based on their needs
- **Complete Role Hierarchy**: Super Admin → Admin → Staff → Agency → Consultant → User

### What We're Building From
**TWO REFERENCE CODEBASES ANALYZED:**

1. **bgplatform-fresh** (Laravel 12) - Clean architecture baseline
   - 27 migrations, 21 models
   - Roles, wallet, referrals, profile system (phases 1-8 complete)
   - Bangladesh localization foundation

2. **bgproject** (Laravel 11) - Feature-rich production system
   - 200+ models, 90+ migrations
   - 9 complete service systems (visa, travel, education, etc.)
   - Agency-category system, consultant management
   - 39 services across 7 roles
   - 15,000+ lines of documentation

### Success Criteria
✅ Zero errors in production  
✅ All old project features preserved and improved  
✅ Multi-agency SaaS architecture fully functional  
✅ Role-based permissions working flawlessly  
✅ Bangladesh localization throughout  
✅ Complete documentation for maintenance  

---

## 🎯 CORE FEATURES TO IMPLEMENT

### 1. Multi-Tenant Agency System
- **Agency Categories**: Travel, Education, Recruitment, Hajj/Umrah, Medical, Legal, Other
- **Agency Profiles**: Complete business information, licenses, certifications
- **Agency-Service Mapping**: Agencies can offer specific services based on category
- **Country Permissions**: Agencies get approved for specific countries (visa processing)
- **Commission Management**: Platform-agency-consultant commission split

### 2. Role-Based Hierarchy (7 Roles)
```
Super Admin (Platform Owner)
  ↓
Admin (Official Staff)
  ↓
Staff (Customer Support)
  ↓
Agency (Service Providers)
  ↓
Consultant (Assigned by Agencies)
  ↓
User (End Customers)
```

### 3. Service Ecosystem (39+ Services)
**Visa Services (8 types)**:
- Tourist, Student, Work, Business, Medical, Family, Transit Visas
- Visa Requirements Browser

**Travel Services (6 types)**:
- Flight Booking, Hotel Booking, Travel Insurance
- Airport Transfer, Car Rental, Tour Packages

**Education Services (4 types)**:
- University Application, Course Counseling
- Language Test Prep, Scholarship Assistance

**Employment Services (5 types)**:
- Job Posting/Search, CV Builder, Interview Prep
- Skill Verification, Work Permit Processing

**Document Services (5 types)**:
- Translation, Attestation, Police Clearance
- Birth Certificate, Passport Services

**Other Services (11 types)**:
- Hajj/Umrah Packages, Relocation Services
- Legal Consultation, Medical Certificate
- Bank Account Opening, etc.

### 4. Admin Control Panel
- **User Management**: Create/edit users, assign roles, suspend accounts
- **Agency Management**: Approve agencies, assign country permissions, monitor performance
- **Consultant Management**: Approve consultants, assign to agencies, track assignments
- **Service Management**: Enable/disable services, configure pricing, set commissions
- **Content Management**: Blog posts (AI-powered), FAQs, templates
- **Financial Management**: Wallet system, referrals, cashouts, transaction history
- **Analytics Dashboard**: Real-time stats, revenue tracking, user activity
- **System Settings**: API integrations (24+ providers), localization, notifications

### 5. Bangladesh Localization (Complete)
- **Currency**: ৳ BDT (Bangladeshi Taka)
- **Date Format**: DD/MM/YYYY
- **Phone**: +880 1XXX-XXXXXX with operator detection
- **Timezone**: Asia/Dhaka (BST +06:00)
- **Geographic Data**: 8 divisions, 64 districts
- **Regulatory Bodies**: ATAB, TOAB, BOESL, Ministry of Religious Affairs
- **Weekend**: Friday-Saturday
- **NID Validation**: 10/17 digits
- **Popular Destinations**: By purpose (work, education, tourism)

### 6. Integration Systems
- **Payment Gateways**: SSLCommerz, bKash, Nagad, Rocket
- **AI Services**: Google Gemini (blog writing, chatbot)
- **Stock Photos**: Pexels (200 req/hour free)
- **Travel APIs**: Amadeus (flights), Bimafy (insurance)
- **SMS/Email**: Twilio, BulkSMS BD, SendGrid, Mailchimp
- **Maps**: Google Maps API
- **Currency**: Fixer.io, ExchangeRate-API

---

## 📊 DATABASE ARCHITECTURE

### Core Tables (From Both Projects)
**Total Migrations**: 90+ tables

#### Authentication & Roles
```
users (auth, role_id, referral_code, wallet integration)
roles (7 roles: super_admin, admin, staff, agency, consultant, user, custom)
password_reset_tokens
sessions
```

#### Agency System
```
agencies (owner_id, category_id, business details, licenses)
agency_categories (5 defaults + custom)
agency_service_category (pivot: agencies ↔ services)
agency_service_permissions (country-specific approvals)
agency_memberships (ATAB/TOAB/etc)
consultant_profiles (user_id, agency_id, specializations)
consultant_assignments (consultant ↔ client assignments)
consultant_availability (working hours, days off)
```

#### Service Management
```
service_categories (15 types: visa, travel, education, etc.)
service_modules (39 services with metadata)
service_applications (generic applications table)
module_role_settings (permissions per role)
module_profile_requirements (profile completion gates)
```

#### User Profiles (9 Tables)
```
user_profiles (basic info, address, demographics)
user_educations (degrees, institutions)
user_work_experiences (employment history)
user_languages (IELTS, TOEFL scores)
user_passports (multiple passports, is_primary flag)
user_visa_history (rejections tracking)
user_travel_history (previous travels)
user_family_members (spouse, children)
user_financial_information (bank statements, income)
user_security_information (criminal records, medical)
```

#### Visa Applications (8 Types)
```
tourist_visas + tourist_visa_documents
student_visa_applications
work_visa_applications
business_visas
medical_visas
family_visas
transit_visas
visa_requirements (per country + visa type)
country_visa_requirements (admin configurable)
```

#### Travel Services
```
flight_bookings + flight_booking_requests
hotel_bookings + hotel_booking_requests
travel_insurance + travel_insurance_quotes
airport_transfers + airport_transfer_requests
car_rentals + car_rental_requests
tour_packages + tour_package_requests
hajj_omrah_packages + hajj_omrah_bookings
```

#### Education Services
```
universities (18 seeded)
courses (70+ seeded)
course_selections
university_applications
language_test_prep
scholarship_assistance
```

#### Employment Services
```
job_categories (10+)
job_postings (60+ fields per job!)
job_applications
cv_templates + cv_orders
interview_preparation
skill_verifications
```

#### Document Services
```
document_types (20+ types)
document_translations
document_attestations
police_clearance
birth_certificates
passport_services
```

#### Financial System
```
wallets (user_id, balance, currency)
wallet_transactions (audit trail with balance snapshots)
referrals (referrer_id, referred_id)
rewards (approval workflow)
referral_campaigns
cashout_requests
payments
financial_transactions
```

#### Blog & Content
```
blog_categories (5 seeded)
blog_posts (10 seeded with AI)
blog_tags (20 seeded)
blog_post_tag (pivot)
chatbot_faqs + chatbot_conversations
email_templates
```

#### Analytics & Logging
```
audit_logs (admin actions)
user_activity_logs
analytics_snapshots
page_audit_logs
search_logs
feedback + feedback_activity_logs
```

#### Geographic Data
```
countries (enriched with visa requirements)
cities
states
airports
```

---

## 🏗️ TECHNICAL ARCHITECTURE

### Backend Stack
- **Framework**: Laravel 12.x
- **PHP**: 8.2+
- **Database**: MySQL 8.0 (dev: SQLite)
- **Cache**: Redis (sessions, API responses)
- **Queue**: Redis (emails, SMS, notifications)
- **Storage**: Laravel Storage (local/S3)

### Frontend Stack
- **Framework**: Vue 3 (Composition API)
- **Meta-Framework**: Inertia.js 2.0 (SPA without API)
- **CSS**: TailwindCSS 3.x
- **Icons**: Heroicons
- **Build Tool**: Vite 7.x
- **State Management**: Vue Composables (no Vuex/Pinia needed)

### Service Layer
- **Pattern**: Thin controllers, fat services
- **Services**: WalletService, ReferralService, AIBlogService, ApplicationAssignmentService, etc.
- **Observers**: UserObserver (auto-create wallet, referral code)
- **Events**: User actions trigger notifications, audit logs

### Security
- **Authentication**: Laravel Sanctum (API tokens) + Breeze (web sessions)
- **Authorization**: Role-based middleware + model policies
- **CSRF Protection**: Automatic with Inertia forms
- **File Upload Validation**: Type, size, malware scanning
- **API Rate Limiting**: Per user/IP
- **SQL Injection Prevention**: Eloquent ORM only

### Performance
- **N+1 Prevention**: Always eager load relationships
- **Database Indexing**: Foreign keys, frequently searched columns
- **Query Optimization**: Paginate large datasets (20 items default)
- **Asset Optimization**: Vite bundling, lazy loading
- **Cache Strategy**: Config, routes, API responses
- **CDN**: Static assets (production)

---

## 🗓️ PHASE-BY-PHASE IMPLEMENTATION PLAN

### **PHASE 0: Project Setup & Foundation** (Week 1)
**Duration**: 5-7 days  
**Goal**: Set up clean Laravel 12 project with all essential packages

#### Tasks:
- [ ] Create fresh Laravel 12 project
- [ ] Install packages: Inertia.js, Vue 3, TailwindCSS, Sanctum, Breeze
- [ ] Configure Vite, Ziggy (route helper)
- [ ] Set up database (MySQL + SQLite for testing)
- [ ] Configure Redis (cache + queues)
- [ ] Set up `.env` files (dev, staging, prod)
- [ ] Create Git repository with proper `.gitignore`
- [ ] Set up directory structure:
  - `app/Services/`
  - `app/Observers/`
  - `app/Helpers/`
  - `resources/js/Composables/`
  - `resources/js/Components/`
  - `resources/js/Layouts/`
- [ ] Configure Bangladesh timezone, locale
- [ ] Create `config/bangladesh.php` (copy from bgproject)
- [ ] Create `app/Helpers/bangladesh_helpers.php` (14 functions)
- [ ] Create `resources/js/Composables/useBangladeshFormat.js`
- [ ] Configure composer autoload for helpers
- [ ] Set up Vue layouts: GuestLayout, AuthenticatedLayout, AdminLayout, AgencyLayout, ConsultantLayout
- [ ] Create base components: Button, Input, Select, Modal, Badge, Card, Table
- [ ] Write `.github/copilot-instructions.md`

**Deliverables**:
- ✅ Working Laravel 12 app with Vite dev server
- ✅ Bangladesh localization helpers working
- ✅ Vue layouts and base components ready
- ✅ Git repo initialized with first commit

---

### **PHASE 1: Authentication & Roles System** (Week 1-2)
**Duration**: 5-7 days  
**Goal**: Complete role-based authentication with 7 roles

#### Tasks:
- [ ] Create migrations:
  - `create_roles_table` (7 roles)
  - `add_role_id_to_users_table`
- [ ] Create `Role` model with relationships
- [ ] Update `User` model:
  - Add role methods: `isAdmin()`, `isAgency()`, `isConsultant()`, `isUser()`
  - Add `hasRole($slug)` method
  - Add relationship: `belongsTo(Role::class)`
- [ ] Create `EnsureUserHasRole` middleware
- [ ] Register middleware in `bootstrap/app.php` as 'role'
- [ ] Create `RolesSeeder`:
  ```php
  super_admin, admin, staff, agency, consultant, user, custom
  ```
- [ ] Create `UserSeeder` (one user per role for testing)
- [ ] Update registration to default to 'user' role
- [ ] Create login/register pages (Inertia)
- [ ] Test role-based access control
- [ ] Create admin middleware group in routes

**Deliverables**:
- ✅ 7 roles seeded
- ✅ Role middleware working
- ✅ Test users created (one per role)
- ✅ Login/register functional

---

### **PHASE 2: User Profile System (9 Tables)** (Week 2)
**Duration**: 5-7 days  
**Goal**: Complete comprehensive user profile system

#### Tasks:
- [ ] Create migrations (9 tables):
  - `user_profiles`
  - `user_educations`
  - `user_work_experiences`
  - `user_languages`
  - `user_passports`
  - `user_visa_history`
  - `user_travel_history`
  - `user_family_members`
  - `user_financial_information`
  - `user_security_information`
- [ ] Create models with relationships:
  - User `hasOne` UserProfile
  - User `hasMany` UserEducation, UserWorkExperience, etc.
- [ ] Create controllers:
  - `ProfileController` (main profile)
  - `UserEducationController`
  - `UserWorkExperienceController`
  - `UserLanguageController`
  - `PassportController`
  - (etc. for all 9 tables)
- [ ] Create Vue pages:
  - `Profile/Show.vue` (dashboard)
  - `Profile/Edit.vue` (edit main profile)
  - `Profile/Education/Index.vue` (list + inline CRUD)
  - (similar for all sections)
- [ ] Create API routes for CRUD operations
- [ ] Implement profile completion calculation:
  ```php
  // User model
  public function calculateProfileCompletion() {
      $score = 0;
      if ($this->name) $score += 10;
      if ($this->email) $score += 10;
      if ($this->userProfile) $score += 20;
      if ($this->educations()->count() > 0) $score += 15;
      if ($this->workExperiences()->count() > 0) $score += 15;
      if ($this->passports()->count() > 0) $score += 10;
      if ($this->languages()->count() > 0) $score += 10;
      if ($this->familyMembers()->count() > 0) $score += 5;
      if ($this->financialInfo) $score += 5;
      return min($score, 100);
  }
  ```
- [ ] Create profile completion middleware (gate features at 80%, 90%)
- [ ] Test all CRUD operations

**Deliverables**:
- ✅ 9-table profile system fully functional
- ✅ Profile completion calculation working
- ✅ All Vue pages responsive and working

---

### **PHASE 3: Wallet & Referral System** (Week 3)
**Duration**: 5-7 days  
**Goal**: Complete financial system with referrals

#### Tasks:
- [ ] Create migrations:
  - `create_wallets_table` (user_id, balance, currency, status)
  - `create_wallet_transactions_table` (balance snapshots, reference_type/id)
  - `create_referrals_table` (referrer_id, referred_id, is_completed)
  - `create_rewards_table` (user_id, amount, status, approved_by)
  - `create_referral_campaigns_table`
  - `create_cashout_requests_table`
  - `add_referral_fields_to_users_table` (referral_code, referred_by)
- [ ] Create models with relationships
- [ ] Create `WalletService`:
  - `createWallet(User $user)`
  - `creditWallet($wallet, $amount, $description, $reference_type, $reference_id)`
  - `debitWallet(...)` with balance check
  - Wrap all in `DB::transaction()`
- [ ] Create `ReferralService`:
  - `generateReferralCode(User $user)` (8-char unique)
  - `trackReferral($referralCode, $newUser)`
  - `approveReward(Reward $reward, $adminId)`
  - `calculateReward($type)` (based on settings)
- [ ] Create `UserObserver`:
  - On `created`: auto-create wallet + generate referral code
  - Register in `AppServiceProvider::boot()`
- [ ] Create controllers:
  - `WalletController` (user: index, transactions)
  - `ReferralController` (user: dashboard, referrals, rewards)
  - `Admin\RewardController` (admin: approve/reject)
  - `CashoutController` (user: request cashout, admin: approve)
- [ ] Create Vue pages:
  - `Wallet/Index.vue` (balance, quick stats, recent transactions)
  - `Wallet/Transactions.vue` (full history with filters)
  - `Referral/Index.vue` (referral code, stats, recent referrals/rewards)
  - `Admin/Rewards/Index.vue` (approve/reject pending rewards)
  - `Admin/Cashouts/Index.vue` (process cashout requests)
- [ ] Update registration to handle `?ref=CODE` parameter
- [ ] Create referral settings table (signup bonus, amounts)
- [ ] Test complete flow: signup with ref → pending reward → admin approve → wallet credited

**Deliverables**:
- ✅ Wallet system with transaction audit trail
- ✅ Referral system with approval workflow
- ✅ UserObserver auto-creating wallet + referral code
- ✅ Cashout requests functional

---

### **PHASE 4: Agency System (Core SaaS)** (Week 4)
**Duration**: 7-10 days  
**Goal**: Multi-agency system with categories

#### Tasks:
- [ ] Create migrations:
  - `create_agency_categories_table` (5 defaults + Other)
  - `create_agencies_table` (owner_id, category_id, business_name, licenses, etc.)
  - `create_agency_memberships_table` (ATAB, TOAB, BOESL memberships)
  - `create_agency_service_permissions_table` (country-specific approvals)
  - `create_service_categories_table` (15 services + Other)
  - `create_agency_service_category_table` (pivot: agency ↔ services)
- [ ] Create models:
  - `AgencyCategory` (Travel, Education, Recruitment, Hajj/Umrah, Other)
  - `Agency` with relationships:
    - `belongsTo(User::class, 'owner_id')`
    - `belongsTo(AgencyCategory::class)`
    - `belongsToMany(ServiceCategory::class)`
    - `hasMany(AgencyServicePermission::class)` (countries)
    - `hasMany(ConsultantProfile::class)`
  - `ServiceCategory` (Visa, Travel, Education, Employment, Documents)
  - `AgencyServicePermission` (country assignments with approval workflow)
- [ ] Create seeders:
  - `AgencyCategorySeeder` (5 categories)
  - `ServiceCategorySeeder` (15 services with commission rates)
- [ ] Create controllers:
  - `Admin\AgencyCategoryController` (CRUD)
  - `Admin\ServiceCategoryController` (CRUD)
  - `Admin\AgencyController` (list, approve, edit, suspend)
  - `Admin\CountryAssignmentController` (assign countries, approve, revoke)
  - `Agency\ProfileController` (agency edits their profile)
  - `Agency\DashboardController`
- [ ] Create Vue pages:
  - `Admin/AgencyCategories/Index.vue`
  - `Admin/Agencies/Index.vue` (with filters, search)
  - `Admin/Agencies/Create.vue` (manual agency creation)
  - `Admin/Agencies/Edit.vue`
  - `Admin/Agencies/CountryAssignments.vue` (assign/approve countries)
  - `Agency/Profile/Edit.vue` (agency edits own profile)
  - `Agency/Dashboard.vue`
- [ ] Update user registration:
  - If role = 'agency', show agency fields (category, business_name)
  - Auto-create agency profile on user creation
- [ ] Create `AgencyService`:
  - `assignCountry($agency, $countryId, $licenseFile)`
  - `approveCountryAssignment($permissionId, $adminId)`
  - `revokeCountryAccess($permissionId, $reason)`
- [ ] Test agency approval workflow

**Deliverables**:
- ✅ 5 agency categories seeded
- ✅ 15 service categories seeded
- ✅ Agency CRUD functional
- ✅ Country assignment approval workflow
- ✅ Agency dashboard showing assigned services

---

### **PHASE 5: Consultant System** (Week 5)
**Duration**: 5-7 days  
**Goal**: Consultant management and assignment

#### Tasks:
- [ ] Create migrations:
  - `create_consultant_profiles_table` (user_id, agency_id, title, bio, specializations, status)
  - `create_consultant_assignments_table` (consultant_id, client_id, service_type, assigned_by, status)
  - `create_consultant_availability_table` (working hours, days off)
- [ ] Create models:
  - `ConsultantProfile` with relationships:
    - `belongsTo(User::class)`
    - `belongsTo(Agency::class)` (nullable, consultant can be independent)
    - `hasMany(ConsultantAssignment::class)`
  - `ConsultantAssignment` with status transitions
- [ ] Update `User` model:
  - `promoteToConsultant($agencyId = null)` method
  - `demoteFromConsultant()` method
  - `isConsultant()` method
  - `consultantProfile` relationship
- [ ] Create controllers:
  - `Admin\ConsultantController` (list, approve, assign to agency)
  - `Agency\ConsultantController` (agency assigns consultants to clients)
  - `Consultant\DashboardController`
  - `Consultant\AssignmentController` (view/manage assignments)
  - `Consultant\ProfileController` (edit own profile)
- [ ] Create Vue pages:
  - `Admin/Consultants/Index.vue` (list all with filters)
  - `Admin/Consultants/Edit.vue` (edit consultant profile)
  - `Agency/Consultants/Index.vue` (agency's consultants)
  - `Agency/Consultants/Assign.vue` (assign consultant to client)
  - `Consultant/Dashboard.vue`
  - `Consultant/Assignments/Index.vue` (list assignments)
  - `Consultant/Assignments/Show.vue` (assignment details + actions)
  - `Consultant/Profile/Edit.vue`
- [ ] Create `ConsultantService`:
  - `assignClientToConsultant($consultantId, $clientId, $serviceType, $assignedBy)`
  - `acceptAssignment($assignmentId)`
  - `completeAssignment($assignmentId)`
  - `reassignConsultant($assignmentId, $newConsultantId)`
- [ ] Test complete flow: admin assigns consultant to agency → agency assigns client to consultant → consultant accepts → completes

**Deliverables**:
- ✅ Consultant profile system
- ✅ Agency-consultant relationship
- ✅ Client assignment workflow
- ✅ Consultant dashboard functional

---

### **PHASE 6: Service Modules System** (Week 6)
**Duration**: 7-10 days  
**Goal**: Generic service application system

#### Tasks:
- [ ] Create migrations:
  - `create_service_modules_table` (39 services with metadata)
  - `create_service_applications_table` (generic applications)
  - `create_module_role_settings_table` (permissions per role)
  - `create_module_profile_requirements_table` (profile completion gates)
- [ ] Create models:
  - `ServiceModule` with methods:
    - `isEnabledForRole($roleSlug)`
    - `requiresProfileCompletion()`
    - `getCommissionRate($agencyId)`
  - `ServiceApplication` (polymorphic to specific visa/service models)
- [ ] Create `ServiceModuleSeeder`:
  ```php
  39 services:
  - Visa (8): Tourist, Student, Work, Business, Medical, Family, Transit, Requirements
  - Travel (6): Flight, Hotel, Insurance, Airport Transfer, Car Rental, Tour Packages
  - Education (4): University, Course Counseling, Language Prep, Scholarship
  - Employment (5): Job Posting, Job Search, CV Builder, Interview Prep, Skill Verify
  - Documents (5): Translation, Attestation, Police Clearance, Birth Cert, Passport
  - Other (11): Hajj/Umrah, Relocation, Legal, Medical Cert, Bank Account, etc.
  ```
- [ ] Create controllers:
  - `Admin\ServiceModuleController` (enable/disable, configure)
  - `Admin\ModuleSettingsController` (set permissions per role)
- [ ] Create Vue pages:
  - `Admin/ServiceModules/Index.vue` (grid of 39 services)
  - `Admin/ServiceModules/Edit.vue` (configure module)
  - `Admin/ServiceModules/Permissions.vue` (set role access)
- [ ] Create service middleware:
  - `EnsureServiceEnabled` (check if service active)
  - `EnsureServiceAccess` (check role has access)
  - `EnsureProfileComplete` (check profile completion)
- [ ] Apply middleware to all service routes
- [ ] Test service enabling/disabling cascades correctly

**Deliverables**:
- ✅ 39 services seeded in service_modules
- ✅ Role-based access control per service
- ✅ Service enable/disable functionality
- ✅ Middleware protecting service routes

---

### **PHASE 7: Visa Applications (8 Types)** (Week 7-8)
**Duration**: 10-14 days  
**Goal**: Complete visa application system

#### Tasks:
- [ ] Create migrations (8 visa types):
  - `create_tourist_visas_table` + `create_tourist_visa_documents_table`
  - `create_student_visa_applications_table`
  - `create_work_visa_applications_table`
  - `create_business_visas_table`
  - `create_medical_visas_table`
  - `create_family_visas_table`
  - `create_transit_visas_table`
  - `create_visa_requirements_table` (per country + type)
  - `create_country_visa_requirements_table` (admin configurable)
- [ ] Create models for each visa type with:
  - Status workflow (pending → document_review → agency_assigned → processing → visa_approved/rejected)
  - Document uploads (polymorphic)
  - Agency assignment
- [ ] Create controllers (3 per visa type = 24 controllers):
  - `Profile\{VisaType}Controller` (user creates application)
  - `Admin\{VisaType}Controller` (admin views, assigns to agency)
  - `Agency\{VisaType}Controller` (agency processes application)
- [ ] Create Vue pages (5 per visa = 40 pages):
  - `Profile/Visa/{Type}/Create.vue` (application form)
  - `Profile/Visa/{Type}/Index.vue` (user's applications)
  - `Profile/Visa/{Type}/Show.vue` (application details)
  - `Admin/Visa/{Type}/Index.vue` (all applications)
  - `Agency/Visa/{Type}/Index.vue` (agency's assigned applications)
- [ ] Create `ApplicationAssignmentService`:
  - `autoAssignToAgency($application)` (based on country + agency permissions)
  - `manualAssignToAgency($applicationId, $agencyId, $adminId)`
  - `reassignApplication($applicationId, $newAgencyId)`
- [ ] Create `VisaRequirementService`:
  - `getRequirements($countryId, $visaType)`
  - `validateDocuments($application)`
- [ ] Create visa requirement browser (public):
  - `Public/VisaRequirements/Index.vue` (filter by country + type)
  - `Public/VisaRequirements/Show.vue` (detailed requirements)
- [ ] Seed sample data:
  - 25 visa requirement sets (10 countries × 2-3 visa types each)
  - 10 test applications per visa type (80 total)
- [ ] Test complete flow:
  - User creates tourist visa application
  - System auto-assigns to agency with Thailand permission
  - Agency reviews and processes
  - Admin monitors and can reassign

**Deliverables**:
- ✅ 8 visa types fully functional
- ✅ Auto-assignment working
- ✅ Public visa requirement browser
- ✅ 80 test applications created

---

### **PHASE 8: Travel Services (6 Types)** (Week 9)
**Duration**: 7-10 days  
**Goal**: Flight, hotel, insurance, transfers, rentals, packages

#### Tasks:
- [ ] Copy complete systems from bgproject:
  - Flight Booking (Amadeus API integration)
  - Hotel Booking
  - Travel Insurance (Bimafy API integration)
  - Airport Transfer
  - Car Rental
  - Tour Packages + Hajj/Umrah
- [ ] Create migrations (already exist in bgproject):
  - `flight_bookings`, `flight_booking_requests`
  - `hotel_bookings`, `hotel_booking_requests`
  - `travel_insurance`, `travel_insurance_quotes`
  - `airport_transfers`, `airport_transfer_requests`
  - `car_rentals`, `car_rental_requests`
  - `tour_packages`, `tour_package_requests`
  - `hajj_omrah_packages`, `hajj_omrah_bookings`
- [ ] Copy models, controllers, services from bgproject
- [ ] Copy Vue pages (42 pages total)
- [ ] Configure API integrations:
  - Amadeus (flight search, pricing)
  - Bimafy (travel insurance quotes)
- [ ] Seed sample data (use bgproject seeders)
- [ ] Test each service end-to-end

**Deliverables**:
- ✅ 6 travel services fully functional
- ✅ API integrations working (Amadeus, Bimafy)
- ✅ Sample bookings created

---

### **PHASE 9: Education Services (4 Types)** (Week 10)
**Duration**: 5-7 days  
**Goal**: University applications, counseling, language prep, scholarships

#### Tasks:
- [ ] Copy from bgproject:
  - University Application
  - Course Counseling
  - Language Test Prep
  - Scholarship Assistance
- [ ] Create migrations:
  - `universities` (18 seeded: Harvard, Oxford, MIT, etc.)
  - `courses` (70+ seeded: CS, Business, Engineering, etc.)
  - `university_applications`
  - `course_selections`
  - `language_test_prep`
  - `scholarship_assistance`
- [ ] Copy models, controllers, Vue pages
- [ ] Seed universities and courses (use bgproject seed scripts)
- [ ] Test application flow

**Deliverables**:
- ✅ 18 universities + 70 courses seeded
- ✅ University application workflow functional
- ✅ Consultant can review education applications

---

### **PHASE 10: Employment Services (5 Types)** (Week 10)
**Duration**: 5-7 days  
**Goal**: Job postings, CV builder, interview prep, skill verification

#### Tasks:
- [ ] Copy from bgproject:
  - Job Posting + Job Search
  - CV Builder + CV Templates
  - Interview Preparation
  - Skill Verification
- [ ] Create migrations:
  - `job_categories` (10+)
  - `job_postings` (60+ columns!)
  - `job_applications`
  - `cv_templates` + `cv_orders`
  - `interview_preparation`
  - `skill_verifications`
- [ ] Copy controllers and Vue pages
- [ ] Seed job categories and sample jobs
- [ ] Test job application flow

**Deliverables**:
- ✅ Job marketplace functional
- ✅ CV builder with templates
- ✅ 10+ job categories + sample jobs

---

### **PHASE 11: Document Services (5 Types)** (Week 11)
**Duration**: 5-7 days  
**Goal**: Translation, attestation, police clearance, certificates

#### Tasks:
- [ ] Copy from bgproject:
  - Document Translation
  - Document Attestation
  - Police Clearance
  - Birth Certificate
  - Passport Services
- [ ] Create migrations:
  - `document_types` (20+ types)
  - `document_translations`
  - `document_attestations`
  - `police_clearance`
  - `birth_certificates`
  - `passport_services`
- [ ] Copy controllers, models, Vue pages
- [ ] Seed document types
- [ ] Test document request flow

**Deliverables**:
- ✅ 5 document services functional
- ✅ 20+ document types seeded

---

### **PHASE 12: Admin Dashboard & Analytics** (Week 11-12)
**Duration**: 7-10 days  
**Goal**: Complete admin control panel

#### Tasks:
- [ ] Create `DashboardStatsService`:
  - Total users (by role)
  - Total agencies (by category, by status)
  - Total applications (by service, by status)
  - Revenue (by service, by month)
  - Active consultants
  - Pending approvals (agencies, consultants, country assignments, rewards)
- [ ] Create controllers:
  - `Admin\DashboardController` (main stats)
  - `Admin\AnalyticsController` (detailed reports)
  - `Admin\UserController` (CRUD users)
  - `Admin\SettingsController` (system settings)
  - `Admin\AuditLogController` (view all actions)
- [ ] Create Vue pages:
  - `Admin/Dashboard.vue` (12 stat cards, 4 charts, recent activity)
  - `Admin/Users/Index.vue` (DataTable with filters)
  - `Admin/Users/Create.vue` (create user, assign role/agency)
  - `Admin/Users/Edit.vue`
  - `Admin/Analytics/Index.vue` (charts, date filters)
  - `Admin/Settings/Index.vue` (tabs for different settings)
  - `Admin/AuditLogs/Index.vue` (searchable log viewer)
- [ ] Create staff/support agent roles:
  - Staff can access most admin routes
  - Support agents limited to user management + viewing applications
- [ ] Create impersonation feature:
  - Admin can impersonate any user
  - `ImpersonationController` + routes
  - Banner showing "You are logged in as [user]"
- [ ] Create audit logging:
  - Log all admin actions (create, update, delete)
  - Log all status changes (application approved, etc.)
  - Model: `AuditLog` with user_id, action, model_type, model_id, changes (JSON)
- [ ] Test all admin CRUD operations

**Deliverables**:
- ✅ Complete admin dashboard with real-time stats
- ✅ User management (create, edit, suspend, delete)
- ✅ Impersonation working
- ✅ Audit logging for all actions

---

### **PHASE 13: AI & Integrations** (Week 12-13)
**Duration**: 7-10 days  
**Goal**: AI blog, chatbot, stock photos, SMS/email

#### Tasks:
- [ ] **AI Blog System**:
  - Copy `AIBlogService` from bgproject (409 lines)
  - Integrate Google Gemini API (60 req/min free)
  - Create blog CRUD (categories, posts, tags)
  - AI features: generate posts, SEO, social posts
  - Copy Vue pages: Blog Create/Edit with AI modals
- [ ] **Stock Photos (Pexels)**:
  - Copy `PexelsService` (330 lines)
  - Free 200 req/hour
  - Integrate into blog post creation
  - Photo search modal with quick shortcuts
- [ ] **AI Chatbot**:
  - Copy chatbot system from bgproject
  - Google Gemini + FAQ knowledge base
  - Conversation learning
  - Public + authenticated modes
- [ ] **API Integrations**:
  - Copy `config/external-apis.php` (24 providers)
  - Create `ApiSettingsController`
  - Admin UI to configure API keys
  - Test connections for each API
- [ ] **SMS/Email Notifications**:
  - Integrate Twilio (SMS) + BulkSMS BD
  - Integrate SendGrid (email) + Mailchimp
  - Create `SmsNotificationService`
  - Create `EmailNotificationService`
  - Templates for: application received, status change, assignment, etc.
- [ ] **Payment Gateways**:
  - SSLCommerz integration
  - bKash, Nagad, Rocket (mobile banking)
  - Create `PaymentGatewayService`
  - Webhooks for payment confirmation
- [ ] Test all integrations end-to-end

**Deliverables**:
- ✅ AI blog with Gemini + Pexels
- ✅ AI chatbot functional
- ✅ 24 API providers configured
- ✅ SMS/Email notifications working
- ✅ Payment gateways integrated

---

### **PHASE 14: Testing & Bug Fixes** (Week 13-14)
**Duration**: 10-14 days  
**Goal**: Comprehensive testing, zero errors

#### Tasks:
- [ ] **Unit Testing**:
  - Test all services (WalletService, ReferralService, etc.)
  - Test models (relationships, methods)
  - Test helpers (Bangladesh formatting)
  - Target: 80%+ code coverage
- [ ] **Feature Testing**:
  - Test all CRUD operations
  - Test authentication + authorization
  - Test role-based access
  - Test file uploads
  - Test API integrations
- [ ] **Integration Testing**:
  - Test complete user flows:
    - User registers → profile → visa application → agency processes → approved
    - Agency registers → admin approves → country assigned → receives applications
    - Consultant assigned → accepts assignment → completes
  - Test wallet: credit → debit → cashout
  - Test referrals: signup → reward → approve → wallet credit
- [ ] **Manual Testing**:
  - Test on different devices (desktop, tablet, mobile)
  - Test on different browsers (Chrome, Firefox, Safari, Edge)
  - Test with different roles
  - Test edge cases (negative balances, invalid data, etc.)
- [ ] **Performance Testing**:
  - Test with 1000+ users
  - Test with 10,000+ applications
  - Check N+1 queries (Laravel Debugbar)
  - Optimize slow queries
- [ ] **Security Testing**:
  - SQL injection attempts
  - XSS attempts
  - CSRF bypass attempts
  - Unauthorized access attempts
  - File upload exploits
- [ ] **Bug Tracking**:
  - Create GitHub Issues for all bugs
  - Prioritize: Critical → High → Medium → Low
  - Fix all critical and high bugs
- [ ] **Code Review**:
  - Review all controllers (thin controllers?)
  - Review all services (business logic separated?)
  - Review all models (relationships correct?)
  - Review all Vue components (reusable?)
- [ ] **Documentation Review**:
  - Update `.github/copilot-instructions.md`
  - Create `DEPLOYMENT.md`
  - Create `TROUBLESHOOTING.md`
  - Create `API_DOCUMENTATION.md`

**Deliverables**:
- ✅ All critical bugs fixed
- ✅ 80%+ test coverage
- ✅ Performance optimized
- ✅ Security hardened
- ✅ Documentation complete

---

### **PHASE 15: Deployment Preparation** (Week 15)
**Duration**: 5-7 days  
**Goal**: Production server setup

#### Tasks:
- [ ] **Server Setup**:
  - VPS with Ubuntu 22.04 LTS (DigitalOcean/AWS/Linode)
  - 4GB RAM minimum (8GB recommended)
  - Install: PHP 8.2, MySQL 8.0, Redis, Nginx
  - Configure firewall (UFW)
  - Set up SSL certificate (Let's Encrypt)
- [ ] **Database Setup**:
  - Create production database
  - Run all migrations
  - Seed initial data (roles, categories, services)
  - Set up daily backups (automated)
- [ ] **Environment Configuration**:
  - Copy `.env.example` → `.env`
  - Set `APP_ENV=production`
  - Set `APP_DEBUG=false`
  - Generate `APP_KEY`
  - Configure database credentials
  - Configure Redis
  - Configure all API keys (24 providers)
  - Configure mail (SendGrid)
  - Configure SMS (Twilio/BulkSMS BD)
  - Configure storage (S3 or local)
- [ ] **Code Deployment**:
  - Set up Git repository on server
  - Create deployment script:
    ```bash
    git pull origin main
    composer install --no-dev --optimize-autoloader
    npm ci && npm run build
    php artisan migrate --force
    php artisan config:cache
    php artisan route:cache
    php artisan view:cache
    php artisan storage:link
    php artisan queue:restart
    ```
- [ ] **Queue Setup**:
  - Configure Supervisor (queue worker)
  - Create queue worker config
  - Test queue processing
- [ ] **Cron Jobs**:
  - Laravel scheduler: `* * * * * php artisan schedule:run`
  - Backup database: Daily at 2 AM
  - Clean old logs: Weekly
- [ ] **Monitoring**:
  - Set up Laravel Telescope (dev only)
  - Set up error tracking (Sentry/Bugsnag)
  - Set up uptime monitoring (Pingdom/UptimeRobot)
  - Set up performance monitoring (New Relic/Scout)
- [ ] **CDN Setup**:
  - Configure CloudFlare (free tier)
  - Cache static assets
  - Enable DDoS protection
- [ ] **Final Checks**:
  - Test all services on production
  - Test all API integrations
  - Test email/SMS sending
  - Test payment gateways
  - Test file uploads
  - Load test (simulate 100 concurrent users)

**Deliverables**:
- ✅ Production server configured
- ✅ Code deployed successfully
- ✅ All services working on production
- ✅ Monitoring and backups set up

---

### **PHASE 16: Soft Launch & Iteration** (Week 16)
**Duration**: 7 days  
**Goal**: Soft launch with limited users, gather feedback

#### Tasks:
- [ ] **Soft Launch**:
  - Invite 50-100 beta testers
  - 10 agencies (2 per category)
  - 5 consultants
  - 20-50 regular users
- [ ] **Feedback Collection**:
  - In-app feedback form
  - User interviews (5-10 users)
  - Monitor user behavior (analytics)
  - Track bugs/issues
- [ ] **Bug Fixes**:
  - Fix all critical bugs reported
  - Fix high-priority bugs
  - Document medium/low bugs for later
- [ ] **Performance Tuning**:
  - Monitor slow queries
  - Optimize heavy pages
  - Reduce API calls
- [ ] **Feature Adjustments**:
  - Adjust UI based on feedback
  - Simplify confusing workflows
  - Add missing features (if small)
- [ ] **Documentation Updates**:
  - Create user guides (PDF/video)
  - Create agency onboarding guide
  - Create consultant guide
  - Update FAQ
- [ ] **Marketing Preparation**:
  - Create landing page
  - Prepare social media posts
  - Prepare email campaigns
  - Prepare press release

**Deliverables**:
- ✅ Soft launch completed
- ✅ Feedback collected and analyzed
- ✅ Critical bugs fixed
- ✅ User guides created

---

### **PHASE 17: Full Launch** (After Week 16)
**Duration**: Ongoing  
**Goal**: Public launch, scaling, continuous improvement

#### Tasks:
- [ ] **Public Launch**:
  - Announce on social media
  - Send email campaigns
  - Publish blog post
  - Submit to directories
- [ ] **Onboarding Campaigns**:
  - Onboard 100+ agencies (month 1)
  - Onboard 50+ consultants (month 1)
  - Acquire 1000+ users (month 1)
- [ ] **Scaling**:
  - Monitor server load
  - Scale vertically (upgrade server) if needed
  - Scale horizontally (load balancer + multiple servers) if needed
  - Optimize database (indexing, query optimization)
- [ ] **Continuous Improvement**:
  - Weekly bug fixes
  - Monthly feature releases
  - Quarterly major updates
- [ ] **Support System**:
  - Hire customer support agents
  - Create support ticket system
  - Knowledge base
  - Live chat support

**Deliverables**:
- ✅ Platform live and public
- ✅ User acquisition growing
- ✅ Revenue generating
- ✅ Support system in place

---

## 📈 RESOURCE REQUIREMENTS

### Team Structure (Recommended)
**Minimum Team**: 1 full-stack developer  
**Optimal Team**: 
- 1 Senior Full-Stack Developer (Laravel + Vue)
- 1 Junior Developer (Frontend focus)
- 1 QA Tester (part-time during testing phase)

### Time Estimates
- **Solo Developer**: 16-20 weeks (4-5 months)
- **2 Developers**: 12-14 weeks (3-3.5 months)
- **3 Developers**: 10-12 weeks (2.5-3 months)

### Server Requirements
**Development**:
- Local machine (XAMPP/Laravel Valet/Docker)
- SQLite database
- Redis (optional for dev)

**Staging**:
- 2GB RAM VPS
- 20GB storage
- MySQL 8.0 + Redis

**Production (Launch)**:
- 4GB RAM VPS (minimum)
- 40GB SSD storage
- MySQL 8.0 + Redis
- CDN (CloudFlare free tier)

**Production (After 1000 users)**:
- 8GB RAM VPS
- 80GB SSD storage
- Database backups (daily)
- Load balancer (if needed)

### Budget Estimates
**Development Costs**:
- Developers: $0 (in-house) or $15,000-$30,000 (outsourced)
- Design: $0 (using TailwindCSS + Heroicons)

**Infrastructure Costs** (Monthly):
- Domain: $10-15/year ($1-2/month)
- VPS (4GB): $20-40/month
- SSL: $0 (Let's Encrypt free)
- CDN: $0 (CloudFlare free tier)
- Backups: $5-10/month
- Email (SendGrid): $0-15/month (free tier 100 emails/day)
- SMS (Twilio): Pay-as-you-go ($0.05/SMS)
- **Total**: ~$30-70/month at launch

**API Costs** (As Usage Grows):
- Google Gemini: FREE (60 req/min)
- Pexels: FREE (200 req/hour)
- Amadeus: FREE tier available (limited)
- Bimafy: Pay-per-quote (negotiate rates)
- Payment Gateways: 2-3% transaction fee

### External Services
**Required**:
- ✅ Google Gemini API (AI) - FREE
- ✅ Pexels API (Stock Photos) - FREE
- ✅ SendGrid (Email) - FREE tier (100 emails/day)

**Optional (Can Add Later)**:
- Twilio (SMS) - Pay-as-you-go
- BulkSMS BD (Bangladesh SMS) - Bulk rates
- Amadeus (Flight Search) - FREE tier
- Bimafy (Travel Insurance) - Per-quote pricing
- SSLCommerz (Payments) - 2% transaction fee
- bKash/Nagad/Rocket (Mobile Payments) - Variable fees

---

## 🎯 SUCCESS METRICS

### Launch Goals (Month 1)
- [ ] 50+ agencies registered
- [ ] 20+ consultants active
- [ ] 500+ user registrations
- [ ] 100+ visa applications submitted
- [ ] 50+ successful referrals
- [ ] Zero critical bugs reported
- [ ] 99.9% uptime

### Growth Goals (Month 3)
- [ ] 200+ agencies (4 per category minimum)
- [ ] 100+ consultants
- [ ] 5,000+ users
- [ ] 1,000+ applications processed
- [ ] 500+ travel bookings
- [ ] $10,000+ revenue generated

### Long-Term Goals (Year 1)
- [ ] 1,000+ agencies
- [ ] 500+ consultants
- [ ] 50,000+ users
- [ ] 10,000+ applications processed
- [ ] $100,000+ revenue
- [ ] Expand to 3+ countries

---

## 🔒 RISK MITIGATION

### Technical Risks
**Risk**: Data loss due to server failure  
**Mitigation**: Daily automated backups to S3, weekly manual backups

**Risk**: Security breach  
**Mitigation**: Regular security audits, input validation, CSRF protection, SQL injection prevention

**Risk**: Poor performance under load  
**Mitigation**: Load testing, database indexing, Redis caching, CDN

**Risk**: Third-party API failures  
**Mitigation**: Graceful degradation, fallback options, error logging, retry mechanisms

### Business Risks
**Risk**: Low user adoption  
**Mitigation**: Beta testing, user feedback, marketing campaigns, referral incentives

**Risk**: Agency churn  
**Mitigation**: Competitive commission rates, excellent support, valuable features

**Risk**: Consultant dissatisfaction  
**Mitigation**: Fair assignment algorithm, performance tracking, rewards

**Risk**: Regulatory changes  
**Mitigation**: Legal consultation, compliance monitoring, flexible architecture

---

## 📚 DOCUMENTATION DELIVERABLES

### For Developers
- [x] `.github/copilot-instructions.md` (Already created)
- [ ] `ARCHITECTURE.md` (System design, patterns)
- [ ] `DATABASE_SCHEMA.md` (All tables with relationships)
- [ ] `API_DOCUMENTATION.md` (All endpoints)
- [ ] `DEPLOYMENT.md` (Step-by-step deployment)
- [ ] `TROUBLESHOOTING.md` (Common issues + solutions)

### For Users
- [ ] User Guide (PDF + video)
- [ ] Agency Onboarding Guide
- [ ] Consultant Guide
- [ ] FAQ (50+ questions)

### For Admins
- [ ] Admin Manual
- [ ] Configuration Guide
- [ ] Backup & Recovery Procedures
- [ ] Security Best Practices

---

## ✅ QUALITY CHECKLIST

Before each phase completion, ensure:
- [ ] All migrations run successfully
- [ ] All models have correct relationships
- [ ] All controllers are thin (business logic in services)
- [ ] All services have error handling
- [ ] All Vue pages are responsive
- [ ] All forms have validation
- [ ] All actions have authorization checks
- [ ] All dates use Bangladesh format (DD/MM/YYYY)
- [ ] All currency uses ৳ symbol
- [ ] All phone numbers use +880 format
- [ ] All routes are named correctly
- [ ] Ziggy routes generated
- [ ] No N+1 queries
- [ ] No console errors
- [ ] No PHP warnings/notices
- [ ] Code follows PSR-12 standards
- [ ] Comments for complex logic
- [ ] Git commits are descriptive

---

## 🚀 QUICK START COMMANDS

### Initial Setup
```powershell
# Create project
composer create-project laravel/laravel bideshgomon-saas
cd bideshgomon-saas

# Install packages
composer require inertiajs/inertia-laravel tightenco/ziggy
npm install @inertiajs/vue3 @vitejs/plugin-vue vue @heroicons/vue

# Database
php artisan migrate:fresh --seed

# Start servers
php artisan serve
npm run dev
```

### Daily Development
```powershell
# After code changes
npm run build
php artisan ziggy:generate

# After route changes
php artisan route:clear
php artisan ziggy:generate

# After config changes
php artisan config:clear

# Run tests
php artisan test
```

### Deployment
```powershell
# On production server
git pull origin main
composer install --no-dev --optimize-autoloader
npm ci && npm run build
php artisan migrate --force
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan queue:restart
```

---

## 📞 SUPPORT & MAINTENANCE

### Support Channels
- **Documentation**: README.md + docs folder
- **Issue Tracking**: GitHub Issues
- **Team Chat**: Slack/Discord
- **Email Support**: support@bideshgomon.com

### Maintenance Schedule
- **Daily**: Monitor errors, check server health
- **Weekly**: Review user feedback, fix bugs
- **Monthly**: Security updates, feature releases
- **Quarterly**: Major version updates, refactoring

---

## 🎉 CONCLUSION

This plan provides a **comprehensive, step-by-step roadmap** to build a **100% error-free, production-ready SaaS platform** in **12-16 weeks**. By leveraging the existing codebases (bgplatform-fresh + bgproject), we can:

✅ **Preserve all features** from the old projects  
✅ **Build a robust multi-agency SaaS architecture**  
✅ **Maintain Bangladesh localization throughout**  
✅ **Deploy with confidence** (zero critical bugs)  

**Key Success Factors**:
1. Follow the phases sequentially (don't skip)
2. Test thoroughly after each phase
3. Copy working code from bgproject (don't reinvent)
4. Use services for business logic (thin controllers)
5. Apply Bangladesh formatting everywhere
6. Document as you build
7. Get feedback early (soft launch)

**Next Steps**:
1. Review this plan with stakeholders
2. Set up development environment
3. Begin Phase 0: Project Setup
4. Track progress using GitHub Projects/Trello
5. Celebrate milestones! 🎉

---

**Let's build something amazing! 🚀🇧🇩**

*Plan Version: 1.0*  
*Last Updated: November 19, 2025*
