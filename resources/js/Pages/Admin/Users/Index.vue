<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { ref } from 'vue';
import {
    UsersIcon,
    MagnifyingGlassIcon,
    FunnelIcon,
    CheckCircleIcon,
    XCircleIcon,
    ShieldCheckIcon,
    ArrowDownTrayIcon,
    EyeIcon,
    NoSymbolIcon,
    TrashIcon,
} from '@heroicons/vue/24/outline';

const props = defineProps({
    users: Object,
    stats: Object,
    filters: Object,
});

const search = ref(props.filters.search || '');
const selectedUsers = ref([]);
const showFilters = ref(false);
const filters = ref({
    role: props.filters.role || '',
    status: props.filters.status || '',
    email_verified: props.filters.email_verified || '',
    country_id: props.filters.country_id || '',
});

const toggleSelectAll = () => {
    if (selectedUsers.value.length === props.users.data.length) {
        selectedUsers.value = [];
    } else {
        selectedUsers.value = props.users.data.map(u => u.id);
    }
};

const toggleSelect = (userId) => {
    const index = selectedUsers.value.indexOf(userId);
    if (index > -1) {
        selectedUsers.value.splice(index, 1);
    } else {
        selectedUsers.value.push(userId);
    }
};

const searchUsers = () => {
    router.get(route('admin.users.index'), {
        search: search.value,
        ...filters.value,
    }, {
        preserveState: true,
        preserveScroll: true,
    });
};

const applyFilters = () => {
    router.get(route('admin.users.index'), {
        search: search.value,
        ...filters.value,
    }, {
        preserveState: true,
        preserveScroll: true,
    });
};

const clearFilters = () => {
    search.value = '';
    filters.value = {
        role: '',
        status: '',
        email_verified: '',
        country_id: '',
    };
    router.get(route('admin.users.index'));
};

const bulkSuspend = () => {
    if (selectedUsers.value.length === 0) {
        alert('Please select users to suspend');
        return;
    }

    const reason = prompt('Enter suspension reason:');
    if (!reason) return;

    if (confirm(`Suspend ${selectedUsers.value.length} user(s)?`)) {
        router.post(route('admin.users.bulk-suspend'), {
            user_ids: selectedUsers.value,
            reason: reason,
        }, {
            onSuccess: () => {
                selectedUsers.value = [];
            },
        });
    }
};

const bulkUnsuspend = () => {
    if (selectedUsers.value.length === 0) {
        alert('Please select users to unsuspend');
        return;
    }

    if (confirm(`Unsuspend ${selectedUsers.value.length} user(s)?`)) {
        router.post(route('admin.users.bulk-unsuspend'), {
            user_ids: selectedUsers.value,
        }, {
            onSuccess: () => {
                selectedUsers.value = [];
            },
        });
    }
};

const exportUsers = () => {
    window.location.href = route('admin.users.export', {
        search: search.value,
        ...filters.value,
    });
};

const formatDate = (date) => {
    return new Date(date).toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'short',
        day: 'numeric',
    });
};
</script>

