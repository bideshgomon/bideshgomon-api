<template>
    <AdminLayout>
        <Head title="SEO Settings" />

        <div class="py-6">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <!-- Header -->
                <div class="mb-8">
                    <div class="flex justify-between items-center">
                        <div>
                            <h1 class="text-3xl font-bold text-gray-900">SEO Settings</h1>
                            <p class="mt-2 text-sm text-gray-600">
                                Configure meta tags, social media previews, and structured data for each page type
                            </p>
                        </div>
                        <div class="flex gap-3">
                            <button
                                @click="generateSitemap"
                                :disabled="generating"
                                class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-lg shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
                            >
                                <svg v-if="!generating" class="h-5 w-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                                <svg v-else class="animate-spin h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                {{ generating ? 'Generating...' : 'Generate Sitemap' }}
                            </button>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-xl shadow-sm border border-gray-200">
                    <!-- Tabs -->
                    <div class="border-b border-gray-200">
                        <nav class="flex space-x-4 px-6 overflow-x-auto" aria-label="Tabs">
                            <button
                                v-for="page in pageTypes"
                                :key="page"
                                type="button"
                                @click="activeTab = page"
                                :class="[
                                    activeTab === page
                                        ? 'border-indigo-500 text-indigo-600'
                                        : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300',
                                    'whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm capitalize transition-colors'
                                ]"
                            >
                                {{ formatPageType(page) }}
                            </button>
                        </nav>
                    </div>

                    <!-- Form -->
                    <form @submit.prevent="submit" class="p-6">
                        <div class="space-y-8">
                            <!-- Basic Meta Tags Section -->
                            <div class="pb-8 border-b border-gray-200">
                                <h3 class="text-lg font-semibold text-gray-900 mb-4">Basic Meta Tags</h3>
                                <div class="space-y-6">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-900 mb-2">
                                            Meta Title <span class="text-red-500">*</span>
                                        </label>
                                        <input
                                            v-model="form.title"
                                            type="text"
                                            maxlength="255"
                                            class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                                            :class="{ 'border-red-500': form.errors.title }"
                                            placeholder="Enter meta title (recommended: 50-60 characters)"
                                        />
                                        <div class="flex justify-between items-center mt-2">
                                            <p v-if="form.errors.title" class="text-red-600 text-sm">{{ form.errors.title }}</p>
                                            <span class="ml-auto text-xs text-gray-500">{{ form.title.length }}/255 characters</span>
                                        </div>
                                    </div>

                                    <div>
                                        <label class="block text-sm font-medium text-gray-900 mb-2">
                                            Meta Description <span class="text-red-500">*</span>
                                        </label>
                                        <textarea
                                            v-model="form.description"
                                            rows="3"
                                            maxlength="500"
                                            class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                                            :class="{ 'border-red-500': form.errors.description }"
                                            placeholder="Enter meta description (recommended: 150-160 characters)"
                                        ></textarea>
                                        <div class="flex justify-between items-center mt-2">
                                            <p v-if="form.errors.description" class="text-red-600 text-sm">{{ form.errors.description }}</p>
                                            <span class="ml-auto text-xs text-gray-500">{{ form.description.length }}/500 characters</span>
                                        </div>
                                    </div>

                                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                                        <div>
                                            <label class="block text-sm font-medium text-gray-900 mb-2">Meta Keywords</label>
                                            <input
                                                v-model="form.keywords"
                                                type="text"
                                                class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                                                placeholder="keyword1, keyword2, keyword3"
                                            />
                                        </div>

                                        <div>
                                            <label class="block text-sm font-medium text-gray-900 mb-2">Canonical URL</label>
                                            <input
                                                v-model="form.canonical_url"
                                                type="url"
                                                class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                                                :class="{ 'border-red-500': form.errors.canonical_url }"
                                                placeholder="https://example.com/page"
                                            />
                                            <p v-if="form.errors.canonical_url" class="mt-2 text-red-600 text-sm">{{ form.errors.canonical_url }}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Open Graph Section -->
                            <div class="pb-8 border-b border-gray-200">
                                <h3 class="text-lg font-semibold text-gray-900 mb-4">Open Graph (Facebook)</h3>
                                <div class="space-y-6">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-900 mb-2">OG Title</label>
                                        <input
                                            v-model="form.og_title"
                                            type="text"
                                            class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                                            placeholder="Title for social media sharing"
                                        />
                                    </div>

                                    <div>
                                        <label class="block text-sm font-medium text-gray-900 mb-2">OG Description</label>
                                        <textarea
                                            v-model="form.og_description"
                                            rows="3"
                                            class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                                            placeholder="Description for social media sharing"
                                        ></textarea>
                                    </div>

                                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                                        <div>
                                            <label class="block text-sm font-medium text-gray-900 mb-2">OG Image URL</label>
                                            <input
                                                v-model="form.og_image"
                                                type="url"
                                                class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                                                placeholder="https://example.com/image.jpg"
                                            />
                                            <p class="text-xs text-gray-500 mt-1">Recommended: 1200×630px</p>
                                        </div>

                                        <div>
                                            <label class="block text-sm font-medium text-gray-900 mb-2">OG Type</label>
                                            <select
                                                v-model="form.og_type"
                                                class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                                            >
                                                <option value="website">Website</option>
                                                <option value="article">Article</option>
                                                <option value="product">Product</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Twitter Card Section -->
                            <div class="pb-8 border-b border-gray-200">
                                <h3 class="text-lg font-semibold text-gray-900 mb-4">Twitter Cards</h3>
                                <div class="space-y-6">
                                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                                        <div>
                                            <label class="block text-sm font-medium text-gray-900 mb-2">Card Type</label>
                                            <select
                                                v-model="form.twitter_card"
                                                class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                                            >
                                                <option value="summary">Summary</option>
                                                <option value="summary_large_image">Summary Large Image</option>
                                                <option value="app">App</option>
                                                <option value="player">Player</option>
                                            </select>
                                        </div>

                                        <div>
                                            <label class="block text-sm font-medium text-gray-900 mb-2">Twitter Handle</label>
                                            <input
                                                v-model="form.twitter_site"
                                                type="text"
                                                class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                                                placeholder="@yourusername"
                                            />
                                        </div>
                                    </div>

                                    <div>
                                        <label class="block text-sm font-medium text-gray-900 mb-2">Twitter Title</label>
                                        <input
                                            v-model="form.twitter_title"
                                            type="text"
                                            class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                                            placeholder="Title for Twitter cards"
                                        />
                                    </div>

                                    <div>
                                        <label class="block text-sm font-medium text-gray-900 mb-2">Twitter Description</label>
                                        <textarea
                                            v-model="form.twitter_description"
                                            rows="3"
                                            class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                                            placeholder="Description for Twitter cards"
                                        ></textarea>
                                    </div>

                                    <div>
                                        <label class="block text-sm font-medium text-gray-900 mb-2">Twitter Image URL</label>
                                        <input
                                            v-model="form.twitter_image"
                                            type="url"
                                            class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                                            placeholder="https://example.com/twitter-image.jpg"
                                        />
                                        <p class="text-xs text-gray-500 mt-1">Recommended: 1200×600px</p>
                                    </div>
                                </div>
                            </div>

                            <!-- Schema.org Section -->
                            <div class="pb-8 border-b border-gray-200">
                                <h3 class="text-lg font-semibold text-gray-900 mb-4">Schema.org Structured Data</h3>
                                <div>
                                    <label class="block text-sm font-medium text-gray-900 mb-2">JSON-LD Markup</label>
                                    <textarea
                                        v-model="schemaMarkup"
                                        rows="8"
                                        class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm font-mono bg-gray-50"
                                        placeholder='{"@context": "https://schema.org", "@type": "Organization", "name": "Your Company"}'
                                    ></textarea>
                                    <p class="text-xs text-gray-500 mt-1">Enter valid JSON-LD structured data markup</p>
                                </div>
                            </div>

                            <!-- Robots Meta Section -->
                            <div>
                                <h3 class="text-lg font-semibold text-gray-900 mb-4">Robots Directives</h3>
                                <div class="space-y-6">
                                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                                        <div class="relative inline-flex items-center">
                                            <input
                                                id="index-checkbox"
                                                v-model="form.index"
                                                type="checkbox"
                                                class="h-4 w-4 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500"
                                            />
                                            <label for="index-checkbox" class="ml-3 block text-sm font-medium text-gray-900">
                                                Allow search engines to index this page
                                            </label>
                                        </div>

                                        <div class="relative inline-flex items-center">
                                            <input
                                                id="follow-checkbox"
                                                v-model="form.follow"
                                                type="checkbox"
                                                class="h-4 w-4 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500"
                                            />
                                            <label for="follow-checkbox" class="ml-3 block text-sm font-medium text-gray-900">
                                                Allow search engines to follow links
                                            </label>
                                        </div>
                                    </div>

                                    <div>
                                        <label class="block text-sm font-medium text-gray-900 mb-2">Additional Directives</label>
                                        <input
                                            v-model="form.robots"
                                            type="text"
                                            class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                                            placeholder="e.g., noarchive, noimageindex"
                                        />
                                        <p class="text-xs text-gray-500 mt-1">Comma-separated directives like: noarchive, noimageindex, nosnippet</p>
                                    </div>

                                    <div class="bg-gray-50 p-4 rounded-lg border border-gray-200">
                                        <h4 class="text-sm font-medium text-gray-900 mb-2">Preview</h4>
                                        <code class="block p-3 bg-white rounded border border-gray-300 text-sm font-mono text-gray-800">
                                            &lt;meta name="robots" content="{{ robotsContent }}" /&gt;
                                        </code>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Submit Button -->
                        <div class="mt-8 flex justify-end gap-3">
                            <button
                                type="button"
                                @click="resetForm"
                                class="inline-flex items-center px-6 py-3 border border-gray-300 rounded-lg shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 transition-colors"
                            >
                                Reset
                            </button>
                            <button
                                type="submit"
                                :disabled="form.processing"
                                class="inline-flex items-center px-6 py-3 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-700 hover:to-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 disabled:opacity-50 disabled:cursor-not-allowed transition-all"
                            >
                                {{ form.processing ? 'Saving...' : 'Save SEO Settings' }}
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Info Panel -->
                <div class="mt-6 bg-blue-50 border border-blue-200 rounded-lg p-4">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <h3 class="text-sm font-medium text-blue-800">SEO Information</h3>
                            <div class="mt-2 text-sm text-blue-700">
                                <ul class="list-disc list-inside space-y-1">
                                    <li>Configure unique SEO settings for each page type</li>
                                    <li>Optimal title length: 50-60 characters</li>
                                    <li>Optimal description length: 150-160 characters</li>
                                    <li>Changes are applied immediately to the frontend</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>

