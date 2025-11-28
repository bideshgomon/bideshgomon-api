# BideshGomon Rhythmic Design System
## A Seamless, User-Centric Visual Language

> **Vision**: Every element flows like visual music—harmonized, balanced, and synchronized.

---

## 🎨 Design Philosophy

### Core Principles

1. **Rhythmic Flow**: Consistent spacing, typography, and animations create a visual beat
2. **Cultural Warmth + Global Feel**: Bangladesh heritage meets international aesthetics
3. **Unified Consistency**: Every component feels orchestrated as one system
4. **Journey Harmony**: Natural progression through user flows

---

## 🌊 Color Palette

### Primary Colors

#### Ocean Blue (Primary Brand)
```css
ocean-500: #0087ff  /* Main brand color - trust, depth, travel */
ocean-50 → ocean-950  /* Full scale for variations */
```
**Usage**: Primary buttons, links, focus states, brand elements

#### Sky Blue (Secondary)
```css
sky-500: #0ea5e9  /* Peaceful, aspirational */
```
**Usage**: Informational elements, secondary actions

#### Growth Green (Success)
```css
growth-500: #10b981  /* Prosperity, achievement, approval */
```
**Usage**: Success states, approvals, positive CTAs, wallet credits

### Accent Colors

#### Sunrise Orange (Energy)
```css
sunrise-500: #f97316  /* Warmth, action, excitement */
```
**Usage**: Primary CTAs, urgent actions, "Get Started" buttons

#### Gold Yellow (Premium)
```css
gold-500: #eab308  /* Achievement, value, premium features */
```
**Usage**: Premium badges, rewards, pending states

#### Heritage Pink/Red (Cultural)
```css
heritage-600: #db2777  /* Bangladesh flag inspiration */
```
**Usage**: Cultural accents, special occasions, national pride elements

### Semantic Colors

```css
/* Status Colors */
Pending: gold-100/700     /* Yellow - awaiting action */
Processing: sky-100/700    /* Blue - in progress */
Approved: growth-100/700   /* Green - success */
Rejected: red-100/700      /* Red - denied */
Cancelled: gray-100/700    /* Gray - inactive */
```

---

## ✍️ Typography

### Font Families

```css
font-sans: 'Inter var', 'Inter', system-ui
font-display: 'Poppins', 'Inter var', system-ui
```

**Inter**: Body text, UI elements (clean, readable)  
**Poppins**: Headings, hero text (bold, confident, aspirational)

### Type Scale (with Vertical Rhythm)

```css
/* Display Sizes (Hero sections) */
display-2xl: 4.5rem (72px) | line-height: 1.1 | weight: 700
display-xl:  3.75rem (60px) | line-height: 1.1 | weight: 700
display-lg:  3rem (48px)    | line-height: 1.2 | weight: 700
display-md:  2.25rem (36px) | line-height: 1.3 | weight: 600
display-sm:  1.875rem (30px) | line-height: 1.4 | weight: 600

/* Body & UI */
text-xl:  1.25rem (20px)  | line-height: 1.75 (rhythm)
text-lg:  1.125rem (18px) | line-height: 1.75
text-base: 1rem (16px)    | line-height: 1.75
text-sm:  0.875rem (14px) | line-height: 1.5
text-xs:  0.75rem (12px)  | line-height: 1.5
```

### Line Height (Golden Ratio)
```css
line-rhythm: 1.75  /* Optimal reading rhythm */
```

---

## 📏 Spacing System (4px Base Grid)

### Rhythmic Spacing Scale

```css
rhythm-xs:  0.25rem (4px)   /* Tight spacing */
rhythm-sm:  0.5rem  (8px)   /* Small gaps */
rhythm-md:  1rem    (16px)  /* Default spacing */
rhythm-lg:  1.5rem  (24px)  /* Section spacing */
rhythm-xl:  2rem    (32px)  /* Large spacing */
rhythm-2xl: 3rem    (48px)  /* Section padding */
rhythm-3xl: 4rem    (64px)  /* Major sections */
rhythm-4xl: 6rem    (96px)  /* Hero spacing */
rhythm-5xl: 8rem    (128px) /* Page breaks */
```

