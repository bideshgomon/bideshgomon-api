# Plugin-Based Service Architecture - BideshGomon Platform

## 🎯 Vision

Transform BideshGomon from a hardcoded service platform into a **dynamic, plugin-based architecture** where every service (Visas, Jobs, Travel, Consultancy) is a configurable module with:

- **Zero Redundancy**: Users fill their profile once, apply for services with one click
- **Admin Power**: Launch new services without developer intervention
- **Smart Auto-Fill**: Forms pre-populate from user profiles automatically
- **Unified Dashboard**: Single "My Activities" view for all applications

---

## 🏗️ Architecture Overview

### The Plugin Concept

Every service is treated as a **"Service Module"** with:
1. **Dynamic Form Fields**: Configured in database, not code
2. **Profile Mapping**: Each field can auto-fill from user profile
3. **Polymorphic Storage**: All applications in one table
4. **Smart Validation**: Rules configured per-field

### The "Smart Bridge" (DataMapperService)

This is the core innovation - when a user clicks "Apply":
```
1. System fetches form structure from service_form_fields
2. For each field with profile_map_key, fetch value from user's profile
3. Return pre-filled form to frontend
4. User reviews, edits if needed, submits
5. Optionally: User can save edited data back to profile
```

---

## 📊 Database Architecture

### Core Tables

#### 1. `service_categories`
Groups services into categories (Immigration, Jobs, Travel, Education)
```sql
id, name, slug, description, icon, color, sort_order, is_active, config
```

#### 2. `service_modules` (Extended)
The "plugins" - each service is a row
```sql
-- Existing columns
id, service_category_id, name, slug, description, is_active, 
is_featured, coming_soon, service_type, sort_order

-- NEW columns added by migration
config (JSON)           -- Flexible service configuration
requires_approval       -- Some services auto-approve (flights vs visas)
processing_days         -- Expected turnaround time
settings (JSON)         -- Module-specific settings
```

#### 3. `service_form_fields` (NEW) ⭐
**The heart of the plugin system** - defines form inputs for each service
```sql
id, service_module_id, field_name, field_label, field_type,
placeholder, help_text, default_value,

-- Validation
is_required, validation_rules, min_length, max_length,

-- 🔥 CRITICAL: Profile Mapping (The "Smart Bridge")
profile_map_key         -- "user_profiles.passport_number"
profile_map_table       -- "user_profiles"
profile_map_column      -- "passport_number"

-- Field Options
options (JSON)          -- For select/radio/checkbox
allow_multiple          -- For checkboxes
accepted_file_types     -- For file uploads
max_file_size           -- File size limit

-- Conditional Logic
conditional_rules (JSON) -- Show/hide based on other fields
-- Example: {"show_if": {"field": "travel_type", "value": "multiple"}}

-- Layout & Organization
sort_order, group_name, section_name, column_width, css_class,
is_active, is_readonly, created_at, updated_at
```

#### 4. `applications` (Uses existing `service_applications`)
**Polymorphic storage** - all service applications in one table
```sql
-- Existing columns
id, user_id, service_module_id, application_number, status,
application_data (JSON), submitted_at, reviewed_at, approved_at

-- NEW columns added
form_data (JSON)        -- The actual form submissions
profile_snapshot (JSON) -- User profile at time of application

-- Status Workflow
status: draft → pending → under_review → additional_info
        → approved/rejected → completed
```

#### 5. `application_documents` (NEW)
File uploads attached to applications
```sql
id, application_id, field_name, document_type, file_path,
original_filename, mime_type, file_size, notes,
is_verified, verified_by, verified_at
```

#### 6. `application_status_history` (NEW)
Audit trail for status changes
```sql
id, application_id, changed_by, from_status, to_status, 
notes, metadata (JSON), created_at
```

#### 7. `profile_field_mappings` (NEW)
Admin reference of all mappable profile fields
```sql
id, table_name, column_name, display_name, data_type,
field_category, description, is_sensitive, is_active
```

---

## 🧩 Laravel Services Architecture

### 1. DataMapperService (The Smart Bridge)

**Purpose**: Auto-fill forms from user profiles

**Key Methods**:
```php
// Get form with pre-filled data
getFormWithData(ServiceModule $service, User $user): array

// Validate form submission
validateFormData(ServiceModule $service, array $formData): array

// Update profile from form data (reverse sync)
updateProfileFromFormData(User $user, ServiceModule $service, 
    array $formData, array $fieldsToUpdate): bool

// Get available profile fields for admin mapping UI
getAvailableProfileFields(): array
```

