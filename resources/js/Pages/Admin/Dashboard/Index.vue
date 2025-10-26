<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Head } from '@inertiajs/vue3';
import { computed } from 'vue'; // Import computed

// --- THE FIX ---
// Define the 'stats' prop passed from the refactored controller
const props = defineProps({
    stats: Object,
});

// Helper to format numbers
const formatNumber = (value) => {
    return value ? value.toLocaleString() : '0';
};

// Helper to format currency
const formatCurrency = (value) => {
    if (!value) return '$0';
    return new Intl.NumberFormat('en-US', { style: 'currency', currency: 'USD', minimumFractionDigits: 0 }).format(value);
};

// Computed property for the stats grid
const statCards = computed(() => [
    { title: 'Total Users', value: formatNumber(props.stats?.totalUsers), color: 'bg-blue-500' },
    { title: 'Total Agencies', value: formatNumber(props.stats?.totalAgencies), color: 'bg-green-500' },
    { title: 'Total Consultants', value: formatNumber(props.stats?.totalConsultants), color: 'bg-indigo-500' },
    { title: 'Pending Applications', value: formatNumber(props.stats?.pendingApplications), color: 'bg-yellow-500' },
    { title: 'Total Admins', value: formatNumber(props.stats?.totalAdmins), color: 'bg-purple-500' },
    { title: 'Total Revenue', value: formatCurrency(props.stats?.totalRevenue), color: 'bg-teal-500' },
]);
</script>

<template>
    <Head title="Admin Dashboard" />
    <AdminLayout>
        <div class="p-4 sm:p-8 bg-white dark:bg-gray-800 shadow sm:rounded-lg mb-6">
            <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">Admin Dashboard</h2>
            <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">Welcome to the Admin Panel.</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <div
                v-for="(stat, index) in statCards"
                :key="index"
                class="p-6 rounded-lg shadow-lg text-white"
                :class="stat.color"
            >
                <div class="text-sm font-medium uppercase tracking-wider">{{ stat.title }}</div>
                <div class="mt-2 text-3xl font-semibold">{{ stat.value }}</div>
            </div>
        </div>
    </AdminLayout>
</template>