# 📊 DATA MANAGEMENT SYSTEM - COMPREHENSIVE PLAN

## 🎯 Overview
Complete admin panel data management system with CRUD operations and bulk CSV upload functionality for all pre-built reference data required by the platform.

---

## 📋 DATA CATEGORIES & PRIORITY

### **🔴 CRITICAL PRIORITY - Foundation Data**

#### 1. **Countries Management** ✅ (Table Exists)
**Table**: `countries`
**Purpose**: Core reference for all international operations

**Fields**:
- name (English & Bengali)
- ISO codes (2-char & 3-char)
- phone_code
- currency_code
- flag_emoji
- region
- is_active

**Features Needed**:
- ✅ CRUD operations
- ✅ Bulk CSV upload
- ✅ Search & filter
- ✅ Enable/disable countries
- ✅ Region grouping
- **CSV Template**: name,name_bn,iso_code_2,iso_code_3,phone_code,currency_code,flag_emoji,region,is_active

**Usage**: Visa applications, job postings, user profiles, travel services

---

#### 2. **Currencies Management** ✅ (Table Exists)
**Table**: `currencies`
**Purpose**: Multi-currency support for payments

**Fields**:
- code (USD, BDT, EUR)
- name
- symbol
- exchange_rate_to_bdt
- is_active

**Features Needed**:
- ✅ CRUD operations
- ✅ Bulk CSV upload
- ✅ Exchange rate updates
- ✅ Historical rate tracking
- **CSV Template**: code,name,symbol,exchange_rate_to_bdt,is_active

**Usage**: Payment processing, pricing display, financial reports

---

#### 3. **Languages Management** ✅ (Table Exists)
**Table**: `languages`
**Purpose**: Language proficiency tracking

**Fields**:
- name (English, Bangla, Arabic)
- code (en, bn, ar)
- native_name
- is_active

**Features Needed**:
- ✅ CRUD operations
- ✅ Bulk CSV upload
- **CSV Template**: name,code,native_name,is_active

**Usage**: User language skills, job requirements, profile completion

---

#### 4. **Language Tests Management** ✅ (Table Exists)
**Table**: `language_tests`
**Purpose**: Standardized language test types

**Fields**:
- language_id
- name (IELTS, TOEFL, etc.)
- code
- max_score
- passing_score
- validity_months
- is_active

**Features Needed**:
- ✅ CRUD operations
- ✅ Bulk CSV upload
- **CSV Template**: language_code,name,code,max_score,passing_score,validity_months,is_active

**Usage**: Language certification tracking

---

### **🟠 HIGH PRIORITY - Service & Visa Data**

#### 5. **Visa Types Management** ⚠️ (Needs Implementation)
**Table**: `visa_types` (NEW)
**Purpose**: Standardized visa categories

**Schema**:
```php
Schema::create('visa_types', function (Blueprint $table) {
    $table->id();
    $table->string('code', 50)->unique(); // tourist, work, student
    $table->string('name', 100); // Tourist Visa
    $table->string('name_bn', 100)->nullable();
    $table->text('description')->nullable();
    $table->string('icon', 50)->nullable(); // 🏖️, 💼, 🎓
    $table->string('color', 20)->default('#3B82F6'); // Tailwind color
    $table->integer('typical_duration_days')->nullable();
    $table->integer('processing_days_min')->nullable();
    $table->integer('processing_days_max')->nullable();
    $table->boolean('is_active')->default(true);
    $table->integer('sort_order')->default(0);
    $table->timestamps();
});
```

**Features Needed**:
- ✅ CRUD operations
- ✅ Bulk CSV upload
- ✅ Drag-and-drop sorting
- **CSV Template**: code,name,name_bn,description,icon,color,typical_duration_days,processing_days_min,processing_days_max,is_active,sort_order

**Initial Data**: tourist, business, student, work, medical, transit, family_reunion, diplomatic, refugee, permanent_resident

**Usage**: Visa applications, service modules, pricing

---

