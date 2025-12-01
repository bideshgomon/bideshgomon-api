# SEO Infrastructure Setup - Phase 2.2

## Completed Components

### 1. Sitemap Generation
✅ **Package Installed**: `spatie/laravel-sitemap`
- Automatic sitemap.xml generation
- Dynamic content from database
- Includes services, countries, blog posts

✅ **SitemapController** (`app/Http/Controllers/SitemapController.php`)
- Static pages (homepage, about, contact, legal)
- Dynamic services and service modules
- Countries catalog
- Blog posts (when available)
- Proper priority and change frequency settings

✅ **Route**: `/sitemap.xml`

### 2. SEO Components

✅ **SEOHead Component** (`resources/js/Components/SEO/SEOHead.vue`)
- Meta tags (title, description, keywords)
- Open Graph tags (Facebook)
- Twitter Card tags
- Canonical URL
- JSON-LD structured data
- Theme colors and mobile app metadata

**Props:**
- `title`: Page title
- `description`: Meta description
- `keywords`: Meta keywords
- `image`: OG image
- `url`: Page URL
- `type`: OG type (website, article, etc.)
- `canonical`: Canonical URL
- `robots`: Robots directive
- `schema`: Custom JSON-LD schema

✅ **Breadcrumb Component** (`resources/js/Components/SEO/Breadcrumb.vue`)
- Visual breadcrumb navigation
- Schema.org BreadcrumbList markup
- Responsive design
- Arrow separators

**Props:**
- `items`: Array of {title, url, current}

### 3. Schema Service

✅ **SchemaService** (`app/Services/SchemaService.php`)
- Organization schema
- Service schema
- FAQPage schema
- JobPosting schema
- Article/BlogPosting schema
- BreadcrumbList schema
- AggregateRating schema
- LocalBusiness schema

**Methods:**
```php
$schema->organization();
$schema->service($service);
$schema->faqPage($faqs);
$schema->jobPosting($job);
$schema->article($post);
$schema->breadcrumbList($breadcrumbs);
$schema->aggregateRating($name, $rating, $count);
$schema->localBusiness();
```

### 4. SEO Composable

✅ **useSEO Composable** (`resources/js/Composables/useSEO.js`)
- Frontend schema generation helpers
- Meta tag utilities
- Canonical URL helper
- Image URL helper

**Methods:**
```javascript
import { useSEO } from '@/Composables/useSEO'
const { generateSchema, getMetaTags, getCanonicalUrl, getFullImageUrl } = useSEO()

// Generate schemas
generateSchema.organization()
generateSchema.service(service)
generateSchema.faq(faqs)
generateSchema.breadcrumb(items)
generateSchema.article(post)
generateSchema.localBusiness()

// Get meta tags
const meta = getMetaTags({
  title: 'Custom Title',
  description: 'Custom description',
  image: '/images/custom.jpg'
})
```

## Usage Examples

### Example 1: Service Page with SEO

```vue
<script setup>
import SEOHead from '@/Components/SEO/SEOHead.vue'
import Breadcrumb from '@/Components/SEO/Breadcrumb.vue'
import { useSEO } from '@/Composables/useSEO'

const props = defineProps(['service'])
const { generateSchema } = useSEO()

const breadcrumbs = [
  { title: 'Home', url: '/', current: false },
  { title: 'Services', url: '/services', current: false },
  { title: props.service.name, url: '', current: true }
]

const serviceSchema = generateSchema.service(props.service)
</script>

<template>
  <SEOHead
    :title="`${service.name} - BideshGomon`"
    :description="service.description"
    :keywords="`${service.name}, visa application, Bangladesh`"
    :image="service.featured_image"
    :schema="serviceSchema"
  />
  
  <div>
    <Breadcrumb :items="breadcrumbs" />
    
    <h1>{{ service.name }}</h1>
    <!-- Page content -->
  </div>
</template>
```

### Example 2: FAQ Page

```vue
<script setup>
import SEOHead from '@/Components/SEO/SEOHead.vue'
import { useSEO } from '@/Composables/useSEO'

const faqs = [
  {
    question: 'How long does visa processing take?',
    answer: 'Visa processing typically takes 7-15 business days...'
  },
  {
    question: 'What documents are required?',
    answer: 'Required documents include passport, photographs...'
  }
]

const { generateSchema } = useSEO()
const faqSchema = generateSchema.faq(faqs)
</script>

<template>
  <SEOHead
    title="Frequently Asked Questions - BideshGomon"
    description="Common questions about visa applications, processing times, and requirements"
    :schema="faqSchema"
  />
  
  <div>
    <h1>FAQs</h1>
    <div v-for="faq in faqs" :key="faq.question">
      <h3>{{ faq.question }}</h3>
      <p>{{ faq.answer }}</p>
    </div>
  </div>
</template>
```

### Example 3: Backend Service Usage

```php
use App\Services\SchemaService;

class ServiceController extends Controller
{
    public function show(Service $service)
    {
        $schemaService = new SchemaService();
        
        return Inertia::render('Services/Show', [
            'service' => $service,
            'schema' => $schemaService->service($service),
            'breadcrumbs' => [
                ['title' => 'Home', 'url' => route('homepage')],
                ['title' => 'Services', 'url' => route('services.index')],
                ['title' => $service->name, 'url' => '']
            ]
        ]);
    }
}
```

