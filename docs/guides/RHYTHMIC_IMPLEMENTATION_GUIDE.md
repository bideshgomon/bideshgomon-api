# Rhythmic Design System - Implementation Guide
**Quick Reference for Applying Design to Existing Pages**

---

## 🎯 Goal: Systematically Apply Rhythmic Design to All Pages

This guide shows how to convert existing pages to use the new rhythmic components.

---

## 📋 Page-by-Page Implementation Plan

### 1. User Dashboard (Priority 1)

**Current File**: `resources/js/Pages/Dashboard.vue`

**Before** (Lines 25-65):
```vue
<!-- Old card structure -->
<div class="bg-white rounded-2xl p-8 shadow">
  <div class="w-14 h-14 rounded-xl bg-gradient-to-br from-blue-500 to-blue-600">
    <AcademicCapIcon class="h-7 w-7 text-white" />
  </div>
  <h3 class="text-xl font-bold">Education</h3>
  <p class="text-gray-600">Manage your academic records</p>
</div>
```

**After** (Convert to RhythmicCard):
```vue
<RhythmicCard
  title="Education"
  description="Manage your academic records"
  :badge="stats.education_count || 0"
  variant="ocean"
  size="md"
>
  <template #icon>
    <AcademicCapIcon class="h-6 w-6 text-ocean-600" />
  </template>
  <template #footer>
    <Link :href="route('profile.edit', { section: 'education' })">
      <FlowButton variant="ocean" size="sm" fullWidth>
        View Education
      </FlowButton>
    </Link>
  </template>
</RhythmicCard>
```

**Steps to Implement**:
1. Import rhythmic components at top of `<script setup>`:
   ```vue
   import RhythmicCard from '@/Components/Rhythmic/RhythmicCard.vue';
   import FlowButton from '@/Components/Rhythmic/FlowButton.vue';
   ```

2. Replace service card loop (lines 25-95) with:
   ```vue
   <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-rhythm-lg">
     <RhythmicCard
       v-for="service in services"
       :key="service.id"
       :title="service.name"
       :description="service.description"
       :badge="service.badge || null"
       :variant="getServiceVariant(service.id)"
       size="md"
     >
       <template #icon>
         <component :is="service.icon" class="h-6 w-6" />
       </template>
       <template #footer>
         <Link :href="route(service.route, service.params)">
           <FlowButton variant="ocean" size="sm" fullWidth>
             Manage {{ service.name }}
           </FlowButton>
         </Link>
       </template>
     </RhythmicCard>
   </div>
   ```

3. Add variant mapper in `<script>`:
   ```vue
   const getServiceVariant = (id) => {
     const variants = {
       education: 'ocean',
       experience: 'sky',
       skills: 'sunrise',
       travel: 'growth',
       family: 'heritage',
       financial: 'gold',
       languages: 'ocean',
       security: 'sunrise',
     };
     return variants[id] || 'default';
   };
   ```

4. Wrap profile completion section in AnimatedSection:
   ```vue
   <AnimatedSection
     title="Complete Your Profile"
     subtitle="Fill out all sections to increase your chances"
     variant="light"
     container="default"
   >
     <!-- Profile completion progress -->
     <ProgressWave
       :steps="profileSteps"
       :currentStep="profileCompletion / 12"
       variant="growth"
       :showPercentage="true"
     />
   </AnimatedSection>
   ```

---

### 2. Service Listings

**Example File**: `resources/js/Pages/Services/Visa/Index.vue` (or similar)

**Convert Service Grid**:

**Before**:
```vue
<div class="grid md:grid-cols-3 gap-6">
  <div v-for="visa in visas" class="bg-white rounded-lg p-6 shadow">
    <h3 class="font-bold">{{ visa.name }}</h3>
    <p class="text-gray-600">{{ visa.description }}</p>
    <button class="mt-4 px-4 py-2 bg-blue-600 text-white rounded">
      Apply Now
    </button>
  </div>
</div>
```

