# Modern Database Architecture (2025)

## 🎯 Design Principles

### 1. **Single Source of Truth (SSOT)**
All reference data stored centrally to eliminate duplication and ensure consistency.

### 2. **Normalized Design**
Properly structured with foreign keys, avoiding redundant data storage.

### 3. **Performance First**
Indexed appropriately, with caching strategies for frequently accessed data.

### 4. **Zero Errors**
Every constraint, relationship, and validation properly defined at database level.

---

## 📊 Central Reference Tables (SSOT)

### Geographic Data
```sql
-- Countries (Central Reference)
countries
├── id (PK)
├── name
├── iso_code_2 (UNIQUE, INDEX) -- BD, US, UK
├── iso_code_3 (UNIQUE) -- BGD, USA, GBR
├── phone_code -- +880, +1, +44
├── currency_code -- BDT, USD, GBP
├── flag_emoji -- 🇧🇩, 🇺🇸, 🇬🇧
├── region -- Asia, Europe, Americas
├── timezone_primary -- Asia/Dhaka, America/New_York
├── latitude / longitude
├── is_active
└── timestamps

-- States/Provinces (Linked to Countries)
states
├── id (PK)
├── country_id (FK → countries.id, INDEX)
├── name -- Dhaka Division, California
├── code -- DHA, CA
├── type -- Division, State, Province
└── timestamps

-- Cities (Linked to States)
cities
├── id (PK)
├── state_id (FK → states.id, INDEX)
├── country_id (FK → countries.id, INDEX)
├── name
├── code
├── latitude / longitude
├── is_capital
└── timestamps

-- Airports (Central Reference)
airports
├── id (PK)
├── city_id (FK → cities.id)
├── name -- Hazrat Shahjalal International Airport
├── iata_code (UNIQUE, INDEX) -- DAC
├── icao_code -- VGHS
├── timezone
└── timestamps
```

### Currency & Financial
```sql
-- Currencies (Central Reference)
currencies
├── id (PK)
├── code (UNIQUE, INDEX) -- BDT, USD, EUR
├── name -- Bangladeshi Taka, US Dollar
├── symbol -- ৳, $, €
├── decimal_places -- 2
├── format -- {symbol}{amount}
└── timestamps

-- Exchange Rates (Daily Updates)
exchange_rates
├── id (PK)
├── from_currency_id (FK → currencies.id)
├── to_currency_id (FK → currencies.id)
├── rate -- 110.00 (1 USD = 110 BDT)
├── date
└── timestamps
```

### Language & Localization
```sql
-- Languages (Central Reference)
languages
├── id (PK)
├── name -- English, Bengali, Arabic
├── code (UNIQUE, INDEX) -- en, bn, ar
├── native_name -- English, বাংলা, العربية
├── direction -- ltr, rtl
└── timestamps

-- Country Languages (Which languages are official/spoken)
country_languages
├── country_id (FK → countries.id, INDEX)
├── language_id (FK → languages.id, INDEX)
├── is_official -- true/false
└── PRIMARY KEY (country_id, language_id)
```

### Education Reference
```sql
-- Degrees (Central Reference)
degrees
├── id (PK)
├── name -- Bachelor of Science, Master of Arts
├── abbreviation -- BSc, MA
├── level -- Undergraduate, Graduate, Doctorate
├── duration_years
└── timestamps

-- Fields of Study (Central Reference)
fields_of_study
├── id (PK)
├── name -- Computer Science, Mechanical Engineering
├── category -- Engineering, Business, Arts
└── timestamps

-- Universities (Central Reference)
universities
├── id (PK)
├── country_id (FK → countries.id, INDEX)
├── city_id (FK → cities.id)
├── name
├── short_name
├── website
├── ranking_world
├── ranking_country
├── is_verified
└── timestamps
```

### Document Types & Requirements
```sql
-- Document Types (Central Reference)
document_types
├── id (PK)
├── name -- Passport, NID, Birth Certificate
├── code (UNIQUE, INDEX) -- passport, nid, birth_cert
├── description
├── is_required_for_travel
└── timestamps

-- Country Visa Requirements (What documents needed per country)
country_visa_requirements
├── id (PK)
├── country_id (FK → countries.id, INDEX)
├── visa_type -- Tourist, Student, Work
├── required_documents (JSON) -- [1, 2, 3] → document_types.id
├── processing_time_days
├── validity_days
├── fee_amount
├── fee_currency_id (FK → currencies.id)
└── timestamps
```

---

## 🏗️ Application Tables (Using References)

