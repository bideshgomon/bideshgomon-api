<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Head, Link, router, usePage } from '@inertiajs/vue3';
import { ref, computed } from 'vue';
import Pagination from '@/Components/Pagination.vue';
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
    UserPlusIcon,
    PencilIcon,
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

// Impersonation helpers
const page = usePage();
const isAdmin = computed(() => page.props.auth?.user?.role?.slug === 'admin');
const impersonating = computed(() => page.props.auth?.user?.impersonating);

const canImpersonate = (user) => {
    if (!isAdmin.value) return false;
    if (impersonating.value) return false; // Already impersonating someone
    // Do not impersonate admins (policy)
    return user.role?.slug !== 'admin';
};

const impersonateUser = (user) => {
    if (!canImpersonate(user)) return;
    
    const purpose = prompt(`Impersonate ${user.name}?\n\nPlease provide a reason (required for audit trail):`);
    if (!purpose || purpose.trim() === '') {
        return; // User cancelled or provided empty reason
    }
    
    router.post(route('admin.users.impersonate', user.id), {
        purpose: purpose.trim()
    }, {
        preserveScroll: true,
        onSuccess: () => {
            // Redirect to dashboard after successful impersonation
            router.visit(route('dashboard'));
        },
        onError: (errors) => {
            console.error('Impersonation failed:', errors);
            alert('Failed to impersonate user. ' + (errors.purpose || 'Please try again.'));
        }
    });
};
</script>

