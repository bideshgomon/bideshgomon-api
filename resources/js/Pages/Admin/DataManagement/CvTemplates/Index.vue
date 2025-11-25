<template>
    <AdminLayout>
        <div class="px-4 sm:px-6 lg:px-8 py-8">
            <!-- Header -->
            <div class="sm:flex sm:items-center sm:justify-between mb-6">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900 dark:text-white">CV Templates</h1>
                    <p class="mt-2 text-sm text-gray-700 dark:text-gray-300">Manage CV templates with premium features and customization.</p>
                </div>
                <div class="mt-4 sm:mt-0 flex gap-3">
                    <Link :href="route('admin.data.cv-templates.bulk-upload')" class="inline-flex items-center px-4 py-2 border border-gray-300 dark:border-gray-600 shadow-sm text-sm font-medium rounded-md text-gray-700 dark:text-gray-200 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700">
                        <ArrowUpTrayIcon class="-ml-1 mr-2 h-5 w-5 text-gray-500 dark:text-gray-400" />
                        Bulk Upload
                    </Link>
                    <Link :href="route('admin.data.cv-templates.create')" class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700">
                        <PlusIcon class="-ml-1 mr-2 h-5 w-5" />
                        Add Template
                    </Link>
                </div>
            </div>

            <!-- Filters -->
            <div class="mb-6 grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-5">
                <div>
                    <input
                        v-model="filters.search"
                        type="text"
                        placeholder="Search templates..."
                        class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-white shadow-sm focus:border-blue-500 focus:ring-blue-500"
                        @input="debouncedSearch"
                    />
                </div>
                <div>
                    <select
                        v-model="filters.category"
                        class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-white shadow-sm focus:border-blue-500 focus:ring-blue-500"
                        @change="applyFilters"
                    >
                        <option value="">All Categories</option>
                        <option value="professional">Professional</option>
                        <option value="creative">Creative</option>
                        <option value="modern">Modern</option>
                        <option value="traditional">Traditional</option>
                        <option value="academic">Academic</option>
                    </select>
                </div>
                <div>
                    <select
                        v-model="filters.is_premium"
                        class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-white shadow-sm focus:border-blue-500 focus:ring-blue-500"
                        @change="applyFilters"
                    >
                        <option value="">All Types</option>
                        <option value="0">Free</option>
                        <option value="1">Premium</option>
                    </select>
                </div>
                <div>
                    <select
                        v-model="filters.is_active"
                        class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-white shadow-sm focus:border-blue-500 focus:ring-blue-500"
                        @change="applyFilters"
                    >
                        <option value="">All Status</option>
                        <option value="1">Active</option>
                        <option value="0">Inactive</option>
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
                            <th class="py-3.5 pl-4 pr-3 text-left text-sm font-semibold text-gray-900 dark:text-white">Template</th>
                            <th class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900 dark:text-white">Category</th>
                            <th class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900 dark:text-white">Type</th>
                            <th class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900 dark:text-white">Price</th>
                            <th class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900 dark:text-white cursor-pointer" @click="sortBy('downloads')">
                                <div class="flex items-center gap-1">
                                    Downloads
                                    <ArrowsUpDownIcon class="h-4 w-4" />
                                </div>
                            </th>
                            <th class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900 dark:text-white cursor-pointer" @click="sortBy('cvs')">
                                <div class="flex items-center gap-1">
                                    User CVs
                                    <ArrowsUpDownIcon class="h-4 w-4" />
                                </div>
                            </th>
                            <th class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900 dark:text-white">Status</th>
                            <th class="relative py-3.5 pl-3 pr-4 sm:pr-6 text-right text-sm font-semibold text-gray-900 dark:text-white">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700 bg-white dark:bg-gray-800">
                        <tr v-for="template in templates.data" :key="template.id" class="hover:bg-gray-50 dark:hover:bg-gray-700">
                            <td class="whitespace-nowrap py-4 pl-4 pr-3">
                                <div class="flex items-center">
                                    <div class="h-16 w-16 flex-shrink-0 rounded bg-gray-100 dark:bg-gray-700 flex items-center justify-center overflow-hidden">
                                        <img v-if="template.thumbnail" :src="template.thumbnail" :alt="template.name" class="h-full w-full object-cover" />
                                        <ClipboardDocumentIcon v-else class="h-8 w-8 text-gray-400" />
                                    </div>
                                    <div class="ml-4">
                                        <div class="font-medium text-gray-900 dark:text-white">{{ template.name }}</div>
                                        <div class="text-sm text-gray-500 dark:text-gray-400">{{ template.slug }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="whitespace-nowrap px-3 py-4">
                                <span class="inline-flex items-center rounded-md px-2 py-1 text-xs font-medium capitalize"
                                    :class="{
                                        'bg-blue-50 text-blue-700 ring-1 ring-inset ring-blue-600/20 dark:bg-blue-500/10 dark:text-blue-400 dark:ring-blue-500/30': template.category === 'professional',
                                        'bg-purple-50 text-purple-700 ring-1 ring-inset ring-purple-600/20 dark:bg-purple-500/10 dark:text-purple-400 dark:ring-purple-500/30': template.category === 'creative',
                                        'bg-green-50 text-green-700 ring-1 ring-inset ring-green-600/20 dark:bg-green-500/10 dark:text-green-400 dark:ring-green-500/30': template.category === 'modern',
                                        'bg-amber-50 text-amber-700 ring-1 ring-inset ring-amber-600/20 dark:bg-amber-500/10 dark:text-amber-400 dark:ring-amber-500/30': template.category === 'traditional',
                                        'bg-indigo-50 text-indigo-700 ring-1 ring-inset ring-indigo-600/20 dark:bg-indigo-500/10 dark:text-indigo-400 dark:ring-indigo-500/30': template.category === 'academic',
                                        'bg-gray-50 text-gray-700 ring-1 ring-inset ring-gray-600/20 dark:bg-gray-500/10 dark:text-gray-400 dark:ring-gray-500/30': !['professional', 'creative', 'modern', 'traditional', 'academic'].includes(template.category)
                                    }">
                                    {{ template.category }}
                                </span>
                            </td>
                            <td class="whitespace-nowrap px-3 py-4">
                                <span v-if="template.is_premium" class="inline-flex items-center gap-1 rounded-md bg-yellow-50 dark:bg-yellow-500/10 px-2 py-1 text-xs font-medium text-yellow-800 dark:text-yellow-400 ring-1 ring-inset ring-yellow-600/20 dark:ring-yellow-500/30">
                                    <SparklesIcon class="h-3 w-3" />
                                    Premium
                                </span>
                                <span v-else class="inline-flex items-center rounded-md bg-green-50 dark:bg-green-500/10 px-2 py-1 text-xs font-medium text-green-700 dark:text-green-400 ring-1 ring-inset ring-green-600/20 dark:ring-green-500/30">
                                    Free
                                </span>
                            </td>
                            <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-900 dark:text-white">
                                <span v-if="template.is_premium" class="font-medium">৳{{ formatNumber(template.price) }}</span>
                                <span v-else class="text-gray-500 dark:text-gray-400">Free</span>
                            </td>
                            <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-900 dark:text-white">
                                <div class="flex items-center gap-1">
                                    <ArrowDownTrayIcon class="h-4 w-4 text-gray-400" />
                                    {{ formatNumber(template.download_count) }}
                                </div>
                            </td>
                            <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-900 dark:text-white">
                                {{ template.user_cvs_count || 0 }}
                            </td>
                            <td class="whitespace-nowrap px-3 py-4">
                                <button
                                    @click="toggleStatus(template)"
                                    class="relative inline-flex h-6 w-11 flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none focus:ring-2 focus:ring-blue-600 focus:ring-offset-2"
                                    :class="template.is_active ? 'bg-blue-600' : 'bg-gray-200 dark:bg-gray-700'"
                                >
                                    <span
                                        class="pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out"
                                        :class="template.is_active ? 'translate-x-5' : 'translate-x-0'"
                                    ></span>
                                </button>
                            </td>
                            <td class="relative whitespace-nowrap py-4 pl-3 pr-4 text-right text-sm font-medium sm:pr-6">
                                <div class="flex justify-end gap-2">
                                    <Link :href="route('admin.data.cv-templates.edit', template.id)" class="text-blue-600 hover:text-blue-900 dark:text-blue-400 dark:hover:text-blue-300">
                                        <PencilIcon class="h-5 w-5" />
                                    </Link>
                                    <button @click="confirmDelete(template)" class="text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-300">
                                        <TrashIcon class="h-5 w-5" />
                                    </button>
                                </div>
                            </td>
                        </tr>
                        <tr v-if="templates.data.length === 0">
                            <td colspan="8" class="px-3 py-8 text-center text-sm text-gray-500 dark:text-gray-400">
                                No CV templates found.
                            </td>
                        </tr>
                    </tbody>
                </table>

                <!-- Pagination -->
                <div v-if="templates.data.length > 0" class="flex items-center justify-between border-t border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 px-4 py-3 sm:px-6">
                    <div class="flex flex-1 justify-between sm:hidden">
                        <Link v-if="templates.prev_page_url" :href="templates.prev_page_url" class="relative inline-flex items-center rounded-md border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-700">Previous</Link>
                        <Link v-if="templates.next_page_url" :href="templates.next_page_url" class="relative ml-3 inline-flex items-center rounded-md border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-700">Next</Link>
                    </div>
                    <div class="hidden sm:flex sm:flex-1 sm:items-center sm:justify-between">
                        <div>
                            <p class="text-sm text-gray-700 dark:text-gray-300">
                                Showing <span class="font-medium">{{ templates.from }}</span> to <span class="font-medium">{{ templates.to }}</span> of <span class="font-medium">{{ templates.total }}</span> results
                            </p>
                        </div>
                        <div>
                            <nav class="isolate inline-flex -space-x-px rounded-md shadow-sm" aria-label="Pagination">
                                <Link v-if="templates.prev_page_url" :href="templates.prev_page_url" class="relative inline-flex items-center rounded-l-md px-2 py-2 text-gray-400 ring-1 ring-inset ring-gray-300 dark:ring-gray-600 hover:bg-gray-50 dark:hover:bg-gray-700 focus:z-20">
                                    <ChevronLeftIcon class="h-5 w-5" />
                                </Link>
                                <Link v-if="templates.next_page_url" :href="templates.next_page_url" class="relative inline-flex items-center rounded-r-md px-2 py-2 text-gray-400 ring-1 ring-inset ring-gray-300 dark:ring-gray-600 hover:bg-gray-50 dark:hover:bg-gray-700 focus:z-20">
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
                                        <DialogTitle as="h3" class="text-base font-semibold leading-6 text-gray-900 dark:text-white">Delete CV Template</DialogTitle>
                                        <div class="mt-2">
                                            <p class="text-sm text-gray-500 dark:text-gray-400">
                                                Are you sure you want to delete "{{ templateToDelete?.name }}"?
                                                <span v-if="templateToDelete?.user_cvs_count > 0" class="block mt-2 text-red-600 dark:text-red-400 font-medium">
                                                    Warning: This template is being used by {{ templateToDelete.user_cvs_count }} user(s). Cannot delete!
                                                </span>
                                                <span v-else class="block mt-2">This action cannot be undone.</span>
                                            </p>
                                        </div>
                                    </div>
                                </div>
                                <div class="mt-5 sm:mt-4 sm:flex sm:flex-row-reverse">
                                    <button
                                        v-if="!templateToDelete?.user_cvs_count || templateToDelete?.user_cvs_count === 0"
                                        type="button"
                                        @click="deleteTemplate"
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
    ArrowDownTrayIcon,
    ChevronLeftIcon,
    ChevronRightIcon,
    ExclamationTriangleIcon,
    ClipboardDocumentIcon,
    SparklesIcon,
    ArrowsUpDownIcon,
} from '@heroicons/vue/24/outline'

