# BideshGomon Design System
**Version 1.0 | World-Class Professional Standards**

---

## 🎨 Brand Colors

### Primary Brand Colors
Based on the official BideshGomon logo:

```css
/* Red - Primary Action & Important Elements */
--bg-red-50: #FFEBEE
--bg-red-100: #FFCDD2
--bg-red-600: #E53935  /* Logo Red - Primary */
--bg-red-700: #D32F2F
--bg-red-800: #C62828

/* Green - Success & Positive Actions */
--bg-green-50: #E8F5E9
--bg-green-100: #C8E6C9
--bg-green-600: #66BB6A  /* Logo Green - Primary */
--bg-green-700: #4CAF50
--bg-green-800: #43A047
```

### Neutral Framework Colors
Professional grays for structure and content:

```css
/* Grays - Framework & Content */
--bg-gray-50: #F9FAFB   /* Lightest background */
--bg-gray-100: #F3F4F6  /* Cards, sections */
--bg-gray-200: #E5E7EB  /* Borders, dividers */
--bg-gray-300: #D1D5DB  /* Disabled states */
--bg-gray-400: #9CA3AF  /* Placeholder text */
--bg-gray-500: #6B7280  /* Secondary text */
--bg-gray-600: #4B5563  /* Primary text */
--bg-gray-700: #374151  /* Headings */
--bg-gray-800: #1F2937  /* Dark backgrounds */
--bg-gray-900: #111827  /* Darkest */
```

### Accent Colors (Sparingly)
Use for specific contexts only:

```css
/* Blue - Informational */
--bg-blue-50: #EFF6FF
--bg-blue-100: #DBEAFE
--bg-blue-600: #2563EB
--bg-blue-700: #1D4ED8

/* Yellow - Warnings */
--bg-yellow-50: #FEFCE8
--bg-yellow-100: #FEF9C3
--bg-yellow-600: #EAB308
--bg-yellow-700: #CA8A04

/* Orange - Alerts */
--bg-orange-50: #FFF7ED
--bg-orange-100: #FFEDD5
--bg-orange-600: #EA580C
--bg-orange-700: #C2410C
```

---

## 🚫 Deprecated Colors (Remove from Codebase)

**NEVER USE:**
- ❌ Pink (`bg-pink-*`, `text-pink-*`)
- ❌ Purple (`bg-purple-*`, `text-purple-*`)
- ❌ Fuchsia (`bg-fuchsia-*`, `text-fuchsia-*`)
- ❌ Rose (`bg-rose-*`, `text-rose-*`)
- ❌ Violet (`bg-violet-*`, `text-violet-*`)

**Found 100+ instances to replace - see implementation section**

---

## 🎯 Color Usage Guidelines

