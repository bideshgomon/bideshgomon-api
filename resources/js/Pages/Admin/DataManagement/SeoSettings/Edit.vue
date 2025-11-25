<template>
    <AdminLayout>
        <div class="px-4 sm:px-6 lg:px-8 py-8">
            <!-- Header -->
            <div class="mb-6">
                <Link :href="route('admin.data.seo-settings.index')" class="text-sm font-medium text-blue-600 hover:text-blue-500 dark:text-blue-400 flex items-center gap-1 mb-4">
                    <ChevronLeftIcon class="h-4 w-4" />
                    Back to SEO Settings
                </Link>
                <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Edit SEO Setting</h1>
                <p class="mt-2 text-sm text-gray-700 dark:text-gray-300">Update SEO metadata for "{{ seoSetting.page_type }}".</p>
            </div>

            <!-- Form -->
            <form @submit.prevent="submit" class="space-y-6">
                <!-- Basic SEO -->
                <div class="bg-white dark:bg-gray-800 shadow-sm ring-1 ring-gray-900/5 rounded-lg p-6 space-y-6">
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white border-b border-gray-200 dark:border-gray-700 pb-3">Basic SEO</h2>
                    
                    <div>
                        <label for="page_type" class="block text-sm font-medium text-gray-900 dark:text-white">Page Type <span class="text-red-500">*</span></label>
                        <input
                            type="text"
                            id="page_type"
                            v-model="form.page_type"
                            class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-blue-500 focus:ring-blue-500"
                            placeholder="home, about, contact, jobs, etc."
                            required
                        />
                        <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Unique identifier for this page (e.g., home, jobs, visa-services)</p>
                        <p v-if="form.errors.page_type" class="mt-1 text-sm text-red-600 dark:text-red-400">{{ form.errors.page_type }}</p>
                    </div>

                    <div>
                        <label for="title" class="block text-sm font-medium text-gray-900 dark:text-white">Meta Title</label>
                        <input
                            type="text"
                            id="title"
                            v-model="form.title"
                            maxlength="70"
                            class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-blue-500 focus:ring-blue-500"
                        />
                        <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">{{ form.title?.length || 0 }}/70 characters (60-70 recommended)</p>
                        <p v-if="form.errors.title" class="mt-1 text-sm text-red-600 dark:text-red-400">{{ form.errors.title }}</p>
                    </div>

                    <div>
                        <label for="description" class="block text-sm font-medium text-gray-900 dark:text-white">Meta Description</label>
                        <textarea
                            id="description"
                            v-model="form.description"
                            maxlength="160"
                            rows="3"
                            class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-blue-500 focus:ring-blue-500"
                        ></textarea>
                        <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">{{ form.description?.length || 0 }}/160 characters (150-160 recommended)</p>
                        <p v-if="form.errors.description" class="mt-1 text-sm text-red-600 dark:text-red-400">{{ form.errors.description }}</p>
                    </div>

                    <div>
                        <label for="keywords" class="block text-sm font-medium text-gray-900 dark:text-white">Meta Keywords</label>
                        <textarea
                            id="keywords"
                            v-model="form.keywords"
                            rows="2"
                            class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-blue-500 focus:ring-blue-500"
                            placeholder="keyword1, keyword2, keyword3"
                        ></textarea>
                        <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Comma-separated keywords</p>
                        <p v-if="form.errors.keywords" class="mt-1 text-sm text-red-600 dark:text-red-400">{{ form.errors.keywords }}</p>
                    </div>

                    <div>
                        <label for="canonical_url" class="block text-sm font-medium text-gray-900 dark:text-white">Canonical URL</label>
                        <input
                            type="url"
                            id="canonical_url"
                            v-model="form.canonical_url"
                            class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-blue-500 focus:ring-blue-500"
                            placeholder="https://bideshgomon.com/page"
                        />
                        <p v-if="form.errors.canonical_url" class="mt-1 text-sm text-red-600 dark:text-red-400">{{ form.errors.canonical_url }}</p>
                    </div>
                </div>

                <!-- Open Graph -->
                <div class="bg-white dark:bg-gray-800 shadow-sm ring-1 ring-gray-900/5 rounded-lg p-6 space-y-6">
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white border-b border-gray-200 dark:border-gray-700 pb-3">Open Graph (Facebook)</h2>
                    
                    <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                        <div>
                            <label for="og_title" class="block text-sm font-medium text-gray-900 dark:text-white">OG Title</label>
                            <input
                                type="text"
                                id="og_title"
                                v-model="form.og_title"
                                class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-blue-500 focus:ring-blue-500"
                            />
                        </div>
                        <div>
                            <label for="og_type" class="block text-sm font-medium text-gray-900 dark:text-white">OG Type</label>
                            <select
                                id="og_type"
                                v-model="form.og_type"
                                class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-blue-500 focus:ring-blue-500"
                            >
                                <option value="website">Website</option>
                                <option value="article">Article</option>
                                <option value="product">Product</option>
                                <option value="profile">Profile</option>
                            </select>
                        </div>
                    </div>

                    <div>
                        <label for="og_description" class="block text-sm font-medium text-gray-900 dark:text-white">OG Description</label>
                        <textarea
                            id="og_description"
                            v-model="form.og_description"
                            rows="2"
                            class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-blue-500 focus:ring-blue-500"
                        ></textarea>
                    </div>

                    <div>
                        <label for="og_image" class="block text-sm font-medium text-gray-900 dark:text-white">OG Image URL</label>
                        <input
                            type="url"
                            id="og_image"
                            v-model="form.og_image"
                            class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-blue-500 focus:ring-blue-500"
                            placeholder="https://example.com/image.jpg"
                        />
                        <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Recommended: 1200x630px</p>
                    </div>
                </div>

                <!-- Twitter Card -->
                <div class="bg-white dark:bg-gray-800 shadow-sm ring-1 ring-gray-900/5 rounded-lg p-6 space-y-6">
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white border-b border-gray-200 dark:border-gray-700 pb-3">Twitter Card</h2>
                    
                    <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                        <div>
                            <label for="twitter_card" class="block text-sm font-medium text-gray-900 dark:text-white">Card Type</label>
                            <select
                                id="twitter_card"
                                v-model="form.twitter_card"
                                class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-blue-500 focus:ring-blue-500"
                            >
                                <option value="summary">Summary</option>
                                <option value="summary_large_image">Summary Large Image</option>
                                <option value="app">App</option>
                                <option value="player">Player</option>
                            </select>
                        </div>
                        <div>
                            <label for="twitter_site" class="block text-sm font-medium text-gray-900 dark:text-white">Twitter Site</label>
                            <input
                                type="text"
                                id="twitter_site"
                                v-model="form.twitter_site"
                                class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                placeholder="@bideshgomon"
                            />
                        </div>
                    </div>

                    <div>
                        <label for="twitter_title" class="block text-sm font-medium text-gray-900 dark:text-white">Twitter Title</label>
                        <input
                            type="text"
                            id="twitter_title"
                            v-model="form.twitter_title"
                            class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-blue-500 focus:ring-blue-500"
                        />
                    </div>

                    <div>
                        <label for="twitter_description" class="block text-sm font-medium text-gray-900 dark:text-white">Twitter Description</label>
                        <textarea
                            id="twitter_description"
                            v-model="form.twitter_description"
                            rows="2"
                            class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-blue-500 focus:ring-blue-500"
                        ></textarea>
                    </div>

                    <div>
                        <label for="twitter_image" class="block text-sm font-medium text-gray-900 dark:text-white">Twitter Image URL</label>
                        <input
                            type="url"
                            id="twitter_image"
                            v-model="form.twitter_image"
                            class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-blue-500 focus:ring-blue-500"
                            placeholder="https://example.com/image.jpg"
                        />
                    </div>
                </div>

                <!-- Schema & Robots -->
                <div class="bg-white dark:bg-gray-800 shadow-sm ring-1 ring-gray-900/5 rounded-lg p-6 space-y-6">
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white border-b border-gray-200 dark:border-gray-700 pb-3">Schema Markup & Robots</h2>
                    
                    <div>
                        <label for="schema_markup" class="block text-sm font-medium text-gray-900 dark:text-white">Schema Markup (JSON-LD)</label>
                        <textarea
                            id="schema_markup"
                            v-model="form.schema_markup"
                            rows="5"
                            class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-blue-500 focus:ring-blue-500 font-mono text-sm"
                            placeholder='{"@context":"https://schema.org","@type":"Organization","name":"Example"}'
                        ></textarea>
                        <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Valid JSON-LD schema markup</p>
                        <p v-if="form.errors.schema_markup" class="mt-1 text-sm text-red-600 dark:text-red-400">{{ form.errors.schema_markup }}</p>
                    </div>

                    <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                        <div class="flex items-center gap-2">
                            <input
                                type="checkbox"
                                id="index"
                                v-model="form.index"
                                class="h-4 w-4 rounded border-gray-300 dark:border-gray-600 text-blue-600 focus:ring-blue-500"
                            />
                            <label for="index" class="text-sm font-medium text-gray-900 dark:text-white">Allow Indexing</label>
                        </div>
                        <div class="flex items-center gap-2">
                            <input
                                type="checkbox"
                                id="follow"
                                v-model="form.follow"
                                class="h-4 w-4 rounded border-gray-300 dark:border-gray-600 text-blue-600 focus:ring-blue-500"
                            />
                            <label for="follow" class="text-sm font-medium text-gray-900 dark:text-white">Follow Links</label>
                        </div>
                    </div>

                    <div>
                        <label for="robots" class="block text-sm font-medium text-gray-900 dark:text-white">Additional Robots Directives</label>
                        <input
                            type="text"
                            id="robots"
                            v-model="form.robots"
                            class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-blue-500 focus:ring-blue-500"
                            placeholder="noarchive, nosnippet, max-snippet:160"
                        />
                        <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Comma-separated additional directives</p>
                    </div>
                </div>

                <!-- Actions -->
                <div class="flex items-center justify-end gap-3">
                    <Link :href="route('admin.data.seo-settings.index')" class="px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm text-sm font-medium text-gray-700 dark:text-gray-200 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700">
                        Cancel
                    </Link>
                    <button
                        type="submit"
                        :disabled="form.processing"
                        class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 disabled:opacity-50"
                    >
                        <span v-if="form.processing">Updating...</span>
                        <span v-else>Update SEO Setting</span>
                    </button>
                </div>
            </form>
        </div>
    </AdminLayout>
