<script setup>
import { Head, Link, router } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { 
    MagnifyingGlassIcon,
    WalletIcon,
    UserIcon,
    CheckCircleIcon,
    ExclamationTriangleIcon,
    XCircleIcon,
    CurrencyDollarIcon,
    UsersIcon,
    BanknotesIcon
} from '@heroicons/vue/24/outline';
import { useBangladeshFormat } from '@/Composables/useBangladeshFormat';
import { ref } from 'vue';

const props = defineProps({
    wallets: Object,
    stats: Object,
    filters: Object,
});

const { formatCurrency } = useBangladeshFormat();

const searchForm = ref({
    search: props.filters.search || '',
    status: props.filters.status || '',
});

const search = () => {
    router.get(route('admin.wallets.index'), searchForm.value, {
        preserveState: true,
        preserveScroll: true,
    });
};

const clearFilters = () => {
    searchForm.value = { search: '', status: '' };
    search();
};

const getStatusColor = (status) => {
    return {
        'active': 'bg-green-100 text-green-800 dark:bg-green-500/10 dark:text-green-400',
        'suspended': 'bg-yellow-100 text-yellow-800 dark:bg-yellow-500/10 dark:text-yellow-400',
        'closed': 'bg-red-100 text-red-800 dark:bg-red-500/10 dark:text-red-400',
    }[status] || 'bg-gray-100 text-gray-800';
};

const getStatusIcon = (status) => {
    return {
        'active': CheckCircleIcon,
        'suspended': ExclamationTriangleIcon,
        'closed': XCircleIcon,
    }[status] || CheckCircleIcon;
};
</script>

<template>
    <Head title="Wallet Management" />

    <AdminLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                Wallet Management
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
                <!-- Stats -->
                <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 bg-blue-500 rounded-md p-3">
                                    <WalletIcon class="h-6 w-6 text-white" />
                                </div>
                                <div class="ml-4">
                                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Total Wallets</p>
                                    <p class="text-2xl font-semibold text-gray-900 dark:text-white">{{ stats.totalWallets }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 bg-green-500 rounded-md p-3">
                                    <CheckCircleIcon class="h-6 w-6 text-white" />
                                </div>
                                <div class="ml-4">
                                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Active</p>
                                    <p class="text-2xl font-semibold text-gray-900 dark:text-white">{{ stats.activeWallets }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 bg-purple-500 rounded-md p-3">
                                    <BanknotesIcon class="h-6 w-6 text-white" />
                                </div>
                                <div class="ml-4">
                                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Total Balance</p>
                                    <p class="text-2xl font-semibold text-gray-900 dark:text-white">{{ formatCurrency(stats.totalBalance) }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 bg-indigo-500 rounded-md p-3">
                                    <CurrencyDollarIcon class="h-6 w-6 text-white" />
                                </div>
                                <div class="ml-4">
                                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Avg Balance</p>
                                    <p class="text-2xl font-semibold text-gray-900 dark:text-white">{{ formatCurrency(stats.averageBalance) }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Filters -->
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <form @submit.prevent="search" class="flex flex-wrap gap-4 items-end">
                            <div class="flex-1 min-w-[200px]">
                                <label for="search" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                    Search
                                </label>
                                <div class="relative">
                                    <input
                                        id="search"
                                        v-model="searchForm.search"
                                        type="text"
                                        placeholder="Search by user name or email..."
                                        class="w-full pl-10 pr-4 py-2 border border-gray-300 dark:border-gray-600 rounded-md focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:text-white"
                                    />
                                    <MagnifyingGlassIcon class="absolute left-3 top-2.5 h-5 w-5 text-gray-400" />
                                </div>
                            </div>

                            <div class="w-48">
                                <label for="status" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                    Status
                                </label>
                                <select
                                    id="status"
                                    v-model="searchForm.status"
                                    class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-md focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:text-white"
                                >
                                    <option value="">All Statuses</option>
                                    <option value="active">Active</option>
                                    <option value="suspended">Suspended</option>
                                    <option value="closed">Closed</option>
                                </select>
                            </div>

                            <div class="flex space-x-2">
                                <button
                                    type="submit"
                                    class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                                >
                                    Search
                                </button>
                                <button
                                    type="button"
                                    @click="clearFilters"
                                    class="px-4 py-2 bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-md hover:bg-gray-300 dark:hover:bg-gray-600"
                                >
                                    Clear
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Wallets Table -->
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div v-if="wallets.data.length > 0">
                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                    <thead class="bg-gray-50 dark:bg-gray-900">
                                        <tr>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">User</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Balance</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Currency</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Status</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Created</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                        <tr v-for="wallet in wallets.data" :key="wallet.id" class="hover:bg-gray-50 dark:hover:bg-gray-700">
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="flex items-center">
                                                    <UserIcon class="h-8 w-8 text-gray-400" />
                                                    <div class="ml-3">
                                                        <div class="text-sm font-medium text-gray-900 dark:text-white">
                                                            {{ wallet.user?.name }}
                                                        </div>
                                                        <div class="text-sm text-gray-500 dark:text-gray-400">
                                                            {{ wallet.user?.email }}
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="text-sm font-semibold text-gray-900 dark:text-white">
                                                    {{ formatCurrency(wallet.balance) }}
                                                </div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="text-sm text-gray-900 dark:text-white">{{ wallet.currency }}</div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <span 
                                                    :class="getStatusColor(wallet.status)"
                                                    class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full items-center gap-1"
                                                >
                                                    <component :is="getStatusIcon(wallet.status)" class="h-3 w-3" />
                                                    {{ wallet.status.charAt(0).toUpperCase() + wallet.status.slice(1) }}
                                                </span>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                                {{ new Date(wallet.created_at).toLocaleDateString() }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                                <Link 
                                                    :href="route('admin.wallets.show', wallet.id)" 
                                                    class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-300"
                                                >
                                                    View Details
                                                </Link>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>

                            <!-- Pagination -->
                            <div class="mt-6 flex items-center justify-between">
                                <div class="text-sm text-gray-700 dark:text-gray-300">
                                    Showing {{ wallets.from }} to {{ wallets.to }} of {{ wallets.total }} wallets
                                </div>
                                <div class="flex space-x-2">
                                    <template v-for="link in wallets.links" :key="link.label">
                                        <Link 
                                            v-if="link.url"
                                            :href="link.url"
                                            :class="{
                                                'bg-indigo-600 text-white': link.active,
                                                'bg-white dark:bg-gray-700 text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-600': !link.active
                                            }"
                                            class="px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md text-sm"
                                            v-html="link.label"
                                        />
                                        <span
                                            v-else
                                            :class="{
                                                'bg-indigo-600 text-white': link.active,
                                                'bg-white dark:bg-gray-700 text-gray-700 dark:text-gray-300': !link.active
                                            }"
                                            class="px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md text-sm opacity-50 cursor-not-allowed"
                                            v-html="link.label"
                                        />
                                    </template>
                                </div>
                            </div>
                        </div>

                        <div v-else class="text-center py-12">
                            <WalletIcon class="mx-auto h-16 w-16 text-gray-400" />
                            <h3 class="mt-4 text-lg font-medium text-gray-900 dark:text-white">No wallets found</h3>
                            <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">
                                {{ filters.search || filters.status ? 'Try adjusting your filters' : 'Wallets will appear here when users register' }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>
