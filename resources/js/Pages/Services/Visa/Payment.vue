<template>
    <AuthenticatedLayout>
        <Head title="Payment - Visa Application" />

        <div class="py-12">
            <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
                <!-- Header -->
                <div class="mb-8">
                    <h1 class="text-3xl font-bold text-gray-900">Complete Payment</h1>
                    <p class="mt-2 text-gray-600">Application: {{ application.application_reference }}</p>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    <!-- Payment Form -->
                    <div class="lg:col-span-2">
                        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                            <h2 class="text-xl font-semibold text-gray-900 mb-6">Payment Details</h2>
                            
                            <form @submit.prevent="submit" class="space-y-6">
                                <!-- Payment Method -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-3">Select Payment Method *</label>
                                    <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
                                        <label
                                            v-for="method in paymentMethods"
                                            :key="method.id"
                                            :class="[
                                                'relative flex items-center justify-center p-4 border-2 rounded-lg cursor-pointer transition-all',
                                                form.payment_method === method.id
                                                    ? 'border-indigo-600 bg-indigo-50'
                                                    : 'border-gray-200 hover:border-gray-300'
                                            ]"
                                        >
                                            <input
                                                v-model="form.payment_method"
                                                type="radio"
                                                :value="method.id"
                                                class="sr-only"
                                            />
                                            <div class="text-center">
                                                <div class="text-2xl mb-1">{{ method.icon }}</div>
                                                <div class="text-sm font-medium text-gray-900">{{ method.name }}</div>
                                            </div>
                                            <div v-if="form.payment_method === method.id" class="absolute top-2 right-2">
                                                <svg class="h-5 w-5 text-indigo-600" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                                </svg>
                                            </div>
                                        </label>
                                    </div>
                                    <div v-if="form.errors.payment_method" class="mt-2 text-sm text-red-600">
                                        {{ form.errors.payment_method }}
                                    </div>
                                </div>

                                <!-- Transaction ID -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Transaction ID *</label>
                                    <input
                                        v-model="form.transaction_id"
                                        type="text"
                                        required
                                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                                        placeholder="Enter your transaction ID"
                                    />
                                    <p class="mt-2 text-xs text-gray-600">
                                        Please enter the transaction ID from your {{ selectedPaymentMethodName }} payment
                                    </p>
                                    <div v-if="form.errors.transaction_id" class="mt-2 text-sm text-red-600">
                                        {{ form.errors.transaction_id }}
                                    </div>
                                </div>

                                <!-- Payment Instructions -->
                                <div v-if="form.payment_method" class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                                    <h3 class="text-sm font-semibold text-blue-900 mb-2">Payment Instructions</h3>
                                    <ul class="text-xs text-blue-800 space-y-1">
                                        <li v-if="form.payment_method === 'bkash'">1. Dial *247# from your mobile</li>
                                        <li v-if="form.payment_method === 'bkash'">2. Select "Send Money"</li>
                                        <li v-if="form.payment_method === 'bkash'">3. Send to: 01XXXXXXXXX</li>
                                        <li v-if="form.payment_method === 'nagad'">1. Open Nagad app</li>
                                        <li v-if="form.payment_method === 'nagad'">2. Select "Send Money"</li>
                                        <li v-if="form.payment_method === 'nagad'">3. Send to: 01XXXXXXXXX</li>
                                        <li v-if="form.payment_method === 'rocket'">1. Dial *322# from your mobile</li>
                                        <li v-if="form.payment_method === 'rocket'">2. Select "Send Money"</li>
                                        <li v-if="form.payment_method === 'rocket'">3. Send to: 01XXXXXXXXX</li>
                                        <li v-if="form.payment_method === 'bank_transfer'">Bank: Example Bank Ltd.</li>
                                        <li v-if="form.payment_method === 'bank_transfer'">Account: 1234567890</li>
                                        <li v-if="form.payment_method === 'bank_transfer'">Name: Bidesh Gomon</li>
                                        <li>Amount: ৳{{ application.total_amount.toLocaleString() }}</li>
                                        <li class="font-semibold">Copy the transaction ID after payment</li>
                                    </ul>
                                </div>

                                <!-- Terms -->
                                <div class="flex items-start">
                                    <input
                                        v-model="form.agree_terms"
                                        type="checkbox"
                                        required
                                        class="mt-1 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500"
                                    />
                                    <label class="ml-2 text-sm text-gray-700">
                                        I agree to the <a href="#" class="text-indigo-600 hover:text-indigo-700">terms and conditions</a> and confirm that I have completed the payment
                                    </label>
                                </div>

                                <!-- Submit -->
                                <div class="flex items-center justify-between pt-4">
                                    <Link
                                        :href="route('visa.show', application.id)"
                                        class="inline-flex items-center px-6 py-3 bg-gray-200 text-gray-700 font-medium rounded-lg hover:bg-gray-300"
                                    >
                                        Cancel
                                    </Link>
                                    <button
                                        type="submit"
                                        :disabled="form.processing || !form.payment_method || !form.transaction_id || !form.agree_terms"
                                        class="inline-flex items-center px-6 py-3 bg-indigo-600 text-white font-medium rounded-lg hover:bg-indigo-700 disabled:opacity-50 disabled:cursor-not-allowed"
                                    >
                                        <svg v-if="form.processing" class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                        </svg>
                                        <span v-if="form.processing">Processing...</span>
                                        <span v-else>Confirm Payment</span>
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- Order Summary -->
                    <div>
                        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 sticky top-6">
                            <h2 class="text-lg font-semibold text-gray-900 mb-4">Order Summary</h2>
                            
                            <div class="space-y-3 mb-4">
                                <div>
                                    <div class="text-sm text-gray-600">Destination</div>
                                    <div class="text-sm font-medium text-gray-900">{{ application.destination_country }}</div>
                                </div>
                                <div>
                                    <div class="text-sm text-gray-600">Visa Type</div>
                                    <div class="text-sm font-medium text-gray-900">{{ formatStatus(application.visa_type) }}</div>
                                </div>
                                <div>
                                    <div class="text-sm text-gray-600">Processing</div>
                                    <div class="text-sm font-medium text-gray-900">{{ formatStatus(application.processing_type) }}</div>
                                </div>
                            </div>

                            <div class="border-t border-gray-200 pt-4 space-y-2">
                                <div class="flex justify-between text-sm">
                                    <span class="text-gray-600">Service Fee</span>
                                    <span class="text-gray-900">৳{{ application.service_fee.toLocaleString() }}</span>
                                </div>
                                <div class="flex justify-between text-sm">
                                    <span class="text-gray-600">Government Fee</span>
                                    <span class="text-gray-900">৳{{ application.government_fee.toLocaleString() }}</span>
                                </div>
                                <div class="flex justify-between text-sm">
                                    <span class="text-gray-600">Processing Fee</span>
                                    <span class="text-gray-900">৳{{ application.processing_fee.toLocaleString() }}</span>
                                </div>
                                <div class="flex justify-between pt-2 border-t border-gray-200">
                                    <span class="text-base font-semibold text-gray-900">Total</span>
                                    <span class="text-xl font-bold text-indigo-600">৳{{ application.total_amount.toLocaleString() }}</span>
                                </div>
                            </div>

                            <div class="mt-6 p-3 bg-green-50 rounded-lg">
                                <div class="flex">
                                    <svg class="h-5 w-5 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                    </svg>
                                    <div class="ml-3">
                                        <p class="text-xs text-green-800">Secure payment processing with 256-bit SSL encryption</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import { computed } from 'vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';

const props = defineProps({
    application: Object,
});

const paymentMethods = [
    { id: 'bkash', name: 'bKash', icon: '💳' },
    { id: 'nagad', name: 'Nagad', icon: '📱' },
    { id: 'rocket', name: 'Rocket', icon: '🚀' },
    { id: 'bank_transfer', name: 'Bank Transfer', icon: '🏦' },
    { id: 'card', name: 'Credit/Debit Card', icon: '💎' },
];

const form = useForm({
    payment_method: '',
    transaction_id: '',
    agree_terms: false,
});

const selectedPaymentMethodName = computed(() => {
    const method = paymentMethods.find(m => m.id === form.payment_method);
    return method ? method.name : '';
});

const formatStatus = (status) => {
    return status.replace(/_/g, ' ').replace(/\b\w/g, l => l.toUpperCase());
};

const submit = () => {
    form.post(route('visa.process-payment', props.application.id));
};
</script>