**After**:
```vue
<AnimatedSection
  title="Visa Application Services"
  subtitle="Apply for visas to 50+ countries with expert guidance"
  badge="Available Services"
  variant="ocean"
  :animated="true"
>
  <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-rhythm-lg">
    <RhythmicCard
      v-for="visa in visas"
      :key="visa.id"
      :title="visa.name"
      :description="visa.description"
      :badge="visa.status"
      :variant="getVisaVariant(visa.type)"
      size="md"
    >
      <template #icon>
        <GlobeAltIcon class="h-6 w-6" />
      </template>
      
      <!-- Price and duration -->
      <div class="flex items-center justify-between mt-rhythm-md mb-rhythm-md">
        <div class="text-sm">
          <span class="font-semibold text-gray-900">৳{{ visa.price }}</span>
        </div>
        <StatusBadge :status="visa.status" size="sm" />
      </div>
      
      <template #footer>
        <Link :href="route('visa.apply', visa.id)">
          <FlowButton variant="ocean" size="sm" fullWidth>
            <template #iconBefore>
              <RocketLaunchIcon class="h-4 w-4" />
            </template>
            Apply Now
          </FlowButton>
        </Link>
      </template>
    </RhythmicCard>
  </div>
</AnimatedSection>
```

**Variant Mapping**:
```vue
const getVisaVariant = (type) => {
  const variants = {
    tourist: 'ocean',
    work: 'sky',
    student: 'growth',
    family: 'heritage',
    business: 'sunrise',
  };
  return variants[type.toLowerCase()] || 'default';
};
```

---

### 3. Application Tracking

**File**: `resources/js/Pages/User/Applications/Show.vue`

**Add ProgressWave Component**:

```vue
<AnimatedSection
  title="Application Status"
  :subtitle="`Track your ${application.service_name} application`"
  variant="light"
>
  <!-- Application Progress -->
  <ProgressWave
    :steps="[
      { label: 'Submitted', description: 'Application received' },
      { label: 'Under Review', description: 'Agency reviewing' },
      { label: 'Payment', description: 'Quote accepted' },
      { label: 'Processing', description: 'Documents submitted' },
      { label: 'Completed', description: 'Visa/Service issued' }
    ]"
    :currentStep="getCurrentStepIndex(application.status)"
    variant="ocean"
    :showPercentage="true"
    :animated="true"
  />
  
  <!-- Status Details Card -->
  <div class="mt-rhythm-2xl">
    <RhythmicCard variant="default" size="lg">
      <div class="flex items-start justify-between">
        <div>
          <h3 class="text-2xl font-bold text-gray-900 mb-rhythm-sm">
            {{ application.service_name }}
          </h3>
          <p class="text-gray-600 mb-rhythm-md">
            Application ID: {{ application.id }}
          </p>
          <StatusBadge 
            :status="application.status" 
            :text="application.status_text"
            size="lg"
            :pulse="application.status === 'processing'"
          />
        </div>
        <div class="text-right">
          <p class="text-sm text-gray-600">Submitted</p>
          <p class="font-semibold">{{ formatDate(application.created_at) }}</p>
        </div>
      </div>
      
      <template #footer>
        <div class="flex gap-rhythm-md">
          <FlowButton variant="ocean" size="md">
            <template #iconBefore>
              <DocumentTextIcon class="h-5 w-5" />
            </template>
            View Documents
          </FlowButton>
          <FlowButton variant="outline" size="md">
            <template #iconBefore>
              <ChatBubbleLeftIcon class="h-5 w-5" />
            </template>
            Contact Support
          </FlowButton>
        </div>
      </template>
    </RhythmicCard>
  </div>
</AnimatedSection>

<script setup>
const getCurrentStepIndex = (status) => {
  const statusMap = {
    pending: 0,
    under_review: 1,
    awaiting_payment: 2,
    processing: 3,
    completed: 4,
    rejected: 1, // Show as stuck at review
    cancelled: 0,
  };
  return statusMap[status] || 0;
};
</script>
```

---

### 4. Admin Panel Tables

**File**: `resources/js/Pages/Admin/**/Index.vue`

**Convert Data Tables**:

**Before**:
```vue
<table class="min-w-full divide-y divide-gray-200">
  <thead class="bg-gray-50">
    <tr>
      <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
        Name
      </th>
      <th>Status</th>
      <th>Actions</th>
    </tr>
  </thead>
  <tbody class="bg-white divide-y divide-gray-200">
    <tr v-for="item in items">
      <td class="px-6 py-4">{{ item.name }}</td>
      <td>
        <span :class="getStatusClass(item.status)">
          {{ item.status }}
        </span>
      </td>
      <td>
        <button class="text-blue-600">Edit</button>
      </td>
    </tr>
  </tbody>
</table>
```

