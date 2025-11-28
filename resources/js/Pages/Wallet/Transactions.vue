<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { useBangladeshFormat } from '@/Composables/useBangladeshFormat';
import RhythmicCard from '@/Components/Rhythmic/RhythmicCard.vue';
import StatusBadge from '@/Components/Rhythmic/StatusBadge.vue';
import FlowButton from '@/Components/Rhythmic/FlowButton.vue';
import { ref } from 'vue';

const props = defineProps({
    transactions: Object,
    filters: Object,
});

const { formatCurrency, formatDate, formatTime } = useBangladeshFormat();

const search = ref(props.filters.search || '');
const type = ref(props.filters.type || '');

const filterTransactions = () => {
    router.get(route('wallet.transactions'), {
        search: search.value,
        type: type.value,
    }, {
        preserveState: true,
        preserveScroll: true,
    });
};

const clearFilters = () => {
    search.value = '';
    type.value = '';
    filterTransactions();
};

const getTransactionColor = (transactionType) => {
    return transactionType === 'credit' ? 'text-green-600' : 'text-red-600';
};

const getTransactionSign = (transactionType) => {
    return transactionType === 'credit' ? '+' : '-';
};
</script>

<template>
    <Head title="Transaction History" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">Transaction History</h2>
                <Link :href="route('wallet.index')" class="text-sm text-indigo-600 hover:text-indigo-800">
                    ← Back to Wallet
                </Link>
            </div>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
                <!-- Filters -->
                <RhythmicCard variant="light" size="lg" class="animate-fadeIn">
                    <template #default>
                        <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Search</label>
                            <input 
                                v-model="search"
                                type="text" 
                                placeholder="Search description..."
                                class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                @keyup.enter="filterTransactions"
                            />
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Type</label>
                            <select 
                                v-model="type"
                                class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                @change="filterTransactions"
                            >
                                <option value="">All Types</option>
                                <option value="credit">Credit (+)</option>
                                <option value="debit">Debit (-)</option>
                            </select>
                        </div>
                        <div class="flex items-end space-x-2">
                            <FlowButton
                                @click="filterTransactions"
                                variant="primary"
                                size="md"
                            >
                                Apply Filters
                            </FlowButton>
                            <FlowButton
                                @click="clearFilters"
                                variant="secondary"
                                size="md"
                            >
                                Clear
                            </FlowButton>
                        </div>
                    </div>
                    </template>
                </RhythmicCard>

                <!-- Transactions List -->
                <div class="bg-white shadow sm:rounded-lg overflow-hidden">
                    <div v-if="transactions.data.length > 0">
                        <!-- Desktop View -->
                        <div class="hidden sm:block">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Date & Time
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Description
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Type
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Amount
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Balance After
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Status
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    <tr v-for="transaction in transactions.data" :key="transaction.id" class="hover:bg-gray-50">
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            <div>{{ formatDate(transaction.created_at) }}</div>
                                            <div class="text-xs text-gray-500">{{ formatTime(transaction.created_at) }}</div>
                                        </td>
                                        <td class="px-6 py-4 text-sm text-gray-900">
                                            <div>{{ transaction.description }}</div>
                                            <div v-if="transaction.reference_type" class="text-xs text-gray-500">
                                                {{ transaction.reference_type.replace('_', ' ') }}
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <StatusBadge :status="transaction.transaction_type" size="sm" />
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold" 
                                            :class="getTransactionColor(transaction.transaction_type)">
                                            {{ getTransactionSign(transaction.transaction_type) }}{{ formatCurrency(transaction.amount) }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            {{ formatCurrency(transaction.balance_after) }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <StatusBadge :status="transaction.status" size="sm" />
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <!-- Mobile View -->
                        <div class="sm:hidden divide-y divide-gray-200">
                            <div v-for="transaction in transactions.data" :key="transaction.id" class="p-4">
                                <div class="flex justify-between items-start mb-2">
                                    <span class="text-sm font-medium text-gray-900">{{ transaction.description }}</span>
                                    <span class="text-sm font-bold" :class="getTransactionColor(transaction.transaction_type)">
                                        {{ getTransactionSign(transaction.transaction_type) }}{{ formatCurrency(transaction.amount) }}
                                    </span>
                                </div>
                                <div class="flex justify-between text-xs text-gray-500">
                                    <span>{{ formatDate(transaction.created_at) }} {{ formatTime(transaction.created_at) }}</span>
                                    <span class="capitalize">{{ transaction.status }}</span>
                                </div>
                                <div class="mt-1 text-xs text-gray-500">
                                    Balance: {{ formatCurrency(transaction.balance_after) }}
                                </div>
                            </div>
                        </div>

                        <!-- Pagination -->
                        <div class="bg-white px-4 py-3 border-t border-gray-200 sm:px-6">
                            <div class="flex items-center justify-between">
                                <div class="flex-1 flex justify-between sm:hidden">
                                    <Link v-if="transactions.prev_page_url" :href="transactions.prev_page_url"
                                          class="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                                        Previous
                                    </Link>
                                    <Link v-if="transactions.next_page_url" :href="transactions.next_page_url"
                                          class="ml-3 relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                                        Next
                                    </Link>
                                </div>
                                <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
                                    <div>
                                        <p class="text-sm text-gray-700">
                                            Showing <span class="font-medium">{{ transactions.from }}</span> to 
                                            <span class="font-medium">{{ transactions.to }}</span> of 
                                            <span class="font-medium">{{ transactions.total }}</span> transactions
                                        </p>
                                    </div>
                                    <div class="flex space-x-2">
                                        <Link v-if="transactions.prev_page_url" :href="transactions.prev_page_url"
                                              class="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                                            Previous
                                        </Link>
                                        <Link v-if="transactions.next_page_url" :href="transactions.next_page_url"
                                              class="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                                            Next
                                        </Link>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div v-else class="p-12 text-center">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                  d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        <p class="mt-2 text-sm text-gray-500">No transactions found</p>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
