<template>
    <AuthenticatedLayout>
        <Head title="SEO Settings Management" />

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 border-b border-gray-200">
                        <div class="flex justify-between items-center mb-6">
                            <div>
                                <h2 class="text-2xl font-bold text-gray-900">SEO Settings Management</h2>
                                <p class="mt-1 text-sm text-gray-600">
                                    Configure meta tags, Open Graph, Twitter Cards, and Schema.org markup for each page type
                                </p>
                            </div>
                            <button
                                @click="generateSitemap"
                                :disabled="generating"
                                class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 focus:bg-green-700 active:bg-green-900 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition ease-in-out duration-150 disabled:opacity-50"
                            >
                                <svg v-if="!generating" class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                                <svg v-else class="animate-spin w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                {{ generating ? 'Generating...' : 'Generate Sitemap' }}
                            </button>
                        </div>

                        <!-- Success/Error Messages -->
                        <div v-if="$page.props.flash.success" class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">
                            {{ $page.props.flash.success }}
                        </div>
                        <div v-if="$page.props.flash.error" class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
                            {{ $page.props.flash.error }}
                        </div>

                        <!-- Tabs -->
                        <div class="border-b border-gray-200">
                            <nav class="-mb-px flex space-x-8" aria-label="Tabs">
                                <button
                                    v-for="page in pageTypes"
                                    :key="page"
                                    @click="activeTab = page"
                                    :class="[
                                        activeTab === page
                                            ? 'border-indigo-500 text-indigo-600'
                                            : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300',
                                        'whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm capitalize'
                                    ]"
                                >
                                    {{ page.replace('-', ' ') }}
                                </button>
                            </nav>
                        </div>

                        <!-- Form for active tab -->
                        <form @submit.prevent="submitForm" class="mt-6 space-y-6">
                            <!-- Basic Meta Tags -->
                            <div class="bg-gray-50 p-4 rounded-lg">
                                <h3 class="text-lg font-medium text-gray-900 mb-4">Basic Meta Tags</h3>
                                
                                <div class="space-y-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">Page Title</label>
                                        <input
                                            v-model="form.title"
                                            type="text"
                                            maxlength="255"
                                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                            placeholder="e.g., BideshGomon - Your Gateway to International Opportunities"
                                        />
                                        <p class="mt-1 text-xs text-gray-500">{{ form.title?.length || 0 }}/255 characters</p>
                                        <p v-if="form.errors.title" class="mt-1 text-sm text-red-600">{{ form.errors.title }}</p>
                                    </div>

                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">Meta Description</label>
                                        <textarea
                                            v-model="form.description"
                                            rows="3"
                                            maxlength="500"
                                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                            placeholder="Write a compelling description for search results..."
                                        ></textarea>
                                        <p class="mt-1 text-xs text-gray-500">{{ form.description?.length || 0 }}/500 characters (155-160 recommended)</p>
                                        <p v-if="form.errors.description" class="mt-1 text-sm text-red-600">{{ form.errors.description }}</p>
                                    </div>

                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">Keywords</label>
                                        <input
                                            v-model="form.keywords"
                                            type="text"
                                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                            placeholder="visa, migration, jobs, Bangladesh, overseas"
                                        />
                                        <p class="mt-1 text-xs text-gray-500">Comma-separated keywords</p>
                                        <p v-if="form.errors.keywords" class="mt-1 text-sm text-red-600">{{ form.errors.keywords }}</p>
                                    </div>

                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">Canonical URL</label>
                                        <input
                                            v-model="form.canonical_url"
                                            type="url"
                                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                            placeholder="https://bideshgomon.com/page"
                                        />
                                        <p class="mt-1 text-xs text-gray-500">Optional: Override default canonical URL</p>
                                        <p v-if="form.errors.canonical_url" class="mt-1 text-sm text-red-600">{{ form.errors.canonical_url }}</p>
                                    </div>
                                </div>
                            </div>

                            <!-- Open Graph (Facebook) -->
                            <div class="bg-gray-50 p-4 rounded-lg">
                                <h3 class="text-lg font-medium text-gray-900 mb-4">Open Graph Tags (Facebook)</h3>
                                
                                <div class="space-y-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">OG Title</label>
                                        <input
                                            v-model="form.og_title"
                                            type="text"
                                            maxlength="255"
                                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                            placeholder="Title for social media shares"
                                        />
                                        <p v-if="form.errors.og_title" class="mt-1 text-sm text-red-600">{{ form.errors.og_title }}</p>
                                    </div>

                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">OG Description</label>
                                        <textarea
                                            v-model="form.og_description"
                                            rows="2"
                                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                            placeholder="Description for social media shares"
                                        ></textarea>
                                        <p v-if="form.errors.og_description" class="mt-1 text-sm text-red-600">{{ form.errors.og_description }}</p>
                                    </div>

                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">OG Image URL</label>
                                        <input
                                            v-model="form.og_image"
                                            type="url"
                                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                            placeholder="https://bideshgomon.com/images/og-image.jpg"
                                        />
                                        <p class="mt-1 text-xs text-gray-500">Recommended: 1200×630px</p>
                                        <p v-if="form.errors.og_image" class="mt-1 text-sm text-red-600">{{ form.errors.og_image }}</p>
                                    </div>

                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">OG Type</label>
                                        <select
                                            v-model="form.og_type"
                                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                        >
                                            <option value="">Select type</option>
                                            <option value="website">Website</option>
                                            <option value="article">Article</option>
                                            <option value="profile">Profile</option>
                                            <option value="video">Video</option>
                                        </select>
                                        <p v-if="form.errors.og_type" class="mt-1 text-sm text-red-600">{{ form.errors.og_type }}</p>
                                    </div>
                                </div>
                            </div>

                            <!-- Twitter Cards -->
                            <div class="bg-gray-50 p-4 rounded-lg">
                                <h3 class="text-lg font-medium text-gray-900 mb-4">Twitter Card Tags</h3>
                                
                                <div class="space-y-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">Twitter Card Type</label>
                                        <select
                                            v-model="form.twitter_card"
                                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                        >
                                            <option value="">Select card type</option>
                                            <option value="summary">Summary</option>
                                            <option value="summary_large_image">Summary Large Image</option>
                                            <option value="app">App</option>
                                            <option value="player">Player</option>
                                        </select>
                                        <p v-if="form.errors.twitter_card" class="mt-1 text-sm text-red-600">{{ form.errors.twitter_card }}</p>
                                    </div>

                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">Twitter Title</label>
                                        <input
                                            v-model="form.twitter_title"
                                            type="text"
                                            maxlength="255"
                                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                            placeholder="Title for Twitter shares"
                                        />
                                        <p v-if="form.errors.twitter_title" class="mt-1 text-sm text-red-600">{{ form.errors.twitter_title }}</p>
                                    </div>

                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">Twitter Description</label>
                                        <textarea
                                            v-model="form.twitter_description"
                                            rows="2"
                                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                            placeholder="Description for Twitter shares"
                                        ></textarea>
                                        <p v-if="form.errors.twitter_description" class="mt-1 text-sm text-red-600">{{ form.errors.twitter_description }}</p>
                                    </div>

                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">Twitter Image URL</label>
                                        <input
                                            v-model="form.twitter_image"
                                            type="url"
                                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                            placeholder="https://bideshgomon.com/images/twitter-card.jpg"
                                        />
                                        <p class="mt-1 text-xs text-gray-500">Recommended: 1200×628px (summary_large_image) or 120×120px (summary)</p>
                                        <p v-if="form.errors.twitter_image" class="mt-1 text-sm text-red-600">{{ form.errors.twitter_image }}</p>
                                    </div>

                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">Twitter Site Handle</label>
                                        <input
                                            v-model="form.twitter_site"
                                            type="text"
                                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                            placeholder="@BideshGomon"
                                        />
                                        <p class="mt-1 text-xs text-gray-500">Include @ symbol</p>
                                        <p v-if="form.errors.twitter_site" class="mt-1 text-sm text-red-600">{{ form.errors.twitter_site }}</p>
                                    </div>
                                </div>
                            </div>

                            <!-- Schema.org Markup -->
                            <div class="bg-gray-50 p-4 rounded-lg">
                                <h3 class="text-lg font-medium text-gray-900 mb-4">Schema.org Structured Data (JSON-LD)</h3>
                                
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">JSON-LD Markup</label>
                                    <textarea
                                        v-model="schemaMarkupString"
                                        rows="10"
                                        class="mt-1 block w-full font-mono text-sm rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                        placeholder='{"@context": "https://schema.org", "@type": "Organization", "name": "BideshGomon"}'
                                    ></textarea>
                                    <p class="mt-1 text-xs text-gray-500">Enter valid JSON-LD markup for rich snippets</p>
                                    <p v-if="form.errors.schema_markup" class="mt-1 text-sm text-red-600">{{ form.errors.schema_markup }}</p>
                                    <p v-if="schemaError" class="mt-1 text-sm text-red-600">{{ schemaError }}</p>
                                </div>
                            </div>

                            <!-- Robots Meta -->
                            <div class="bg-gray-50 p-4 rounded-lg">
                                <h3 class="text-lg font-medium text-gray-900 mb-4">Search Engine Directives</h3>
                                
                                <div class="space-y-4">
                                    <div class="flex items-center space-x-6">
                                        <label class="flex items-center">
                                            <input
                                                v-model="form.index"
                                                type="checkbox"
                                                class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                            />
                                            <span class="ml-2 text-sm text-gray-700">Allow Indexing</span>
                                        </label>

                                        <label class="flex items-center">
                                            <input
                                                v-model="form.follow"
                                                type="checkbox"
                                                class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                            />
                                            <span class="ml-2 text-sm text-gray-700">Allow Following Links</span>
                                        </label>
                                    </div>

                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">Additional Robots Directives</label>
                                        <input
                                            v-model="form.robots"
                                            type="text"
                                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                            placeholder="e.g., max-snippet:-1, max-image-preview:large"
                                        />
                                        <p class="mt-1 text-xs text-gray-500">Optional additional directives (comma-separated)</p>
                                        <p v-if="form.errors.robots" class="mt-1 text-sm text-red-600">{{ form.errors.robots }}</p>
                                    </div>

                                    <div class="bg-blue-50 border border-blue-200 rounded-md p-3">
                                        <p class="text-sm text-blue-800">
                                            <strong>Current Robots Meta:</strong> {{ computedRobotsMeta }}
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <!-- Actions -->
                            <div class="flex items-center justify-between pt-4 border-t border-gray-200">
                                <button
                                    v-if="hasExistingSettings"
                                    @click.prevent="deleteSeoSettings"
                                    type="button"
                                    class="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700 focus:bg-red-700 active:bg-red-900 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition ease-in-out duration-150"
                                >
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                    </svg>
                                    Reset to Default
                                </button>
                                <div v-else></div>

                                <button
                                    type="submit"
                                    :disabled="form.processing"
                                    class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150 disabled:opacity-50"
                                >
                                    <svg v-if="!form.processing" class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                    </svg>
                                    <svg v-else class="animate-spin w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                    </svg>
                                    {{ form.processing ? 'Saving...' : 'Save SEO Settings' }}
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import { ref, computed, watch } from 'vue'
import { Head, useForm, router } from '@inertiajs/vue3'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'