<script setup>
import { ref, computed, watch } from 'vue'
import { router, useForm } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'
import { Head } from '@inertiajs/vue3'

const props = defineProps({
    settings: {
        type: Object,
        default: () => ({})
    },
    pageTypes: {
        type: Array,
        default: () => ['home', 'services', 'jobs', 'blog', 'visa', 'insurance', 'hotels', 'flights', 'cv-builder', 'about', 'contact']
    }
})

const activeTab = ref(props.pageTypes?.[0] || 'home')
const generating = ref(false)

const form = useForm({
    title: '',
    description: '',
    keywords: '',
    canonical_url: '',
    og_title: '',
    og_description: '',
    og_image: '',
    og_type: 'website',
    twitter_card: 'summary_large_image',
    twitter_site: '',
    twitter_title: '',
    twitter_description: '',
    twitter_image: '',
    schema_markup: {},
    index: true,
    follow: true,
    robots: ''
})

const schemaMarkup = ref('')

const robotsContent = computed(() => {
    let content = []
    if (form.index) content.push('index')
    else content.push('noindex')
    
    if (form.follow) content.push('follow')
    else content.push('nofollow')
    
    if (form.robots) {
        content.push(form.robots)
    }
    
    return content.join(', ')
})

watch(activeTab, (newTab) => {
    loadSettings(newTab)
})