**After** (with rhythmic styling):
```vue
<AnimatedSection
  title="Applications Management"
  subtitle="Review and manage all user applications"
  variant="light"
  container="wide"
>
  <div class="bg-white rounded-rhythm-xl shadow-rhythm overflow-hidden">
    <table class="min-w-full divide-y divide-gray-200">
      <thead class="bg-gradient-to-r from-ocean-50 to-sky-50">
        <tr>
          <th class="px-rhythm-lg py-rhythm-md text-left text-sm font-display font-semibold text-ocean-900">
            Applicant
          </th>
          <th class="px-rhythm-lg py-rhythm-md text-left text-sm font-display font-semibold text-ocean-900">
            Service
          </th>
          <th class="px-rhythm-lg py-rhythm-md text-left text-sm font-display font-semibold text-ocean-900">
            Status
          </th>
          <th class="px-rhythm-lg py-rhythm-md text-right text-sm font-display font-semibold text-ocean-900">
            Actions
          </th>
        </tr>
      </thead>
      <tbody class="bg-white divide-y divide-gray-100">
        <tr 
          v-for="item in items" 
          :key="item.id"
          class="hover:bg-ocean-50/30 transition-colors duration-300"
        >
          <td class="px-rhythm-lg py-rhythm-md">
            <div class="flex items-center">
              <img :src="item.avatar" class="w-10 h-10 rounded-full mr-rhythm-sm" />
              <div>
                <p class="font-semibold text-gray-900">{{ item.name }}</p>
                <p class="text-sm text-gray-600">{{ item.email }}</p>
              </div>
            </div>
          </td>
          <td class="px-rhythm-lg py-rhythm-md">
            <p class="font-medium text-gray-900">{{ item.service }}</p>
            <p class="text-sm text-gray-600">{{ item.country }}</p>
          </td>
          <td class="px-rhythm-lg py-rhythm-md">
            <StatusBadge 
              :status="item.status" 
              size="md"
              :pulse="item.status === 'processing'"
            />
          </td>
          <td class="px-rhythm-lg py-rhythm-md text-right">
            <div class="flex justify-end gap-rhythm-sm">
              <Link :href="route('admin.applications.show', item.id)">
                <FlowButton variant="ocean" size="xs">
                  <template #iconBefore>
                    <EyeIcon class="h-3 w-3" />
                  </template>
                  View
                </FlowButton>
              </Link>
              <Link :href="route('admin.applications.edit', item.id)">
                <FlowButton variant="outline" size="xs">
                  <template #iconBefore>
                    <PencilIcon class="h-3 w-3" />
                  </template>
                  Edit
                </FlowButton>
              </Link>
            </div>
          </td>
        </tr>
      </tbody>
    </table>
  </div>
  
  <!-- Pagination -->
  <div class="mt-rhythm-lg flex justify-between items-center">
    <p class="text-sm text-gray-600">
      Showing {{ items.from }} to {{ items.to }} of {{ items.total }} results
    </p>
    <div class="flex gap-rhythm-sm">
      <FlowButton 
        v-for="link in items.links"
        :key="link.label"
        variant="ghost"
        size="sm"
        :href="link.url"
        :disabled="!link.url"
      >
        {{ link.label }}
      </FlowButton>
    </div>
  </div>
</AnimatedSection>
```

---

## 🎨 Component Selection Guide

### When to Use RhythmicCard
✅ **Use for**:
- Service listings
- Feature highlights
- Profile sections
- Dashboard widgets
- Product cards
- Testimonials
- Contact cards

❌ **Don't use for**:
- Data tables (use styled `<table>` instead)
- Forms (use form components)
- Navigation (use nav components)

### When to Use FlowButton
✅ **Use for**:
- Primary CTAs
- Form submissions
- Navigation actions
- Card actions (in footer slot)
- Modal buttons

❌ **Don't use for**:
- Text links (use `<Link>` instead)
- Table row actions (use icon buttons)
- Dropdown items

### When to Use ProgressWave
✅ **Use for**:
- Application tracking
- Multi-step forms
- Onboarding flows
- Order status
- Profile completion

❌ **Don't use for**:
- Simple loading indicators (use spinner)
- Percentage-only progress (use progress bar)
- Binary states (use toggle)

### When to Use StatusBadge
✅ **Use for**:
- Application statuses
- Payment states
- User roles
- Activity indicators
- Priority labels

❌ **Don't use for**:
- Category labels (use regular badges)
- Navigation tabs
- Toggle switches

### When to Use AnimatedSection
✅ **Use for**:
- Major page sections
- Hero banners
- Feature blocks
- Content sections

❌ **Don't use for**:
- Small content blocks
- Table rows
- List items
- Inline content

---

## 🔄 Migration Checklist

### Per Page Migration:

