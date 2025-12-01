<script setup>
import { Head, Link, router, useForm } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { 
    ArrowLeftIcon,
    WalletIcon,
    PlusCircleIcon,
    MinusCircleIcon,
    ArrowPathIcon,
    CheckCircleIcon,
    XCircleIcon,
    ExclamationTriangleIcon
} from '@heroicons/vue/24/outline';
import { useBangladeshFormat } from '@/Composables/useBangladeshFormat';
import { ref } from 'vue';

const props = defineProps({
    wallet: Object,
    transactions: Object,
});

const { formatCurrency, formatDate, formatTime } = useBangladeshFormat();

const showCreditModal = ref(false);
const showDebitModal = ref(false);

const creditForm = useForm({
    amount: '',
    description: '',
    note: '',
});

const debitForm = useForm({
    amount: '',
    description: '',
    note: '',
});

const credit = () => {
    creditForm.post(route('admin.wallets.credit', props.wallet.id), {
        preserveScroll: true,
        onSuccess: () => {
            creditForm.reset();
            showCreditModal.value = false;
        }
    });
};

const debit = () => {
    debitForm.post(route('admin.wallets.debit', props.wallet.id), {
        preserveScroll: true,
        onSuccess: () => {
            debitForm.reset();
            showDebitModal.value = false;
        }
    });
};

const toggleStatus = () => {
    if (confirm(`Are you sure you want to ${props.wallet.status === 'active' ? 'suspend' : 'activate'} this wallet?`)) {
        router.post(route('admin.wallets.toggle-status', props.wallet.id));
    }
};

const reverseTransaction = (transactionId) => {
    if (confirm('Are you sure you want to reverse this transaction? This action cannot be undone.')) {
        router.post(route('admin.wallets.reverse-transaction', transactionId));
    }
};

const getTransactionTypeColor = (type) => {
    return type === 'credit' 
        ? 'text-green-600 dark:text-green-400' 
        : 'text-red-600 dark:text-red-400';
};

const getTransactionTypeIcon = (type) => {
    return type === 'credit' ? PlusCircleIcon : MinusCircleIcon;
};

const getStatusColor = (status) => {
    return {
        'active': 'bg-green-100 text-green-800 dark:bg-green-500/10 dark:text-green-400',
        'suspended': 'bg-yellow-100 text-yellow-800 dark:bg-yellow-500/10 dark:text-yellow-400',
        'closed': 'bg-red-100 text-red-800 dark:bg-red-500/10 dark:text-red-400',
    }[status] || 'bg-gray-100 text-gray-800';
};
</script>

