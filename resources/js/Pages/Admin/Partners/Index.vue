<template>
    <AdminLayout title="Partners Management">
        <div class="mb-6">
            <div class="flex justify-between items-center">
                <h1 class="text-3xl font-bold text-gray-800">Partners Management</h1>
                <Link :href="route('admin.partners.create')" class="inline-flex items-center px-4 py-2 bg-teal-600 hover:bg-teal-700 text-white font-medium rounded-lg transition-colors">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    Add Partner
                </Link>
            </div>
        </div>

        <!-- Filters -->
        <div class="mb-6 bg-white rounded-lg shadow-sm p-4">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Search</label>
                    <input 
                        v-model="form.search" 
                        @input="search"
                        type="text" 
                        placeholder="Search partners..." 
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent"
                    />
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                    <select v-model="form.status" @change="search" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent">
                        <option value="all">All Status</option>
                        <option value="active">Active</option>
                        <option value="inactive">Inactive</option>
                    </select>
                </div>
                <div class="flex items-end">
                    <button
                        @click="resetFilters"
                        class="w-full px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg transition-colors"
                    >
                        Reset Filters
                    </button>
                </div>
            </div>
        </div>

        <!-- Partners Table -->
        <div class="bg-white rounded-lg shadow-sm overflow-hidden">
            <div v-if="partners.data.length === 0" class="p-12 text-center">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                </svg>
                <h3 class="mt-2 text-sm font-medium text-gray-900">No partners found</h3>
                <p class="mt-1 text-sm text-gray-500">Get started by adding a new partner.</p>
            </div>

            <table v-else class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Logo</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Website</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Order</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <tr v-for="partner in partners.data" :key="partner.id" class="hover:bg-gray-50 transition-colors">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="h-12 w-24 flex items-center">
                                <img :src="partner.logo_url" :alt="partner.name" class="max-h-12 w-auto object-contain"/>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-sm font-medium text-gray-900">{{ partner.name }}</div>
                            <div v-if="partner.name_bn" class="text-xs text-gray-500 mt-1">{{ partner.name_bn }}</div>
                        </td>
                        <td class="px-6 py-4">
                            <a v-if="partner.website_url" :href="partner.website_url" target="_blank" class="text-sm text-primary-600 hover:text-primary-800 inline-flex items-center">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                                </svg>
                                Visit
                            </a>
                            <span v-else class="text-sm text-gray-400">-</span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 py-1 text-xs font-medium rounded-full bg-blue-100 text-blue-800">
                                {{ partner.partner_type }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="text-sm text-gray-900">{{ partner.sort_order }}</span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <button @click="toggleActive(partner)" class="inline-flex items-center">
                                <span v-if="partner.is_active" class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800 hover:bg-green-200 transition-colors">
                                    Active
                                </span>
                                <span v-else class="px-2 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-800 hover:bg-gray-200 transition-colors">
                                    Inactive
                                </span>
                            </button>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            <div class="flex items-center justify-end space-x-3">
                                <Link :href="route('admin.partners.edit', partner.id)" class="text-primary-600 hover:text-primary-900 transition-colors">
                                    Edit
                                </Link>
                                <button @click="confirmDelete(partner)" class="text-red-600 hover:text-red-900 transition-colors">
                                    Delete
                                </button>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div v-if="partners.data.length > 0" class="mt-6 flex justify-between items-center bg-white px-4 py-3 rounded-lg shadow-sm">
            <div class="text-sm text-gray-700">
                Showing <span class="font-medium">{{ partners.from }}</span> to <span class="font-medium">{{ partners.to }}</span> of <span class="font-medium">{{ partners.total }}</span> partners
            </div>
            <div class="flex space-x-2">
                <Link v-if="partners.prev_page_url" :href="partners.prev_page_url" class="px-4 py-2 bg-white border border-gray-300 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50 transition-colors">
                    Previous
                </Link>
                <Link v-if="partners.next_page_url" :href="partners.next_page_url" class="px-4 py-2 bg-white border border-gray-300 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50 transition-colors">
                    Next
                </Link>
            </div>
        </div>
    </AdminLayout>
</template>

<script setup>
import { ref, reactive } from 'vue';
import { Link, router } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';

const props = defineProps({
    partners: Object,
    filters: Object
});

const form = reactive({
    search: props.filters?.search || '',
    status: props.filters?.status || 'all'
});

let timeout = null;
const search = () => {
    clearTimeout(timeout);
    timeout = setTimeout(() => {
        router.get(route('admin.partners.index'), form, {
            preserveState: true,
            replace: true
        });
    }, 300);
};

const resetFilters = () => {
    form.search = '';
    form.status = 'all';
    search();
};

const toggleActive = (partner) => {
    if (confirm(`Are you sure you want to ${partner.is_active ? 'deactivate' : 'activate'} this partner?`)) {
        router.post(route('admin.partners.toggle-active', partner.id), {}, {
            preserveScroll: true
        });
    }
};

const confirmDelete = (partner) => {
    if (confirm(`Are you sure you want to delete "${partner.name}"? This action cannot be undone.`)) {
        router.delete(route('admin.partners.destroy', partner.id));
    }
};

const formatDate = (date) => {
    return new Date(date).toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'short',
        day: 'numeric'
    });
};
</script>