const props = defineProps({
    templates: Object,
    filters: Object,
})

const filters = reactive({
    search: props.filters.search || '',
    category: props.filters.category || '',
    is_premium: props.filters.is_premium || '',
    is_active: props.filters.is_active || '',
    sort: props.filters.sort || 'sort_order',
    direction: props.filters.direction || 'asc',
})

const showDeleteModal = ref(false)
const templateToDelete = ref(null)

let searchTimeout = null
const debouncedSearch = () => {
    clearTimeout(searchTimeout)
    searchTimeout = setTimeout(() => {
        applyFilters()
    }, 300)
}

const applyFilters = () => {
    router.get(route('admin.data.cv-templates.index'), filters, {
        preserveState: true,
        preserveScroll: true,
    })
}

const sortBy = (field) => {
    if (filters.sort === field) {
        filters.direction = filters.direction === 'asc' ? 'desc' : 'asc'
    } else {
        filters.sort = field
        filters.direction = 'asc'
    }
    applyFilters()
}

const resetFilters = () => {
    filters.search = ''
    filters.category = ''
    filters.is_premium = ''
    filters.is_active = ''
    filters.sort = 'sort_order'
    filters.direction = 'asc'
    applyFilters()
}

const toggleStatus = (template) => {
    router.post(route('admin.data.cv-templates.toggle-status', template.id), {}, {
        preserveScroll: true,
        onSuccess: () => {
            template.is_active = !template.is_active
        },
    })
}

const confirmDelete = (template) => {
    templateToDelete.value = template
    showDeleteModal.value = true
}

const deleteTemplate = () => {
    router.delete(route('admin.data.cv-templates.destroy', templateToDelete.value.id), {
        preserveScroll: true,
        onSuccess: () => {
            showDeleteModal.value = false
            templateToDelete.value = null
        },
    })
}

const formatNumber = (num) => {
    return new Intl.NumberFormat('en-US').format(num)
}
</script>