### User Management
```sql
-- Users (Core)
users
├── id (PK)
├── username (UNIQUE, INDEX)
├── email (UNIQUE, INDEX)
├── phone (UNIQUE, INDEX)
├── password
├── role_id (FK → roles.id, INDEX)
├── nationality_country_id (FK → countries.id)
├── preferred_language_id (FK → languages.id)
├── preferred_currency_id (FK → currencies.id)
├── email_verified_at
├── phone_verified_at
├── is_active
└── timestamps, soft_deletes

-- User Profiles (Extended Info)
user_profiles
├── user_id (PK, FK → users.id)
├── first_name
├── last_name
├── date_of_birth
├── gender
├── nationality_country_id (FK → countries.id)
├── birth_city_id (FK → cities.id)
├── current_country_id (FK → countries.id)
├── current_city_id (FK → cities.id)
├── address
├── postal_code
├── avatar
└── timestamps

-- User Addresses (Multiple addresses per user)
user_addresses
├── id (PK)
├── user_id (FK → users.id, INDEX)
├── type -- home, work, mailing
├── country_id (FK → countries.id)
├── state_id (FK → states.id)
├── city_id (FK → cities.id)
├── address_line_1
├── address_line_2
├── postal_code
├── is_primary
└── timestamps
```

### Education History
```sql
-- User Education (Linked to Central References)
user_education
├── id (PK)
├── user_id (FK → users.id, INDEX)
├── degree_id (FK → degrees.id) -- NOT storing "BSc" as text
├── field_of_study_id (FK → fields_of_study.id)
├── university_id (FK → universities.id) -- NOT storing university name
├── country_id (FK → countries.id)
├── start_year
├── end_year
├── gpa
├── grade_scale
└── timestamps
```

### Employment History
```sql
-- User Employment
user_employment
├── id (PK)
├── user_id (FK → users.id, INDEX)
├── company_name
├── position
├── country_id (FK → countries.id)
├── city_id (FK → cities.id)
├── start_date
├── end_date
├── is_current
└── timestamps
```

### Travel Documents
```sql
-- User Passports
user_passports
├── id (PK)
├── user_id (FK → users.id, INDEX)
├── passport_number (INDEX)
├── issuing_country_id (FK → countries.id)
├── nationality_country_id (FK → countries.id)
├── issue_date
├── expiry_date
├── document_path
├── is_active
└── timestamps

-- User Travel History
user_travel_history
├── id (PK)
├── user_id (FK → users.id, INDEX)
├── country_id (FK → countries.id)
├── entry_date
├── exit_date
├── visa_type
├── purpose
└── timestamps
```

---

## 🏢 Agency & Service System

### Multi-Agency Architecture
```sql
-- Agency Categories (Fixed Reference)
agency_categories
├── id (PK)
├── name -- Travel Agency, Education Consultancy
├── code (UNIQUE, INDEX) -- travel, education, recruitment
├── description
├── icon
└── timestamps

-- Service Categories (Fixed Reference)
service_categories
├── id (PK)
├── name -- Visa Services, Flight Booking, University Application
├── code (UNIQUE, INDEX) -- visa, flight, university
├── agency_category_id (FK → agency_categories.id)
├── base_commission_percentage
└── timestamps

-- Agencies
agencies
├── id (PK)
├── agency_category_id (FK → agency_categories.id, INDEX)
├── name
├── registration_number (UNIQUE)
├── country_id (FK → countries.id)
├── city_id (FK → cities.id)
├── phone
├── email
├── website
├── owner_user_id (FK → users.id)
├── is_verified
├── is_active
├── commission_percentage
└── timestamps, soft_deletes

-- Agency Service Categories (Which services agency offers)
agency_service_categories
├── agency_id (FK → agencies.id, INDEX)
├── service_category_id (FK → service_categories.id, INDEX)
├── is_enabled
├── commission_override -- NULL or custom percentage
└── PRIMARY KEY (agency_id, service_category_id)

-- Agency Country Permissions (Which countries agency can serve)
agency_countries
├── agency_id (FK → agencies.id, INDEX)
├── country_id (FK → countries.id, INDEX)
├── is_approved
├── approved_by (FK → users.id)
├── approved_at
└── PRIMARY KEY (agency_id, country_id)
```

