<script setup>
import { Head, Link } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';

const props = defineProps({
    stats: Object,
});
</script>

<template>
    <Head title="Consultant Dashboard" />

    <AuthenticatedLayout>
        <div class="py-12">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="mb-8">
                    <h1 class="text-3xl font-bold text-gray-900">Consultant Dashboard</h1>
                    <p class="mt-2 text-sm text-gray-600">Manage your consultations and clients</p>
                </div>

                <div class="bg-blue-50 border border-blue-200 rounded-lg p-6">
                    <div class="flex items-start gap-4">
                        <svg class="w-12 h-12 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <div>
                            <h3 class="text-lg font-semibold text-blue-900">Consultant Features Coming Soon</h3>
                            <p class="mt-2 text-blue-800">
                                The consultant dashboard is under development. You'll be able to manage consultations, track clients, and more.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