</template>

<script setup>
import { useForm, Link } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'
import { ChevronLeftIcon } from '@heroicons/vue/24/outline'

const props = defineProps({
    seoSetting: Object,
})

const form = useForm({
    page_type: props.seoSetting.page_type,
    title: props.seoSetting.title || '',
    description: props.seoSetting.description || '',
    keywords: props.seoSetting.keywords || '',
    canonical_url: props.seoSetting.canonical_url || '',
    og_title: props.seoSetting.og_title || '',
    og_description: props.seoSetting.og_description || '',
    og_image: props.seoSetting.og_image || '',
    og_type: props.seoSetting.og_type || 'website',
    twitter_card: props.seoSetting.twitter_card || 'summary_large_image',
    twitter_title: props.seoSetting.twitter_title || '',
    twitter_description: props.seoSetting.twitter_description || '',
    twitter_image: props.seoSetting.twitter_image || '',
    twitter_site: props.seoSetting.twitter_site || '',
    schema_markup: props.seoSetting.schema_markup ? JSON.stringify(props.seoSetting.schema_markup) : '',
    index: props.seoSetting.index,
    follow: props.seoSetting.follow,
    robots: props.seoSetting.robots || '',
})

const submit = () => {
    form.put(route('admin.data.seo-settings.update', props.seoSetting.id))
}
</script>
