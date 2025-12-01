<script setup>
import { computed } from 'vue'
import { usePage } from '@inertiajs/vue3'

const props = defineProps({
    title: {
        type: String,
        default: null
    },
    description: {
        type: String,
        default: null
    },
    keywords: {
        type: String,
        default: null
    },
    image: {
        type: String,
        default: null
    },
    url: {
        type: String,
        default: null
    },
    type: {
        type: String,
        default: 'website'
    },
    canonical: {
        type: String,
        default: null
    },
    robots: {
        type: String,
        default: 'index, follow'
    },
    schema: {
        type: Object,
        default: null
    }
})

const page = usePage()

const defaultTitle = 'BideshGomon - Your Trusted Bangladesh Visa & Immigration Partner'
const defaultDescription = 'Professional visa application services for Bangladeshi citizens. Student visas, work permits, tourism visas, and immigration assistance for USA, UK, Canada, Australia, and 50+ countries.'
const defaultKeywords = 'visa application Bangladesh, immigration services Bangladesh, student visa, work permit, tourism visa, BideshGomon, visa consultant Dhaka'
const defaultImage = '/images/og-image.jpg'

const pageTitle = computed(() => {
    return props.title || page.props.title || defaultTitle
})

const pageDescription = computed(() => {
    return props.description || page.props.description || defaultDescription
})

const pageKeywords = computed(() => {
    return props.keywords || page.props.keywords || defaultKeywords
})

const pageImage = computed(() => {
    const img = props.image || page.props.image || defaultImage
    return img.startsWith('http') ? img : `${window.location.origin}${img}`
})

const pageUrl = computed(() => {
    return props.url || props.canonical || window.location.href
})

const pageCanonical = computed(() => {
    return props.canonical || window.location.href.split('?')[0]
})

const structuredData = computed(() => {
    if (props.schema) {
        return JSON.stringify(props.schema)
    }
    
    // Default Organization schema
    return JSON.stringify({
        '@context': 'https://schema.org',
        '@type': 'Organization',
        'name': 'BideshGomon',
        'url': 'https://bideshgomon.com',
        'logo': 'https://bideshgomon.com/images/logo.png',
        'description': defaultDescription,
        'address': {
            '@type': 'PostalAddress',
            'addressCountry': 'BD',
            'addressLocality': 'Dhaka',
            'addressRegion': 'Dhaka Division'
        },
        'contactPoint': {
            '@type': 'ContactPoint',
            'telephone': '+880-1234-567890',
            'contactType': 'Customer Service',
            'areaServed': 'BD',
            'availableLanguage': ['Bengali', 'English']
        },
        'sameAs': [
            'https://facebook.com/bideshgomon',
            'https://twitter.com/bideshgomon',
            'https://linkedin.com/company/bideshgomon'
        ]
    })
})
</script>

<template>
    <Head>
        <!-- Title -->
        <title>{{ pageTitle }}</title>
        
        <!-- Meta Tags -->
        <meta name="description" :content="pageDescription" />
        <meta name="keywords" :content="pageKeywords" />
        <meta name="robots" :content="robots" />
        <meta name="author" content="BideshGomon" />
        
        <!-- Canonical URL -->
        <link rel="canonical" :href="pageCanonical" />
        
        <!-- Open Graph / Facebook -->
        <meta property="og:type" :content="type" />
        <meta property="og:url" :content="pageUrl" />
        <meta property="og:title" :content="pageTitle" />
        <meta property="og:description" :content="pageDescription" />
        <meta property="og:image" :content="pageImage" />
        <meta property="og:site_name" content="BideshGomon" />
        <meta property="og:locale" content="en_US" />
        <meta property="og:locale:alternate" content="bn_BD" />
        
        <!-- Twitter Card -->
        <meta name="twitter:card" content="summary_large_image" />
        <meta name="twitter:url" :content="pageUrl" />
        <meta name="twitter:title" :content="pageTitle" />
        <meta name="twitter:description" :content="pageDescription" />
        <meta name="twitter:image" :content="pageImage" />
        <meta name="twitter:site" content="@bideshgomon" />
        <meta name="twitter:creator" content="@bideshgomon" />
        
        <!-- Additional Meta -->
        <meta name="theme-color" content="#0087ff" />
        <meta name="mobile-web-app-capable" content="yes" />
        <meta name="apple-mobile-web-app-capable" content="yes" />
        <meta name="apple-mobile-web-app-status-bar-style" content="default" />
        <meta name="apple-mobile-web-app-title" content="BideshGomon" />
        
        <!-- JSON-LD Structured Data -->
        <script type="application/ld+json" v-html="structuredData"></script>
    </Head>
</template>
