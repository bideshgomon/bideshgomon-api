<script setup>
import { ref } from 'vue'
import { Head, Link, router } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'
import { 
    ArrowLeftIcon, 
    CheckCircleIcon, 
    XCircleIcon,
    ClockIcon,
    UserIcon,
    EnvelopeIcon,
    PhoneIcon,
    DocumentTextIcon,
    CurrencyDollarIcon
} from '@heroicons/vue/24/outline'

const props = defineProps({
    application: Object,
    quotes: Array
})

const isUpdating = ref(false)

const updateStatus = (status) => {
    if (confirm(`Are you sure you want to change status to "${status}"?`)) {
        isUpdating.value = true
        router.put(route('service-applications.update-status', props.application.id), {
            status: status
        }, {
            onFinish: () => isUpdating.value = false
        })
    }
}

const getStatusColor = (status) => {
    const colors = {
        pending: 'yellow',
        quoted: 'blue',
        in_progress: 'purple',
        completed: 'green',
        cancelled: 'red'
    }
    return colors[status] || 'gray'
}

const getStatusIcon = (status) => {
    switch(status) {
        case 'completed': return CheckCircleIcon
        case 'cancelled': return XCircleIcon
        default: return ClockIcon
    }
}
</script>

<template>
    <Head :title="`Application #${application.id}`" />

    <AdminLayout>
        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <!-- Header -->
                <div class="mb-6 flex items-center justify-between">
                    <div class="flex items-center gap-4">
                        <Link :href="route('service-applications.index')" class="text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white">
                            <ArrowLeftIcon class="w-6 h-6" />
                        </Link>
                        <div>
                            <h1 class="text-3xl font-bold text-gray-900 dark:text-white">
                                Application #{{ application.id }}
                            </h1>
                            <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                                {{ application.service_module?.name }}
                            </p>
                        </div>
                    </div>
                    <div :class="[
                        'px-4 py-2 rounded-full text-sm font-semibold',
                        getStatusColor(application.status) === 'yellow' ? 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200' :
                        getStatusColor(application.status) === 'blue' ? 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200' :
                        getStatusColor(application.status) === 'purple' ? 'bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-200' :
                        getStatusColor(application.status) === 'green' ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200' :
                        'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200'
                    ]">
                        {{ application.status.replace('_', ' ').toUpperCase() }}
                    </div>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    <!-- Main Content -->
                    <div class="lg:col-span-2 space-y-6">
                        <!-- User Information -->
                        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                            <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                                <h2 class="text-xl font-semibold text-gray-900 dark:text-white flex items-center gap-2">
                                    <UserIcon class="w-5 h-5" />
                                    User Information
                                </h2>
                            </div>
                            <div class="p-6 space-y-4">
                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <p class="text-sm text-gray-600 dark:text-gray-400">Name</p>
                                        <p class="font-semibold text-gray-900 dark:text-white">{{ application.user?.name }}</p>
                                    </div>
                                    <div>
                                        <p class="text-sm text-gray-600 dark:text-gray-400">Email</p>
                                        <p class="font-semibold text-gray-900 dark:text-white flex items-center gap-2">
                                            <EnvelopeIcon class="w-4 h-4" />
                                            {{ application.user?.email }}
                                        </p>
                                    </div>
                                    <div v-if="application.user?.phone">
                                        <p class="text-sm text-gray-600 dark:text-gray-400">Phone</p>
                                        <p class="font-semibold text-gray-900 dark:text-white flex items-center gap-2">
                                            <PhoneIcon class="w-4 h-4" />
                                            {{ application.user.phone }}
                                        </p>
                                    </div>
                                    <div>
                                        <p class="text-sm text-gray-600 dark:text-gray-400">Submitted</p>
                                        <p class="font-semibold text-gray-900 dark:text-white">
                                            {{ new Date(application.created_at).toLocaleDateString() }}
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Application Data -->
                        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                            <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                                <h2 class="text-xl font-semibold text-gray-900 dark:text-white flex items-center gap-2">
                                    <DocumentTextIcon class="w-5 h-5" />
                                    Application Details
                                </h2>
                            </div>
                            <div class="p-6">
                                <div v-if="application.application_data" class="space-y-3">
                                    <div v-for="(value, key) in application.application_data" :key="key" class="flex justify-between py-2 border-b border-gray-100 dark:border-gray-700">
                                        <span class="text-gray-600 dark:text-gray-400 capitalize">
                                            {{ typeof key === 'string' ? key.replace(/_/g, ' ') : key }}
                                        </span>
                                        <span class="font-semibold text-gray-900 dark:text-white">
                                            {{ typeof value === 'object' ? JSON.stringify(value) : value }}
                                        </span>
                                    </div>
                                </div>
                                <p v-else class="text-gray-500 dark:text-gray-400 italic">No additional data provided</p>
                            </div>
                        </div>

                        <!-- Quotes -->
                        <div v-if="quotes && quotes.length > 0" class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                            <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                                <h2 class="text-xl font-semibold text-gray-900 dark:text-white flex items-center gap-2">
                                    <CurrencyDollarIcon class="w-5 h-5" />
                                    Quotes ({{ quotes.length }})
                                </h2>
                            </div>
                            <div class="p-6">
                                <div class="space-y-4">
                                    <div v-for="quote in quotes" :key="quote.id" class="border border-gray-200 dark:border-gray-700 rounded-lg p-4">
                                        <div class="flex justify-between items-start mb-3">
                                            <div>
                                                <p class="font-semibold text-gray-900 dark:text-white">{{ quote.agency?.name || 'Agency' }}</p>
                                                <p class="text-sm text-gray-600 dark:text-gray-400">{{ quote.agency?.email }}</p>
                                            </div>
                                            <span :class="[
                                                'px-3 py-1 rounded-full text-xs font-semibold',
                                                quote.status === 'accepted' ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200' :
                                                quote.status === 'rejected' ? 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200' :
                                                'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200'
                                            ]">
                                                {{ quote.status }}
                                            </span>
                                        </div>
                                        <div class="grid grid-cols-2 gap-4 text-sm">
                                            <div>
                                                <p class="text-gray-600 dark:text-gray-400">Quoted Amount</p>
                                                <p class="font-bold text-lg text-gray-900 dark:text-white">${{ parseFloat(quote.quoted_price || 0).toFixed(2) }}</p>
                                            </div>
                                            <div>
                                                <p class="text-gray-600 dark:text-gray-400">Processing Time</p>
                                                <p class="font-semibold text-gray-900 dark:text-white">{{ quote.processing_time_days || 'N/A' }} days</p>
                                            </div>
                                        </div>
                                        <div v-if="quote.notes" class="mt-3 pt-3 border-t border-gray-200 dark:border-gray-700">
                                            <p class="text-sm text-gray-600 dark:text-gray-400">{{ quote.notes }}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Sidebar -->
                    <div class="space-y-6">
                        <!-- Actions -->
                        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                            <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                                <h2 class="text-xl font-semibold text-gray-900 dark:text-white">Actions</h2>
                            </div>
                            <div class="p-6 space-y-3">
                                <button
                                    v-if="application.status === 'pending'"
                                    @click="updateStatus('in_progress')"
                                    :disabled="isUpdating"
                                    class="w-full px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 disabled:opacity-50 transition"
                                >
                                    Mark In Progress
                                </button>
                                <button
                                    v-if="application.status === 'in_progress'"
                                    @click="updateStatus('completed')"
                                    :disabled="isUpdating"
                                    class="w-full px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 disabled:opacity-50 transition"
                                >
                                    Mark Completed
                                </button>
                                <button
                                    v-if="application.status !== 'cancelled'"
                                    @click="updateStatus('cancelled')"
                                    :disabled="isUpdating"
                                    class="w-full px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 disabled:opacity-50 transition"
                                >
                                    Cancel Application
                                </button>
                            </div>
                        </div>

                        <!-- Timeline -->
                        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                            <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                                <h2 class="text-xl font-semibold text-gray-900 dark:text-white">Timeline</h2>
                            </div>
                            <div class="p-6">
                                <div class="space-y-4">
                                    <div class="flex gap-3">
                                        <div class="flex-shrink-0">
                                            <div class="w-8 h-8 rounded-full bg-green-100 dark:bg-green-900 flex items-center justify-center">
                                                <CheckCircleIcon class="w-5 h-5 text-green-600 dark:text-green-400" />
                                            </div>
                                        </div>
                                        <div>
                                            <p class="font-semibold text-gray-900 dark:text-white">Application Created</p>
                                            <p class="text-sm text-gray-600 dark:text-gray-400">
                                                {{ new Date(application.created_at).toLocaleString() }}
                                            </p>
                                        </div>
                                    </div>
                                    
                                    <div v-if="application.quoted_at" class="flex gap-3">
                                        <div class="flex-shrink-0">
                                            <div class="w-8 h-8 rounded-full bg-blue-100 dark:bg-blue-900 flex items-center justify-center">
                                                <CurrencyDollarIcon class="w-5 h-5 text-blue-600 dark:text-blue-400" />
                                            </div>
                                        </div>
                                        <div>
                                            <p class="font-semibold text-gray-900 dark:text-white">Quote Received</p>
                                            <p class="text-sm text-gray-600 dark:text-gray-400">
                                                {{ new Date(application.quoted_at).toLocaleString() }}
                                            </p>
                                        </div>
                                    </div>

                                    <div v-if="application.accepted_at" class="flex gap-3">
                                        <div class="flex-shrink-0">
                                            <div class="w-8 h-8 rounded-full bg-purple-100 dark:bg-purple-900 flex items-center justify-center">
                                                <CheckCircleIcon class="w-5 h-5 text-purple-600 dark:text-purple-400" />
                                            </div>
                                        </div>
                                        <div>
                                            <p class="font-semibold text-gray-900 dark:text-white">Quote Accepted</p>
                                            <p class="text-sm text-gray-600 dark:text-gray-400">
                                                {{ new Date(application.accepted_at).toLocaleString() }}
                                            </p>
                                        </div>
                                    </div>

                                    <div v-if="application.completed_at" class="flex gap-3">
                                        <div class="flex-shrink-0">
                                            <div class="w-8 h-8 rounded-full bg-green-100 dark:bg-green-900 flex items-center justify-center">
                                                <CheckCircleIcon class="w-5 h-5 text-green-600 dark:text-green-400" />
                                            </div>
                                        </div>
                                        <div>
                                            <p class="font-semibold text-gray-900 dark:text-white">Completed</p>
                                            <p class="text-sm text-gray-600 dark:text-gray-400">
                                                {{ new Date(application.completed_at).toLocaleString() }}
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>