const props = defineProps({
    settings: Object,
    pageTypes: Array
})

const activeTab = ref(props.pageTypes[0])
const generating = ref(false)
const schemaError = ref(null)

// Convert schema_markup object to string for textarea
const schemaMarkupString = ref('')

const form = useForm({
    title: '',
    description: '',
    keywords: '',
    canonical_url: '',
    og_title: '',
    og_description: '',
    og_image: '',
    og_type: '',
    twitter_card: '',
    twitter_title: '',
    twitter_description: '',
    twitter_image: '',
    twitter_site: '',
    schema_markup: null,
    index: true,
    follow: true,
    robots: ''
})

// Load settings for active tab
const loadTabSettings = () => {
    const settings = props.settings[activeTab.value]
    if (settings) {
        form.title = settings.title || ''
        form.description = settings.description || ''
        form.keywords = settings.keywords || ''
        form.canonical_url = settings.canonical_url || ''
        form.og_title = settings.og_title || ''
        form.og_description = settings.og_description || ''
        form.og_image = settings.og_image || ''
        form.og_type = settings.og_type || ''
        form.twitter_card = settings.twitter_card || ''
        form.twitter_title = settings.twitter_title || ''
        form.twitter_description = settings.twitter_description || ''
        form.twitter_image = settings.twitter_image || ''
        form.twitter_site = settings.twitter_site || ''
        form.index = settings.index !== undefined ? settings.index : true
        form.follow = settings.follow !== undefined ? settings.follow : true
        form.robots = settings.robots || ''
        
        // Convert schema_markup to JSON string
        schemaMarkupString.value = settings.schema_markup 
            ? JSON.stringify(settings.schema_markup, null, 2) 
            : ''
    } else {
        // Reset form for new page type
        form.reset()
        schemaMarkupString.value = ''
    }
    
    form.clearErrors()
    schemaError.value = null
}

