# Mobile-First Design System (2025)

## 🎯 Design Philosophy: Mobile First, Always

> **Reality**: 85%+ of Bangladesh users access via mobile (Android 80%, iOS 5%)  
> **Strategy**: Design for 375px (iPhone SE) first, scale up to desktop  
> **Goal**: Butter-smooth 60fps performance on budget Android devices

---

## 📱 Responsive Breakpoints (Mobile First)

```javascript
// tailwind.config.js
module.exports = {
  theme: {
    screens: {
      // Mobile first - no prefix needed for mobile styles
      'xs': '375px',  // Small phones (iPhone SE, Galaxy S8)
      'sm': '640px',  // Large phones (iPhone 14, Pixel 7)
      'md': '768px',  // Tablets (iPad Mini)
      'lg': '1024px', // Small laptops (iPad Pro, small laptops)
      'xl': '1280px', // Desktop
      '2xl': '1536px', // Large desktop
    },
  },
}
```

### Usage Pattern (Mobile First)
```vue
<!-- ✅ CORRECT: Mobile first, then larger screens -->
<div class="p-4 md:p-6 lg:p-8">
  <h1 class="text-2xl md:text-3xl lg:text-4xl">Heading</h1>
  <button class="w-full md:w-auto">Submit</button>
</div>

<!-- ❌ WRONG: Desktop first -->
<div class="p-8 md:p-6 p-4">
  <button class="w-auto md:w-full">Submit</button>
</div>
```

---

## 🎨 Mobile-First Color System

### Brand Colors (High Contrast for Sunlight Readability)
```javascript
// tailwind.config.js
colors: {
  primary: {
    50: '#E8F5E9',   // Very light - backgrounds
    100: '#C8E6C9',  // Light - hover states
    200: '#A5D6A7',
    300: '#81C784',
    400: '#66BB6A',
    500: '#4CAF50',  // Main brand color
    600: '#43A047',  // Darker - pressed states
    700: '#388E3C',
    800: '#2E7D32',
    900: '#1B5E20',  // Very dark - text on light
  },
  // Bangladesh flag inspired
  'bd-green': '#006A4E', // Bangladesh flag green
  'bd-red': '#F42A41',   // Bangladesh flag red
  
  // Neutral (optimized for AMOLED screens)
  gray: {
    50: '#FAFAFA',
    100: '#F5F5F5',
    200: '#EEEEEE',
    300: '#E0E0E0',
    400: '#BDBDBD',
    500: '#9E9E9E',
    600: '#757575',
    700: '#616161',
    800: '#424242',
    900: '#212121',  // Text on white backgrounds
    950: '#0A0A0A',  // Pure black for AMOLED
  },
}
```

### Status Colors (Accessible)
```javascript
success: '#10B981',  // Green (wcag AA compliant)
warning: '#F59E0B',  // Amber
error: '#EF4444',    // Red
info: '#3B82F6',     // Blue
```

---

## 🖼️ Touch-Optimized Component Sizes

### Minimum Touch Targets (Apple & Google Guidelines)
```css
/* Minimum 44x44px for iOS, 48x48px for Android */
/* We use 48px as minimum for all touch targets */

.btn {
  min-height: 48px;
  min-width: 48px;
  padding: 12px 24px;
}

.btn-sm {
  min-height: 40px;
  padding: 8px 16px;
}

.btn-lg {
  min-height: 56px;
  padding: 16px 32px;
}

/* Icon buttons */
.btn-icon {
  width: 48px;
  height: 48px;
  display: flex;
  align-items: center;
  justify-content: center;
}
```

### Input Fields (Easy Thumb Typing)
```css
.input {
  height: 48px;
  padding: 12px 16px;
  font-size: 16px; /* Prevents iOS zoom on focus */
  border-radius: 8px;
}

.textarea {
  min-height: 120px;
  padding: 12px 16px;
  font-size: 16px;
}

.select {
  height: 48px;
  padding: 12px 16px;
  font-size: 16px;
}
```

---

## 📐 Spacing System (Consistent Rhythm)

```javascript
// tailwind.config.js
spacing: {
  'safe-top': 'env(safe-area-inset-top)',    // iOS notch
  'safe-bottom': 'env(safe-area-inset-bottom)', // iOS home indicator
  'safe-left': 'env(safe-area-inset-left)',
  'safe-right': 'env(safe-area-inset-right)',
}
```