#### 6. **Visa Requirements Management** ✅ (Partially Exists)
**Table**: `visa_requirements`
**Purpose**: Country-specific visa rules

**Current Fields** (enhance if needed):
- country
- country_code
- visa_type
- required_documents
- eligibility_criteria
- processing_time
- fees
- validity_period

**Features Needed**:
- ✅ CRUD operations
- ✅ Bulk CSV upload
- ✅ Document checklist builder
- ✅ Profession-specific requirements
- **CSV Template**: country,country_code,visa_type,required_documents_json,eligibility_criteria_json,processing_time,service_fee,government_fee,validity_period

**Usage**: Application forms, agency assignments

---

#### 7. **Service Modules Management** ✅ (Table Exists)
**Table**: `service_modules`
**Purpose**: Platform service catalog

**Current**: 39 pre-defined services
**Features Needed**:
- ✅ CRUD operations
- ✅ Bulk CSV upload
- ✅ Enable/disable services
- ✅ Pricing management
- **CSV Template**: name,slug,category,description,icon,is_active,price_min,price_max,is_featured

**Usage**: Service listing, booking, pricing

---

### **🟡 MEDIUM PRIORITY - Job & Skills Data**

#### 8. **Job Categories Management** ⚠️ (Needs Implementation)
**Table**: `job_categories` (NEW)
**Purpose**: Job classification system

**Schema**:
```php
Schema::create('job_categories', function (Blueprint $table) {
    $table->id();
    $table->string('name', 100);
    $table->string('name_bn', 100)->nullable();
    $table->string('slug', 100)->unique();
    $table->text('description')->nullable();
    $table->string('icon', 50)->nullable(); // 💼, 🏥, 🏗️
    $table->foreignId('parent_id')->nullable()->constrained('job_categories')->nullOnDelete();
    $table->boolean('is_active')->default(true);
    $table->integer('sort_order')->default(0);
    $table->timestamps();
    
    $table->index(['parent_id', 'is_active']);
});
```

**Features Needed**:
- ✅ CRUD with hierarchical structure
- ✅ Bulk CSV upload
- ✅ Parent-child relationships
- **CSV Template**: name,name_bn,slug,description,icon,parent_slug,is_active,sort_order

**Initial Data**: Healthcare, IT, Construction, Hospitality, Manufacturing, Education, Transportation, Retail, etc.

**Usage**: Job postings, candidate matching

---

#### 9. **Skills Management** ⚠️ (Needs Implementation)
**Table**: `skills` (NEW)
**Purpose**: Standardized skill tags

**Schema**:
```php
Schema::create('skills', function (Blueprint $table) {
    $table->id();
    $table->string('name', 100)->unique();
    $table->string('slug', 100)->unique();
    $table->foreignId('category_id')->nullable()->constrained('skill_categories')->nullOnDelete();
    $table->enum('type', ['hard', 'soft', 'technical', 'language'])->default('hard');
    $table->boolean('is_active')->default(true);
    $table->timestamps();
    
    $table->index(['category_id', 'type', 'is_active']);
});
```

**Features Needed**:
- ✅ CRUD operations
- ✅ Bulk CSV upload
- ✅ Auto-suggest/search
- **CSV Template**: name,slug,category_name,type,is_active

**Initial Data**: Programming (Python, PHP, React), Medical (Surgery, Nursing), Construction (Welding, Carpentry), etc.

**Usage**: User profiles, job requirements, matching algorithm

---

#### 10. **Skill Categories Management** ⚠️ (Needs Implementation)
**Table**: `skill_categories` (NEW)
**Purpose**: Organize skills by domain

**Schema**:
```php
Schema::create('skill_categories', function (Blueprint $table) {
    $table->id();
    $table->string('name', 100)->unique();
    $table->string('slug', 100)->unique();
    $table->text('description')->nullable();
    $table->boolean('is_active')->default(true);
    $table->timestamps();
});
```

