# Quick Start: Using New Architectural Standards

**Last Updated:** December 2025  
**Audience:** Frontend & Backend Developers

---

## 🎯 TL;DR

1. **Run audit weekly:** `php scripts/architectural-audit.php`
2. **Use safe data in Vue:** `import { useSafeData } from '@/Composables/useSafeData'`
3. **Wrap forms:** Use `<SmartForm>` component
4. **Show errors to users:** Never silent catch blocks

---

## 🔍 Example 1: Audit Your Database (Phase 1)

### When to Use
- Before every major release
- After adding new models
- When you see "Table doesn't exist" errors

### How to Run
```powershell
cd c:\xampp\htdocs\bgplatfrom-new\bideshgomon-api
php scripts/architectural-audit.php
```

### Expected Output
```
🔍 PHASE 1: DATABASE & MODEL SYNCHRONIZATION AUDIT
======================================================================

📦 Scanning Models...
   ✅ Found 116 models

🗄️  Scanning Database Tables...
   ✅ Found 124 tables

📋 Scanning Migrations...
   ✅ Found 138 migrations

======================================================================
📊 GAP ANALYSIS REPORT
======================================================================

Summary:
  🔴 CRITICAL: 0 issue(s)
  🟠 HIGH: 1 issue(s)
  🟡 MEDIUM: 3 issue(s)
  🟢 LOW: 6 issue(s)

📄 Full report saved to: docs/ARCHITECTURAL_AUDIT_REPORT.md
✅ Audit complete!
```

### What to Do With Results

