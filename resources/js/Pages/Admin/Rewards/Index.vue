<script setup>
import { Head, Link, router, useForm } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { 
    MagnifyingGlassIcon,
    CheckCircleIcon, 
    XCircleIcon,
    CurrencyDollarIcon,
    ClockIcon,
    ChartBarIcon
} from '@heroicons/vue/24/outline';
import { useBangladeshFormat } from '@/Composables/useBangladeshFormat';
import { ref } from 'vue';

const props = defineProps({
    rewards: Object,
    filters: Object,
    globalStats: Object,
});

const { formatCurrency, formatDate, formatTime } = useBangladeshFormat();

const searchForm = useForm({
    search: props.filters.search || '',
    status: props.filters.status || '',
});

const search = () => {
    searchForm.get(route('admin.rewards.index'), {
        preserveState: true,
        preserveScroll: true,
    });
};

const clearFilters = () => {
    searchForm.reset();
    search();
};

const approveReward = (rewardId) => {
    if (confirm('Are you sure you want to approve this reward? This will credit the user\'s wallet.')) {
        router.post(route('admin.rewards.approve', rewardId), {}, {
            preserveScroll: true,
            onSuccess: () => {
                // Success message handled by backend
            }
        });
    }
};

const rejectReward = (rewardId) => {
    const reason = prompt('Enter rejection reason (optional):');
    if (reason !== null) { // User clicked OK (even if empty)
        router.post(route('admin.rewards.reject', rewardId), {
            reason: reason
        }, {
            preserveScroll: true,
            onSuccess: () => {
                // Success message handled by backend
            }
        });
    }
};
</script>

