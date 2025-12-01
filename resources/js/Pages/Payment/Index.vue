<template>
    <AuthenticatedLayout>
        <Head title="Payment History" />

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <!-- Header -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                    <div class="p-6">
                        <h2 class="text-2xl font-bold text-gray-900">Payment History</h2>
                        <p class="text-gray-600 mt-2">View all your payment transactions</p>
                    </div>
                </div>

                <!-- Transactions List -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div v-if="transactions.data.length > 0">
                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-200">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                Transaction ID
                                            </th>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                Gateway
                                            </th>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                Amount
                                            </th>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                Status
                                            </th>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                Date
                                            </th>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                Actions
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-200">
                                        <tr v-for="transaction in transactions.data" :key="transaction.id">
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                                {{ transaction.transaction_id }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                {{ (transaction.gateway || '').toUpperCase() }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                ৳{{ parseFloat(transaction.amount).toLocaleString('en-BD', { minimumFractionDigits: 2 }) }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <span :class="{
                                                    'bg-green-100 text-green-800': transaction.status === 'completed',
                                                    'bg-yellow-100 text-yellow-800': transaction.status === 'pending' || transaction.status === 'processing',
                                                    'bg-red-100 text-red-800': transaction.status === 'failed' || transaction.status === 'cancelled',
                                                    'bg-blue-100 text-blue-800': transaction.status === 'refunded'
                                                }" class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full">
                                                    {{ ((transaction.status || '').charAt(0).toUpperCase() || '') + (transaction.status || '').slice(1) }}
                                                </span>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                {{ formatDate(transaction.created_at) }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                                <Link :href="route('payment.show', transaction.id)" class="text-blue-600 hover:text-blue-900">
                                                    View Details
                                                </Link>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>

                            <!-- Pagination -->
                            <div v-if="transactions.links.length > 3" class="mt-6 flex justify-center">
                                <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px">
                                    <Link
                                        v-for="(link, index) in transactions.links"
                                        :key="index"
                                        :href="link.url"
                                        :class="{
                                            'bg-blue-600 text-white': link.active,
                                            'text-gray-700 hover:bg-gray-50': !link.active,
                                            'cursor-not-allowed opacity-50': !link.url
                                        }"
                                        class="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium"
                                        :disabled="!link.url"
                                        v-html="link.label"
                                    />
                                </nav>
                            </div>
                        </div>

                        <!-- Empty State -->
                        <div v-else class="text-center py-12">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                            </svg>
                            <h3 class="mt-2 text-sm font-medium text-gray-900">No transactions yet</h3>
                            <p class="mt-1 text-sm text-gray-500">You haven't made any payments yet.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import { Head, Link } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';

defineProps({
    transactions: {
        type: Object,
        required: true
    }
});

const formatDate = (dateString) => {
    const date = new Date(dateString);
    return date.toLocaleDateString('en-GB', {
        day: '2-digit',
        month: '2-digit',
        year: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    });
};
</script>