### Mobile Layout Spacing
```vue
<template>
  <!-- Page wrapper with safe areas -->
  <div class="min-h-screen pb-safe-bottom">
    <!-- Header with safe top -->
    <header class="h-14 pt-safe-top px-4 bg-white border-b">
      <div class="flex items-center justify-between h-full">
        <!-- 44px touch target for back button -->
        <button class="w-11 h-11 flex items-center justify-center -ml-2">
          <ChevronLeftIcon class="w-6 h-6" />
        </button>
        <h1 class="text-lg font-semibold truncate mx-2">Page Title</h1>
        <button class="w-11 h-11 flex items-center justify-center -mr-2">
          <MenuIcon class="w-6 h-6" />
        </button>
      </div>
    </header>
    
    <!-- Content with generous padding -->
    <main class="px-4 py-6">
      <!-- Cards with breathing room -->
      <div class="space-y-4">
        <Card />
        <Card />
      </div>
    </main>
    
    <!-- Bottom navigation (if used) -->
    <nav class="fixed bottom-0 left-0 right-0 h-16 pb-safe-bottom bg-white border-t">
      <div class="flex items-center justify-around h-full">
        <button class="flex-1 h-full flex flex-col items-center justify-center">
          <HomeIcon class="w-6 h-6" />
          <span class="text-xs mt-1">Home</span>
        </button>
        <!-- More nav items -->
      </div>
    </nav>
  </div>
</template>
```

---

## 🎭 Typography (Readable on Small Screens)

### Font Scale (Mobile Optimized)
```javascript
// tailwind.config.js
fontSize: {
  'xs': '0.75rem',     // 12px - Helper text
  'sm': '0.875rem',    // 14px - Secondary text
  'base': '1rem',      // 16px - Body text (prevents iOS zoom)
  'lg': '1.125rem',    // 18px - Large body
  'xl': '1.25rem',     // 20px - Small headings
  '2xl': '1.5rem',     // 24px - Section headings
  '3xl': '1.875rem',   // 30px - Page titles
  '4xl': '2.25rem',    // 36px - Hero text
}
```

### Line Heights (Comfortable Reading)
```css
.text-body {
  line-height: 1.6; /* 160% for body text */
}

.text-heading {
  line-height: 1.3; /* 130% for headings */
}
```

### Font Weights
```javascript
fontWeight: {
  normal: '400',    // Body text
  medium: '500',    // Emphasis
  semibold: '600',  // Subheadings
  bold: '700',      // Headings
}
```

---

## 🧩 Component Library (Mobile First)

### Button Component
```vue
<!-- components/Button.vue -->
<template>
  <button
    :class="[
      'relative inline-flex items-center justify-center',
      'min-h-[48px] px-6 rounded-lg',
      'font-medium text-base',
      'transition-all duration-200',
      'active:scale-95', // Touch feedback
      'disabled:opacity-50 disabled:cursor-not-allowed',
      variantClasses,
      sizeClasses,
      props.fullWidth ? 'w-full' : 'w-auto',
    ]"
    :disabled="props.loading || props.disabled"
    @click="handleClick"
  >
    <!-- Loading spinner -->
    <span v-if="props.loading" class="absolute inset-0 flex items-center justify-center">
      <svg class="animate-spin h-5 w-5" viewBox="0 0 24 24">
        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" fill="none" />
        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z" />
      </svg>
    </span>
    
    <!-- Content -->
    <span :class="{ 'invisible': props.loading }">
      <slot />
    </span>
  </button>
</template>

<script setup>
const props = defineProps({
  variant: {
    type: String,
    default: 'primary', // primary, secondary, outline, ghost
  },
  size: {
    type: String,
    default: 'md', // sm, md, lg
  },
  fullWidth: {
    type: Boolean,
    default: false,
  },
  loading: {
    type: Boolean,
    default: false,
  },
  disabled: {
    type: Boolean,
    default: false,
  },
})

const emit = defineEmits(['click'])

const variantClasses = computed(() => {
  const variants = {
    primary: 'bg-primary-600 text-white active:bg-primary-700 shadow-sm',
    secondary: 'bg-gray-200 text-gray-900 active:bg-gray-300',
    outline: 'border-2 border-primary-600 text-primary-600 active:bg-primary-50',
    ghost: 'text-primary-600 active:bg-primary-50',
    danger: 'bg-red-600 text-white active:bg-red-700',
  }
  return variants[props.variant] || variants.primary
})

const sizeClasses = computed(() => {
  const sizes = {
    sm: 'min-h-[40px] px-4 text-sm',
    md: 'min-h-[48px] px-6 text-base',
    lg: 'min-h-[56px] px-8 text-lg',
  }
  return sizes[props.size] || sizes.md
})

const handleClick = (e) => {
  if (!props.loading && !props.disabled) {
    // Haptic feedback (if supported)
    if (navigator.vibrate) {
      navigator.vibrate(10) // 10ms vibration
    }
    emit('click', e)
  }
}
</script>
```