### Usage Guidelines

- **Component padding**: `rhythm-md` to `rhythm-lg`
- **Section padding**: `rhythm-2xl` to `rhythm-3xl`
- **Element gaps**: `rhythm-sm` to `rhythm-md`
- **Page margins**: `rhythm-xl` to `rhythm-2xl`

---

## 🔲 Border Radius

```css
rhythm-sm:  0.5rem  (8px)   /* Buttons, badges */
rhythm-md:  0.75rem (12px)  /* Inputs, small cards */
rhythm-lg:  1rem    (16px)  /* Cards, modals */
rhythm-xl:  1.5rem  (24px)  /* Large cards */
rhythm-2xl: 2rem    (32px)  /* Hero sections */
full:       9999px          /* Pills, avatars */
```

---

## 🌑 Shadows (Depth & Elevation)

```css
/* Rhythmic Shadows */
shadow-rhythm-sm:  0 2px 8px rgba(0,0,0,0.05)   /* Subtle hover */
shadow-rhythm:     0 4px 16px rgba(0,0,0,0.08)  /* Default cards */
shadow-rhythm-lg:  0 8px 24px rgba(0,0,0,0.12)  /* Elevated cards */
shadow-rhythm-xl:  0 12px 32px rgba(0,0,0,0.15) /* Modals, dropdowns */

/* Glow Effects (Brand accents) */
shadow-glow-ocean:   0 0 24px rgba(0,135,255,0.3)
shadow-glow-growth:  0 0 24px rgba(16,185,129,0.3)
shadow-glow-sunrise: 0 0 24px rgba(249,115,22,0.3)
```

---

## 🎬 Animation System

### Timing & Duration

```css
/* Duration */
duration-200: 200ms   /* Micro-interactions (hover, focus) */
duration-300: 300ms   /* Standard UI transitions */
duration-400: 400ms   /* Rhythmic default */
duration-600: 600ms   /* Page transitions, major changes */

/* Easing */
ease-rhythm: cubic-bezier(0.4, 0, 0.2, 1)  /* Smooth, natural */
```

### Keyframe Animations

```css
/* Entrance Animations */
animate-fade-in:      0.6s ease-out
animate-fade-in-up:   0.6s ease-out (translateY 20px → 0)
animate-fade-in-down: 0.6s ease-out (translateY -20px → 0)
animate-slide-in-right: 0.5s ease-out
animate-slide-in-left:  0.5s ease-out
animate-scale-in:     0.4s ease-out

/* Continuous Animations */
animate-pulse-slow:   3s infinite (opacity pulse)
animate-wave:         2.5s infinite (vertical float)
animate-float:        3s infinite (smooth hover)
animate-blob:         7s infinite (organic movement)
```

### Usage Guidelines

- **Page loads**: `fade-in-up` for hero sections
- **Cards entering**: `scale-in` on scroll
- **CTAs**: Subtle `hover:-translate-y-0.5` lift
- **Status indicators**: `pulse-slow` for active states
- **Background elements**: `blob` for organic feel

---

## 🧩 Core Components

### RhythmicCard

```vue
<RhythmicCard
  title="Card Title"
  description="Card description"
  badge="New"
  variant="ocean|sky|growth|sunrise|gold"
  size="sm|md|lg"
>
  <template #icon>
    <GlobeIcon class="h-6 w-6 text-ocean-600" />
  </template>
  <!-- Card content -->
  <template #footer>
    <!-- Actions -->
  </template>
</RhythmicCard>
```

**Features**:
- Consistent padding, shadows, hover effects
- Variant-based color schemes
- Icon, title, description, badge, footer slots
- Smooth transitions on hover

### FlowButton

