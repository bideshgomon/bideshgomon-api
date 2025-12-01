# BideshGomon Design System

## Overview
A comprehensive, Bangladesh-focused design system built with Tailwind CSS for the BideshGomon platform. This system provides consistent, professional UI components with cultural awareness and optimal user experience.

---

## Color Palette

### Primary Colors

#### Ocean Blue (Primary Brand)
Main brand identity color representing trust, professionalism, and global reach.

```javascript
ocean: {
  50: '#e6f3ff',   // Lightest - backgrounds
  100: '#cce7ff',
  200: '#99cfff',
  300: '#66b7ff',
  400: '#339fff',
  500: '#0087ff',  // Primary - buttons, links
  600: '#006ccc',  // Hover states
  700: '#005199',
  800: '#003666',
  900: '#001b33',  // Darkest - text
}
```

**Usage:**
- Primary buttons: `bg-ocean-500 hover:bg-ocean-600`
- Links: `text-ocean-600`
- Focus rings: `focus:ring-ocean-300`

#### Sky (Secondary)
Peaceful, calming tones for secondary elements.

```javascript
sky: {
  500: '#0ea5e9',  // Secondary actions
  600: '#0284c7',  // Secondary hover
}
```

#### Growth Green (Success)
Represents success, aspiration, and positive outcomes.

```javascript
growth: {
  500: '#10b981',  // Success states
  600: '#059669',  // Success hover
}
```

#### Sunrise Orange (CTAs & Energy)
Warm, energetic color for calls-to-action and highlights.

```javascript
sunrise: {
  500: '#f97316',  // Warning/CTA
  600: '#ea580c',  // Warning hover
}
```

#### Gold (Premium)
Achievement and premium features.

```javascript
gold: {
  500: '#eab308',  // Premium badges
  600: '#ca8a04',  // Premium hover
}
```

#### Heritage (Cultural Accent)
Bangladesh cultural heritage color - deep red tones.

```javascript
heritage: {
  600: '#db2777',  // Accent elements
  700: '#be185d',  // Accent hover
}
```

---

## Typography

### Font Family
```javascript
fontFamily: {
  sans: ['Proxima Nova', 'Arial', ...defaultTheme.fontFamily.sans],
  display: ['Proxima Nova', 'Arial', ...defaultTheme.fontFamily.sans],
}
```

### Display Sizes
Large, impactful text for hero sections and major headings.

```javascript
'display-2xl': '4.5rem',  // 72px - Hero headlines
'display-xl': '3.75rem',  // 60px - Page titles
'display-lg': '3rem',     // 48px - Section headers
'display-md': '2.25rem',  // 36px - Card titles
'display-sm': '1.875rem', // 30px - Subsections
```

**Usage Example:**
```vue
<h1 class="text-display-xl font-bold text-gray-900">Welcome to BideshGomon</h1>
```

---

## Spacing System

### Rhythmic Spacing
Based on 4px units for visual consistency.

```javascript
'rhythm-xs': '0.25rem',   // 4px
'rhythm-sm': '0.5rem',    // 8px
'rhythm-md': '1rem',      // 16px
'rhythm-lg': '1.5rem',    // 24px
'rhythm-xl': '2rem',      // 32px
'rhythm-2xl': '3rem',     // 48px
'rhythm-3xl': '4rem',     // 64px
'rhythm-4xl': '6rem',     // 96px
'rhythm-5xl': '8rem',     // 128px
```

**Usage:**
- Component padding: `p-rhythm-lg` (24px)
- Section spacing: `py-rhythm-3xl` (64px vertical)
- Element gaps: `gap-rhythm-md` (16px)

---

## Border Radius

### Rhythmic Corners
```javascript
'rhythm-sm': '0.5rem',   // 8px - Small elements
'rhythm-md': '0.75rem',  // 12px - Input fields
'rhythm-lg': '1rem',     // 16px - Cards
'rhythm-xl': '1.5rem',   // 24px - Large cards
'rhythm-2xl': '2rem',    // 32px - Modals
```

**Usage:**
```vue
<div class="rounded-rhythm-lg"> <!-- Cards -->
<input class="rounded-rhythm-md"> <!-- Form inputs -->
```

