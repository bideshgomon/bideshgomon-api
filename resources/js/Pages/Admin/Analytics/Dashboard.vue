<template>
    <AdminLayout>
        <Head title="Analytics Dashboard" />

        <div class="py-6">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <!-- Header -->
                <div class="mb-8">
                    <h1 class="text-3xl font-bold text-gray-900">Analytics Dashboard</h1>
                    <p class="mt-2 text-sm text-gray-600">Monitor key metrics and platform performance</p>
                </div>

                <!-- Quick Stats Grid -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                    <!-- Total Users -->
                    <div class="bg-white rounded-lg shadow p-6 border-l-4 border-blue-500">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm font-medium text-gray-600">Total Users</p>
                                <p class="text-3xl font-bold text-gray-900 mt-2">{{ stats.totalUsers?.toLocaleString() || 0 }}</p>
                                <p class="text-sm text-green-600 mt-2">+{{ stats.newUsersToday || 0 }} today</p>
                            </div>
                            <div class="text-blue-500">
                                <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                                </svg>
                            </div>
                        </div>
                    </div>

                    <!-- Total Revenue -->
                    <div class="bg-white rounded-lg shadow p-6 border-l-4 border-green-500">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm font-medium text-gray-600">Total Revenue</p>
                                <p class="text-3xl font-bold text-gray-900 mt-2">৳{{ (stats.totalRevenue || 0).toLocaleString() }}</p>
                                <p class="text-sm text-green-600 mt-2">+৳{{ (stats.revenueToday || 0).toLocaleString() }} today</p>
                            </div>
                            <div class="text-green-500">
                                <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                        </div>
                    </div>

                    <!-- Active Agencies -->
                    <div class="bg-white rounded-lg shadow p-6 border-l-4 border-purple-500">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm font-medium text-gray-600">Active Agencies</p>
                                <p class="text-3xl font-bold text-gray-900 mt-2">{{ stats.activeAgencies || 0 }}</p>
                                <p class="text-sm text-blue-600 mt-2">{{ stats.pendingVerification || 0 }} pending</p>
                            </div>
                            <div class="text-purple-500">
                                <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                </svg>
                            </div>
                        </div>
                    </div>

                    <!-- Pending Transactions -->
                    <div class="bg-white rounded-lg shadow p-6 border-l-4 border-orange-500">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm font-medium text-gray-600">Pending Transactions</p>
                                <p class="text-3xl font-bold text-gray-900 mt-2">{{ stats.pendingTransactions || 0 }}</p>
                                <p class="text-sm text-orange-600 mt-2">৳{{ (stats.pendingAmount || 0).toLocaleString() }}</p>
                            </div>
                            <div class="text-orange-500">
                                <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                                </svg>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Charts Row -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
                    <!-- User Growth Chart -->
                    <div class="bg-white rounded-lg shadow p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">User Growth (Last 7 Days)</h3>
                        <div class="h-64">
                            <canvas ref="userGrowthChart"></canvas>
                        </div>
                    </div>

                    <!-- Revenue Chart -->
                    <div class="bg-white rounded-lg shadow p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Revenue (Last 7 Days)</h3>
                        <div class="h-64">
                            <canvas ref="revenueChart"></canvas>
                        </div>
                    </div>
                </div>

                <!-- Recent Activity -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <!-- Recent Users -->
                    <div class="bg-white rounded-lg shadow">
                        <div class="px-6 py-4 border-b border-gray-200">
                            <h3 class="text-lg font-semibold text-gray-900">Recent Users</h3>
                        </div>
                        <div class="divide-y divide-gray-200">
                            <div v-for="user in recentUsers" :key="user.id" class="px-6 py-4 hover:bg-gray-50">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center space-x-3">
                                        <div class="flex-shrink-0">
                                            <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center">
                                                <span class="text-blue-600 font-semibold">{{ user.name?.charAt(0) || '?' }}</span>
                                            </div>
                                        </div>
                                        <div>
                                            <p class="text-sm font-medium text-gray-900">{{ user.name }}</p>
                                            <p class="text-xs text-gray-500">{{ user.email }}</p>
                                        </div>
                                    </div>
                                    <div class="text-right">
                                        <p class="text-xs text-gray-500">{{ formatDate(user.created_at) }}</p>
                                        <span :class="getRoleBadgeClass(user.role)" class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium">
                                            {{ user.role?.name || 'User' }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                            <div v-if="!recentUsers.length" class="px-6 py-12 text-center text-gray-500">
                                No recent users
                            </div>
                        </div>
                    </div>

                    <!-- Recent Transactions -->
                    <div class="bg-white rounded-lg shadow">
                        <div class="px-6 py-4 border-b border-gray-200">
                            <h3 class="text-lg font-semibold text-gray-900">Recent Transactions</h3>
                        </div>
                        <div class="divide-y divide-gray-200">
                            <div v-for="transaction in recentTransactions" :key="transaction.id" class="px-6 py-4 hover:bg-gray-50">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <p class="text-sm font-medium text-gray-900">{{ transaction.description }}</p>
                                        <p class="text-xs text-gray-500">{{ transaction.user?.name || 'Unknown' }}</p>
                                    </div>
                                    <div class="text-right">
                                        <p :class="transaction.transaction_type === 'credit' ? 'text-green-600' : 'text-red-600'" class="text-sm font-semibold">
                                            {{ transaction.transaction_type === 'credit' ? '+' : '-' }}৳{{ transaction.amount?.toLocaleString() || 0 }}
                                        </p>
                                        <p class="text-xs text-gray-500">{{ formatDate(transaction.created_at) }}</p>
                                    </div>
                                </div>
                            </div>
                            <div v-if="!recentTransactions.length" class="px-6 py-12 text-center text-gray-500">
                                No recent transactions
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { Head } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'
import Chart from 'chart.js/auto'

const props = defineProps({
    stats: Object,
    recentUsers: Array,
    recentTransactions: Array,
    userGrowthData: Object,
    revenueData: Object,
})

const userGrowthChart = ref(null)
const revenueChart = ref(null)

onMounted(() => {
    if (userGrowthChart.value && props.userGrowthData) {
        new Chart(userGrowthChart.value, {
            type: 'line',
            data: {
                labels: props.userGrowthData.labels || [],
                datasets: [{
                    label: 'New Users',
                    data: props.userGrowthData.data || [],
                    borderColor: 'rgb(59, 130, 246)',
                    backgroundColor: 'rgba(59, 130, 246, 0.1)',
                    tension: 0.4,
                    fill: true,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            precision: 0
                        }
                    }
                }
            }
        })
    }

    if (revenueChart.value && props.revenueData) {
        new Chart(revenueChart.value, {
            type: 'bar',
            data: {
                labels: props.revenueData.labels || [],
                datasets: [{
                    label: 'Revenue (৳)',
                    data: props.revenueData.data || [],
                    backgroundColor: 'rgba(34, 197, 94, 0.8)',
                    borderColor: 'rgb(34, 197, 94)',
                    borderWidth: 1,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return '৳' + value.toLocaleString()
                            }
                        }
                    }
                }
            }
        })
    }
})

const formatDate = (date) => {
    if (!date) return 'N/A'
    const d = new Date(date)
    const now = new Date()
    const diffMs = now - d
    const diffMins = Math.floor(diffMs / 60000)
    
    if (diffMins < 1) return 'Just now'
    if (diffMins < 60) return `${diffMins}m ago`
    
    const diffHours = Math.floor(diffMins / 60)
    if (diffHours < 24) return `${diffHours}h ago`
    
    const diffDays = Math.floor(diffHours / 24)
    if (diffDays < 7) return `${diffDays}d ago`
    
    return d.toLocaleDateString()
}

const getRoleBadgeClass = (role) => {
    const roleSlug = role?.slug || 'user'
    const classes = {
        admin: 'bg-red-100 text-red-800',
        agency: 'bg-purple-100 text-purple-800',
        consultant: 'bg-blue-100 text-blue-800',
        user: 'bg-gray-100 text-gray-800',
    }
    return classes[roleSlug] || classes.user
}
</script>