### Input Component
```vue
<!-- components/Input.vue -->
<template>
  <div class="space-y-1">
    <!-- Label -->
    <label v-if="props.label" :for="inputId" class="block text-sm font-medium text-gray-700">
      {{ props.label }}
      <span v-if="props.required" class="text-red-500">*</span>
    </label>
    
    <!-- Input wrapper -->
    <div class="relative">
      <!-- Prefix icon -->
      <div v-if="$slots.prefix" class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400">
        <slot name="prefix" />
      </div>
      
      <!-- Input field -->
      <input
        :id="inputId"
        :type="props.type"
        :value="modelValue"
        :placeholder="props.placeholder"
        :disabled="props.disabled"
        :required="props.required"
        :autocomplete="props.autocomplete"
        :inputmode="props.inputmode"
        :class="[
          'w-full h-12 px-4 rounded-lg border',
          'text-base text-gray-900 placeholder:text-gray-400',
          'transition-colors duration-200',
          'focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent',
          'disabled:bg-gray-100 disabled:cursor-not-allowed',
          $slots.prefix && 'pl-10',
          $slots.suffix && 'pr-10',
          props.error ? 'border-red-500' : 'border-gray-300',
        ]"
        @input="$emit('update:modelValue', $event.target.value)"
        @blur="$emit('blur', $event)"
        @focus="$emit('focus', $event)"
      />
      
      <!-- Suffix icon -->
      <div v-if="$slots.suffix" class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400">
        <slot name="suffix" />
      </div>
    </div>
    
    <!-- Helper text -->
    <p v-if="props.helper && !props.error" class="text-sm text-gray-500">
      {{ props.helper }}
    </p>
    
    <!-- Error message -->
    <p v-if="props.error" class="text-sm text-red-600">
      {{ props.error }}
    </p>
  </div>
</template>

<script setup>
import { computed } from 'vue'

const props = defineProps({
  modelValue: [String, Number],
  type: {
    type: String,
    default: 'text',
  },
  label: String,
  placeholder: String,
  helper: String,
  error: String,
  disabled: Boolean,
  required: Boolean,
  autocomplete: String,
  inputmode: String, // 'numeric', 'tel', 'email', 'search'
})

defineEmits(['update:modelValue', 'blur', 'focus'])

const inputId = computed(() => `input-${Math.random().toString(36).substr(2, 9)}`)
</script>
```

### Bottom Sheet (Mobile Modal)
```vue
<!-- components/BottomSheet.vue -->
<template>
  <Teleport to="body">
    <Transition
      enter-active-class="transition-opacity duration-200"
      leave-active-class="transition-opacity duration-200"
      enter-from-class="opacity-0"
      leave-to-class="opacity-0"
    >
      <div
        v-if="props.open"
        class="fixed inset-0 bg-black/50 z-50"
        @click="handleClose"
      />
    </Transition>
    
    <Transition
      enter-active-class="transition-transform duration-300 ease-out"
      leave-active-class="transition-transform duration-200 ease-in"
      enter-from-class="translate-y-full"
      leave-to-class="translate-y-full"
    >
      <div
        v-if="props.open"
        class="fixed bottom-0 left-0 right-0 z-50 bg-white rounded-t-2xl shadow-2xl"
        @click.stop
      >
        <!-- Drag handle -->
        <div class="flex justify-center pt-3 pb-2">
          <div class="w-12 h-1 bg-gray-300 rounded-full" />
        </div>
        
        <!-- Header -->
        <div v-if="props.title" class="px-4 pb-3 border-b">
          <div class="flex items-center justify-between">
            <h3 class="text-lg font-semibold text-gray-900">{{ props.title }}</h3>
            <button
              class="w-8 h-8 flex items-center justify-center rounded-full hover:bg-gray-100"
              @click="handleClose"
            >
              <XIcon class="w-5 h-5" />
            </button>
          </div>
        </div>
        
        <!-- Content -->
        <div class="px-4 py-6 max-h-[70vh] overflow-y-auto pb-safe-bottom">
          <slot />
        </div>
      </div>
    </Transition>
  </Teleport>
</template>

<script setup>
const props = defineProps({
  open: Boolean,
  title: String,
})

const emit = defineEmits(['close'])

const handleClose = () => {
  emit('close')
}
</script>
```

