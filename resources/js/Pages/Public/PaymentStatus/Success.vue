<script setup>
import PublicLayout from '@/Layouts/PublicLayout.vue';
import { Head, Link } from '@inertiajs/vue3';

defineProps({
    query: Object, // Contains transaction_id and status from the URL query string
});
</script>

<template>
    <Head title="Payment Successful" />

    <PublicLayout title="Payment Successful">
        <div class="py-12 bg-brand-light dark:bg-gray-900">
            <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 md:p-8 text-center text-gray-900 dark:text-gray-100">
                         <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-green-100 dark:bg-green-900 mb-4">
                            <svg class="h-6 w-6 text-green-600 dark:text-green-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
                            </svg>
                        </div>
                        <h2 class="text-2xl font-bold mb-3">Payment Successful!</h2>
                        <p class="text-gray-600 dark:text-gray-400 mb-4">
                            Your payment has been processed successfully. Your travel insurance policy is being issued.
                        </p>
                        <p v-if="query?.transaction_id" class="text-sm text-gray-500 dark:text-gray-500 mb-6">
                            Transaction ID: {{ query.transaction_id }}
                        </p>
                        <p class="text-sm text-gray-600 dark:text-gray-400 mb-6">
                            You should receive an email with your policy details shortly (once issued by the provider). Please check your spam folder as well.
                        </p>
                        <Link :href="route('dashboard')" class="inline-flex items-center justify-center px-4 py-2 bg-brand-primary border border-transparent rounded-lg font-semibold text-sm text-white tracking-widest hover:bg-opacity-90 transition ease-in-out duration-150">
                            Go to Dashboard
                        </Link>
                    </div>
                </div>
            </div>
        </div>
    </PublicLayout>
</template>