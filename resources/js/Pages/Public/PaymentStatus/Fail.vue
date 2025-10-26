<script setup>
import PublicLayout from '@/Layouts/PublicLayout.vue';
import { Head, Link } from '@inertiajs/vue3';

defineProps({
    query: Object, // Contains transaction_id from the URL query string
});
</script>

<template>
    <Head title="Payment Failed" />

    <PublicLayout title="Payment Failed">
         <div class="py-12 bg-brand-light dark:bg-gray-900">
            <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 md:p-8 text-center text-gray-900 dark:text-gray-100">
                         <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100 dark:bg-red-900 mb-4">
                            <svg class="h-6 w-6 text-red-600 dark:text-red-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </div>
                        <h2 class="text-2xl font-bold mb-3">Payment Failed</h2>
                        <p class="text-gray-600 dark:text-gray-400 mb-4">
                            Unfortunately, your payment could not be processed.
                        </p>
                         <p v-if="query?.transaction_id" class="text-sm text-gray-500 dark:text-gray-500 mb-6">
                            Transaction ID: {{ query.transaction_id }}
                        </p>
                        <p class="text-sm text-gray-600 dark:text-gray-400 mb-6">
                            Please check your payment details and try again, or contact support if the problem persists.
                        </p>
                        <Link :href="route('public.travel-insurance.index')" class="inline-flex items-center justify-center px-4 py-2 bg-gray-300 dark:bg-gray-600 border border-transparent rounded-lg font-semibold text-sm text-gray-800 dark:text-gray-200 tracking-widest hover:bg-opacity-90 transition ease-in-out duration-150">
                            Try Again
                        </Link>
                    </div>
                </div>
            </div>
        </div>
    </PublicLayout>
</template>