### Card Component
```vue
<!-- components/Card.vue -->
<template>
  <div
    :class="[
      'bg-white rounded-xl shadow-sm border border-gray-200',
      'transition-all duration-200',
      props.interactive && 'active:scale-98 cursor-pointer',
      props.padding ? `p-${props.padding}` : 'p-4',
    ]"
    @click="handleClick"
  >
    <slot />
  </div>
</template>

<script setup>
const props = defineProps({
  interactive: Boolean,
  padding: {
    type: String,
    default: '4',
  },
})

const emit = defineEmits(['click'])

const handleClick = (e) => {
  if (props.interactive) {
    emit('click', e)
  }
}
</script>
```

---

## 🚀 Performance Optimizations

### 1. Image Optimization
```vue
<template>
  <!-- Use WebP with fallback -->
  <picture>
    <source srcset="/images/photo.webp" type="image/webp" />
    <img
      src="/images/photo.jpg"
      alt="Description"
      loading="lazy"
      class="w-full h-auto"
    />
  </picture>
  
  <!-- Blur placeholder for better perceived performance -->
  <img
    :src="imageUrl"
    :style="{ backgroundImage: `url(${blurDataUrl})` }"
    class="w-full h-auto bg-cover bg-center"
    loading="lazy"
  />
</template>
```

### 2. Lazy Loading
```javascript
// Use Intersection Observer for lazy loading
const observer = new IntersectionObserver((entries) => {
  entries.forEach(entry => {
    if (entry.isIntersecting) {
      // Load component/image
      loadComponent()
      observer.unobserve(entry.target)
    }
  })
})
```

### 3. Virtual Scrolling (Long Lists)
```vue
<template>
  <!-- Use virtual-scroller for 100+ items -->
  <RecycleScroller
    :items="items"
    :item-size="80"
    key-field="id"
    class="h-screen"
  >
    <template #default="{ item }">
      <div class="h-20 border-b px-4 py-3">
        {{ item.name }}
      </div>
    </template>
  </RecycleScroller>
</template>
```

---

## 📲 Progressive Web App (PWA)

### Manifest Configuration
```json
{
  "name": "Bideshgomon",
  "short_name": "Bideshgomon",
  "description": "International Travel & Migration Services",
  "start_url": "/",
  "display": "standalone",
  "background_color": "#ffffff",
  "theme_color": "#4CAF50",
  "orientation": "portrait",
  "icons": [
    {
      "src": "/icons/icon-72x72.png",
      "sizes": "72x72",
      "type": "image/png",
      "purpose": "any maskable"
    },
    {
      "src": "/icons/icon-192x192.png",
      "sizes": "192x192",
      "type": "image/png",
      "purpose": "any maskable"
    },
    {
      "src": "/icons/icon-512x512.png",
      "sizes": "512x512",
      "type": "image/png",
      "purpose": "any maskable"
    }
  ]
}
```

### Service Worker (Offline Support)
```javascript
// public/sw.js
const CACHE_NAME = 'bideshgomon-v1'
const STATIC_CACHE = [
  '/',
  '/css/app.css',
  '/js/app.js',
  '/offline.html',
]

self.addEventListener('install', (event) => {
  event.waitUntil(
    caches.open(CACHE_NAME).then((cache) => {
      return cache.addAll(STATIC_CACHE)
    })
  )
})

self.addEventListener('fetch', (event) => {
  event.respondWith(
    caches.match(event.request).then((response) => {
      return response || fetch(event.request)
    })
  )
})
```