**Example Output**:
```php
[
    'service' => ['id' => 5, 'name' => 'Tourist Visa - USA'],
    'fields' => [
        [
            'name' => 'passport_number',
            'label' => 'Passport Number',
            'type' => 'text',
            'value' => 'A12345678',  // ✨ Pre-filled from profile
            'is_required' => true,
            'is_prefilled' => true,
            'has_profile_mapping' => true,
            'profile_map_key' => 'user_passports.passport_number'
        ],
        [
            'name' => 'travel_date',
            'label' => 'Date of Travel',
            'type' => 'date',
            'value' => '',  // Empty - no profile mapping
            'is_required' => true,
            'is_prefilled' => false
        ]
    ],
    'groups' => [
        'Personal Information' => [...],
        'Travel Details' => [...]
    ]
]
```

### 2. ServiceApplicationService

**Purpose**: Handle application lifecycle

**Key Methods**:
```php
// Create new application (draft or submitted)
createApplication(User $user, ServiceModule $service, 
    array $formData, array $files, bool $isDraft): ServiceApplication

// Submit draft application
submitDraftApplication(ServiceApplication $application): ServiceApplication

// Change status (admin)
changeStatus(ServiceApplication $application, string $newStatus, 
    ?string $notes, ?User $changedBy): ServiceApplication

// Sync profile from application (when user wants to update profile)
syncProfileFromApplication(ServiceApplication $application, 
    array $fieldsToUpdate): bool
```

---

## 🎨 Frontend Architecture (To Build)

### Universal ServiceApplicationForm.vue Component

**Core Concept**: Single component renders ANY service form dynamically

```vue
<template>
  <div class="application-form">
    <form @submit.prevent="submitApplication">
      <!-- Dynamic Field Rendering -->
      <div v-for="group in groupedFields" :key="group.name" 
           class="field-group">
        <h3>{{ group.name }}</h3>
        
        <div v-for="field in group.fields" :key="field.id" 
             v-show="shouldShowField(field)"
             :class="`col-span-${field.column_width}`">
          
          <!-- Text Input -->
          <input v-if="field.type === 'text'" 
                 v-model="formData[field.name]"
                 :placeholder="field.placeholder"
                 :required="field.is_required"
                 :readonly="field.is_readonly">
          
          <!-- Date Input -->
          <input v-else-if="field.type === 'date'" 
                 type="date"
                 v-model="formData[field.name]"
                 :required="field.is_required">
          
          <!-- Select Dropdown -->
          <select v-else-if="field.type === 'select'"
                  v-model="formData[field.name]"
                  :required="field.is_required">
            <option v-for="option in field.options" 
                    :key="option.value" 
                    :value="option.value">
              {{ option.label }}
            </option>
          </select>
          
          <!-- File Upload -->
          <input v-else-if="field.type === 'file'"
                 type="file"
                 @change="handleFileUpload(field.name, $event)"
                 :accept="field.accepted_file_types"
                 :required="field.is_required">
          
          <!-- Help Text -->
          <p v-if="field.help_text" class="text-sm text-gray-500">
            {{ field.help_text }}
          </p>
          
          <!-- Pre-filled Indicator -->
          <span v-if="field.is_prefilled" 
                class="text-xs text-green-600">
            ✓ Auto-filled from your profile
          </span>
        </div>
      </div>
      
      <!-- Profile Update Checkbox -->
      <div v-if="hasUpdatableFields" class="mt-6">
        <label>
          <input type="checkbox" v-model="updateProfile">
          Save changed information to my profile for future applications
        </label>
        
        <!-- Select which fields to update -->
        <div v-if="updateProfile" class="ml-4 mt-2">
          <label v-for="field in updatableFields" :key="field.name">
            <input type="checkbox" 
                   :value="field.name"
                   v-model="fieldsToUpdateInProfile">
            Update {{ field.label }}
          </label>
        </div>
      </div>
      
      <!-- Actions -->
      <div class="flex gap-4 mt-6">
        <button type="button" @click="saveDraft" 
                class="btn-secondary">
          Save as Draft
        </button>
        <button type="submit" class="btn-primary">
          Submit Application
        </button>
      </div>
    </form>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue'
import { useForm } from '@inertiajs/vue3'

const props = defineProps({
  service: Object,
  formStructure: Object  // From DataMapperService
})

const formData = ref({})
const updateProfile = ref(false)
const fieldsToUpdateInProfile = ref([])

// Initialize with pre-filled values
onMounted(() => {
  props.formStructure.fields.forEach(field => {
    formData.value[field.name] = field.value || field.default_value || ''
  })
})

// Group fields by group_name
const groupedFields = computed(() => {
  return Object.entries(props.formStructure.groups).map(([name, fields]) => ({
    name,
    fields
  }))
})

// Conditional logic - show/hide fields
function shouldShowField(field) {
  if (!field.conditional_rules) return true
  
  const rules = field.conditional_rules
  if (rules.show_if) {
    const { field: dependentField, value } = rules.show_if
    return formData.value[dependentField] === value
  }
  
  return true
}

// Handle file uploads
function handleFileUpload(fieldName, event) {
  formData.value[fieldName] = event.target.files[0]
}

// Updatable fields (user modified + has profile mapping)
const updatableFields = computed(() => {
  return props.formStructure.fields.filter(field => 
    field.has_profile_mapping && 
    formData.value[field.name] !== field.value
  )
})

const hasUpdatableFields = computed(() => updatableFields.value.length > 0)

// Submit application
function submitApplication() {
  const form = useForm({
    form_data: formData.value,
    update_profile: updateProfile.value,
    fields_to_update: fieldsToUpdateInProfile.value
  })
  
  form.post(route('applications.store', { service: props.service.id }))
}

// Save as draft
function saveDraft() {
  const form = useForm({
    form_data: formData.value,
    is_draft: true
  })
  
  form.post(route('applications.draft', { service: props.service.id }))
}
</script>
```