<template>
    <Head title="User Management" />

    <AdminLayout>
        <div class="min-h-screen bg-gray-50 py-8">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <!-- Header -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-6">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <div class="w-12 h-12 bg-indigo-100 rounded-lg flex items-center justify-center">
                                <UsersIcon class="h-6 w-6 text-indigo-600" />
                            </div>
                            <div>
                                <h1 class="text-2xl font-bold text-gray-900">User Management</h1>
                                <p class="text-sm text-gray-600 mt-0.5">Manage and moderate platform users</p>
                            </div>
                        </div>
                        <div class="flex gap-3">
                            <Link
                                :href="route('admin.users.create')"
                                class="bg-indigo-600 text-white hover:bg-indigo-700 px-4 py-2.5 rounded-lg font-medium transition-colors flex items-center gap-2"
                            >
                                <UserPlusIcon class="h-5 w-5" />
                                Create User
                            </Link>
                            <button
                                @click="exportUsers"
                                class="bg-white border border-gray-300 text-gray-700 hover:bg-gray-50 px-4 py-2.5 rounded-lg font-medium transition-colors flex items-center gap-2"
                            >
                                <ArrowDownTrayIcon class="h-5 w-5" />
                                Export CSV
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Stats Cards -->
                <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4 mb-6">
                    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-5">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm font-medium text-gray-600 mb-1">Total Users</p>
                                <p class="text-2xl font-bold text-gray-900">{{ stats.total }}</p>
                            </div>
                            <UsersIcon class="h-8 w-8 text-indigo-500" />
                        </div>
                    </div>

                    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-5">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm font-medium text-gray-600 mb-1">Active</p>
                                <p class="text-2xl font-bold text-green-600">{{ stats.active }}</p>
                            </div>
                            <CheckCircleIcon class="h-8 w-8 text-green-500" />
                        </div>
                    </div>

                    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-5">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm font-medium text-gray-600 mb-1">Suspended</p>
                                <p class="text-2xl font-bold text-red-600">{{ stats.suspended }}</p>
                            </div>
                            <NoSymbolIcon class="h-8 w-8 text-red-500" />
                        </div>
                    </div>

                    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-5">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm font-medium text-gray-600 mb-1">Verified</p>
                                <p class="text-2xl font-bold text-green-600">{{ stats.verified }}</p>
                            </div>
                            <ShieldCheckIcon class="h-8 w-8 text-green-500" />
                        </div>
                    </div>

                    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-5">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm font-medium text-gray-600 mb-1">Unverified</p>
                                <p class="text-2xl font-bold text-amber-600">{{ stats.unverified }}</p>
                            </div>
                            <XCircleIcon class="h-8 w-8 text-amber-500" />
                        </div>
                    </div>

                    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-5">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm font-medium text-gray-600 mb-1">Admins</p>
                                <p class="text-2xl font-bold text-indigo-600">{{ stats.admins }}</p>
                            </div>
                            <ShieldCheckIcon class="h-8 w-8 text-indigo-500" />
                        </div>
                    </div>
                </div>

                <!-- Search and Filters -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-6">
                    <div class="flex flex-col lg:flex-row gap-4">
                        <div class="flex-1">
                            <div class="relative">
                                <MagnifyingGlassIcon class="absolute left-3 top-1/2 -translate-y-1/2 h-5 w-5 text-gray-500" />
                                <input
                                    v-model="search"
                                    type="text"
                                    @keyup.enter="searchUsers"
                                    placeholder="Search by name, email, or phone..."
                                    class="w-full pl-10 pr-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500"
                                />
                            </div>
                        </div>
                        <button
                            @click="showFilters = !showFilters"
                            class="px-5 py-2.5 border border-gray-300 text-gray-700 rounded-lg font-medium hover:bg-gray-50 transition-colors flex items-center justify-center gap-2"
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
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500"
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
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500"
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
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500"
                                >
                                    <option value="">All</option>
                                    <option value="verified">Verified</option>
                                    <option value="unverified">Unverified</option>
                                </select>
                            </div>

                            <div class="flex items-end gap-2">
                                <button
                                    @click="applyFilters"
                                    class="flex-1 px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors font-medium"
                                >
                                    Apply
                                </button>
                                <button
                                    @click="clearFilters"
                                    class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors font-medium"
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
                    class="bg-indigo-50 border border-indigo-200 rounded-lg p-4 mb-6 flex items-center justify-between"
                >
                    <span class="text-indigo-900 font-medium">{{ selectedUsers.length }} user(s) selected</span>
                    <div class="flex gap-2">
                        <button
                            @click="bulkSuspend"
                            class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors font-medium text-sm"
                        >
                            <NoSymbolIcon class="h-4 w-4 inline mr-1" />
                            Suspend
                        </button>
                        <button
                            @click="bulkUnsuspend"
                            class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors font-medium text-sm"
                        >
                            <CheckCircleIcon class="h-4 w-4 inline mr-1" />
                            Unsuspend
                        </button>
                    </div>
                </div>

                <!-- Users Table -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left">
                                        <input
                                            type="checkbox"
                                            :checked="selectedUsers.length === users.data.length && users.data.length > 0"
                                            @change="toggleSelectAll"
                                            class="h-4 w-4 text-indigo-600 rounded border-gray-300 focus:ring-indigo-500"
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
                                            <div class="h-10 w-10 rounded-full bg-indigo-600 flex items-center justify-center text-white font-semibold">
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
                                                user.role?.slug === 'admin' ? 'bg-purple-100 text-purple-800' : 'bg-blue-100 text-blue-800',
                                            ]"
                                        >
                                            {{ user.role?.name || 'N/A' }}
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
                                        <Link
                                            :href="route('admin.users.edit', user.id)"
                                            class="ml-3 text-indigo-600 hover:text-indigo-900 inline-flex items-center gap-1"
                                        >
                                            <PencilIcon class="h-4 w-4" />
                                            Edit
                                        </Link>
                                        <button
                                            v-if="canImpersonate(user)"
                                            @click="impersonateUser(user)"
                                            class="ml-3 text-amber-600 hover:text-amber-800 inline-flex items-center gap-1"
                                            type="button"
                                        >
                                            <UserPlusIcon class="h-4 w-4" />
                                            Impersonate
                                        </button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div class="bg-gray-50 px-6 py-4 border-t border-gray-200">
                        <Pagination :links="users.links" />
                    </div>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>