// Watch for tab changes
watch(activeTab, loadTabSettings)

// Initialize with first tab
loadTabSettings()

const hasExistingSettings = computed(() => {
    return props.settings[activeTab.value] !== undefined
})

const computedRobotsMeta = computed(() => {
    let directives = []
    directives.push(form.index ? 'index' : 'noindex')
    directives.push(form.follow ? 'follow' : 'nofollow')
    if (form.robots) {
        directives.push(form.robots)
    }
    return directives.join(', ')
})

// Validate and parse schema markup
watch(schemaMarkupString, (newValue) => {
    if (!newValue.trim()) {
        form.schema_markup = null
        schemaError.value = null
        return
    }
    
    try {
        form.schema_markup = JSON.parse(newValue)
        schemaError.value = null
    } catch (e) {
        schemaError.value = 'Invalid JSON format'
    }
})

const submitForm = () => {
    form.put(route('seo-settings.update', activeTab.value), {
        preserveScroll: true,
        onSuccess: () => {
            // Form will automatically show flash messages
        }
    })
}

const deleteSeoSettings = () => {
    if (confirm('Are you sure you want to reset SEO settings for this page? This will revert to default values.')) {
        router.delete(route('seo-settings.destroy', activeTab.value), {
            preserveScroll: true,
            onSuccess: () => {
                loadTabSettings()
            }
        })
    }
}

const generateSitemap = () => {
    if (confirm('Generate XML sitemap from all indexed pages?')) {
        generating.value = true
        router.post(route('seo-settings.generate-sitemap'), {}, {
            preserveScroll: true,
            onFinish: () => {
                generating.value = false
            }
        })
    }
}
</script>
