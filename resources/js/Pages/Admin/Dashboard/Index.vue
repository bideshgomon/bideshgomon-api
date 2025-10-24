<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Head } from '@inertiajs/vue3';
</script>

<template>
    <Head title="Admin Dashboard" />
    <AdminLayout>
        <div class="p-4 sm:p-8 bg-white dark:bg-gray-800 shadow sm:rounded-lg">
            <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">Admin Dashboard</h2>
            <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">Welcome to the Admin Panel.</p>
            </div>
    </AdminLayout>
</template>