---

## Shadows

### Elevation System
```javascript
'rhythm-sm': '0 2px 8px rgba(0, 0, 0, 0.05)',    // Subtle elevation
'rhythm': '0 4px 16px rgba(0, 0, 0, 0.08)',       // Default cards
'rhythm-lg': '0 8px 24px rgba(0, 0, 0, 0.12)',    // Elevated cards
'rhythm-xl': '0 12px 32px rgba(0, 0, 0, 0.15)',   // Modals, popovers

// Glow effects
'glow-ocean': '0 0 24px rgba(0, 135, 255, 0.3)',
'glow-growth': '0 0 24px rgba(16, 185, 129, 0.3)',
'glow-sunrise': '0 0 24px rgba(249, 115, 22, 0.3)',
```

**Usage:**
```vue
<div class="shadow-rhythm hover:shadow-rhythm-lg transition-shadow">
```

---

## Animations

### Available Animations
```javascript
'fade-in': 'fadeIn 0.6s ease-out',
'fade-in-up': 'fadeInUp 0.6s ease-out',
'fade-in-down': 'fadeInDown 0.6s ease-out',
'slide-in-right': 'slideInRight 0.5s ease-out',
'slide-in-left': 'slideInLeft 0.5s ease-out',
'scale-in': 'scaleIn 0.4s ease-out',
'pulse-slow': 'pulse 3s cubic-bezier(0.4, 0, 0.6, 1) infinite',
'wave': 'wave 2.5s ease-in-out infinite',
'float': 'float 3s ease-in-out infinite',
```

**Usage:**
```vue
<div class="animate-fade-in-up"> <!-- Entrance animations -->
<div class="animate-pulse-slow"> <!-- Loading states -->
```

---

## Gradient Utilities

### Custom Gradients
```css
.gradient-ocean     /* Ocean blue gradient */
.gradient-sky       /* Sky blue gradient */
.gradient-growth    /* Green success gradient */
.gradient-sunrise   /* Orange energy gradient */
.gradient-twilight  /* Purple accent gradient */
```

**Usage:**
```vue
<div class="gradient-ocean text-white p-8 rounded-rhythm-xl">
  <h2>Premium Feature</h2>
</div>
```

---

## Base Components

### BaseCard
Professional card container with multiple variants.

**Props:**
- `variant`: 'default' | 'elevated' | 'bordered' | 'flat' | 'interactive'
- `padding`: 'none' | 'xs' | 'sm' | 'md' | 'lg' | 'xl'
- `hover`: boolean
- `clickable`: boolean

**Usage:**
```vue
<BaseCard variant="elevated" padding="lg">
  <h3>Card Title</h3>
  <p>Card content...</p>
</BaseCard>
```

### BaseButton
Comprehensive button component with multiple states.

**Props:**
- `variant`: 'primary' | 'secondary' | 'success' | 'warning' | 'danger' | 'ghost' | 'outline'
- `size`: 'xs' | 'sm' | 'md' | 'lg' | 'xl'
- `href`: string (renders as Link)
- `disabled`: boolean
- `loading`: boolean
- `icon`: boolean (icon-only button)
- `fullWidth`: boolean

**Usage:**
```vue
<BaseButton variant="primary" size="lg" @click="handleSubmit">
  Submit Application
</BaseButton>

<BaseButton variant="outline" href="/services">
  Browse Services
</BaseButton>
```

### BaseInput
Form input with validation and icons.

**Props:**
- `modelValue`: string | number
- `type`: 'text' | 'email' | 'password' | 'number' | etc.
- `label`: string
- `placeholder`: string
- `error`: string (error message)
- `hint`: string
- `disabled`: boolean
- `required`: boolean
- `size`: 'sm' | 'md' | 'lg'
- `icon`: Component (Heroicon)

**Usage:**
```vue
<BaseInput
  v-model="form.email"
  type="email"
  label="Email Address"
  placeholder="Enter your email"
  :error="form.errors.email"
  required
  :icon="EnvelopeIcon"
/>
```

### BaseSelect
Dropdown select component.