**Features Needed**:
- ✅ CRUD operations
- ✅ Bulk CSV upload
- **CSV Template**: name,slug,description,is_active

**Usage**: Skill organization

---

### **🟢 MEDIUM-LOW PRIORITY - Geographic & Infrastructure**

#### 11. **Cities Management** ✅ (Table Exists)
**Table**: `cities`
**Purpose**: Major cities per country

**Fields**:
- country_id
- name
- name_bn
- is_capital
- is_active

**Features Needed**:
- ✅ CRUD operations
- ✅ Bulk CSV upload
- ✅ Country filter
- **CSV Template**: country_code,name,name_bn,is_capital,is_active

**Usage**: User addresses, job locations

---

#### 12. **Airports Management** ⚠️ (Needs Implementation)
**Table**: `airports` (NEW)
**Purpose**: Flight booking support

**Schema**:
```php
Schema::create('airports', function (Blueprint $table) {
    $table->id();
    $table->foreignId('country_id')->constrained()->cascadeOnDelete();
    $table->foreignId('city_id')->nullable()->constrained()->nullOnDelete();
    $table->string('name', 200);
    $table->string('iata_code', 3)->unique(); // DXB, JFK
    $table->string('icao_code', 4)->unique(); // OMDB, KJFK
    $table->boolean('is_international')->default(true);
    $table->boolean('is_active')->default(true);
    $table->timestamps();
    
    $table->index(['country_id', 'is_active']);
});
```

**Features Needed**:
- ✅ CRUD operations
- ✅ Bulk CSV upload
- **CSV Template**: country_code,city_name,name,iata_code,icao_code,is_international,is_active

**Usage**: Flight bookings

---

#### 13. **Hotels/Accommodations Management** ✅ (Partially Exists)
**Table**: `hotels`
**Purpose**: Hotel booking inventory

**Features Needed**:
- ✅ CRUD operations
- ✅ Bulk CSV upload
- ✅ Image management
- **CSV Template**: name,country_code,city_name,address,star_rating,contact_email,contact_phone,is_active

**Usage**: Hotel booking service

---

### **🔵 LOW PRIORITY - Supporting Data**

#### 14. **Document Types Management** ⚠️ (Needs Implementation)
**Table**: `document_types` (NEW)
**Purpose**: Standardized document categories

**Schema**:
```php
Schema::create('document_types', function (Blueprint $table) {
    $table->id();
    $table->string('code', 50)->unique(); // passport, nid, certificate
    $table->string('name', 100);
    $table->string('name_bn', 100)->nullable();
    $table->text('description')->nullable();
    $table->string('icon', 50)->nullable(); // 🛂, 🪪, 📄
    $table->json('accepted_formats')->nullable(); // ['pdf', 'jpg', 'png']
    $table->integer('max_file_size_mb')->default(10);
    $table->boolean('requires_verification')->default(false);
    $table->boolean('is_active')->default(true);
    $table->timestamps();
});
```

**Features Needed**:
- ✅ CRUD operations
- ✅ Bulk CSV upload
- **CSV Template**: code,name,name_bn,description,icon,accepted_formats_json,max_file_size_mb,requires_verification,is_active

**Usage**: Document upload validation

---

#### 15. **Qualification Levels Management** ⚠️ (Needs Implementation)
**Table**: `qualification_levels` (NEW)
**Purpose**: Education level standards

**Schema**:
```php
Schema::create('qualification_levels', function (Blueprint $table) {
    $table->id();
    $table->string('name', 100);
    $table->string('name_bn', 100)->nullable();
    $table->integer('level')->unique(); // 1=Primary, 2=Secondary, 3=Bachelor, etc.
    $table->text('description')->nullable();
    $table->boolean('is_active')->default(true);
    $table->timestamps();
});
```

**Features Needed**:
- ✅ CRUD operations
- ✅ Bulk CSV upload
- **CSV Template**: name,name_bn,level,description,is_active

**Initial Data**: Primary, Secondary, Higher Secondary, Bachelor's, Master's, PhD, Diploma, Certificate

