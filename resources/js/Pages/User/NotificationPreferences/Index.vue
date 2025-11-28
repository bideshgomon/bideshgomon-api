<template>
    <AuthenticatedLayout>
        <Head title="Notification Preferences" />

        <div class="py-6">
            <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
                <!-- Header -->
                <div class="mb-6">
                    <h1 class="text-2xl font-bold text-gray-900">Notification Preferences</h1>
                    <p class="mt-1 text-sm text-gray-600">Manage how you receive notifications from the platform</p>
                </div>

                <!-- Preferences Form -->
                <form @submit.prevent="updatePreferences" class="bg-white rounded-lg shadow">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h2 class="text-lg font-semibold text-gray-900">Communication Channels</h2>
                        <p class="mt-1 text-sm text-gray-600">Choose how you want to receive each type of notification</p>
                    </div>

                    <div class="divide-y divide-gray-200">
                        <div v-for="(pref, index) in form.preferences" :key="pref.type" class="px-6 py-5">
                            <div class="flex items-start justify-between">
                                <div class="flex-1">
                                    <h3 class="text-base font-medium text-gray-900">{{ pref.label }}</h3>
                                    <p class="mt-1 text-sm text-gray-500">{{ getDescription(pref.type) }}</p>
                                </div>

                                <div class="ml-6 flex items-center space-x-6">
                                    <!-- Email Toggle -->
                                    <div class="flex items-center">
                                        <button
                                            type="button"
                                            @click="togglePreference(index, 'email_enabled')"
                                            :class="[
                                                pref.email_enabled ? 'bg-blue-600' : 'bg-gray-200',
                                                'relative inline-flex h-6 w-11 flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2'
                                            ]"
                                        >
                                            <span
                                                :class="[
                                                    pref.email_enabled ? 'translate-x-5' : 'translate-x-0',
                                                    'pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out'
                                                ]"
                                            />
                                        </button>
                                        <span class="ml-2 text-sm text-gray-700">Email</span>
                                    </div>

                                    <!-- In-App Toggle -->
                                    <div class="flex items-center">
                                        <button
                                            type="button"
                                            @click="togglePreference(index, 'in_app_enabled')"
                                            :class="[
                                                pref.in_app_enabled ? 'bg-blue-600' : 'bg-gray-200',
                                                'relative inline-flex h-6 w-11 flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2'
                                            ]"
                                        >
                                            <span
                                                :class="[
                                                    pref.in_app_enabled ? 'translate-x-5' : 'translate-x-0',
                                                    'pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out'
                                                ]"
                                            />
                                        </button>
                                        <span class="ml-2 text-sm text-gray-700">In-App</span>
                                    </div>

                                    <!-- Push Toggle (Future Feature) -->
                                    <div class="flex items-center opacity-50" title="Coming soon">
                                        <button
                                            type="button"
                                            disabled
                                            :class="[
                                                pref.push_enabled ? 'bg-blue-600' : 'bg-gray-200',
                                                'relative inline-flex h-6 w-11 flex-shrink-0 cursor-not-allowed rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out'
                                            ]"
                                        >
                                            <span
                                                :class="[
                                                    pref.push_enabled ? 'translate-x-5' : 'translate-x-0',
                                                    'pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out'
                                                ]"
                                            />
                                        </button>
                                        <span class="ml-2 text-sm text-gray-500">Push</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="px-6 py-4 bg-gray-50 border-t border-gray-200 flex items-center justify-between rounded-b-lg">
                        <p class="text-sm text-gray-600">
                            <span class="font-medium">Note:</span> Push notifications are coming soon
                        </p>
                        <button
                            type="submit"
                            :disabled="form.processing"
                            class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition ease-in-out duration-150 disabled:opacity-50"
                        >
                            <span v-if="form.processing">Saving...</span>
                            <span v-else>Save Preferences</span>
                        </button>
                    </div>
                </form>

                <!-- Info Box -->
                <div class="mt-6 bg-blue-50 border border-blue-200 rounded-lg p-4">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-blue-400" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <h3 class="text-sm font-medium text-blue-800">About Notification Preferences</h3>
                            <div class="mt-2 text-sm text-blue-700">
                                <ul class="list-disc pl-5 space-y-1">
                                    <li><strong>Email:</strong> Receive notifications via email to {{ $page.props.auth.user.email }}</li>
                                    <li><strong>In-App:</strong> See notifications in the notification bell icon</li>
                                    <li><strong>Push:</strong> Browser push notifications (coming soon)</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import { ref } from 'vue'
import { Head, useForm } from '@inertiajs/vue3'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'

const props = defineProps({
    preferences: Array,
})

const form = useForm({
    preferences: props.preferences.map(pref => ({
        type: pref.type,
        label: pref.label,
        email_enabled: pref.email_enabled,
        in_app_enabled: pref.in_app_enabled,
        push_enabled: pref.push_enabled,
    })),
})

const togglePreference = (index, field) => {
    form.preferences[index][field] = !form.preferences[index][field]
}

const updatePreferences = () => {
    form.post(route('notification-preferences.update'), {
        preserveScroll: true,
        onSuccess: () => {
            // Success message handled by backend
        },
    })
}

const getDescription = (type) => {
    const descriptions = {
        verification_approved: 'When your agency verification is approved',
        verification_rejected: 'When your agency verification is rejected',
        wallet_credited: 'When funds are added to your wallet',
        withdrawal_completed: 'When your withdrawal request is processed',
        job_application: 'Updates about job applications',
        visa_application: 'Updates about visa applications',
        booking_confirmed: 'When bookings are confirmed',
        general: 'General platform announcements and updates',
    }
    return descriptions[type] || 'Notification updates'
}
</script>