```vue
<FlowButton
  variant="ocean|sky|growth|sunrise|outline|ghost"
  size="xs|sm|md|lg|xl"
  rounded="sm|md|lg|xl|full"
  :loading="isLoading"
  :disabled="isDisabled"
>
  <template #iconBefore>
    <RocketIcon class="h-5 w-5" />
  </template>
  Get Started
  <template #iconAfter>
    <ArrowRightIcon class="h-5 w-5" />
  </template>
</FlowButton>
```

**Features**:
- Gradient backgrounds, glow shadows
- Loading states with spinner
- Icon slots before/after text
- Smooth hover lift effect

### ProgressWave

```vue
<ProgressWave
  :steps="[
    { label: 'Submit', description: 'Application form' },
    { label: 'Review', description: 'Agency review' },
    { label: 'Payment', description: 'Process payment' },
    { label: 'Complete', description: 'Visa issued' }
  ]"
  :currentStep="2"
  variant="ocean|growth|sunrise"
  :animated="true"
  :showPercentage="true"
/>
```

**Features**:
- Wave-style progress indicator
- Checkmarks for completed steps
- Pulse animation on current step
- Optional percentage display

### StatusBadge

```vue
<StatusBadge
  status="pending|processing|approved|rejected|active"
  text="Approved"
  size="sm|md|lg"
  :showDot="true"
  :pulse="true"
/>
```

**Features**:
- Semantic color coding
- Optional status dot indicator
- Pulse animation option
- Consistent sizing

### AnimatedSection

```vue
<AnimatedSection
  title="Section Title"
  subtitle="Section description"
  badge="Featured"
  variant="light|dark|ocean|gradient|sunrise"
  container="default|narrow|wide|full"
  :blobs="true"
  :pattern="true"
  :animated="true"
>
  <template #header>
    <!-- Custom header -->
  </template>
  <!-- Section content -->
  <template #footer>
    <!-- Section footer -->
  </template>
</AnimatedSection>
```

**Features**:
- Section-level container with consistent padding
- Animated background blobs
- Optional pattern overlays
- Title, subtitle, badge support
- Multiple variant styles

### CountryFlag

```vue
<CountryFlag
  country-code="BD"
  country-name="Bangladesh"
  size="xs|sm|md|lg"
  :showName="true"
  :useImage="true"
/>
```

**Features**:
- High-quality flag images from CDN
- Fallback to emoji flags
- Optional country name display
- Consistent sizing

---

## 📱 Responsive Design

### Breakpoints

```css
sm:  640px   /* Mobile landscape */
md:  768px   /* Tablet portrait */
lg:  1024px  /* Tablet landscape */
xl:  1280px  /* Desktop */
2xl: 1536px  /* Large desktop */
```

### Mobile-First Approach

```vue
<!-- Base: Mobile -->
<div class="text-sm px-4 py-2">
  <!-- sm: Mobile landscape -->
  <div class="sm:text-base sm:px-6">
    <!-- md: Tablet -->
    <div class="md:text-lg md:px-8">
      <!-- lg: Desktop -->
      <div class="lg:text-xl lg:px-10">
      </div>
    </div>
  </div>
</div>
```

### Responsive Typography

- **Hero headings**: `text-3xl sm:text-4xl lg:text-5xl`
- **Section titles**: `text-2xl sm:text-3xl lg:text-4xl`
- **Body text**: `text-base sm:text-lg`
- **Captions**: `text-xs sm:text-sm`

---

## 🎯 Component Usage Examples

### Service Card

```vue
<RhythmicCard variant="ocean" size="md">
  <template #icon>
    <GlobeAltIcon class="h-6 w-6 text-ocean-600" />
  </template>
  <template #default>
    <h3 class="font-display font-semibold text-xl text-ocean-900 mb-2">
      Tourist Visa
    </h3>
    <p class="text-gray-600 mb-4">
      Apply for tourist visas to 50+ countries with smart form auto-fill.
    </p>
    <StatusBadge status="active" text="Available" size="sm" />
  </template>
  <template #footer>
    <FlowButton variant="ocean" size="sm" fullWidth>
      Apply Now
      <template #iconAfter>
        <ArrowRightIcon class="h-4 w-4" />
      </template>
    </FlowButton>
  </template>
</RhythmicCard>
```