<template>
    <Head :title="`Wallet - ${wallet.user?.name}`" />

    <AdminLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <Link :href="route('admin.wallets.index')" class="text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200">
                        <ArrowLeftIcon class="h-5 w-5" />
                    </Link>
                    <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                        Wallet Details
                    </h2>
                </div>
                <div class="flex gap-2">
                    <button
                        @click="showCreditModal = true"
                        class="inline-flex items-center px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500"
                    >
                        <PlusCircleIcon class="h-5 w-5 mr-2" />
                        Credit
                    </button>
                    <button
                        @click="showDebitModal = true"
                        class="inline-flex items-center px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500"
                    >
                        <MinusCircleIcon class="h-5 w-5 mr-2" />
                        Debit
                    </button>
                    <button
                        @click="toggleStatus"
                        class="inline-flex items-center px-4 py-2 bg-yellow-600 text-white rounded-md hover:bg-yellow-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-yellow-500"
                    >
                        {{ wallet.status === 'active' ? 'Suspend' : 'Activate' }}
                    </button>
                </div>
            </div>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
                <!-- Wallet Info -->
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-4">
                                <div class="bg-indigo-100 dark:bg-indigo-900/20 p-4 rounded-lg">
                                    <WalletIcon class="h-12 w-12 text-indigo-600 dark:text-indigo-400" />
                                </div>
                                <div>
                                    <h3 class="text-2xl font-bold text-gray-900 dark:text-white">{{ wallet.user?.name }}</h3>
                                    <p class="text-sm text-gray-500 dark:text-gray-400">{{ wallet.user?.email }}</p>
                                </div>
                            </div>
                            <div class="text-right">
                                <div class="text-sm text-gray-500 dark:text-gray-400 mb-1">Current Balance</div>
                                <div class="text-3xl font-bold text-gray-900 dark:text-white">{{ formatCurrency(wallet.balance) }}</div>
                                <span :class="getStatusColor(wallet.status)" class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium mt-2">
                                    {{ ((wallet.status || '').charAt(0).toUpperCase() || '') + (wallet.status || '').slice(1) }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Transactions -->
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Transaction History</h3>
                        
                        <div v-if="transactions.data.length > 0">
                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                    <thead class="bg-gray-50 dark:bg-gray-900">
                                        <tr>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Type</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Amount</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Balance</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Description</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Status</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Date</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                        <tr v-for="transaction in transactions.data" :key="transaction.id">
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="flex items-center gap-2">
                                                    <component :is="getTransactionTypeIcon(transaction.transaction_type)" :class="getTransactionTypeColor(transaction.transaction_type)" class="h-5 w-5" />
                                                    <span class="text-sm font-medium" :class="getTransactionTypeColor(transaction.transaction_type)">
                                                        {{ ((transaction.transaction_type || '').charAt(0).toUpperCase() || '') + (transaction.transaction_type || '').slice(1) }}
                                                    </span>
                                                </div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <span class="text-sm font-semibold" :class="getTransactionTypeColor(transaction.transaction_type)">
                                                    {{ transaction.transaction_type === 'credit' ? '+' : '-' }}{{ formatCurrency(transaction.amount) }}
                                                </span>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="text-sm text-gray-900 dark:text-white">
                                                    <div>After: {{ formatCurrency(transaction.balance_after) }}</div>
                                                    <div class="text-xs text-gray-500 dark:text-gray-400">Before: {{ formatCurrency(transaction.balance_before) }}</div>
                                                </div>
                                            </td>
                                            <td class="px-6 py-4">
                                                <div class="text-sm text-gray-900 dark:text-white">{{ transaction.description }}</div>
                                                <div v-if="transaction.reference_type" class="text-xs text-gray-500 dark:text-gray-400">
                                                    Ref: {{ transaction.reference_type }}
                                                </div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <span 
                                                    :class="{
                                                        'bg-green-100 text-green-800 dark:bg-green-500/10 dark:text-green-400': transaction.status === 'completed',
                                                        'bg-yellow-100 text-yellow-800 dark:bg-yellow-500/10 dark:text-yellow-400': transaction.status === 'pending',
                                                        'bg-red-100 text-red-800 dark:bg-red-500/10 dark:text-red-400': transaction.status === 'failed' || transaction.status === 'reversed'
                                                    }"
                                                    class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full"
                                                >
                                                    {{ transaction.status }}
                                                </span>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="text-sm text-gray-500 dark:text-gray-400">
                                                    {{ formatDate(transaction.created_at) }}
                                                    <div class="text-xs">{{ formatTime(transaction.created_at) }}</div>
                                                </div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                                <button
                                                    v-if="transaction.status === 'completed'"
                                                    @click="reverseTransaction(transaction.id)"
                                                    class="text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-300 flex items-center gap-1"
                                                >
                                                    <ArrowPathIcon class="h-4 w-4" />
                                                    Reverse
                                                </button>
                                                <span v-else class="text-gray-400">-</span>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>

                            <!-- Pagination -->
                            <div class="mt-6 flex items-center justify-between">
                                <div class="text-sm text-gray-700 dark:text-gray-300">
                                    Showing {{ transactions.from }} to {{ transactions.to }} of {{ transactions.total }} transactions
                                </div>
                                <div class="flex space-x-2">
                                    <template v-for="link in transactions.links" :key="link.label">
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
                            <h3 class="mt-4 text-lg font-medium text-gray-900 dark:text-white">No transactions yet</h3>
                            <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">This wallet has no transaction history</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Credit Modal -->
        <div v-if="showCreditModal" class="fixed inset-0 z-50 overflow-y-auto">
            <div class="flex items-center justify-center min-h-screen px-4">
                <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" @click="showCreditModal = false"></div>
                
                <div class="relative bg-white dark:bg-gray-800 rounded-lg max-w-lg w-full p-6">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Credit Wallet</h3>
                    
                    <form @submit.prevent="credit" class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Amount</label>
                            <input
                                v-model="creditForm.amount"
                                type="number"
                                step="0.01"
                                required
                                class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-md focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:text-white"
                            />
                            <div v-if="creditForm.errors.amount" class="text-red-600 text-sm mt-1">{{ creditForm.errors.amount }}</div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Description</label>
                            <input
                                v-model="creditForm.description"
                                type="text"
                                required
                                class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-md focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:text-white"
                            />
                            <div v-if="creditForm.errors.description" class="text-red-600 text-sm mt-1">{{ creditForm.errors.description }}</div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Admin Note (Optional)</label>
                            <textarea
                                v-model="creditForm.note"
                                rows="3"
                                class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-md focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:text-white"
                            ></textarea>
                        </div>

                        <div class="flex justify-end gap-2">
                            <button
                                type="button"
                                @click="showCreditModal = false"
                                class="px-4 py-2 bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-md hover:bg-gray-300 dark:hover:bg-gray-600"
                            >
                                Cancel
                            </button>
                            <button
                                type="submit"
                                :disabled="creditForm.processing"
                                class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 disabled:opacity-50"
                            >
                                Credit Wallet
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Debit Modal -->
        <div v-if="showDebitModal" class="fixed inset-0 z-50 overflow-y-auto">
            <div class="flex items-center justify-center min-h-screen px-4">
                <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" @click="showDebitModal = false"></div>
                
                <div class="relative bg-white dark:bg-gray-800 rounded-lg max-w-lg w-full p-6">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Debit Wallet</h3>
                    
                    <form @submit.prevent="debit" class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Amount</label>
                            <input
                                v-model="debitForm.amount"
                                type="number"
                                step="0.01"
                                required
                                class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-md focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:text-white"
                            />
                            <div v-if="debitForm.errors.amount" class="text-red-600 text-sm mt-1">{{ debitForm.errors.amount }}</div>
                            <div class="text-sm text-gray-500 dark:text-gray-400 mt-1">Available: {{ formatCurrency(wallet.balance) }}</div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Description</label>
                            <input
                                v-model="debitForm.description"
                                type="text"
                                required
                                class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-md focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:text-white"
                            />
                            <div v-if="debitForm.errors.description" class="text-red-600 text-sm mt-1">{{ debitForm.errors.description }}</div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Admin Note (Optional)</label>
                            <textarea
                                v-model="debitForm.note"
                                rows="3"
                                class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-md focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:text-white"
                            ></textarea>
                        </div>

                        <div class="flex justify-end gap-2">
                            <button
                                type="button"
                                @click="showDebitModal = false"
                                class="px-4 py-2 bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-md hover:bg-gray-300 dark:hover:bg-gray-600"
                            >
                                Cancel
                            </button>
                            <button
                                type="submit"
                                :disabled="debitForm.processing"
                                class="px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700 disabled:opacity-50"
                            >
                                Debit Wallet
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>