### Service Show Page (Universal)

```vue
<!-- resources/js/Pages/Services/Show.vue -->
<template>
  <div class="service-page">
    <h1>{{ service.name }}</h1>
    <p>{{ service.description }}</p>
    
    <div class="service-details">
      <span>Processing Time: {{ service.processing_days }} days</span>
      <span>Base Price: {{ formatCurrency(service.base_price) }}</span>
    </div>
    
    <!-- THE MAGIC: Universal Form Component -->
    <ServiceApplicationForm 
      :service="service"
      :formStructure="formStructure" />
  </div>
</template>

<script setup>
import ServiceApplicationForm from '@/Components/ServiceApplicationForm.vue'

const props = defineProps({
  service: Object,        // Service details
  formStructure: Object   // Pre-filled form from DataMapperService
})
</script>
```

---

## 🔧 Admin UI Features (To Build)

### 1. Service Management

**Page**: `/admin/services`

Features:
- List all services with categories
- Drag-and-drop sequencing (update `sort_order`)
- Toggle `is_active`, `is_featured`, `coming_soon`
- Click to edit service details

### 2. Form Builder

**Page**: `/admin/services/{service}/fields`

Features:
- Add/Edit/Delete form fields
- Drag-and-drop field ordering
- Field types dropdown (text, date, file, select, etc.)
- **Profile Mapper UI**: Dropdown showing available profile fields
  ```
  [ Profile Mapping ]
  Connect to user profile: [ Dropdown: user_profiles.passport_number ▼ ]
  ```
- Validation rules builder
- Conditional logic editor
- Preview form in real-time

### 3. Applications Dashboard

**Page**: `/admin/applications`

Features:
- List all applications across ALL services
- Filters: Service, Status, Date Range
- Bulk actions: Approve, Reject, Request Info
- Click to view application details
- Timeline view of status changes

---

## 🚀 API Routes (To Build)

### Public Routes (User-Facing)

```php
// Get all active services
GET /api/services

// Get services by category
GET /api/services/category/{slug}

// Get service details with form structure
GET /api/services/{slug}/form

// Submit application
POST /api/services/{slug}/apply

// Save as draft
POST /api/services/{slug}/draft

// Get user's applications
GET /api/my-applications

// Get specific application
GET /api/applications/{number}
```

### Admin Routes

```php
// Service Management
GET /admin/services
POST /admin/services
PUT /admin/services/{id}
DELETE /admin/services/{id}
PUT /admin/services/reorder

// Form Fields
GET /admin/services/{id}/fields
POST /admin/services/{id}/fields
PUT /admin/services/{id}/fields/{fieldId}
DELETE /admin/services/{id}/fields/{fieldId}

// Profile Mapper
GET /admin/profile-fields

// Applications
GET /admin/applications
GET /admin/applications/{id}
PUT /admin/applications/{id}/status
```

---

## 📦 Seeder Strategy (To Build)

### Migrate Existing Data

We need to create seeders that:

1. **Map existing visas to service_modules** (if not already there)
2. **Create form fields for each service**
3. **Set up profile mappings**

Example for Tourist Visa:

```php
$touristVisa = ServiceModule::create([
    'service_category_id' => $immigrationCategory->id,
    'name' => 'Tourist Visa Application',
    'slug' => 'tourist-visa',
    'description' => 'Apply for tourist visa to your destination country',
    'is_active' => true,
    'requires_approval' => true,
    'processing_days' => 15,
]);

// Create form fields
ServiceFormField::create([
    'service_module_id' => $touristVisa->id,
    'field_name' => 'passport_number',
    'field_label' => 'Passport Number',
    'field_type' => 'text',
    'is_required' => true,
    'profile_map_key' => 'user_passports.passport_number',
    'sort_order' => 1,
    'group_name' => 'Passport Information',
]);

ServiceFormField::create([
    'service_module_id' => $touristVisa->id,
    'field_name' => 'travel_date',
    'field_label' => 'Intended Travel Date',
    'field_type' => 'date',
    'is_required' => true,
    'sort_order' => 2,
    'group_name' => 'Travel Details',
]);

// No profile mapping for travel_date - user must input manually
```

