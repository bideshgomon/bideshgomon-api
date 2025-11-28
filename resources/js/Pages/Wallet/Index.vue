<script setup>
import { ref } from 'vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { useBangladeshFormat } from '@/Composables/useBangladeshFormat';
import PaymentGatewaySelector from '@/Components/PaymentGatewaySelector.vue';
import {
    PlusCircleIcon,
    MinusCircleIcon,
    ArrowTrendingUpIcon,
    ArrowTrendingDownIcon,
    ClockIcon,
    CheckCircleIcon,
    XCircleIcon,
    ArrowPathIcon,
    ChartBarIcon,
    DocumentArrowDownIcon,
    ChevronLeftIcon
} from '@heroicons/vue/24/outline';

const props = defineProps({
    wallet: Object,
    balance: [Number, String],
    formattedBalance: String,
    recentTransactions: Array,
    totalIn: [Number, String],
    totalOut: [Number, String],
    transactionCount: Number,
});

const { formatCurrency, formatDate, formatTime } = useBangladeshFormat();

const showAddFunds = ref(false);
const showWithdraw = ref(false);
const agreeToTerms = ref(false);

const addFundsForm = useForm({
    amount: '',
    gateway: null,
});

const withdrawForm = useForm({
    amount: '',
    account_number: '',
    account_type: 'Bank',
});

const getTransactionColor = (type) => {
    return type === 'credit' ? 'text-green-600' : 'text-red-600';
};

const getTransactionSign = (type) => {
    return type === 'credit' ? '+' : '-';
};

const getTransactionIcon = (type) => {
    return type === 'credit' ? ArrowTrendingUpIcon : ArrowTrendingDownIcon;
};

const getStatusColor = (status) => {
    const colors = {
        completed: 'bg-green-100 text-green-800',
        pending: 'bg-yellow-100 text-yellow-800',
        failed: 'bg-red-100 text-red-800',
    };
    return colors[status] || 'bg-gray-100 text-gray-800';
};

const submitAddFunds = () => {
    addFundsForm.post(route('wallet.add-funds'), {
        onSuccess: () => {
            addFundsForm.reset();
            showAddFunds.value = false;
        },
    });
};

const submitWithdraw = () => {
    withdrawForm.post(route('wallet.withdraw'), {
        onSuccess: () => {
            withdrawForm.reset();
            showWithdraw.value = false;
        },
    });
};
</script>