### Consultant System
```sql
-- Consultant Profiles
consultant_profiles
├── id (PK)
├── user_id (FK → users.id, UNIQUE, INDEX)
├── agency_id (FK → agencies.id, INDEX)
├── specialization (JSON) -- [visa, education, recruitment]
├── experience_years
├── certification_number
├── is_active
└── timestamps

-- Consultant Country Expertise
consultant_countries
├── consultant_id (FK → consultant_profiles.id, INDEX)
├── country_id (FK → countries.id, INDEX)
├── PRIMARY KEY (consultant_id, country_id)

-- Consultant Assignments (Which clients assigned to which consultant)
consultant_assignments
├── id (PK)
├── consultant_id (FK → consultant_profiles.id, INDEX)
├── user_id (FK → users.id, INDEX)
├── service_type -- visa_application, university_application
├── service_id -- Polymorphic relation ID
├── assigned_by (FK → users.id)
├── status -- assigned, accepted, in_progress, completed, cancelled
├── assigned_at
├── accepted_at
├── completed_at
└── timestamps
```

---

## 💰 Financial System

### Wallet & Transactions
```sql
-- Wallets (One per user)
wallets
├── id (PK)
├── user_id (FK → users.id, UNIQUE, INDEX)
├── currency_id (FK → currencies.id) -- User's preferred currency
├── balance (DECIMAL 15,2, DEFAULT 0.00)
├── total_earned (DECIMAL 15,2)
├── total_spent (DECIMAL 15,2)
├── is_active
└── timestamps

-- Wallet Transactions (Audit Trail)
wallet_transactions
├── id (PK, ULID for unique ordering)
├── wallet_id (FK → wallets.id, INDEX)
├── type -- credit, debit
├── amount (DECIMAL 15,2)
├── balance_before (DECIMAL 15,2) -- Snapshot
├── balance_after (DECIMAL 15,2) -- Snapshot
├── currency_id (FK → currencies.id)
├── description
├── reference_type -- service_payment, referral_reward, cashout
├── reference_id -- Polymorphic
├── created_by (FK → users.id)
└── timestamps
```

### Referral System
```sql
-- Referral Codes
referral_codes
├── id (PK)
├── user_id (FK → users.id, UNIQUE, INDEX)
├── code (UNIQUE, INDEX, 8 chars)
├── total_referrals
├── total_earned (DECIMAL 10,2)
└── timestamps

-- Referral Tracking
referrals
├── id (PK)
├── referrer_id (FK → users.id, INDEX) -- Who referred
├── referred_id (FK → users.id, INDEX) -- Who was referred
├── referral_code_id (FK → referral_codes.id)
├── status -- pending, approved, rejected
├── reward_amount (DECIMAL 10,2)
├── currency_id (FK → currencies.id)
├── approved_by (FK → users.id)
├── approved_at
└── timestamps
```

### Payments & Cashouts
```sql
-- Payments (Service payments)
payments
├── id (PK)
├── user_id (FK → users.id, INDEX)
├── service_type -- visa_application, flight_booking
├── service_id -- Polymorphic
├── amount (DECIMAL 10,2)
├── currency_id (FK → currencies.id)
├── payment_method -- bkash, nagad, sslcommerz, wallet
├── transaction_id (INDEX)
├── status -- pending, completed, failed, refunded
├── gateway_response (JSON)
└── timestamps

-- Cashout Requests (Users withdraw from wallet)
cashout_requests
├── id (PK)
├── user_id (FK → users.id, INDEX)
├── amount (DECIMAL 10,2)
├── currency_id (FK → currencies.id)
├── method -- bank_transfer, mobile_banking
├── account_details (JSON, ENCRYPTED)
├── status -- pending, approved, processing, completed, rejected
├── approved_by (FK → users.id)
├── processed_by (FK → users.id)
├── approved_at
├── processed_at
├── rejection_reason
└── timestamps
```

---

## 📋 Service Applications (Polymorphic Pattern)