**Props:**
- `modelValue`: string | number
- `options`: Array (objects or primitives)
- `label`: string
- `placeholder`: string
- `error`: string
- `hint`: string
- `disabled`: boolean
- `required`: boolean
- `size`: 'sm' | 'md' | 'lg'
- `valueKey`: string (default: 'value')
- `labelKey`: string (default: 'label')

**Usage:**
```vue
<BaseSelect
  v-model="form.country"
  :options="countries"
  label="Select Country"
  placeholder="Choose destination"
  :error="form.errors.country"
  required
/>
```

### BaseBadge
Status and category badges.

**Props:**
- `variant`: 'default' | 'primary' | 'success' | 'warning' | 'danger' | 'info'
- `size`: 'sm' | 'md' | 'lg'
- `rounded`: boolean
- `removable`: boolean

**Usage:**
```vue
<BaseBadge variant="success" rounded>Approved</BaseBadge>
<BaseBadge variant="warning" size="sm">Pending</BaseBadge>
```

### BaseModal
Modal dialog with animations.

**Props:**
- `show`: boolean
- `maxWidth`: 'sm' | 'md' | 'lg' | 'xl' | '2xl' | '4xl' | '6xl'
- `closeable`: boolean
- `showClose`: boolean

**Slots:**
- `header`: Modal header content
- `default`: Modal body
- `footer`: Modal footer (actions)

**Usage:**
```vue
<BaseModal :show="showModal" @close="showModal = false" max-width="lg">
  <template #header>
    <h3 class="text-lg font-semibold">Confirm Action</h3>
  </template>
  
  <p>Are you sure you want to proceed?</p>
  
  <template #footer>
    <BaseButton variant="secondary" @click="showModal = false">Cancel</BaseButton>
    <BaseButton variant="primary" @click="confirm">Confirm</BaseButton>
  </template>
</BaseModal>
```

### BaseAlert
Alert messages with variants.

**Props:**
- `variant`: 'success' | 'error' | 'warning' | 'info'
- `title`: string
- `dismissible`: boolean
- `show`: boolean

**Usage:**
```vue
<BaseAlert variant="success" title="Success!" dismissible>
  Your application has been submitted successfully.
</BaseAlert>
```

### BaseProgress
Progress bar with animations.

**Props:**
- `value`: number (current value)
- `max`: number (maximum value, default: 100)
- `variant`: 'primary' | 'success' | 'warning' | 'danger' | 'gradient'
- `size`: 'sm' | 'md' | 'lg'
- `showLabel`: boolean
- `animated`: boolean

**Usage:**
```vue
<BaseProgress :value="65" :max="100" variant="gradient" show-label animated />
```

### BaseSpinner
Loading spinner.

**Props:**
- `size`: 'sm' | 'md' | 'lg' | 'xl'
- `variant`: 'primary' | 'white'

**Usage:**
```vue
<BaseSpinner size="lg" />
```

### BaseStepper
Multi-step progress indicator.

**Props:**
- `items`: Array<{title: string, description?: string}>
- `activeStep`: number (0-indexed)
- `variant`: 'horizontal' | 'vertical'
- `clickable`: boolean

**Events:**
- `step-click`: (stepIndex: number) => void

**Usage:**
```vue
<BaseStepper
  :items="[
    { title: 'Personal Info', description: 'Basic details' },
    { title: 'Documents', description: 'Upload files' },
    { title: 'Review', description: 'Verify information' }
  ]"
  :active-step="1"
  variant="horizontal"
  clickable
  @step-click="goToStep"
/>
```

---

## Component Import

All base components are located in `resources/js/Components/Base/`:

```javascript
import BaseCard from '@/Components/Base/BaseCard.vue'
import BaseButton from '@/Components/Base/BaseButton.vue'
import BaseInput from '@/Components/Base/BaseInput.vue'
import BaseSelect from '@/Components/Base/BaseSelect.vue'
import BaseBadge from '@/Components/Base/BaseBadge.vue'
import BaseModal from '@/Components/Base/BaseModal.vue'
import BaseAlert from '@/Components/Base/BaseAlert.vue'
import BaseProgress from '@/Components/Base/BaseProgress.vue'
import BaseSpinner from '@/Components/Base/BaseSpinner.vue'
import BaseStepper from '@/Components/Base/BaseStepper.vue'
```