---

## 🎯 Use Cases

### User Journey: Applying for a Visa

**Old Way (Hardcoded)**:
1. Click "Apply for USA Tourist Visa"
2. Fill 50 fields manually (Passport, Name, Address, etc.)
3. Submit
4. Apply for another visa? Fill 50 fields again! 😫

**New Way (Plugin-Based)**:
1. User fills profile once (during registration or later)
2. Click "Apply for USA Tourist Visa"
3. Form appears PRE-FILLED with data from profile ✨
4. User reviews, adds travel date (not in profile), submits
5. Checkbox: "Save travel date to profile for next time?" ✅
6. Next visa application? 90% pre-filled! 🎉

### Admin Journey: Launching "Hajj Package" Service

**Old Way**:
1. Call developer
2. Developer codes new form in Vue
3. Developer codes validation in backend
4. Developer creates database table
5. Deploy after 1 week

**New Way**:
1. Admin goes to `/admin/services`
2. Click "Add Service" → Enter "Hajj Package 2026"
3. Add fields:
   - Passport Number → Map to `user_passports.passport_number`
   - Age → Map to `user_profiles.date_of_birth` (auto-calculate)
   - Pilgrim Type → Dropdown: ["First Time", "Repeat"]
4. Save
5. Service is LIVE in 5 minutes! 🚀

---

## 🔐 Security Considerations

1. **Profile Data Privacy**
   - Only user can see their profile data
   - Admins see application data, not full profile
   - `profile_snapshot` stored for audit purposes

2. **File Uploads**
   - Validate file types and sizes
   - Store in secure location (`storage/app/applications`)
   - Scan for viruses (future enhancement)

3. **Authorization**
   - User can only view their own applications
   - Admin can view all applications
   - Agencies can view assigned applications

---

## 📈 Performance Optimizations

1. **Caching**
   - Cache service form structures (rarely change)
   - Cache profile field mappings

2. **Eager Loading**
   ```php
   $applications = ServiceApplication::with([
       'user', 'serviceModule', 'documents', 'statusHistory'
   ])->paginate(20);
   ```

3. **Database Indexing**
   - Index on `service_form_fields.profile_map_key`
   - Index on `applications.status`
   - Index on `applications.user_id`

---

## 🧪 Testing Strategy

### Unit Tests
- DataMapperService field mapping logic
- Validation rules generation
- Profile update logic

### Feature Tests
- Application submission flow
- Draft save and submit
- Status changes
- File uploads

### Browser Tests (Dusk)
- Complete user journey: Profile → Apply → Submit
- Admin: Create service → Add fields → Approve application

---

## 🚀 Migration Path

### Phase 1: Backend Foundation ✅ DONE
- [x] Database migrations
- [x] Models created
- [x] DataMapperService built
- [x] ServiceApplicationService built

### Phase 2: Admin Controllers (Current)
- [ ] ServiceController (CRUD)
- [ ] ServiceFormFieldController (CRUD)
- [ ] ApplicationController (status management)
- [ ] ProfileFieldMappingController (reference data)

### Phase 3: Frontend Components
- [ ] ServiceApplicationForm.vue
- [ ] Service Show page
- [ ] My Applications dashboard

### Phase 4: Admin UI
- [ ] Service management page
- [ ] Form builder interface
- [ ] Application review dashboard

### Phase 5: Data Migration
- [ ] Seeder for existing services
- [ ] Migrate old applications data

### Phase 6: Testing & Launch
- [ ] End-to-end testing
- [ ] User training
- [ ] Gradual rollout

---

## 💡 Future Enhancements

1. **Multi-Language Forms**
   - Store field labels in translations table
   - Support Bengali and English

2. **Payment Integration**
   - Dynamic pricing based on service config
   - Payment before submission

3. **AI Auto-Fill**
   - OCR for passport scans
   - Extract data automatically

4. **Workflow Builder**
   - Visual approval workflows
   - Multiple reviewers

5. **Analytics Dashboard**
   - Most popular services
   - Conversion rates
   - Average processing time

---

## 🎓 Key Takeaways

1. **Zero Hardcoding**: Forms are data, not code
2. **Single Source of Truth**: User profile powers everything
3. **Admin Empowerment**: Launch services without developers
4. **User Delight**: One-click applications
5. **Future-Proof**: Scale to 100+ services without code changes

---

**Status**: Phase 1-3 Complete | Phase 4 In Progress  
**Last Updated**: December 1, 2025  
**Architecture**: Plugin-Based Modular System  
**Core Innovation**: Smart Data Bridge (DataMapperService)