**Usage**: Education history, job requirements

---

#### 16. **Professions/Occupations Management** ⚠️ (Needs Implementation)
**Table**: `professions` (NEW)
**Purpose**: Job title standardization

**Schema**:
```php
Schema::create('professions', function (Blueprint $table) {
    $table->id();
    $table->string('name', 150);
    $table->string('name_bn', 150)->nullable();
    $table->string('slug', 150)->unique();
    $table->foreignId('category_id')->nullable()->constrained('job_categories')->nullOnDelete();
    $table->text('description')->nullable();
    $table->boolean('is_active')->default(true);
    $table->timestamps();
});
```

**Features Needed**:
- ✅ CRUD operations
- ✅ Bulk CSV upload
- **CSV Template**: name,name_bn,slug,category_slug,description,is_active

**Usage**: Profile profession, visa requirements by profession

---

#### 17. **Nationality Management** ⚠️ (Needs Implementation)
**Table**: `nationalities` (NEW)
**Purpose**: Citizenship reference

**Schema**:
```php
Schema::create('nationalities', function (Blueprint $table) {
    $table->id();
    $table->foreignId('country_id')->constrained()->cascadeOnDelete();
    $table->string('name', 100); // Bangladeshi, American
    $table->string('name_bn', 100)->nullable();
    $table->boolean('is_active')->default(true);
    $table->timestamps();
    
    $table->index('country_id');
});
```

**Features Needed**:
- ✅ CRUD operations
- ✅ Bulk CSV upload
- **CSV Template**: country_code,name,name_bn,is_active

**Usage**: User profiles, visa applications

---

#### 18. **Visa Processing Fees Management** ⚠️ (Needs Implementation)
**Table**: `visa_processing_fees` (NEW)
**Purpose**: Dynamic pricing by country/type

**Schema**:
```php
Schema::create('visa_processing_fees', function (Blueprint $table) {
    $table->id();
    $table->foreignId('country_id')->constrained()->cascadeOnDelete();
    $table->foreignId('visa_type_id')->constrained('visa_types')->cascadeOnDelete();
    $table->decimal('service_fee', 10, 2);
    $table->decimal('government_fee', 10, 2)->nullable();
    $table->decimal('urgent_fee', 10, 2)->nullable();
    $table->string('currency_code', 3)->default('BDT');
    $table->date('effective_from');
    $table->date('effective_to')->nullable();
    $table->boolean('is_active')->default(true);
    $table->timestamps();
    
    $table->unique(['country_id', 'visa_type_id', 'effective_from']);
});
```

**Features Needed**:
- ✅ CRUD operations
- ✅ Bulk CSV upload
- ✅ Historical pricing
- **CSV Template**: country_code,visa_type_code,service_fee,government_fee,urgent_fee,currency_code,effective_from,effective_to,is_active

**Usage**: Pricing calculation

---

## 🏗️ IMPLEMENTATION ARCHITECTURE

### **Backend Structure**

```
app/Http/Controllers/Admin/DataManagement/
├── CountryController.php          ✅
├── CurrencyController.php         ✅
├── LanguageController.php         ✅
├── LanguageTestController.php     ✅
├── VisaTypeController.php         ⚠️ NEW
├── JobCategoryController.php      ⚠️ NEW
├── SkillController.php            ⚠️ NEW
├── SkillCategoryController.php    ⚠️ NEW
├── CityController.php             ✅
├── AirportController.php          ⚠️ NEW
├── DocumentTypeController.php     ⚠️ NEW
├── QualificationLevelController.php ⚠️ NEW
├── ProfessionController.php       ⚠️ NEW
├── NationalityController.php      ⚠️ NEW
└── VisaProcessingFeeController.php ⚠️ NEW
```

