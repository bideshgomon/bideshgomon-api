<template>
    <AuthenticatedLayout>
        <Head :title="`Transaction ${transaction.transaction_id}`" />

        <div class="py-12">
            <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
                <!-- Header -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                    <div class="p-6">
                        <div class="flex items-center justify-between mb-4">
                            <Link :href="route('payment.index')" class="text-sm text-gray-600 hover:text-gray-900">
                                ← Back to Payment History
                            </Link>
                        </div>
                        <h2 class="text-2xl font-bold text-gray-900">Transaction Details</h2>
                        <p class="text-gray-600 mt-2">Transaction ID: {{ transaction.transaction_id }}</p>
                    </div>
                </div>

                <!-- Transaction Details -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <!-- Status Badge -->
                        <div class="mb-6">
                            <span :class="{
                                'bg-green-100 text-green-800': transaction.status === 'completed',
                                'bg-yellow-100 text-yellow-800': transaction.status === 'pending' || transaction.status === 'processing',
                                'bg-red-100 text-red-800': transaction.status === 'failed' || transaction.status === 'cancelled',
                                'bg-blue-100 text-blue-800': transaction.status === 'refunded'
                            }" class="px-3 py-1 text-sm font-semibold rounded-full">
                                {{ transaction.status.charAt(0).toUpperCase() + transaction.status.slice(1) }}
                            </span>
                        </div>

                        <!-- Details Grid -->
                        <dl class="grid grid-cols-1 gap-x-4 gap-y-6 sm:grid-cols-2">
                            <div class="sm:col-span-1">
                                <dt class="text-sm font-medium text-gray-500">Amount</dt>
                                <dd class="mt-1 text-lg font-semibold text-gray-900">
                                    ৳{{ parseFloat(transaction.amount).toLocaleString('en-BD', { minimumFractionDigits: 2 }) }}
                                </dd>
                            </div>

                            <div class="sm:col-span-1">
                                <dt class="text-sm font-medium text-gray-500">Currency</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ transaction.currency }}</dd>
                            </div>

                            <div class="sm:col-span-1">
                                <dt class="text-sm font-medium text-gray-500">Gateway</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ transaction.gateway.toUpperCase() }}</dd>
                            </div>

                            <div class="sm:col-span-1">
                                <dt class="text-sm font-medium text-gray-500">Payment Method</dt>
                                <dd class="mt-1 text-sm text-gray-900">
                                    {{ transaction.payment_method || 'N/A' }}
                                </dd>
                            </div>

                            <div class="sm:col-span-1">
                                <dt class="text-sm font-medium text-gray-500">Gateway Transaction ID</dt>
                                <dd class="mt-1 text-sm text-gray-900 font-mono">
                                    {{ transaction.gateway_transaction_id || 'N/A' }}
                                </dd>
                            </div>

                            <div class="sm:col-span-1">
                                <dt class="text-sm font-medium text-gray-500">Gateway Fee</dt>
                                <dd class="mt-1 text-sm text-gray-900">
                                    ৳{{ parseFloat(transaction.gateway_fee || 0).toLocaleString('en-BD', { minimumFractionDigits: 2 }) }}
                                </dd>
                            </div>

                            <div class="sm:col-span-1">
                                <dt class="text-sm font-medium text-gray-500">Net Amount</dt>
                                <dd class="mt-1 text-sm text-gray-900">
                                    ৳{{ parseFloat(transaction.net_amount).toLocaleString('en-BD', { minimumFractionDigits: 2 }) }}
                                </dd>
                            </div>

                            <div class="sm:col-span-1">
                                <dt class="text-sm font-medium text-gray-500">Created At</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ formatDate(transaction.created_at) }}</dd>
                            </div>

                            <div v-if="transaction.paid_at" class="sm:col-span-1">
                                <dt class="text-sm font-medium text-gray-500">Paid At</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ formatDate(transaction.paid_at) }}</dd>
                            </div>

                            <div v-if="transaction.failed_at" class="sm:col-span-1">
                                <dt class="text-sm font-medium text-gray-500">Failed At</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ formatDate(transaction.failed_at) }}</dd>
                            </div>

                            <div v-if="transaction.cancelled_at" class="sm:col-span-1">
                                <dt class="text-sm font-medium text-gray-500">Cancelled At</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ formatDate(transaction.cancelled_at) }}</dd>
                            </div>

                            <div v-if="transaction.refunded_at" class="sm:col-span-1">
                                <dt class="text-sm font-medium text-gray-500">Refunded At</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ formatDate(transaction.refunded_at) }}</dd>
                            </div>

                            <div v-if="transaction.refund_amount > 0" class="sm:col-span-1">
                                <dt class="text-sm font-medium text-gray-500">Refund Amount</dt>
                                <dd class="mt-1 text-sm text-gray-900">
                                    ৳{{ parseFloat(transaction.refund_amount).toLocaleString('en-BD', { minimumFractionDigits: 2 }) }}
                                </dd>
                            </div>
                        </dl>

                        <!-- Customer Information -->
                        <div class="mt-8 pt-8 border-t border-gray-200">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Customer Information</h3>
                            <dl class="grid grid-cols-1 gap-x-4 gap-y-6 sm:grid-cols-2">
                                <div class="sm:col-span-1">
                                    <dt class="text-sm font-medium text-gray-500">Name</dt>
                                    <dd class="mt-1 text-sm text-gray-900">{{ transaction.customer_name }}</dd>
                                </div>

                                <div class="sm:col-span-1">
                                    <dt class="text-sm font-medium text-gray-500">Email</dt>
                                    <dd class="mt-1 text-sm text-gray-900">{{ transaction.customer_email }}</dd>
                                </div>

                                <div class="sm:col-span-1">
                                    <dt class="text-sm font-medium text-gray-500">Phone</dt>
                                    <dd class="mt-1 text-sm text-gray-900">{{ transaction.customer_phone }}</dd>
                                </div>
                            </dl>
                        </div>

                        <!-- Error Information (if failed) -->
                        <div v-if="transaction.error_message" class="mt-8 pt-8 border-t border-gray-200">
                            <h3 class="text-lg font-medium text-red-900 mb-4">Error Details</h3>
                            <div class="bg-red-50 border border-red-200 rounded-lg p-4">
                                <p class="text-sm text-red-800">
                                    <strong>Error Code:</strong> {{ transaction.error_code || 'N/A' }}
                                </p>
                                <p class="text-sm text-red-800 mt-2">
                                    <strong>Message:</strong> {{ transaction.error_message }}
                                </p>
                            </div>
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
    transaction: {
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