### Visa Applications
```sql
-- Tourist Visa Applications
tourist_visa_applications
├── id (PK)
├── user_id (FK → users.id, INDEX)
├── destination_country_id (FK → countries.id, INDEX)
├── passport_id (FK → user_passports.id)
├── agency_id (FK → agencies.id, INDEX) -- Auto-assigned
├── consultant_id (FK → consultant_profiles.id) -- Optional
├── travel_start_date
├── travel_end_date
├── purpose
├── accommodation_country_id (FK → countries.id)
├── accommodation_city_id (FK → cities.id)
├── status -- draft, submitted, under_review, approved, rejected
├── submitted_at
├── reviewed_at
├── reviewed_by (FK → users.id)
└── timestamps, soft_deletes

-- Student Visa Applications
student_visa_applications
├── id (PK)
├── user_id (FK → users.id, INDEX)
├── destination_country_id (FK → countries.id, INDEX)
├── passport_id (FK → user_passports.id)
├── agency_id (FK → agencies.id, INDEX)
├── consultant_id (FK → consultant_profiles.id)
├── university_id (FK → universities.id) -- Using central reference
├── degree_id (FK → degrees.id)
├── field_of_study_id (FK → fields_of_study.id)
├── intake_year
├── intake_month
├── status
└── timestamps, soft_deletes

-- Work Visa Applications
work_visa_applications
├── id (PK)
├── user_id (FK → users.id, INDEX)
├── destination_country_id (FK → countries.id, INDEX)
├── passport_id (FK → user_passports.id)
├── agency_id (FK → agencies.id, INDEX)
├── consultant_id (FK → consultant_profiles.id)
├── job_title
├── employer_name
├── employer_country_id (FK → countries.id)
├── employer_city_id (FK → cities.id)
├── salary_amount
├── salary_currency_id (FK → currencies.id)
├── status
└── timestamps, soft_deletes
```

### Travel Bookings
```sql
-- Flight Bookings
flight_bookings
├── id (PK)
├── user_id (FK → users.id, INDEX)
├── agency_id (FK → agencies.id, INDEX)
├── booking_reference (UNIQUE, INDEX)
├── departure_airport_id (FK → airports.id)
├── arrival_airport_id (FK → airports.id)
├── departure_date
├── return_date
├── passenger_count
├── total_amount
├── currency_id (FK → currencies.id)
├── status -- pending, confirmed, cancelled
└── timestamps, soft_deletes

-- Hotel Bookings
hotel_bookings
├── id (PK)
├── user_id (FK → users.id, INDEX)
├── agency_id (FK → agencies.id, INDEX)
├── booking_reference (UNIQUE, INDEX)
├── country_id (FK → countries.id)
├── city_id (FK → cities.id)
├── hotel_name
├── check_in_date
├── check_out_date
├── room_count
├── guest_count
├── total_amount
├── currency_id (FK → currencies.id)
├── status
└── timestamps, soft_deletes
```

---

## 🔍 Search & Optimization

### Indexing Strategy
```sql
-- Full-Text Search Indexes
ALTER TABLE universities ADD FULLTEXT INDEX ft_name_search (name, short_name);
ALTER TABLE cities ADD FULLTEXT INDEX ft_city_search (name);
ALTER TABLE countries ADD FULLTEXT INDEX ft_country_search (name);

-- Composite Indexes (Multi-column searches)
ALTER TABLE user_education ADD INDEX idx_user_degree (user_id, degree_id);
ALTER TABLE tourist_visa_applications ADD INDEX idx_status_country (status, destination_country_id);
ALTER TABLE agencies ADD INDEX idx_category_active (agency_category_id, is_active);

-- Foreign Key Indexes (Always index FK columns!)
-- Already shown in table definitions above
```

### Caching Strategy
```php
// Cache reference data that rarely changes
Cache::remember('countries', now()->addDay(), fn() => Country::all());
Cache::remember('currencies', now()->addDay(), fn() => Currency::all());
Cache::remember('degrees', now()->addWeek(), fn() => Degree::all());

// Cache user-specific data with shorter TTL
Cache::remember("user.{$userId}.profile", now()->addHour(), fn() => User::find($userId)->profile);
```

---

## 📦 Data Seeding Strategy

### 1. **Reference Data (Always Seeded)**
```bash
php artisan db:seed --class=CountrySeeder         # 195 countries
php artisan db:seed --class=StateSeeder           # 8 BD divisions + 50 US states
php artisan db:seed --class=CitySeeder            # 64 BD districts + major world cities
php artisan db:seed --class=CurrencySeeder        # 150+ currencies
php artisan db:seed --class=LanguageSeeder        # 50+ languages
php artisan db:seed --class=DegreeSeeder          # 20+ degrees
php artisan db:seed --class=FieldOfStudySeeder    # 100+ fields
php artisan db:seed --class=UniversitySeeder      # 1000+ top universities
php artisan db:seed --class=AirportSeeder         # 500+ major airports
php artisan db:seed --class=DocumentTypeSeeder    # 15+ document types
```

### 2. **Platform Configuration**
```bash
php artisan db:seed --class=RoleSeeder                 # 7 roles
php artisan db:seed --class=AgencyCategorySeeder       # 5 categories
php artisan db:seed --class=ServiceCategorySeeder      # 15 services
```