### 1. Primary Actions & CTAs
**Use Red (#E53935)**
```vue
<!-- Primary Buttons -->
<button class="bg-red-600 hover:bg-red-700 text-white">
  Apply Now
</button>

<!-- Important Badges -->
<span class="bg-red-100 text-red-800">New</span>
<span class="bg-red-100 text-red-800">Featured</span>
```

### 2. Success States & Positive Actions
**Use Green (#66BB6A)**
```vue
<!-- Success Buttons -->
<button class="bg-green-600 hover:bg-green-700 text-white">
  Save Changes
</button>

<!-- Success Messages -->
<div class="bg-green-50 border-green-200 text-green-800">
  Profile updated successfully!
</div>

<!-- Approved/Completed Status -->
<span class="bg-green-100 text-green-800">Approved</span>
```

### 3. Neutral/Secondary Actions
**Use Gray**
```vue
<!-- Secondary Buttons -->
<button class="bg-gray-200 hover:bg-gray-300 text-gray-700">
  Cancel
</button>

<!-- Cards & Sections -->
<div class="bg-gray-50 border border-gray-200">
  Content
</div>
```

### 4. Status Color Mapping

```javascript
const statusColors = {
  // Success States (Green)
  approved: 'bg-green-100 text-green-800',
  completed: 'bg-green-100 text-green-800',
  verified: 'bg-green-100 text-green-800',
  active: 'bg-green-100 text-green-800',
  
  // Pending States (Yellow)
  pending: 'bg-yellow-100 text-yellow-800',
  under_review: 'bg-yellow-100 text-yellow-800',
  processing: 'bg-yellow-100 text-yellow-800',
  
  // Danger States (Red)
  rejected: 'bg-red-100 text-red-800',
  cancelled: 'bg-red-100 text-red-800',
  failed: 'bg-red-100 text-red-800',
  expired: 'bg-red-100 text-red-800',
  
  // Info States (Blue)
  draft: 'bg-blue-100 text-blue-800',
  submitted: 'bg-blue-100 text-blue-800',
  
  // Neutral States (Gray)
  inactive: 'bg-gray-100 text-gray-800',
  archived: 'bg-gray-100 text-gray-800',
}
```

---

## 📐 Typography System

### Font Families
```css
font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
```

### Font Sizes
```javascript
// Tailwind Scale
text-xs: 0.75rem    // 12px - Small labels
text-sm: 0.875rem   // 14px - Body text, forms
text-base: 1rem     // 16px - Default body
text-lg: 1.125rem   // 18px - Large body
text-xl: 1.25rem    // 20px - Small headings
text-2xl: 1.5rem    // 24px - Section headings
text-3xl: 1.875rem  // 30px - Page headings
text-4xl: 2.25rem   // 36px - Hero headings
```

### Font Weights
```javascript
font-normal: 400    // Body text
font-medium: 500    // Emphasis, labels
font-semibold: 600  // Subheadings, buttons
font-bold: 700      // Headings, important
```

---

## 📏 Spacing & Layout

### Standard Spacing Scale
```javascript
p-2: 0.5rem   // 8px  - Tight padding
p-3: 0.75rem  // 12px - Form elements
p-4: 1rem     // 16px - Card padding
p-6: 1.5rem   // 24px - Section padding
p-8: 2rem     // 32px - Page padding
p-12: 3rem    // 48px - Large sections

gap-2: 0.5rem   // 8px  - Tight spacing
gap-3: 0.75rem  // 12px - Default gap
gap-4: 1rem     // 16px - Section gap
gap-6: 1.5rem   // 24px - Large gap
```

### Container Max Widths
```javascript
max-w-sm: 384px    // Small cards
max-w-md: 448px    // Medium dialogs
max-w-lg: 512px    // Large forms
max-w-xl: 576px    // Extra large forms
max-w-2xl: 672px   // Modal content
max-w-4xl: 896px   // Dashboard sections
max-w-7xl: 1280px  // Main content container
```

---

## 🎨 Component Patterns

### 1. Profile Section Card
```vue
<template>
  <div class="bg-white rounded-lg border border-gray-200 overflow-hidden">
    <!-- Header with Icon -->
    <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
      <div class="flex items-center gap-3">
        <div class="w-10 h-10 rounded-lg bg-red-100 flex items-center justify-center">
          <Icon class="w-5 h-5 text-red-600" />
        </div>
        <div>
          <h3 class="text-lg font-semibold text-gray-900">Section Title</h3>
          <p class="text-sm text-gray-500">Description text</p>
        </div>
      </div>
    </div>
    
    <!-- Content -->
    <div class="p-6">
      <slot />
    </div>
  </div>
</template>
```

### 2. Primary Button (Red)
```vue
<button
  type="submit"
  :disabled="form.processing"
  class="px-6 py-3 bg-red-600 text-white font-semibold rounded-lg
         hover:bg-red-700 focus:ring-4 focus:ring-red-100
         disabled:bg-gray-300 disabled:cursor-not-allowed
         transition-all duration-200"
>
  <span v-if="form.processing">
    <LoadingIcon class="animate-spin h-5 w-5 inline" />
    Processing...
  </span>
  <span v-else>Save Changes</span>
</button>
```

### 3. Success Button (Green)
```vue
<button
  type="button"
  class="px-6 py-3 bg-green-600 text-white font-semibold rounded-lg
         hover:bg-green-700 focus:ring-4 focus:ring-green-100
         transition-all duration-200"
>
  Confirm & Continue
</button>
```

### 4. Secondary Button (Gray)
```vue
<button
  type="button"
  class="px-6 py-3 bg-gray-100 text-gray-700 font-semibold rounded-lg
         hover:bg-gray-200 focus:ring-4 focus:ring-gray-100
         transition-all duration-200"
>
  Cancel
</button>
```

### 5. Form Input Field
```vue
<div class="space-y-2">
  <label class="block text-sm font-medium text-gray-700">
    Field Label
    <span class="text-red-600">*</span>
  </label>
  
  <input
    v-model="form.field"
    type="text"
    class="w-full px-4 py-3 border border-gray-300 rounded-lg
           focus:ring-2 focus:ring-red-500 focus:border-red-500
           disabled:bg-gray-50 disabled:text-gray-500
           transition-all"
    :class="{ 'border-red-500 bg-red-50': form.errors.field }"
    placeholder="Enter value"
  />
  
  <p v-if="form.errors.field" class="text-sm text-red-600">
    {{ form.errors.field }}
  </p>
</div>
```

### 6. Status Badge
```vue
<span
  class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium"
  :class="{
    'bg-green-100 text-green-800': status === 'approved',
    'bg-yellow-100 text-yellow-800': status === 'pending',
    'bg-red-100 text-red-800': status === 'rejected',
    'bg-gray-100 text-gray-800': status === 'draft',
  }"
>
  {{ status }}
</span>
```

### 7. Success Alert
```vue
<div
  v-if="$page.props.flash.success"
  class="mb-6 p-4 bg-green-50 border border-green-200 rounded-lg"
>
  <div class="flex items-start gap-3">
    <CheckCircleIcon class="w-5 h-5 text-green-600 flex-shrink-0 mt-0.5" />
    <div>
      <h4 class="font-semibold text-green-900">Success</h4>
      <p class="text-sm text-green-800">{{ $page.props.flash.success }}</p>
    </div>
  </div>
</div>
```

### 8. Error Alert
```vue
<div
  v-if="$page.props.flash.error"
  class="mb-6 p-4 bg-red-50 border border-red-200 rounded-lg"
>
  <div class="flex items-start gap-3">
    <XCircleIcon class="w-5 h-5 text-red-600 flex-shrink-0 mt-0.5" />
    <div>
      <h4 class="font-semibold text-red-900">Error</h4>
      <p class="text-sm text-red-800">{{ $page.props.flash.error }}</p>
    </div>
  </div>
</div>
```

### 9. Loading State
```vue
<div v-if="loading" class="flex items-center justify-center py-12">
  <div class="text-center space-y-3">
    <div class="w-12 h-12 border-4 border-gray-200 border-t-red-600 rounded-full animate-spin mx-auto"></div>
    <p class="text-sm text-gray-500">Loading data...</p>
  </div>
</div>
```

### 10. Empty State
```vue
<div v-if="items.length === 0" class="text-center py-12">
  <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
    <Icon class="w-8 h-8 text-gray-400" />
  </div>
  <h3 class="text-lg font-medium text-gray-900 mb-2">No items found</h3>
  <p class="text-sm text-gray-500 mb-6">Get started by adding your first item.</p>
  <button class="px-6 py-3 bg-red-600 text-white font-semibold rounded-lg hover:bg-red-700">
    Add New Item
  </button>
</div>
```

---

## 🔧 Profile Sections Standards

### Section Structure Template
Every profile section must follow this structure:

```vue
<template>
  <div class="bg-white rounded-lg border border-gray-200 overflow-hidden">
    <!-- Header -->
    <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
      <div class="flex items-center justify-between">
        <div class="flex items-center gap-3">
          <div class="w-10 h-10 rounded-lg bg-red-100 flex items-center justify-center">
            <SectionIcon class="w-5 h-5 text-red-600" />
          </div>
          <div>
            <h3 class="text-lg font-semibold text-gray-900">Section Name</h3>
            <p class="text-sm text-gray-500">Brief description</p>
          </div>
        </div>
        <button
          v-if="!isEditing"
          @click="isEditing = true"
          class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200"
        >
          Edit
        </button>
      </div>
    </div>

    <!-- Loading State -->
    <div v-if="loading" class="p-12 text-center">
      <div class="w-12 h-12 border-4 border-gray-200 border-t-red-600 rounded-full animate-spin mx-auto"></div>
      <p class="mt-4 text-sm text-gray-500">Loading...</p>
    </div>

    <!-- Empty State -->
    <div v-else-if="items.length === 0 && !isEditing" class="p-12 text-center">
      <EmptyStateIcon class="w-12 h-12 text-gray-400 mx-auto mb-4" />
      <h4 class="font-medium text-gray-900 mb-2">No data added yet</h4>
      <p class="text-sm text-gray-500 mb-6">Add information to complete your profile.</p>
      <button
        @click="isEditing = true"
        class="px-6 py-3 bg-red-600 text-white rounded-lg hover:bg-red-700"
      >
        Add Information
      </button>
    </div>

    <!-- Display Mode -->
    <div v-else-if="!isEditing" class="p-6 space-y-4">
      <!-- Display content here -->
    </div>

    <!-- Edit Mode -->
    <form v-else @submit.prevent="submit" class="p-6 space-y-6">
      <!-- Flash Messages -->
      <div v-if="$page.props.flash.success" class="p-4 bg-green-50 border border-green-200 rounded-lg">
        <p class="text-sm text-green-800">{{ $page.props.flash.success }}</p>
      </div>
      
      <div v-if="$page.props.flash.error" class="p-4 bg-red-50 border border-red-200 rounded-lg">
        <p class="text-sm text-red-800">{{ $page.props.flash.error }}</p>
      </div>

      <!-- Form Fields -->
      <slot />

      <!-- Actions -->
      <div class="flex gap-3 pt-4">
        <button
          type="submit"
          :disabled="form.processing"
          class="px-6 py-3 bg-green-600 text-white font-semibold rounded-lg
                 hover:bg-green-700 disabled:bg-gray-300 transition-all"
        >
          <span v-if="form.processing">Saving...</span>
          <span v-else>Save Changes</span>
        </button>
        <button
          type="button"
          @click="isEditing = false"
          :disabled="form.processing"
          class="px-6 py-3 bg-gray-100 text-gray-700 font-semibold rounded-lg
                 hover:bg-gray-200 disabled:bg-gray-50 transition-all"
        >
          Cancel
        </button>
      </div>
    </form>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import { useForm } from '@inertiajs/vue3'

const isEditing = ref(false)
const loading = ref(false)

const form = useForm({
  // form fields
})

const submit = () => {
  form.post(route('profile.section.store'), {
    preserveScroll: true,
    onSuccess: () => {
      isEditing.value = false
    },
  })
}
</script>
```

---

## 📋 Profile Sections Checklist

Each section MUST have:

### ✅ Required Features
- [ ] Loading state with spinner
- [ ] Empty state with icon + message
- [ ] Display mode (read-only view)
- [ ] Edit mode toggle
- [ ] Form validation with error messages
- [ ] Success/error flash messages
- [ ] Processing state on submit button
- [ ] Cancel button
- [ ] Consistent color scheme (red/green/gray)
- [ ] Proper spacing and typography
- [ ] Mobile responsive design
- [ ] Proper authorization (user_id check)
- [ ] DB transaction wrapper for saves
- [ ] Auto-close edit mode on success

### 🎨 Design Requirements
- [ ] Use red (#E53935) for primary actions
- [ ] Use green (#66BB6A) for success states
- [ ] Use gray for neutrals
- [ ] NO pink, purple, fuchsia colors
- [ ] Consistent border-radius: `rounded-lg`
- [ ] Consistent padding: `p-6` for sections
- [ ] Consistent gaps: `gap-3` or `gap-4`
- [ ] Icons size: `w-5 h-5` for inline, `w-10 h-10` for headers

---

## 🚀 Implementation Priority

### Phase 1: Color Replacement (Immediate)
Replace all deprecated colors:

```bash
# Find all pink/purple/fuchsia usage
grep -r "bg-pink\|text-pink\|bg-purple\|text-purple\|bg-fuchsia\|text-fuchsia" resources/js/

# Replacement mapping:
bg-pink-* → bg-red-* (for primary actions)
bg-purple-* → bg-green-* (for success states)
text-pink-* → text-red-*
text-purple-* → text-green-*
```

**Files to update (100+ instances found):**
- Welcome.vue
- User/Support/Show.vue, Index.vue
- User/Events/Show.vue, Index.vue
- User/Applications/Show.vue
- User/Appointments/Index.vue
- Services/Show.vue
- Services/FlightRequest/Index.vue
- Profile/Show.vue
- Profile/Partials/*.vue
- And 20+ more files

### Phase 2: Profile Sections Fixes
Fix each section systematically:

1. **Basic Details** (Edit.vue)
2. **Passports** (PassportManagement.vue)
3. **Visa History** (VisaHistoryManagement.vue)
4. **Travel History** (TravelHistoryManagement.vue)
5. **Family Members** (FamilyMembersManagement.vue)
6. **Financial** (FinancialSection.vue)
7. **Security** (SecurityInformationSection.vue)
8. **Education** (EducationSection.vue)
9. **Work Experience** (WorkExperienceSection.vue)
10. **Languages** (LanguagesSection.vue)
11. **Documents** (DocumentsManagement.vue)

### Phase 3: Admin Data Management
Create admin interface with proper colors:
- Use red for primary actions
- Use green for success states
- Use gray framework
- Consistent table designs
- Proper form layouts

---

## 📱 Mobile-First Responsive Design

### Breakpoints
```javascript
sm: 640px   // Small tablets
md: 768px   // Tablets
lg: 1024px  // Small desktops
xl: 1280px  // Desktops
2xl: 1536px // Large desktops
```

### Mobile Pattern
```vue
<div class="space-y-4 md:space-y-0 md:flex md:gap-6">
  <!-- Stack on mobile, row on desktop -->
</div>

<button class="w-full md:w-auto">
  <!-- Full width on mobile, auto on desktop -->
</button>

<div class="p-4 md:p-6">
  <!-- Less padding on mobile -->
</div>
```

---

## 🎯 Success Criteria

### Design Quality
- ✅ Consistent brand colors (red/green/gray)
- ✅ NO deprecated colors (pink/purple)
- ✅ Professional, clean aesthetic
- ✅ Proper contrast ratios (WCAG AA)
- ✅ Consistent spacing and typography

### Functionality
- ✅ All sections load properly
- ✅ All forms save correctly
- ✅ Validation works on all fields
- ✅ Success/error messages display
- ✅ Loading states prevent double-submit
- ✅ Mobile responsive

### User Experience
- ✅ Clear visual hierarchy
- ✅ Intuitive navigation
- ✅ Helpful error messages
- ✅ Smooth transitions
- ✅ Fast page loads

---

## 📚 Resources

### Color Tools
- **Contrast Checker:** https://webaim.org/resources/contrastchecker/
- **Palette Generator:** https://coolors.co/

### Tailwind CSS
- **Documentation:** https://tailwindcss.com/docs
- **Color Reference:** https://tailwindcss.com/docs/customizing-colors

### Component Libraries
- **Headless UI:** https://headlessui.com/
- **Hero Icons:** https://heroicons.com/

---

**Last Updated:** November 30, 2025  
**Status:** Implementation Ready  
**Priority:** HIGH - Essential for world-class platform