### Hero Section

```vue
<AnimatedSection
  variant="ocean"
  :blobs="true"
  badge="Your Gateway to Global Opportunities"
  title="Go Abroad with Confidence"
  subtitle="All-in-one platform for visa applications, job search, and travel services"
  container="default"
>
  <div class="flex justify-center gap-4 mt-8">
    <FlowButton variant="sunrise" size="lg">
      <template #iconBefore>
        <RocketLaunchIcon class="h-5 w-5" />
      </template>
      Get Started Free
      <template #iconAfter>
        <ArrowRightIcon class="h-5 w-5" />
      </template>
    </FlowButton>
    <FlowButton variant="outline" size="lg">
      Learn More
    </FlowButton>
  </div>
</AnimatedSection>
```

### Application Tracker

```vue
<div class="card-rhythm">
  <ProgressWave
    :steps="applicationSteps"
    :currentStep="currentStepIndex"
    variant="growth"
    :showPercentage="true"
    :animated="true"
  />
  
  <div class="mt-6">
    <StatusBadge 
      :status="application.status" 
      :text="application.statusText"
      size="md"
      :pulse="application.status === 'processing'"
    />
  </div>
</div>
```

---

## 🌍 Cultural Integration

### Bangladesh Elements

1. **Color Accents**: Heritage pink/red from flag
2. **Patterns**: Subtle traditional Bengali patterns in backgrounds
3. **Typography**: Bengali font support (`font-sans` includes Bengali)
4. **Imagery**: Local landmarks, cultural elements in hero sections
5. **Localization**: Date formats (DD/MM/YYYY), currency (৳), phone (01XXX)

### Global Feel

1. **Universal Icons**: Heroicons for consistency
2. **Country Flags**: High-quality flags for 195 countries
3. **Professional Tone**: International business aesthetic
4. **Accessibility**: WCAG AA compliant colors, focus states

---

## ✅ Implementation Checklist

### Phase 1: Foundation ✅
- [x] Update `tailwind.config.js` with design tokens
- [x] Create color palette (ocean, sky, growth, sunrise, gold, heritage)
- [x] Define typography scale with vertical rhythm
- [x] Set up spacing system (4px grid)
- [x] Configure animation keyframes

### Phase 2: Core Components ✅
- [x] RhythmicCard component
- [x] FlowButton component
- [x] ProgressWave component
- [x] StatusBadge component
- [x] AnimatedSection component
- [x] CountryFlag component

### Phase 3: Page Redesigns (In Progress)
- [ ] Landing page (Welcome.vue)
- [ ] User Dashboard
- [ ] Service Listings
- [ ] Application Tracking
- [ ] Admin Panel

### Phase 4: Polish
- [ ] Add cultural imagery
- [ ] Optimize animations for mobile
- [ ] Test responsive behavior
- [ ] Accessibility audit
- [ ] Performance optimization

---

## 📚 Resources

### Design Tools
- **Figma**: [Design file link]
- **Color Palette**: [coolors.co link]
- **Typography**: Google Fonts (Inter, Poppins)

### Code Examples
- See `resources/js/Components/Rhythmic/` for all components
- Check `tailwind.config.js` for all design tokens
- Reference existing pages for usage patterns

### External Assets
- **Flag Images**: https://flagcdn.com
- **Icons**: Heroicons (https://heroicons.com)
- **Images**: Unsplash, Pexels (travel, migration themes)

---

## 🤝 Contributing

When adding new components:

1. Follow naming convention: `RhythmicComponentName.vue`
2. Use design tokens from `tailwind.config.js`
3. Support `variant` and `size` props
4. Include hover/focus states
5. Add smooth transitions (400ms default)
6. Document props and slots
7. Provide usage example

---

**Last Updated**: November 28, 2025  
**Design System Version**: 1.0.0  
**Status**: Foundation Complete, Page Redesigns In Progress