watch(() => props.settings, () => {
    loadSettings(activeTab.value)
}, { deep: true })

function loadSettings(pageType) {
    const settings = props.settings?.[pageType]
    if (settings) {
        form.title = settings.title || ''
        form.description = settings.description || ''
        form.keywords = settings.keywords || ''
        form.canonical_url = settings.canonical_url || ''
        form.og_title = settings.og_title || ''
        form.og_description = settings.og_description || ''
        form.og_image = settings.og_image || ''
        form.og_type = settings.og_type || 'website'
        form.twitter_card = settings.twitter_card || 'summary_large_image'
        form.twitter_site = settings.twitter_site || ''
        form.twitter_title = settings.twitter_title || ''
        form.twitter_description = settings.twitter_description || ''
        form.twitter_image = settings.twitter_image || ''
        form.index = settings.index ?? true
        form.follow = settings.follow ?? true
        form.robots = settings.robots || ''
        
        schemaMarkup.value = settings.schema_markup 
            ? JSON.stringify(settings.schema_markup, null, 2) 
            : ''
    } else {
        resetForm()
    }
}

function submit() {
    // Parse schema markup
    try {
        form.schema_markup = schemaMarkup.value ? JSON.parse(schemaMarkup.value) : {}
    } catch (e) {
        alert('Invalid JSON in Schema Markup field')
        return
    }
    
    form.put(route('admin.seo-settings.update', activeTab.value), {
        preserveScroll: true,
        onSuccess: () => {
            // Success handled by flash message
        }
    })
}

function resetForm() {
    form.reset()
    schemaMarkup.value = ''
}

function generateSitemap() {
    if (confirm('Generate sitemap.xml file?')) {
        generating.value = true
        router.post(route('admin.seo-settings.generate-sitemap'), {}, {
            onFinish: () => {
                generating.value = false
            }
        })
    }
}

function formatPageType(type) {
    return type.split('-').map(word => word.charAt(0).toUpperCase() + word.slice(1)).join(' ')
}

// Load initial settings
loadSettings(activeTab.value)
</script>