## Sitemap Configuration

### Routes Included

**Static Pages** (Priority 0.7-1.0)
- Homepage: Priority 1.0, Daily updates
- Services: Priority 0.9, Weekly updates
- About: Priority 0.8, Monthly updates
- Contact: Priority 0.7, Monthly updates
- Legal pages: Priority 0.3, Yearly updates

**Dynamic Content** (Priority 0.6-0.8)
- Services: Priority 0.8, Weekly updates
- Service Modules: Priority 0.7, Weekly updates
- Countries: Priority 0.6, Monthly updates
- Blog Posts: Priority 0.6, Monthly updates

### Testing Sitemap

```bash
# View sitemap in browser
http://localhost/sitemap.xml

# Test with curl
curl http://localhost/sitemap.xml

# Validate with Google Search Console
# Upload sitemap.xml URL to Search Console
```

## SEO Best Practices Applied

### 1. Meta Tags
✅ Unique title tags (50-60 characters)
✅ Meta descriptions (150-160 characters)
✅ Keywords optimization
✅ Canonical URLs to prevent duplicates

### 2. Open Graph
✅ Facebook/LinkedIn sharing optimization
✅ Custom OG images (1200x630px recommended)
✅ Proper OG type tags
✅ Locale tags (en_US, bn_BD)

### 3. Twitter Cards
✅ Summary Large Image cards
✅ Twitter handle (@bideshgomon)
✅ Optimized images

### 4. Structured Data
✅ JSON-LD format (Google recommended)
✅ Multiple schema types
✅ Valid schema.org markup
✅ Rich snippets enabled

### 5. Mobile Optimization
✅ Viewport meta tags
✅ Theme color for mobile browsers
✅ Apple mobile web app tags
✅ Mobile-friendly design system

## Bangladesh-Specific SEO

### Language Support
- English (primary): `en_US`
- Bengali (alternate): `bn_BD`
- Bilingual keywords and content

### Local Business Markup
- Dhaka location coordinates
- BST timezone (Asia/Dhaka)
- BDT currency (৳)
- Local business hours

### Target Keywords
- "visa application Bangladesh"
- "immigration services Bangladesh"
- "visa consultant Dhaka"
- "বিদেশ গমন" (Bengali)
- Country-specific: "USA visa Bangladesh", "Canada immigration Bangladesh"

## Performance Considerations

### Sitemap Caching
Consider caching the sitemap for large sites:

```php
// In SitemapController
public function index()
{
    return Cache::remember('sitemap', 3600, function () {
        $sitemap = Sitemap::create();
        // ... generate sitemap
        return $sitemap->toResponse(request());
    });
}
```

### Image Optimization
- Compress OG images (< 1MB)
- Use WebP format when possible
- Lazy load non-critical images
- Use CDN for static assets

### Schema Validation
Test schemas with:
- Google Rich Results Test: https://search.google.com/test/rich-results
- Schema.org Validator: https://validator.schema.org/

## Next Steps (Not Yet Implemented)

### Inertia SSR (Server-Side Rendering)
For better SEO and initial load performance:

```bash
# Install dependencies
npm install @inertiajs/server

# Build SSR bundle
npm run build -- --ssr

# Start SSR server
node bootstrap/ssr/ssr.mjs
```

Update `vite.config.js`:
```javascript
export default defineConfig({
    plugins: [
        laravel({
            input: 'resources/js/app.js',
            ssr: 'resources/js/ssr.js', // Add SSR entry
            refresh: true,
        }),
    ],
})
```

### OG Image Generator
Dynamic image generation for better social sharing:

```bash
composer require intervention/image
```

Create dynamic OG images with service names, descriptions, and branding.

### hreflang Tags
For multi-language support:

```html
<link rel="alternate" hreflang="en" href="https://bideshgomon.com/services" />
<link rel="alternate" hreflang="bn" href="https://bideshgomon.com/bn/services" />
```

## Testing Checklist

- [ ] Sitemap accessible at `/sitemap.xml`
- [ ] All dynamic content included in sitemap
- [ ] Meta tags rendering correctly
- [ ] OG tags working (test with Facebook Debugger)
- [ ] Twitter cards working (test with Twitter Card Validator)
- [ ] Schemas valid (test with Google Rich Results)
- [ ] Canonical URLs correct
- [ ] Breadcrumbs rendering with schema
- [ ] Mobile meta tags working
- [ ] Image URLs absolute (not relative)

## Resources

- **Sitemap Package**: https://github.com/spatie/laravel-sitemap
- **Schema.org**: https://schema.org/
- **Google Search Console**: https://search.google.com/search-console
- **Facebook Debugger**: https://developers.facebook.com/tools/debug/
- **Twitter Card Validator**: https://cards-dev.twitter.com/validator
- **Google Rich Results Test**: https://search.google.com/test/rich-results

---

**Status**: ✅ Phase 2.2 Core Components Complete  
**Remaining**: SSR setup, OG image generator, hreflang tags (optional enhancements)