<template>
    <Head title="Reward Management" />

    <AdminLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Reward Management
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
                <!-- Global Stats -->
                <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 bg-blue-500 rounded-md p-3">
                                    <ChartBarIcon class="h-6 w-6 text-white" />
                                </div>
                                <div class="ml-4">
                                    <p class="text-sm font-medium text-gray-500">Total Rewards</p>
                                    <p class="text-2xl font-semibold text-gray-900">{{ globalStats.totalRewards }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 bg-yellow-500 rounded-md p-3">
                                    <ClockIcon class="h-6 w-6 text-white" />
                                </div>
                                <div class="ml-4">
                                    <p class="text-sm font-medium text-gray-500">Pending</p>
                                    <p class="text-2xl font-semibold text-gray-900">{{ globalStats.pendingCount }}</p>
                                    <p class="text-xs text-gray-400">{{ formatCurrency(globalStats.pendingAmount) }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 bg-green-500 rounded-md p-3">
                                    <CheckCircleIcon class="h-6 w-6 text-white" />
                                </div>
                                <div class="ml-4">
                                    <p class="text-sm font-medium text-gray-500">Approved</p>
                                    <p class="text-2xl font-semibold text-gray-900">{{ globalStats.approvedCount }}</p>
                                    <p class="text-xs text-gray-400">{{ formatCurrency(globalStats.approvedAmount) }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 bg-purple-500 rounded-md p-3">
                                    <CurrencyDollarIcon class="h-6 w-6 text-white" />
                                </div>
                                <div class="ml-4">
                                    <p class="text-sm font-medium text-gray-500">Total Paid</p>
                                    <p class="text-2xl font-semibold text-gray-900">{{ formatCurrency(globalStats.totalAmount) }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Filters -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <form @submit.prevent="search" class="flex flex-wrap gap-4 items-end">
                            <div class="flex-1 min-w-[200px]">
                                <label for="search" class="block text-sm font-medium text-gray-700 mb-1">
                                    Search
                                </label>
                                <div class="relative">
                                    <input
                                        id="search"
                                        v-model="searchForm.search"
                                        type="text"
                                        placeholder="Search by user name or email..."
                                        class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-md focus:ring-indigo-500 focus:border-indigo-500"
                                    />
                                    <MagnifyingGlassIcon class="absolute left-3 top-2.5 h-5 w-5 text-gray-400" />
                                </div>
                            </div>

                            <div class="w-48">
                                <label for="status" class="block text-sm font-medium text-gray-700 mb-1">
                                    Status
                                </label>
                                <select
                                    id="status"
                                    v-model="searchForm.status"
                                    class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-indigo-500 focus:border-indigo-500"
                                >
                                    <option value="">All Statuses</option>
                                    <option value="pending">Pending</option>
                                    <option value="approved">Approved</option>
                                    <option value="rejected">Rejected</option>
                                    <option value="paid">Paid</option>
                                </select>
                            </div>

                            <div class="flex space-x-2">
                                <button
                                    type="submit"
                                    class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                                    :disabled="searchForm.processing"
                                >
                                    Search
                                </button>
                                <button
                                    type="button"
                                    @click="clearFilters"
                                    class="px-4 py-2 bg-gray-200 text-gray-700 rounded-md hover:bg-gray-300"
                                >
                                    Clear
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Rewards Table -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div v-if="rewards.data.length > 0">
                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-200">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">User</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Amount</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Created</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-200">
                                        <tr v-for="reward in rewards.data" :key="reward.id">
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="flex items-center">
                                                    <div>
                                                        <div class="text-sm font-medium text-gray-900">
                                                            {{ reward.user?.name }}
                                                        </div>
                                                        <div class="text-sm text-gray-500">
                                                            {{ reward.user?.email }}
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="px-6 py-4">
                                                <div class="text-sm text-gray-900">{{ reward.type }}</div>
                                                <div class="text-xs text-gray-500">{{ reward.description }}</div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="text-sm font-semibold text-gray-900">
                                                    {{ formatCurrency(reward.amount) }}
                                                </div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <span 
                                                    :class="{
                                                        'bg-green-100 text-green-800': reward.status === 'approved' || reward.status === 'paid',
                                                        'bg-yellow-100 text-yellow-800': reward.status === 'pending',
                                                        'bg-red-100 text-red-800': reward.status === 'rejected'
                                                    }"
                                                    class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full"
                                                >
                                                    {{ reward.status.charAt(0).toUpperCase() + reward.status.slice(1) }}
                                                </span>
                                                <div v-if="reward.status === 'rejected' && reward.metadata?.rejection_reason" class="text-xs text-red-600 mt-1">
                                                    {{ reward.metadata.rejection_reason }}
                                                </div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="text-sm text-gray-500">
                                                    {{ formatDate(reward.created_at) }}
                                                    <div class="text-xs text-gray-400">{{ formatTime(reward.created_at) }}</div>
                                                </div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                                <div v-if="reward.status === 'pending'" class="flex space-x-2">
                                                    <button
                                                        @click="approveReward(reward.id)"
                                                        class="text-green-600 hover:text-green-900 flex items-center"
                                                    >
                                                        <CheckCircleIcon class="h-5 w-5 mr-1" />
                                                        Approve
                                                    </button>
                                                    <button
                                                        @click="rejectReward(reward.id)"
                                                        class="text-red-600 hover:text-red-900 flex items-center"
                                                    >
                                                        <XCircleIcon class="h-5 w-5 mr-1" />
                                                        Reject
                                                    </button>
                                                </div>
                                                <span v-else class="text-gray-400">
                                                    No actions
                                                </span>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>

                            <!-- Pagination -->
                            <div class="mt-6 flex items-center justify-between">
                                <div class="text-sm text-gray-700">
                                    Showing {{ rewards.from }} to {{ rewards.to }} of {{ rewards.total }} rewards
                                </div>
                                <div class="flex space-x-2">
                                    <Link 
                                        v-for="link in rewards.links" 
                                        :key="link.label"
                                        :href="link.url"
                                        :class="{
                                            'bg-indigo-600 text-white': link.active,
                                            'bg-white text-gray-700 hover:bg-gray-50': !link.active,
                                            'opacity-50 cursor-not-allowed': !link.url
                                        }"
                                        class="px-3 py-2 border rounded-md text-sm"
                                        v-html="link.label"
                                    />
                                </div>
                            </div>
                        </div>

                        <div v-else class="text-center py-12">
                            <CurrencyDollarIcon class="mx-auto h-16 w-16 text-gray-400" />
                            <h3 class="mt-4 text-lg font-medium text-gray-900">No rewards found</h3>
                            <p class="mt-2 text-sm text-gray-500">
                                {{ filters.search || filters.status ? 'Try adjusting your filters' : 'Rewards will appear here when users make referrals' }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>