### **Each Controller Must Have**:
1. `index()` - List with pagination, search, filter
2. `create()` - Show form
3. `store()` - Save single record
4. `show()` - View details
5. `edit()` - Edit form
6. `update()` - Update record
7. `destroy()` - Delete (soft delete preferred)
8. `bulkUpload()` - CSV upload form
9. `processBulkUpload()` - Process CSV
10. `export()` - Export to CSV
11. `toggleStatus()` - Enable/disable
12. `downloadTemplate()` - CSV template

---

### **Frontend Structure**

```
resources/js/Pages/Admin/DataManagement/
├── Countries/
│   ├── Index.vue
│   ├── Create.vue
│   ├── Edit.vue
│   └── BulkUpload.vue
├── Currencies/
│   └── [same structure]
├── Languages/
│   └── [same structure]
├── VisaTypes/
│   └── [same structure]
├── JobCategories/
│   └── [same structure]
├── Skills/
│   └── [same structure]
└── [... rest follow same pattern]
```

### **Shared Components**:
```
resources/js/Components/DataManagement/
├── DataTable.vue           // Reusable table with sorting
├── SearchFilter.vue        // Search & filter bar
├── BulkUploadModal.vue     // CSV upload modal
├── StatusToggle.vue        // Enable/disable switch
├── DeleteConfirmation.vue  // Delete modal
└── ExportButton.vue        // CSV export
```

---

## 📦 CSV UPLOAD FEATURES

### **Standard CSV Upload Flow**:
1. **Upload Screen**:
   - Drag & drop or file select
   - Download template button
   - Format instructions
   - Example data preview

2. **Validation**:
   - Column headers check
   - Data type validation
   - Required field check
   - Unique constraint check
   - Foreign key validation

3. **Preview**:
   - Show first 10 rows
   - Highlight errors in red
   - Valid rows in green
   - Error summary

4. **Processing**:
   - Batch insert (1000 rows at a time)
   - Progress bar
   - Error log download
   - Success count display

5. **Post-Upload**:
   - Redirect to list
   - Success message
   - Option to upload more

---

## 🔧 IMPLEMENTATION PHASES

### **Phase 1: Foundation (Week 1)** 🔴
- Countries ✅
- Currencies ✅
- Languages ✅
- Language Tests ✅
- Create base controller & view templates

### **Phase 2: Visa System (Week 2)** 🟠
- Visa Types ⚠️
- Visa Requirements enhancement
- Visa Processing Fees ⚠️

### **Phase 3: Job System (Week 3)** 🟡
- Job Categories ⚠️
- Skills ⚠️
- Skill Categories ⚠️
- Professions ⚠️

### **Phase 4: Geographic & Infrastructure (Week 4)** 🟢
- Cities enhancement
- Airports ⚠️
- Hotels enhancement

### **Phase 5: Supporting Data (Week 5)** 🔵
- Document Types ⚠️
- Qualification Levels ⚠️
- Nationalities ⚠️

---

## 🎯 SUCCESS METRICS

- ✅ All 18 data management modules operational
- ✅ CSV upload working for all modules
- ✅ CSV export working for all modules
- ✅ Search & filter functional
- ✅ Bulk operations available
- ✅ Admin can manage 10,000+ records easily
- ✅ Upload 1000 records in < 30 seconds
- ✅ Zero data loss during bulk operations

---

## 📝 NEXT IMMEDIATE ACTIONS

1. **Create base controller trait**:
   ```php
   trait BulkUploadable {
       public function bulkUpload() { }
       public function processBulkUpload() { }
       public function downloadTemplate() { }
       public function export() { }
   }
   ```

2. **Create base Vue component**:
   - DataManagementLayout.vue
   - Reusable table component

3. **Implement Phase 1 enhancements**:
   - Add CSV upload to existing tables
   - Create admin routes
   - Build admin UI

4. **Create migration files for new tables**:
   - visa_types
   - job_categories
   - skills
   - skill_categories
   - airports
   - document_types
   - qualification_levels
   - professions
   - nationalities
   - visa_processing_fees

**Would you like me to start implementing Phase 1 (Countries & Currencies management) with full CRUD + CSV upload?**