<template>
    <Head title="User Management" />

    <AuthenticatedLayout>
        <div class="min-h-screen bg-gradient-to-br from-gray-50 to-gray-100 py-8">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <!-- Header -->
                <div class="bg-gradient-to-r from-blue-600 to-indigo-700 rounded-2xl shadow-lg p-8 mb-8 text-white">
                    <div class="flex items-center justify-between">
                        <div>
                            <h1 class="text-3xl font-bold mb-2">User Management</h1>
                            <p class="text-blue-100">Manage and moderate platform users</p>
                        </div>
                        <button
                            @click="exportUsers"
                            class="bg-white/20 hover:bg-white/30 px-6 py-3 rounded-xl font-semibold transition-colors flex items-center gap-2"
                        >
                            <ArrowDownTrayIcon class="h-5 w-5" />
                            Export CSV
                        </button>
                    </div>
                </div>

                <!-- Stats Cards -->
                <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4 mb-8">
                    <div class="bg-white rounded-xl shadow-sm p-6">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm text-gray-600 mb-1">Total Users</p>
                                <p class="text-2xl font-bold text-gray-900">{{ stats.total }}</p>
                            </div>
                            <UsersIcon class="h-8 w-8 text-blue-500" />
                        </div>
                    </div>

                    <div class="bg-white rounded-xl shadow-sm p-6">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm text-gray-600 mb-1">Active</p>
                                <p class="text-2xl font-bold text-green-600">{{ stats.active }}</p>
                            </div>
                            <CheckCircleIcon class="h-8 w-8 text-green-500" />
                        </div>
                    </div>

                    <div class="bg-white rounded-xl shadow-sm p-6">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm text-gray-600 mb-1">Suspended</p>
                                <p class="text-2xl font-bold text-red-600">{{ stats.suspended }}</p>
                            </div>
                            <NoSymbolIcon class="h-8 w-8 text-red-500" />
                        </div>
                    </div>

                    <div class="bg-white rounded-xl shadow-sm p-6">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm text-gray-600 mb-1">Verified</p>
                                <p class="text-2xl font-bold text-green-600">{{ stats.verified }}</p>
                            </div>
                            <ShieldCheckIcon class="h-8 w-8 text-green-500" />
                        </div>
                    </div>

                    <div class="bg-white rounded-xl shadow-sm p-6">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm text-gray-600 mb-1">Unverified</p>
                                <p class="text-2xl font-bold text-yellow-600">{{ stats.unverified }}</p>
                            </div>
                            <XCircleIcon class="h-8 w-8 text-yellow-500" />
                        </div>
                    </div>

                    <div class="bg-white rounded-xl shadow-sm p-6">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm text-gray-600 mb-1">Admins</p>
                                <p class="text-2xl font-bold text-purple-600">{{ stats.admins }}</p>
                            </div>
                            <ShieldCheckIcon class="h-8 w-8 text-purple-500" />
                        </div>
                    </div>
                </div>

                <!-- Search and Filters -->
                <div class="bg-white rounded-xl shadow-sm p-6 mb-6">
                    <div class="flex flex-col lg:flex-row gap-4">
                        <div class="flex-1">
                            <div class="relative">
                                <MagnifyingGlassIcon class="absolute left-3 top-1/2 -translate-y-1/2 h-5 w-5 text-gray-400" />
                                <input
                                    v-model="search"
                                    type="text"
                                    @keyup.enter="searchUsers"
                                    placeholder="Search by name, email, or phone..."
                                    class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                                />
                            </div>
                        </div>
                        <button
                            @click="showFilters = !showFilters"
                            class="px-6 py-3 border-2 border-gray-300 text-gray-700 rounded-lg font-semibold hover:bg-gray-50 transition-colors flex items-center justify-center gap-2"
                        >
                            <FunnelIcon class="h-5 w-5" />
                            Filters
                        </button>
                    </div>

                    <!-- Filters Panel -->
                    <div v-if="showFilters" class="mt-4 pt-4 border-t border-gray-200">
                        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Role</label>
                                <select
                                    v-model="filters.role"
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                                >
                                    <option value="">All Roles</option>
                                    <option value="user">User</option>
                                    <option value="admin">Admin</option>
                                </select>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                                <select
                                    v-model="filters.status"
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                                >
                                    <option value="">All Statuses</option>
                                    <option value="active">Active</option>
                                    <option value="suspended">Suspended</option>
                                </select>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Email Verification</label>
                                <select
                                    v-model="filters.email_verified"
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                                >
                                    <option value="">All</option>
                                    <option value="verified">Verified</option>
                                    <option value="unverified">Unverified</option>
                                </select>
                            </div>

                            <div class="flex items-end gap-2">
                                <button
                                    @click="applyFilters"
                                    class="flex-1 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors font-semibold"
                                >
                                    Apply
                                </button>
                                <button
                                    @click="clearFilters"
                                    class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors font-semibold"
                                >
                                    Clear
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Bulk Actions -->
                <div
                    v-if="selectedUsers.length > 0"
                    class="bg-blue-50 border border-blue-200 rounded-xl p-4 mb-6 flex items-center justify-between"
                >
                    <span class="text-blue-900 font-semibold">{{ selectedUsers.length }} user(s) selected</span>
                    <div class="flex gap-2">
                        <button
                            @click="bulkSuspend"
                            class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors font-semibold text-sm"
                        >
                            <NoSymbolIcon class="h-4 w-4 inline mr-1" />
                            Suspend
                        </button>
                        <button
                            @click="bulkUnsuspend"
                            class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors font-semibold text-sm"
                        >
                            <CheckCircleIcon class="h-4 w-4 inline mr-1" />
                            Unsuspend
                        </button>
                    </div>
                </div>

                <!-- Users Table -->
                <div class="bg-white rounded-xl shadow-sm overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left">
                                        <input
                                            type="checkbox"
                                            :checked="selectedUsers.length === users.data.length && users.data.length > 0"
                                            @change="toggleSelectAll"
                                            class="h-4 w-4 text-blue-600 rounded border-gray-300 focus:ring-blue-500"
                                        />
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        User
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Contact
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Role
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Status
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Registered
                                    </th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Actions
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <tr v-for="user in users.data" :key="user.id" class="hover:bg-gray-50">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <input
                                            type="checkbox"
                                            :checked="selectedUsers.includes(user.id)"
                                            @change="toggleSelect(user.id)"
                                            class="h-4 w-4 text-blue-600 rounded border-gray-300 focus:ring-blue-500"
                                        />
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="flex items-center gap-3">
                                            <div class="h-10 w-10 rounded-full bg-gradient-to-br from-blue-500 to-purple-600 flex items-center justify-center text-white font-semibold">
                                                {{ user.name.charAt(0).toUpperCase() }}
                                            </div>
                                            <div>
                                                <div class="font-medium text-gray-900">{{ user.name }}</div>
                                                <div class="text-sm text-gray-500">{{ user.country?.name }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="text-sm">
                                            <div class="text-gray-900">{{ user.email }}</div>
                                            <div v-if="user.phone" class="text-gray-500">{{ user.phone }}</div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span
                                            :class="[
                                                'px-3 py-1 rounded-full text-xs font-semibold',
                                                user.role === 'admin' ? 'bg-purple-100 text-purple-800' : 'bg-blue-100 text-blue-800',
                                            ]"
                                        >
                                            {{ user.role }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="flex flex-col gap-1">
                                            <span
                                                :class="[
                                                    'px-3 py-1 rounded-full text-xs font-semibold w-fit',
                                                    user.suspended_at ? 'bg-red-100 text-red-800' : 'bg-green-100 text-green-800',
                                                ]"
                                            >
                                                {{ user.suspended_at ? 'Suspended' : 'Active' }}
                                            </span>
                                            <span
                                                :class="[
                                                    'px-3 py-1 rounded-full text-xs font-semibold w-fit',
                                                    user.email_verified_at ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800',
                                                ]"
                                            >
                                                {{ user.email_verified_at ? 'Verified' : 'Unverified' }}
                                            </span>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ formatDate(user.created_at) }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <Link
                                            :href="route('admin.users.show', user.id)"
                                            class="text-blue-600 hover:text-blue-900 inline-flex items-center gap-1"
                                        >
                                            <EyeIcon class="h-4 w-4" />
                                            View
                                        </Link>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div v-if="users.links.length > 3" class="bg-gray-50 px-6 py-4 border-t border-gray-200">
                        <div class="flex flex-wrap items-center justify-center gap-2">
                            <component
                                v-for="(link, index) in users.links"
                                :key="index"
                                :is="link.url ? Link : 'span'"
                                :href="link.url || undefined"
                                :class="[
                                    'px-4 py-2 rounded-lg text-sm font-medium transition-colors',
                                    link.active
                                        ? 'bg-blue-600 text-white'
                                        : link.url
                                        ? 'bg-white text-gray-700 hover:bg-gray-100 border border-gray-300'
                                        : 'bg-gray-100 text-gray-400 cursor-not-allowed',
                                ]"
                                v-html="link.label"
                            />
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