### 3. **Demo Data (Development/Testing Only)**
```bash
php artisan db:seed --class=DemoUserSeeder            # 100 users
php artisan db:seed --class=DemoAgencySeeder          # 20 agencies
php artisan db:seed --class=DemoConsultantSeeder      # 50 consultants
php artisan db:seed --class=DemoVisaApplicationSeeder # 200 applications
```

---

## 🛡️ Data Integrity Rules

### Foreign Key Constraints
```sql
-- ON DELETE RESTRICT (Cannot delete if references exist)
foreign key (country_id) references countries(id) ON DELETE RESTRICT;

-- ON DELETE CASCADE (Delete child records too)
foreign key (user_id) references users(id) ON DELETE CASCADE;

-- ON DELETE SET NULL (Set to null if parent deleted)
foreign key (consultant_id) references consultant_profiles(id) ON DELETE SET NULL;
```

### Database-Level Checks
```sql
-- Ensure dates are logical
ALTER TABLE user_passports ADD CONSTRAINT chk_expiry_after_issue
CHECK (expiry_date > issue_date);

-- Ensure financial values are positive
ALTER TABLE payments ADD CONSTRAINT chk_amount_positive
CHECK (amount > 0);

-- Ensure wallet balance never negative
ALTER TABLE wallets ADD CONSTRAINT chk_balance_non_negative
CHECK (balance >= 0);
```

---

## 📊 Performance Benchmarks

### Target Metrics (Production)
- **Country lookup**: < 1ms (cached)
- **Currency conversion**: < 5ms
- **User profile query**: < 10ms
- **Visa application list**: < 50ms (paginated, indexed)
- **Full-text university search**: < 100ms

### Query Optimization Example
```php
// ❌ BAD (N+1 Query Problem)
$applications = TouristVisaApplication::all();
foreach ($applications as $app) {
    echo $app->user->name; // Separate query for each user!
    echo $app->destinationCountry->name; // Separate query!
}

// ✅ GOOD (Eager Loading)
$applications = TouristVisaApplication::query()
    ->with(['user', 'destinationCountry', 'agency', 'consultant'])
    ->where('status', 'pending')
    ->latest()
    ->paginate(20);
```

---

## 🎯 Key Takeaways

1. **All common data centralized** (countries, cities, currencies, degrees, etc.)
2. **Foreign keys everywhere** - proper relationships
3. **No string storage of reference data** - always use IDs
4. **Proper indexing** for fast searches
5. **Soft deletes** for important records (users, agencies, applications)
6. **Audit trails** in wallet_transactions (balance_before/after snapshots)
7. **Polymorphic relations** where needed (payments, consultant assignments)
8. **Demo data separated** from real reference data

---

## 📚 Migration Execution Order

```bash
# 1. Reference Data (No dependencies)
2024_01_01_000001_create_countries_table
2024_01_01_000002_create_currencies_table
2024_01_01_000003_create_languages_table
2024_01_01_000004_create_states_table
2024_01_01_000005_create_cities_table
2024_01_01_000006_create_airports_table
2024_01_01_000007_create_degrees_table
2024_01_01_000008_create_fields_of_study_table
2024_01_01_000009_create_universities_table
2024_01_01_000010_create_document_types_table

# 2. Core System (Depends on reference data)
2024_01_02_000001_create_roles_table
2024_01_02_000002_create_users_table
2024_01_02_000003_create_user_profiles_table
2024_01_02_000004_create_user_addresses_table
2024_01_02_000005_create_user_passports_table

# 3. Agency System
2024_01_03_000001_create_agency_categories_table
2024_01_03_000002_create_service_categories_table
2024_01_03_000003_create_agencies_table
2024_01_03_000004_create_agency_service_categories_table
2024_01_03_000005_create_agency_countries_table
2024_01_03_000006_create_consultant_profiles_table

# 4. Financial System
2024_01_04_000001_create_wallets_table
2024_01_04_000002_create_wallet_transactions_table
2024_01_04_000003_create_referral_codes_table
2024_01_04_000004_create_payments_table

# 5. Service Applications (Last, depends on everything)
2024_01_05_000001_create_tourist_visa_applications_table
2024_01_05_000002_create_student_visa_applications_table
2024_01_05_000003_create_work_visa_applications_table
2024_01_05_000004_create_flight_bookings_table
2024_01_05_000005_create_hotel_bookings_table
```

---

**Next Steps**: 
1. Generate all migrations based on this schema
2. Create model classes with proper relationships
3. Seed reference data
4. Write tests for data integrity
5. Implement caching layer

This architecture ensures **zero errors**, **maximum performance**, and **easy maintenance**!