---

## Best Practices

### 1. Consistent Spacing
Always use rhythm spacing utilities for consistent visual rhythm.

```vue
<!-- Good -->
<div class="p-rhythm-lg space-y-rhythm-md">

<!-- Avoid -->
<div class="p-5 space-y-3">
```

### 2. Elevation Hierarchy
Use shadow utilities to create depth hierarchy.

```vue
<!-- Base card -->
<div class="shadow-rhythm">

<!-- Elevated card on hover -->
<div class="shadow-rhythm hover:shadow-rhythm-lg transition-shadow">

<!-- Modal/Popover -->
<div class="shadow-rhythm-xl">
```

### 3. Color Semantics
Use semantic colors for meaning.

```vue
<!-- Success -->
<BaseBadge variant="success">Approved</BaseBadge>

<!-- Warning -->
<BaseAlert variant="warning">Please review</BaseAlert>

<!-- Error -->
<BaseButton variant="danger">Delete</BaseButton>
```

### 4. Animation Timing
Use consistent timing functions for smooth interactions.

```vue
<!-- Standard transitions -->
<div class="transition-all duration-300">

<!-- Rhythmic timing -->
<div class="transition-rhythm duration-400">
```

### 5. Responsive Design
Mobile-first approach with breakpoints.

```vue
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-rhythm-lg">
```

---

## Bangladesh-Specific Considerations

### Currency Display
Always use ৳ symbol with proper formatting.

```javascript
// Use useBangladeshFormat composable
import { useBangladeshFormat } from '@/Composables/useBangladeshFormat'
const { formatCurrency } = useBangladeshFormat()

formatCurrency(5000) // "৳5,000.00"
```

### Date Format
DD/MM/YYYY format (not MM/DD/YYYY).

```javascript
formatDate('2025-12-01') // "01/12/2025"
```

### Cultural Colors
Use heritage color for cultural elements and national celebrations.

```vue
<div class="bg-heritage-600 text-white">
  🇧🇩 Victory Day Special Offer
</div>
```

---

## Performance Considerations

### 1. Tree Shaking
Tailwind automatically removes unused styles in production builds.

### 2. Component Lazy Loading
Lazy load heavy components when possible.

```javascript
const BaseModal = defineAsyncComponent(() => 
  import('@/Components/Base/BaseModal.vue')
)
```

### 3. Animation Performance
Use `transform` and `opacity` for smooth 60fps animations.

```css
/* Good - GPU accelerated */
.animate { transform: translateY(10px); opacity: 0.5; }

/* Avoid - repaints */
.animate { top: 10px; background: blue; }
```

---

## Accessibility

### 1. Semantic HTML
Base components use proper semantic elements.

### 2. ARIA Attributes
Components include appropriate ARIA labels and roles.

### 3. Keyboard Navigation
All interactive components support keyboard navigation.

### 4. Focus States
Clear focus indicators on all focusable elements.

```vue
<button class="focus:ring-4 focus:ring-ocean-300 focus:ring-offset-2">
```

### 5. Color Contrast
All text meets WCAG AA standards (4.5:1 minimum ratio).

---

## Migration Guide

### Upgrading Existing Components

**Before:**
```vue
<div class="bg-white shadow-md rounded-lg p-6">
  <button class="bg-blue-500 text-white px-4 py-2 rounded">
    Submit
  </button>
</div>
```

**After:**
```vue
<BaseCard variant="elevated" padding="lg">
  <BaseButton variant="primary">
    Submit
  </BaseButton>
</BaseCard>
```

---

## Support & Resources

- **Tailwind Documentation**: https://tailwindcss.com/docs
- **Heroicons**: https://heroicons.com
- **Project Config**: `tailwind.config.js`
- **Base Components**: `resources/js/Components/Base/`

---

**Last Updated:** December 2025  
**Version:** 1.0.0  
**Platform:** BideshGomon Visa Application System