**🔴 CRITICAL (Table doesn't exist):**
```powershell
# 1. Create migration
php artisan make:migration create_table_name_table

# 2. Open migration file, add schema:
Schema::create('table_name', function (Blueprint $table) {
    $table->id();
    // ... add columns from Model's $fillable array
    $table->timestamps();
});

# 3. Run migration
php artisan migrate --path=database/migrations/YYYY_MM_DD_XXXXXX_create_table_name_table.php
```

**🟠 HIGH (Migration missing):**
Same as CRITICAL.

**🟡 MEDIUM (Orphaned table):**
Either create a Model or drop the table if unused.

**🟢 LOW (Naming mismatch):**
Intentional - document why (e.g., `user_educations` vs `user_education`).

---

## 🛡️ Example 2: Safe Data Access (Phase 2)

### When to Use
- Displaying user-provided data
- Accessing API responses
- Working with route props/params
- Any external data source

### ❌ Before (Error-Prone)
```vue
<script setup>
const props = defineProps(['user', 'posts'])

// Problem: Crashes if user is null
const displayName = props.user.profile.name.toUpperCase()

// Problem: Runtime error if posts is null
const postCount = props.posts.length

// Problem: Crashes on null
const bio = props.user.bio.replace('\n', '<br>')
</script>

<template>
  <h1>{{ displayName }}</h1>
  <p>{{ postCount }} posts</p>
  <div v-html="bio"></div>
</template>
```

### ✅ After (Bulletproof)
```vue
<script setup>
import { useSafeData } from '@/Composables/useSafeData'
const { safeString, safeArray, safeGet, safeReplace } = useSafeData()

const props = defineProps(['user', 'posts'])

// Never crashes - returns 'ANONYMOUS' if user/profile/name is null
const displayName = safeString(safeGet(props.user, 'profile.name', 'Anonymous')).toUpperCase()

// Never crashes - returns 0 if posts is null
const postCount = safeArray(props.posts).length

// Never crashes - returns empty string if bio is null
const bio = safeReplace(safeGet(props.user, 'bio', ''), '\n', '<br>')
</script>

<template>
  <h1>{{ displayName }}</h1>
  <p>{{ postCount }} posts</p>
  <div v-html="bio"></div>
</template>
```

### All Available Functions
```javascript
import { useSafeData } from '@/Composables/useSafeData'
const {
  // Core accessors
  safeString,      // (value, fallback='') - Always returns string
  safeNumber,      // (value, fallback=0) - Returns 0 for NaN
  safeArray,       // (value, fallback=[]) - Always returns array
  safeObject,      // (value, fallback={}) - Always returns object
  safeBoolean,     // (value, fallback=false) - Handles 'true'/'false' strings
  
  // Advanced utilities
  safeGet,         // (obj, 'path.to.prop', fallback) - Deep property access
  safeReplace,     // (str, search, replace) - Null-safe replace
  safeSplit,       // (str, delimiter=',') - Returns array
  safeDate,        // (value, fallback='N/A') - Formats dates
  safeCurrency,    // (value, currency='৳', fallback='৳0.00') - BD currency
  
  // Validators
  isTruthy,        // (value) - Check if truthy
  isEmpty,         // (value) - Check if empty
} = useSafeData()
```

### Real-World Example: User Profile Card
```vue
<script setup>
import { useSafeData } from '@/Composables/useSafeData'
const { safeString, safeGet, safeCurrency, safeDate } = useSafeData()

const props = defineProps(['user'])

const fullName = safeString(safeGet(props.user, 'profile.full_name', 'Unknown User'))
const email = safeString(props.user?.email, 'No email')
const phone = safeString(props.user?.phone, 'No phone')
const balance = safeCurrency(safeGet(props.user, 'wallet.balance'))
const joinDate = safeDate(props.user?.created_at)
</script>

<template>
  <div class="profile-card">
    <h2>{{ fullName }}</h2>
    <p>Email: {{ email }}</p>
    <p>Phone: {{ phone }}</p>
    <p>Balance: {{ balance }}</p>
    <p>Member since: {{ joinDate }}</p>
  </div>
</template>
```

---

## 📝 Example 3: Smart Forms (Phase 3)

### When to Use
- User profile editing
- Admin forms
- Data entry forms
- Any form with validation

### ❌ Before (Boilerplate Hell)
```vue
<script setup>
import { useForm } from '@inertiajs/vue3'

const form = useForm({
    name: '',
    email: ''
})

const submit = () => {
    form.post(route('profile.update'))
}
</script>

<template>
  <form @submit.prevent="submit">
    <!-- Loading indicator -->
    <div v-if="form.processing" class="loading">Saving...</div>
    
    <!-- Success message -->
    <div v-if="form.recentlySuccessful" class="success">
      Saved successfully!
    </div>
    
    <!-- Error summary -->
    <div v-if="Object.keys(form.errors).length > 0" class="error-summary">
      <h3>Please fix the following errors:</h3>
      <ul>
        <li v-for="(error, field) in form.errors" :key="field">
          {{ field }}: {{ error }}
        </li>
      </ul>
    </div>
    
    <!-- Form fields -->
    <div>
      <label>Name</label>
      <input v-model="form.name" />
      <span v-if="form.errors.name" class="error">{{ form.errors.name }}</span>
    </div>
    
    <div>
      <label>Email</label>
      <input v-model="form.email" type="email" />
      <span v-if="form.errors.email" class="error">{{ form.errors.email }}</span>
    </div>
    
    <!-- Actions -->
    <button type="submit" :disabled="form.processing">
      {{ form.processing ? 'Saving...' : 'Save Changes' }}
    </button>
  </form>
</template>
```

### ✅ After (Clean & Consistent)
```vue
<script setup>
import { useForm } from '@inertiajs/vue3'
import SmartForm from '@/Components/SmartForm.vue'
import TextInput from '@/Components/TextInput.vue'
import PrimaryButton from '@/Components/PrimaryButton.vue'

const form = useForm({
    name: '',
    email: ''
})

const submit = () => {
    form.post(route('profile.update'))
}
</script>

<template>
  <SmartForm 
    @submit="submit"
    :errors="form.errors"
    :success="form.recentlySuccessful"
    successMessage="Profile updated successfully!"
  >
    <!-- Form fields -->
    <TextInput 
      v-model="form.name" 
      label="Name" 
      name="name"
      :error="form.errors.name"
    />
    
    <TextInput 
      v-model="form.email" 
      label="Email" 
      name="email" 
      type="email"
      :error="form.errors.email"
    />
    
    <!-- Actions slot -->
    <template #actions>
      <PrimaryButton type="submit" :disabled="form.processing">
        Save Changes
      </PrimaryButton>
    </template>
  </SmartForm>
</template>
```

### What SmartForm Does Automatically
✅ Shows loading overlay during submission  
✅ Displays success banner (auto-hides after 5s)  
✅ Lists ALL validation errors in one place  
✅ Formats field names (snake_case → Title Case)  
✅ Handles form state (isSubmitting, errors, success)  
✅ Smooth transitions (fade-in/out)

### Customization Options
```vue
<SmartForm 
  @submit="handleSubmit"
  :errors="form.errors"
  :success="form.recentlySuccessful"
  :loading="form.processing"
  successMessage="Custom success message!"
  loadingMessage="Please wait while we process..."
>
  <!-- Your form fields -->
</SmartForm>
```

---

## 🚨 Example 4: Never Silent Failures

### ❌ Bad: Silent Catch Block
```javascript
const handleSubmit = async () => {
    try {
        await axios.post('/api/save', data)
    } catch (error) {
        console.error('Save failed', error) // ❌ User sees nothing!
    }
}
```

### ✅ Good: User-Facing Error
```javascript
const handleSubmit = async () => {
    try {
        isLoading.value = true
        await axios.post('/api/save', data)
        alert('Saved successfully!') // ✅ User feedback
        router.visit(route('dashboard'))
    } catch (error) {
        console.error('Save failed', error)
        
        // ✅ Show error to user
        if (error.response?.status === 422) {
            alert('Validation failed. Please check your inputs.')
        } else {
            alert('Save failed. Please try again.')
        }
    } finally {
        isLoading.value = false
    }
}
```

### Better: Use SmartForm
SmartForm handles all of this automatically! Just pass errors and success props.

---

## 🔧 Example 5: Creating Migrations from Models

### Step 1: Find Model with Missing Table
```powershell
php scripts/architectural-audit.php
# Output shows:
# 🔴 App\Models\ExampleModel
#    Table: example_table
#    Issues:
#       - [CRITICAL] Table 'example_table' does not exist in database
```

### Step 2: Check Model Structure
```php
// app/Models/ExampleModel.php
class ExampleModel extends Model
{
    protected $fillable = [
        'user_id',
        'title',
        'description',
        'status',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
```

### Step 3: Create Migration
```powershell
php artisan make:migration create_example_table
```

### Step 4: Populate Migration
```php
// database/migrations/YYYY_MM_DD_XXXXXX_create_example_table.php
public function up(): void
{
    Schema::create('example_table', function (Blueprint $table) {
        $table->id();
        $table->foreignId('user_id')->constrained()->onDelete('cascade');
        $table->string('title', 200);
        $table->text('description')->nullable();
        $table->enum('status', ['pending', 'active', 'completed'])->default('pending');
        $table->boolean('is_active')->default(true);
        $table->timestamps();

        $table->index('user_id');
        $table->index('status');
    });
}
```

### Step 5: Run Migration
```powershell
php artisan migrate --path=database/migrations/YYYY_MM_DD_XXXXXX_create_example_table.php
```

### Step 6: Verify
```powershell
php scripts/architectural-audit.php
# Should now show 0 CRITICAL issues for ExampleModel
```

---

## 📅 Weekly Maintenance Checklist

### Monday Morning Routine
```powershell
# 1. Pull latest changes
git pull origin main

# 2. Install dependencies if changed
composer install
npm install

# 3. Run migrations
php artisan migrate

# 4. Run architectural audit
php scripts/architectural-audit.php

# 5. Review audit report
code docs/ARCHITECTURAL_AUDIT_REPORT.md

# 6. Fix CRITICAL issues immediately
# 7. Schedule HIGH issues for this week
# 8. Add MEDIUM issues to backlog
```

### Before Every Release
```powershell
# 1. Run audit
php scripts/architectural-audit.php

# 2. Ensure no CRITICAL or HIGH issues
# Result should be:
#   🔴 CRITICAL: 0 issue(s)
#   🟠 HIGH: 0 issue(s)

# 3. Compile assets
npm run build

# 4. Clear caches
php artisan config:clear
php artisan route:clear
php artisan cache:clear

# 5. Run tests
php artisan test

# 6. Tag release
git tag v1.x.x
git push --tags
```

---

## 🆘 Troubleshooting

### "Table doesn't exist" Error
```
1. Run: php scripts/architectural-audit.php
2. Find the missing table in CRITICAL section
3. Create migration as shown in Example 5
4. Run migration
5. Re-run audit to verify
```

### "Property of undefined" in Vue
```
1. Import useSafeData composable
2. Replace direct property access with safeGet()
3. Example:
   BEFORE: const name = user.profile.name
   AFTER:  const name = safeGet(user, 'profile.name', 'Unknown')
```

### Form Submits but No Feedback
```
1. Wrap form with <SmartForm>
2. Pass :errors and :success props
3. SmartForm will automatically show loading, success, and error states
```

### "Migration already run"
```powershell
# Check migration status
php artisan migrate:status

# If showing as run but table missing, rollback specific migration:
php artisan migrate:rollback --step=1

# Then re-run:
php artisan migrate
```

---

## 📚 Additional Resources

- **Detailed Standards:** `docs/ARCHITECTURAL_STANDARDS.md`
- **Audit Report:** `docs/ARCHITECTURAL_AUDIT_REPORT.md` (auto-generated)
- **Session Summary:** `docs/summaries/ARCHITECTURAL_OVERHAUL_SESSION_SUMMARY.md`
- **Project Instructions:** `.github/copilot-instructions.md`
- **All Docs Index:** `docs/INDEX.md`

---

## 💬 Need Help?

1. Check `docs/ARCHITECTURAL_STANDARDS.md` for detailed patterns
2. Run audit script: `php scripts/architectural-audit.php`
3. Review Laravel logs: `storage/logs/laravel.log`
4. Check migration status: `php artisan migrate:status`

---

**Last Updated:** December 2025  
**Maintained by:** Development Team  
**Questions?** See docs/ folder or contact team lead