<template>
    <Head title="My Wallet" />

    <AuthenticatedLayout>
        <!-- Desktop & Mobile Content -->
        <div class="py-6 sm:py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <!-- Header Section -->
                <div class="bg-gradient-to-br from-emerald-600 to-emerald-700 px-4 sm:px-6 lg:px-8 pt-6 pb-32 sm:rounded-3xl mb-6">
                    <div class="flex items-center gap-3">
                        <Link :href="route('dashboard')" class="text-white hover:text-emerald-100 transition-colors">
                            <ChevronLeftIcon class="h-6 w-6" />
                        </Link>
                        <div>
                            <h1 class="text-2xl sm:text-3xl font-bold text-white">My Wallet</h1>
                            <p class="text-emerald-100 text-sm">Manage your balance and transactions</p>
                        </div>
                    </div>
                </div>

            <!-- Balance Card - Overlapping -->
            <div class="-mt-24 px-4 sm:px-0 mb-6">
                <div class="bg-white rounded-3xl shadow-xl p-6 sm:p-8">
                    <!-- Status Badge -->
                    <div class="flex justify-between items-start mb-4">
                        <div class="flex items-center space-x-2">
                            <div class="w-3 h-3 bg-green-500 rounded-full animate-pulse"></div>
                            <span class="text-sm font-medium text-gray-700">{{ wallet.status === 'active' ? 'Active' : 'Inactive' }}</span>
                        </div>
                        <span class="text-xs text-gray-500">#{{ wallet.id }}</span>
                    </div>

                    <!-- Balance Display -->
                    <div class="text-center py-4">
                        <p class="text-sm text-gray-600 mb-2">Available Balance</p>
                        <h2 class="text-5xl font-bold text-gray-900 mb-1">{{ formattedBalance }}</h2>
                        <p class="text-sm text-gray-500">Bangladeshi Taka (BDT)</p>
                        
                        <!-- Zero Balance Hint -->
                        <div v-if="balance === 0 || balance === '0'" class="mt-4 px-4 py-3 bg-amber-50 rounded-lg border border-amber-200">
                            <p class="text-xs text-amber-800">
                                💡 <span class="font-semibold">Get Started:</span> Add funds to your wallet to start using our services
                            </p>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="grid grid-cols-2 gap-3 mt-6">
                        <button
                            @click="showAddFunds = true"
                            class="flex items-center justify-center space-x-2 py-4 bg-emerald-600 text-white rounded-2xl font-semibold hover:bg-emerald-700 transition-colors touch-manipulation min-h-[48px]"
                        >
                            <PlusCircleIcon class="h-5 w-5" />
                            <span>Add Funds</span>
                        </button>
                        <button
                            @click="showWithdraw = true"
                            class="flex items-center justify-center space-x-2 py-4 bg-gray-100 text-gray-700 rounded-2xl font-semibold hover:bg-gray-200 transition-colors touch-manipulation min-h-[48px]"
                        >
                            <MinusCircleIcon class="h-5 w-5" />
                            <span>Withdraw</span>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Quick Stats -->
            <div class="px-4 sm:px-0 mb-6">
                <div class="grid grid-cols-3 gap-3 sm:gap-4">
                    <div class="bg-white rounded-2xl p-4 sm:p-5 shadow-sm">
                        <div class="flex flex-col">
                            <div class="flex items-center justify-between mb-2">
                                <p class="text-xs sm:text-sm text-gray-500">Total In</p>
                                <ArrowTrendingUpIcon class="h-5 w-5 sm:h-6 sm:w-6 text-green-600" />
                            </div>
                            <p class="text-lg sm:text-xl font-bold text-green-600">{{ formatCurrency(totalIn || 0) }}</p>
                        </div>
                    </div>
                    <div class="bg-white rounded-2xl p-4 sm:p-5 shadow-sm">
                        <div class="flex flex-col">
                            <div class="flex items-center justify-between mb-2">
                                <p class="text-xs sm:text-sm text-gray-500">Total Out</p>
                                <ArrowTrendingDownIcon class="h-5 w-5 sm:h-6 sm:w-6 text-red-600" />
                            </div>
                            <p class="text-lg sm:text-xl font-bold text-red-600">{{ formatCurrency(totalOut || 0) }}</p>
                        </div>
                    </div>
                    <div class="bg-white rounded-2xl p-4 sm:p-5 shadow-sm">
                        <div class="flex flex-col">
                            <div class="flex items-center justify-between mb-2">
                                <p class="text-xs sm:text-sm text-gray-500">Transactions</p>
                                <ChartBarIcon class="h-5 w-5 sm:h-6 sm:w-6 text-indigo-600" />
                            </div>
                            <p class="text-lg sm:text-xl font-bold text-gray-900">{{ transactionCount || 0 }}</p>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Quick Actions Bar -->
            <div class="px-4 sm:px-0 mb-6">
                <div class="bg-white rounded-2xl p-4 sm:p-5 shadow-sm">
                    <div class="flex items-center justify-between gap-3">
                        <Link 
                            :href="route('wallet.transactions')"
                            class="flex-1 flex items-center justify-center gap-2 py-3 bg-indigo-50 text-indigo-700 rounded-xl font-medium hover:bg-indigo-100 transition-colors"
                        >
                            <ClockIcon class="h-5 w-5" />
                            <span class="text-sm">History</span>
                        </Link>
                        <button 
                            @click="window.location.reload()"
                            class="flex-1 flex items-center justify-center gap-2 py-3 bg-gray-50 text-gray-700 rounded-xl font-medium hover:bg-gray-100 transition-colors"
                        >
                            <ArrowPathIcon class="h-5 w-5" />
                            <span class="text-sm">Refresh</span>
                        </button>
                        <button 
                            class="flex-1 flex items-center justify-center gap-2 py-3 bg-gray-50 text-gray-700 rounded-xl font-medium hover:bg-gray-100 transition-colors"
                        >
                            <DocumentArrowDownIcon class="h-5 w-5" />
                            <span class="text-sm">Export</span>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Recent Transactions -->
            <div class="px-4 sm:px-0">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-gray-900">Recent Transactions</h3>
                    <Link
                        :href="route('wallet.transactions')"
                        class="text-sm font-medium text-emerald-600 hover:text-emerald-700"
                    >
                        View All
                    </Link>
                </div>

                <!-- Transaction List -->
                <div v-if="recentTransactions.length > 0" class="space-y-3">
                    <div
                        v-for="transaction in recentTransactions"
                        :key="transaction.id"
                        class="bg-white rounded-2xl p-4 sm:p-5 shadow-sm hover:shadow-md transition-shadow"
                    >
                        <div class="flex items-start space-x-3">
                            <!-- Icon -->
                            <div
                                class="flex-shrink-0 w-12 h-12 rounded-xl flex items-center justify-center"
                                :class="transaction.transaction_type === 'credit' ? 'bg-green-100' : 'bg-red-100'"
                            >
                                <component
                                    :is="getTransactionIcon(transaction.transaction_type)"
                                    class="h-6 w-6"
                                    :class="transaction.transaction_type === 'credit' ? 'text-green-600' : 'text-red-600'"
                                />
                            </div>

                            <!-- Details -->
                            <div class="flex-1 min-w-0">
                                <p class="font-semibold text-gray-900 truncate">{{ transaction.description }}</p>
                                <div class="flex items-center space-x-2 mt-1">
                                    <span class="text-xs text-gray-500">{{ formatDate(transaction.created_at) }}</span>
                                    <span class="text-xs text-gray-500">•</span>
                                    <span
                                        class="text-xs font-medium px-2 py-0.5 rounded-full"
                                        :class="getStatusColor(transaction.status)"
                                    >
                                        {{ transaction.status }}
                                    </span>
                                </div>
                            </div>

                            <!-- Amount -->
                            <div class="flex-shrink-0 text-right">
                                <p
                                    class="text-lg font-bold"
                                    :class="getTransactionColor(transaction.transaction_type)"
                                >
                                    {{ getTransactionSign(transaction.transaction_type) }}{{ formatCurrency(transaction.amount) }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Empty State -->
                <div v-else class="bg-white rounded-2xl p-12 sm:p-16 text-center shadow-sm">
                    <ClockIcon class="mx-auto h-16 w-16 text-gray-300 mb-4" />
                    <p class="text-gray-500 font-medium mb-2">No transactions yet</p>
                    <p class="text-sm text-gray-400">Your transaction history will appear here</p>
                </div>
            </div>
            </div>
        </div>

        <!-- Add Funds Modal -->
        <div
            v-if="showAddFunds"
            class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-end sm:items-center justify-center p-0 sm:p-4"
            @click.self="showAddFunds = false"
        >
            <div class="bg-white rounded-t-3xl sm:rounded-3xl w-full sm:max-w-md max-h-[85vh] sm:max-h-[90vh] overflow-y-auto animate-slide-up">
                <div class="p-6 pb-[calc(1.5rem+env(safe-area-inset-bottom,0px))]">
                    <h3 class="text-2xl font-bold text-gray-900 mb-6">Add Funds</h3>

                <form @submit.prevent="submitAddFunds" class="space-y-6">
                    <!-- Amount Input -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Amount (BDT)</label>
                        <input
                            v-model="addFundsForm.amount"
                            type="number"
                            min="100"
                            max="100000"
                            placeholder="Enter amount (min ৳100)"
                            class="w-full px-4 py-4 text-lg border-2 border-gray-200 rounded-2xl focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500"
                            required
                        />
                        <p class="text-xs text-gray-500 mt-2">Minimum: ৳100 • Maximum: ৳100,000</p>
                    </div>

                    <!-- Payment Gateway Selector -->
                    <div v-if="addFundsForm.amount >= 100">
                        <label class="block text-sm font-semibold text-gray-700 mb-3">Select Payment Gateway</label>
                        <PaymentGatewaySelector
                            :amount="Number(addFundsForm.amount)"
                            v-model="addFundsForm.gateway"
                            @update:agreeToTerms="agreeToTerms = $event"
                        />
                    </div>

                    <!-- Action Buttons -->
                    <div class="flex space-x-3 pt-4 pb-2">
                        <button
                            type="button"
                            @click="showAddFunds = false"
                            class="flex-1 py-4 bg-gray-100 text-gray-700 rounded-2xl font-semibold hover:bg-gray-200 transition-colors min-h-[48px] touch-manipulation"
                        >
                            Cancel
                        </button>
                        <button
                            type="submit"
                            :disabled="addFundsForm.processing || !addFundsForm.gateway || !agreeToTerms || addFundsForm.amount < 100"
                            class="flex-1 py-4 bg-emerald-600 text-white rounded-2xl font-semibold hover:bg-emerald-700 transition-colors disabled:opacity-50 disabled:cursor-not-allowed min-h-[48px] touch-manipulation"
                        >
                            {{ addFundsForm.processing ? 'Processing...' : 'Proceed to Payment' }}
                        </button>
                    </div>
                </form>
                </div>
            </div>
        </div>

        <!-- Withdraw Modal -->
        <div
            v-if="showWithdraw"
            class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-end sm:items-center justify-center p-0 sm:p-4"
            @click.self="showWithdraw = false"
        >
            <div class="bg-white rounded-t-3xl sm:rounded-3xl w-full sm:max-w-md max-h-[85vh] sm:max-h-[90vh] overflow-y-auto animate-slide-up">
                <div class="p-6 pb-[calc(1.5rem+env(safe-area-inset-bottom,0px))]">
                    <h3 class="text-2xl font-bold text-gray-900 mb-6">Withdraw Funds</h3>

                <form @submit.prevent="submitWithdraw" class="space-y-4">
                    <!-- Amount Input -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Amount (BDT)</label>
                        <input
                            v-model="withdrawForm.amount"
                            type="number"
                            placeholder="Enter amount"
                            :max="balance"
                            class="w-full px-4 py-4 text-lg border-2 border-gray-200 rounded-2xl focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500"
                            required
                        />
                        <p class="text-xs text-gray-500 mt-2">Available: {{ formattedBalance }}</p>
                    </div>

                    <!-- Account Type -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Withdraw To</label>
                        <select
                            v-model="withdrawForm.account_type"
                            class="w-full px-4 py-4 border-2 border-gray-200 rounded-2xl focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500"
                        >
                            <option value="Bank">Bank Account</option>
                            <option value="bKash">bKash</option>
                            <option value="Nagad">Nagad</option>
                            <option value="Rocket">Rocket</option>
                        </select>
                    </div>

                    <!-- Account Number -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Account Number</label>
                        <input
                            v-model="withdrawForm.account_number"
                            type="text"
                            placeholder="Enter account number"
                            class="w-full px-4 py-4 border-2 border-gray-200 rounded-2xl focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500"
                            required
                        />
                    </div>

                    <!-- Action Buttons -->
                    <div class="flex space-x-3 pt-4 pb-2">
                        <button
                            type="button"
                            @click="showWithdraw = false"
                            class="flex-1 py-4 bg-gray-100 text-gray-700 rounded-2xl font-semibold hover:bg-gray-200 transition-colors min-h-[48px] touch-manipulation"
                        >
                            Cancel
                        </button>
                        <button
                            type="submit"
                            :disabled="withdrawForm.processing"
                            class="flex-1 py-4 bg-emerald-600 text-white rounded-2xl font-semibold hover:bg-emerald-700 transition-colors disabled:opacity-50 min-h-[48px] touch-manipulation"
                        >
                            {{ withdrawForm.processing ? 'Processing...' : 'Withdraw' }}
                        </button>
                    </div>
                </form>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<style scoped>
@keyframes slide-up {
    from {
        transform: translateY(100%);
        opacity: 0;
    }
    to {
        transform: translateY(0);
        opacity: 1;
    }
}

.animate-slide-up {
    animation: slide-up 0.3s ease-out;
}

/* Ensure modals have proper spacing on mobile with notches */
@media (max-width: 640px) {
    /* Add extra bottom padding for mobile devices */
    .max-h-\[85vh\] {
        max-height: calc(85vh - env(safe-area-inset-bottom, 0px));
    }
}

/* For very short screens, reduce modal height further */
@media (max-height: 700px) {
    .max-h-\[85vh\] {
        max-height: 80vh;
    }
}

@media (max-height: 600px) {
    .max-h-\[85vh\] {
        max-height: 75vh;
    }
}
</style>