- [ ] **Step 1**: Import rhythmic components
  ```vue
  import RhythmicCard from '@/Components/Rhythmic/RhythmicCard.vue';
  import FlowButton from '@/Components/Rhythmic/FlowButton.vue';
  import AnimatedSection from '@/Components/Rhythmic/AnimatedSection.vue';
  import StatusBadge from '@/Components/Rhythmic/StatusBadge.vue';
  import ProgressWave from '@/Components/Rhythmic/ProgressWave.vue';
  ```

- [ ] **Step 2**: Wrap page content in AnimatedSection
  ```vue
  <AnimatedSection
    title="Page Title"
    subtitle="Page description"
    variant="light"
  >
    <!-- Page content -->
  </AnimatedSection>
  ```

- [ ] **Step 3**: Convert cards to RhythmicCard
  - Replace `<div class="bg-white rounded...">` with `<RhythmicCard>`
  - Move icons to `#icon` slot
  - Move actions to `#footer` slot
  - Add `variant` prop based on content

- [ ] **Step 4**: Convert buttons to FlowButton
  - Replace all CTAs with `<FlowButton>`
  - Choose appropriate `variant` (ocean/sunrise/outline)
  - Add icons to `#iconBefore` or `#iconAfter` slots
  - Set proper `size` (xs/sm/md/lg)

- [ ] **Step 5**: Replace status indicators with StatusBadge
  - Find all status displays
  - Replace with `<StatusBadge :status="item.status" />`
  - Remove custom color classes

- [ ] **Step 6**: Replace spacing classes
  - Replace `mb-6` → `mb-rhythm-lg`
  - Replace `p-8` → `p-rhythm-2xl`
  - Replace `gap-4` → `gap-rhythm-md`

- [ ] **Step 7**: Update typography classes
  - Replace `text-3xl font-bold` → `text-display-md font-display font-bold`
  - Replace `text-lg` → `text-xl text-rhythm`
  - Replace `text-gray-600` → `text-gray-600 text-rhythm`

- [ ] **Step 8**: Test responsive behavior
  - Verify mobile layout
  - Check tablet breakpoints
  - Ensure desktop harmony

- [ ] **Step 9**: Commit changes
  ```bash
  git add .
  git commit -m "Redesign: Apply rhythmic design to [PageName]"
  ```

---

## 📊 Progress Tracking

### Pages Completed ✅
- [x] Welcome.vue (Landing Page)

### Pages In Progress 🚧
- [ ] Dashboard.vue (User Dashboard)
- [ ] Services/Visa/Index.vue (Service Listings)
- [ ] User/Applications/Show.vue (Application Tracking)

### Pages Remaining 📝
- [ ] Admin/**/Index.vue (Admin Tables)
- [ ] Profile/Edit.vue (Profile Management)
- [ ] Wallet/Index.vue (Wallet Dashboard)
- [ ] Agency/Dashboard.vue (Agency Panel)
- [ ] Consultant/Dashboard.vue (Consultant Panel)

---

## 🎓 Best Practices

### 1. Variant Consistency
Always use the same variant for related content:
```vue
<!-- ✅ Good: Visa services always ocean -->
<RhythmicCard variant="ocean" title="Tourist Visa" />
<RhythmicCard variant="ocean" title="Work Visa" />

<!-- ❌ Bad: Inconsistent variants -->
<RhythmicCard variant="ocean" title="Tourist Visa" />
<RhythmicCard variant="sunrise" title="Work Visa" />
```

### 2. Rhythmic Spacing
Always use rhythm-* spacing, never arbitrary values:
```vue
<!-- ✅ Good -->
<div class="mb-rhythm-lg gap-rhythm-md p-rhythm-xl">

<!-- ❌ Bad -->
<div class="mb-6 gap-4 p-8">
```

### 3. Semantic Status Colors
Let StatusBadge handle colors, don't override:
```vue
<!-- ✅ Good -->
<StatusBadge status="approved" />

<!-- ❌ Bad -->
<StatusBadge status="approved" class="bg-blue-500 text-white" />
```

### 4. Icon Consistency
Use Heroicons throughout, consistent sizes:
```vue
<!-- ✅ Good -->
<GlobeAltIcon class="h-6 w-6 text-ocean-600" />

<!-- ❌ Bad -->
<i class="fa fa-globe text-blue-500"></i>
```

---

**Next**: Start with User Dashboard redesign, then Service Listings, then Application Tracking.  
**Timeline**: ~2 hours per major page, ~30 minutes per admin table page.