---

## 🎯 Mobile Navigation Patterns

### Bottom Navigation (Primary Pattern)
```vue
<template>
  <nav class="fixed bottom-0 left-0 right-0 h-16 pb-safe-bottom bg-white border-t z-40">
    <div class="flex items-center justify-around h-full">
      <NavItem
        v-for="item in navItems"
        :key="item.name"
        :icon="item.icon"
        :label="item.label"
        :active="route.name === item.route"
        @click="router.push({ name: item.route })"
      />
    </div>
  </nav>
</template>

<script setup>
const navItems = [
  { name: 'home', icon: HomeIcon, label: 'Home', route: 'user.dashboard' },
  { name: 'services', icon: BriefcaseIcon, label: 'Services', route: 'user.services' },
  { name: 'applications', icon: ClipboardIcon, label: 'Applications', route: 'user.applications' },
  { name: 'wallet', icon: WalletIcon, label: 'Wallet', route: 'user.wallet' },
  { name: 'profile', icon: UserIcon, label: 'Profile', route: 'user.profile' },
]
</script>
```

### Hamburger Menu (Secondary Pattern)
```vue
<template>
  <!-- Slide-out menu -->
  <Transition
    enter-active-class="transition-transform duration-300"
    leave-active-class="transition-transform duration-200"
    enter-from-class="-translate-x-full"
    leave-to-class="-translate-x-full"
  >
    <div
      v-if="open"
      class="fixed top-0 left-0 bottom-0 w-80 bg-white shadow-2xl z-50 pt-safe-top pb-safe-bottom"
    >
      <div class="p-4 border-b">
        <div class="flex items-center justify-between">
          <h2 class="text-lg font-semibold">Menu</h2>
          <button @click="close" class="w-10 h-10 flex items-center justify-center">
            <XIcon class="w-6 h-6" />
          </button>
        </div>
      </div>
      
      <nav class="p-2">
        <MenuItem v-for="item in menuItems" :key="item.name" v-bind="item" />
      </nav>
    </div>
  </Transition>
</template>
```

---

## ✅ Mobile Testing Checklist

```markdown
- [ ] Tested on iPhone SE (375px) - smallest common size
- [ ] Tested on Android budget phone (Chrome)
- [ ] All touch targets minimum 48x48px
- [ ] No horizontal scrolling on any page
- [ ] Forms prevent iOS zoom (font-size >= 16px)
- [ ] Loading states for all actions
- [ ] Offline support via service worker
- [ ] Fast page transitions (< 100ms)
- [ ] Images optimized (WebP, lazy loading)
- [ ] Text readable without zooming
- [ ] Safe area insets respected (notch/home bar)
- [ ] Haptic feedback on important actions
```

---

## 🌐 Bangladesh-Specific Optimizations

### 1. Data Saver Mode
```javascript
// Detect data saver mode
const dataSaverEnabled = navigator.connection?.saveData || false

if (dataSaverEnabled) {
  // Load lower quality images
  // Disable autoplay videos
  // Reduce unnecessary API calls
}
```

### 2. Slow Network Handling
```javascript
// Detect slow connection
const connection = navigator.connection
const isSlow = connection?.effectiveType === '2g' || connection?.effectiveType === '3g'

if (isSlow) {
  // Show "Slow connection" warning
  // Reduce image quality
  // Enable aggressive caching
}
```

### 3. Bengali Language Support
```css
/* Ensure Bengali text renders properly */
body {
  font-family: 'Noto Sans Bengali', 'Hind Siliguri', system-ui, sans-serif;
}

/* Increase line height for Bengali */
.bn {
  line-height: 1.8;
}
```

---

## 🎨 Dark Mode (Battery Saver for AMOLED)

```vue
<template>
  <div :class="isDark ? 'dark' : ''">
    <!-- App content -->
  </div>
</template>

<style>
/* Dark mode colors */
.dark {
  --bg-primary: #0A0A0A;      /* Pure black for AMOLED
  --bg-secondary: #1A1A1A;
  --text-primary: #FFFFFF;
  --text-secondary: #A0A0A0;
}
</style>
```

---

**Summary**: Design every component for mobile first (375px), ensure 48px minimum touch targets, use bottom navigation, optimize images, implement PWA features, and test on real budget Android devices!
