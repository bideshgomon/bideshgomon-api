<template>
    <AdminLayout>
        <div class="px-4 sm:px-6 lg:px-8 py-8">
            <!-- Header -->
            <div class="sm:flex sm:items-center sm:justify-between mb-6">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900 dark:text-white">SEO Settings</h1>
                    <p class="mt-2 text-sm text-gray-700 dark:text-gray-300">Manage SEO metadata and social sharing settings for different pages.</p>
                </div>
                <div class="mt-4 sm:mt-0 flex gap-3">
                    <Link :href="route('admin.data.seo-settings.bulk-upload')" class="inline-flex items-center px-4 py-2 border border-gray-300 dark:border-gray-600 shadow-sm text-sm font-medium rounded-md text-gray-700 dark:text-gray-200 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700">
                        <ArrowUpTrayIcon class="-ml-1 mr-2 h-5 w-5 text-gray-500 dark:text-gray-400" />
                        Bulk Upload
                    </Link>
                    <Link :href="route('admin.data.seo-settings.create')" class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700">
                        <PlusIcon class="-ml-1 mr-2 h-5 w-5" />
                        Add SEO Setting
                    </Link>
                </div>
            </div>

            <!-- Filters -->
            <div class="mb-6 grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
                <div class="lg:col-span-2">
                    <input
                        v-model="filters.search"
                        type="text"
                        placeholder="Search pages..."
                        class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-white shadow-sm focus:border-blue-500 focus:ring-blue-500"
                        @input="debouncedSearch"
                    />
                </div>
                <div>
                    <select
                        v-model="filters.index"
                        class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-white shadow-sm focus:border-blue-500 focus:ring-blue-500"
                        @change="applyFilters"
                    >
                        <option value="">All Indexing</option>
                        <option value="1">Indexed</option>
                        <option value="0">No Index</option>
                    </select>
                </div>
                <div>
                    <button
                        @click="resetFilters"
                        class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm text-sm font-medium text-gray-700 dark:text-gray-200 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700"
                    >
                        Reset Filters
                    </button>
                </div>
            </div>

            <!-- Table -->
            <div class="bg-white dark:bg-gray-800 shadow-sm ring-1 ring-gray-900/5 rounded-lg overflow-hidden">
                <table class="min-w-full divide-y divide-gray-300 dark:divide-gray-700">
                    <thead class="bg-gray-50 dark:bg-gray-900">
                        <tr>
                            <th class="py-3.5 pl-4 pr-3 text-left text-sm font-semibold text-gray-900 dark:text-white">Page Type</th>
                            <th class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900 dark:text-white">Title</th>
                            <th class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900 dark:text-white">Robots</th>
                            <th class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900 dark:text-white">Social</th>
                            <th class="relative py-3.5 pl-3 pr-4 sm:pr-6 text-right text-sm font-semibold text-gray-900 dark:text-white">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700 bg-white dark:bg-gray-800">
                        <tr v-for="setting in seoSettings.data" :key="setting.id" class="hover:bg-gray-50 dark:hover:bg-gray-700">
                            <td class="whitespace-nowrap py-4 pl-4 pr-3">
                                <div class="flex items-center">
                                    <MagnifyingGlassCircleIcon class="h-8 w-8 text-blue-500 mr-3" />
                                    <div>
                                        <div class="font-medium text-gray-900 dark:text-white">{{ setting.page_type }}</div>
                                        <div v-if="setting.canonical_url" class="text-xs text-gray-500 dark:text-gray-400 truncate max-w-xs">{{ setting.canonical_url }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-3 py-4">
                                <div class="max-w-md">
                                    <div v-if="setting.title" class="text-sm font-medium text-gray-900 dark:text-white truncate">{{ setting.title }}</div>
                                    <div v-if="setting.description" class="text-xs text-gray-500 dark:text-gray-400 line-clamp-2">{{ setting.description }}</div>
                                    <div v-if="!setting.title && !setting.description" class="text-sm text-gray-400 dark:text-gray-500 italic">No metadata</div>
                                </div>
                            </td>
                            <td class="whitespace-nowrap px-3 py-4">
                                <div class="flex flex-col gap-1">
                                    <span class="inline-flex items-center rounded-md px-2 py-1 text-xs font-medium"
                                        :class="setting.index 
                                            ? 'bg-green-50 text-green-700 ring-1 ring-inset ring-green-600/20 dark:bg-green-500/10 dark:text-green-400 dark:ring-green-500/30'
                                            : 'bg-red-50 text-red-700 ring-1 ring-inset ring-red-600/20 dark:bg-red-500/10 dark:text-red-400 dark:ring-red-500/30'">
                                        {{ setting.index ? 'Index' : 'NoIndex' }}
                                    </span>
                                    <span class="inline-flex items-center rounded-md px-2 py-1 text-xs font-medium"
                                        :class="setting.follow 
                                            ? 'bg-green-50 text-green-700 ring-1 ring-inset ring-green-600/20 dark:bg-green-500/10 dark:text-green-400 dark:ring-green-500/30'
                                            : 'bg-red-50 text-red-700 ring-1 ring-inset ring-red-600/20 dark:bg-red-500/10 dark:text-red-400 dark:ring-red-500/30'">
                                        {{ setting.follow ? 'Follow' : 'NoFollow' }}
                                    </span>
                                </div>
                            </td>
                            <td class="whitespace-nowrap px-3 py-4">
                                <div class="flex items-center gap-2">
                                    <span v-if="setting.og_title" class="inline-flex items-center gap-1 text-xs text-blue-600 dark:text-blue-400">
                                        <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 24 24"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>
                                        OG
                                    </span>
                                    <span v-if="setting.twitter_title" class="inline-flex items-center gap-1 text-xs text-sky-600 dark:text-sky-400">
                                        <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 24 24"><path d="M23.953 4.57a10 10 0 01-2.825.775 4.958 4.958 0 002.163-2.723c-.951.555-2.005.959-3.127 1.184a4.92 4.92 0 00-8.384 4.482C7.69 8.095 4.067 6.13 1.64 3.162a4.822 4.822 0 00-.666 2.475c0 1.71.87 3.213 2.188 4.096a4.904 4.904 0 01-2.228-.616v.06a4.923 4.923 0 003.946 4.827 4.996 4.996 0 01-2.212.085 4.936 4.936 0 004.604 3.417 9.867 9.867 0 01-6.102 2.105c-.39 0-.779-.023-1.17-.067a13.995 13.995 0 007.557 2.209c9.053 0 13.998-7.496 13.998-13.985 0-.21 0-.42-.015-.63A9.935 9.935 0 0024 4.59z"/></svg>
                                        Twitter
                                    </span>
                                    <span v-if="setting.schema_markup" class="inline-flex items-center gap-1 text-xs text-purple-600 dark:text-purple-400">
                                        <CodeBracketIcon class="h-4 w-4" />
                                        Schema
                                    </span>
                                </div>
                            </td>
                            <td class="relative whitespace-nowrap py-4 pl-3 pr-4 text-right text-sm font-medium sm:pr-6">
                                <div class="flex justify-end gap-2">
                                    <Link :href="route('admin.data.seo-settings.edit', setting.id)" class="text-blue-600 hover:text-blue-900 dark:text-blue-400 dark:hover:text-blue-300">
                                        <PencilIcon class="h-5 w-5" />
                                    </Link>
                                    <button @click="confirmDelete(setting)" class="text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-300">
                                        <TrashIcon class="h-5 w-5" />
                                    </button>
                                </div>
                            </td>
                        </tr>
                        <tr v-if="seoSettings.data.length === 0">
                            <td colspan="5" class="px-3 py-8 text-center text-sm text-gray-500 dark:text-gray-400">
                                No SEO settings found.
                            </td>
                        </tr>
                    </tbody>
                </table>

                <!-- Pagination -->
                <div v-if="seoSettings.data.length > 0" class="flex items-center justify-between border-t border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 px-4 py-3 sm:px-6">
                    <div class="flex flex-1 justify-between sm:hidden">
                        <Link v-if="seoSettings.prev_page_url" :href="seoSettings.prev_page_url" class="relative inline-flex items-center rounded-md border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-700">Previous</Link>
                        <Link v-if="seoSettings.next_page_url" :href="seoSettings.next_page_url" class="relative ml-3 inline-flex items-center rounded-md border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-700">Next</Link>
                    </div>
                    <div class="hidden sm:flex sm:flex-1 sm:items-center sm:justify-between">
                        <div>
                            <p class="text-sm text-gray-700 dark:text-gray-300">
                                Showing <span class="font-medium">{{ seoSettings.from }}</span> to <span class="font-medium">{{ seoSettings.to }}</span> of <span class="font-medium">{{ seoSettings.total }}</span> results
                            </p>
                        </div>
                        <div>
                            <nav class="isolate inline-flex -space-x-px rounded-md shadow-sm" aria-label="Pagination">
                                <Link v-if="seoSettings.prev_page_url" :href="seoSettings.prev_page_url" class="relative inline-flex items-center rounded-l-md px-2 py-2 text-gray-400 ring-1 ring-inset ring-gray-300 dark:ring-gray-600 hover:bg-gray-50 dark:hover:bg-gray-700 focus:z-20">
                                    <ChevronLeftIcon class="h-5 w-5" />
                                </Link>
                                <Link v-if="seoSettings.next_page_url" :href="seoSettings.next_page_url" class="relative inline-flex items-center rounded-r-md px-2 py-2 text-gray-400 ring-1 ring-inset ring-gray-300 dark:ring-gray-600 hover:bg-gray-50 dark:hover:bg-gray-700 focus:z-20">
                                    <ChevronRightIcon class="h-5 w-5" />
                                </Link>
                            </nav>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Delete Confirmation Modal -->
        <TransitionRoot as="template" :show="showDeleteModal">
            <Dialog as="div" class="relative z-50" @close="showDeleteModal = false">
                <TransitionChild as="template" enter="ease-out duration-300" enter-from="opacity-0" enter-to="opacity-100" leave="ease-in duration-200" leave-from="opacity-100" leave-to="opacity-0">
                    <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" />
                </TransitionChild>
                <div class="fixed inset-0 z-10 overflow-y-auto">
                    <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
                        <TransitionChild as="template" enter="ease-out duration-300" enter-from="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" enter-to="opacity-100 translate-y-0 sm:scale-100" leave="ease-in duration-200" leave-from="opacity-100 translate-y-0 sm:scale-100" leave-to="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95">
                            <DialogPanel class="relative transform overflow-hidden rounded-lg bg-white dark:bg-gray-800 px-4 pb-4 pt-5 text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-lg sm:p-6">
                                <div class="sm:flex sm:items-start">
                                    <div class="mx-auto flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-full bg-red-100 dark:bg-red-900/20 sm:mx-0 sm:h-10 sm:w-10">
                                        <ExclamationTriangleIcon class="h-6 w-6 text-red-600 dark:text-red-400" />
                                    </div>
                                    <div class="mt-3 text-center sm:ml-4 sm:mt-0 sm:text-left">
                                        <DialogTitle as="h3" class="text-base font-semibold leading-6 text-gray-900 dark:text-white">Delete SEO Setting</DialogTitle>
                                        <div class="mt-2">
                                            <p class="text-sm text-gray-500 dark:text-gray-400">
                                                Are you sure you want to delete SEO settings for "{{ settingToDelete?.page_type }}"? This action cannot be undone.
                                            </p>
                                        </div>
                                    </div>
                                </div>
                                <div class="mt-5 sm:mt-4 sm:flex sm:flex-row-reverse">
                                    <button
                                        type="button"
                                        @click="deleteSetting"
                                        class="inline-flex w-full justify-center rounded-md bg-red-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-red-500 sm:ml-3 sm:w-auto"
                                    >
                                        Delete
                                    </button>
                                    <button type="button" @click="showDeleteModal = false" class="mt-3 inline-flex w-full justify-center rounded-md bg-white dark:bg-gray-700 px-3 py-2 text-sm font-semibold text-gray-900 dark:text-white shadow-sm ring-1 ring-inset ring-gray-300 dark:ring-gray-600 hover:bg-gray-50 dark:hover:bg-gray-600 sm:mt-0 sm:w-auto">
                                        Cancel
                                    </button>
                                </div>
                            </DialogPanel>
                        </TransitionChild>
                    </div>
                </div>
            </Dialog>
        </TransitionRoot>
    </AdminLayout>
</template>

<script setup>
import { ref, reactive } from 'vue'
import { Link, router } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'
import { Dialog, DialogPanel, DialogTitle, TransitionChild, TransitionRoot } from '@headlessui/vue'
import {
    PlusIcon,
    PencilIcon,
    TrashIcon,
    ArrowUpTrayIcon,
    ChevronLeftIcon,
    ChevronRightIcon,
    ExclamationTriangleIcon,
    MagnifyingGlassCircleIcon,
    CodeBracketIcon,
} from '@heroicons/vue/24/outline'

const props = defineProps({
    seoSettings: Object,
    filters: Object,
})

const filters = reactive({
    search: props.filters.search || '',
    index: props.filters.index || '',
    follow: props.filters.follow || '',
    sort: props.filters.sort || 'page_type',
    direction: props.filters.direction || 'asc',
})

const showDeleteModal = ref(false)
const settingToDelete = ref(null)

let searchTimeout = null
const debouncedSearch = () => {
    clearTimeout(searchTimeout)
    searchTimeout = setTimeout(() => {
        applyFilters()
    }, 300)
}

const applyFilters = () => {
    router.get(route('admin.data.seo-settings.index'), filters, {
        preserveState: true,
        preserveScroll: true,
    })
}

const resetFilters = () => {
    filters.search = ''
    filters.index = ''
    filters.follow = ''
    filters.sort = 'page_type'
    filters.direction = 'asc'
    applyFilters()
}

const confirmDelete = (setting) => {
    settingToDelete.value = setting
    showDeleteModal.value = true
}

const deleteSetting = () => {
    router.delete(route('admin.data.seo-settings.destroy', settingToDelete.value.id), {
        preserveScroll: true,
        onSuccess: () => {
            showDeleteModal.value = false
            settingToDelete.value = null
        },
    })
}
</script>
