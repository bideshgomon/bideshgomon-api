<template>
    <AuthenticatedLayout>
        <Head title="Notification Preferences" />

        <div class="py-6">
            <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
                <!-- Header -->
                <div class="mb-8">
                    <div class="flex items-center gap-3 mb-2">
                        <div class="p-2 bg-teal-100 rounded-lg">
                            <BellIcon class="w-8 h-8 text-teal-600" />
                        </div>
                        <div>
                            <h1 class="text-3xl font-bold text-gray-900">Notification Preferences</h1>
                            <p class="mt-1 text-sm text-gray-600">Manage how you receive notifications from the platform</p>
                        </div>
                    </div>
                </div>

                <!-- Preferences Form -->
                <form @submit.prevent="updatePreferences" class="bg-white border-2 border-gray-200 rounded-xl">
                    <div class="px-6 py-5 border-b-2 border-gray-200">
                        <div class="flex items-center gap-3">
                            <div class="p-2 bg-teal-100 rounded-lg">
                                <ComputerDesktopIcon class="h-5 w-5 text-teal-600" />
                            </div>
                            <div>
                                <h2 class="text-lg font-semibold text-gray-900">Communication Channels</h2>
                                <p class="mt-1 text-sm text-gray-600">Choose how you want to receive each type of notification</p>
                            </div>
                        </div>
                    </div>

                    <div class="divide-y divide-gray-200">
                        <div v-for="(pref, index) in form.preferences" :key="pref.type" class="px-6 py-5 hover:bg-gray-50 transition-colors">
                            <div class="flex items-start justify-between">
                                <div class="flex-1">
                                    <h3 class="text-base font-medium text-gray-900">{{ pref.label }}</h3>
                                    <p class="mt-1 text-sm text-gray-500">{{ getDescription(pref.type) }}</p>
                                </div>

                                <div class="ml-6 flex items-center gap-6">
                                    <!-- Email Toggle -->
                                    <div class="flex items-center gap-2">
                                        <button
                                            type="button"
                                            @click="togglePreference(index, 'email_enabled')"
                                            :class="[
                                                pref.email_enabled ? 'bg-teal-600' : 'bg-gray-200',
                                                'relative inline-flex h-6 w-11 flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none focus:ring-2 focus:ring-teal-500 focus:ring-offset-2'
                                            ]"
                                        >
                                            <span
                                                :class="[
                                                    pref.email_enabled ? 'translate-x-5' : 'translate-x-0',
                                                    'pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out'
                                                ]"
                                            />
                                        </button>
                                        <EnvelopeIcon class="h-4 w-4 text-gray-600" />
                                        <span class="text-sm text-gray-700 font-medium">Email</span>
                                    </div>

                                    <!-- In-App Toggle -->
                                    <div class="flex items-center gap-2">
                                        <button
                                            type="button"
                                            @click="togglePreference(index, 'in_app_enabled')"
                                            :class="[
                                                pref.in_app_enabled ? 'bg-teal-600' : 'bg-gray-200',
                                                'relative inline-flex h-6 w-11 flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none focus:ring-2 focus:ring-teal-500 focus:ring-offset-2'
                                            ]"
                                        >
                                            <span
                                                :class="[
                                                    pref.in_app_enabled ? 'translate-x-5' : 'translate-x-0',
                                                    'pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out'
                                                ]"
                                            />
                                        </button>
                                        <BellIcon class="h-4 w-4 text-gray-600" />
                                        <span class="text-sm text-gray-700 font-medium">In-App</span>
                                    </div>

                                    <!-- Push Toggle (Coming Soon) -->
                                    <div class="relative flex items-center gap-2 opacity-60" title="Push notifications require additional browser permissions. Coming soon!">
                                        <button
                                            type="button"
                                            disabled
                                            class="relative inline-flex h-6 w-11 flex-shrink-0 cursor-not-allowed rounded-full border-2 border-gray-300 transition-colors duration-200 ease-in-out bg-gray-100"
                                        >
                                            <span class="pointer-events-none inline-block h-5 w-5 transform rounded-full bg-gray-300 shadow-sm ring-0 transition duration-200 ease-in-out translate-x-0" />
                                        </button>
                                        <ComputerDesktopIcon class="h-4 w-4 text-gray-400" />
                                        <span class="text-sm text-gray-500 font-medium">Push</span>
                                        <span class="absolute -top-1 -right-1 flex h-3 w-3">
                                            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-gray-400 opacity-75"></span>
                                            <span class="relative inline-flex rounded-full h-3 w-3 bg-gray-400"></span>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="px-6 py-4 bg-gray-50 border-t-2 border-gray-200 flex items-center justify-between rounded-b-xl">
                        <div class="flex items-center gap-2 text-sm text-gray-600">
                            <InformationCircleIcon class="h-4 w-4 text-gray-500" />
                            <p>
                                <span class="font-medium">Note:</span> Push notifications require browser permissions and are being implemented.
                            </p>
                        </div>
                        <button
                            type="submit"
                            :disabled="form.processing"
                            class="inline-flex items-center gap-2 px-6 py-2.5 bg-teal-600 text-white rounded-lg font-medium hover:bg-teal-700 focus:outline-none focus:ring-2 focus:ring-teal-500 focus:ring-offset-2 transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
                        >
                            <span v-if="form.processing">Saving...</span>
                            <span v-else>Save Preferences</span>
                        </button>
                    </div>
                </form>

                <!-- Info Box -->
                <div class="mt-6 bg-teal-50 border-2 border-teal-200 rounded-xl p-6">
                    <div class="flex gap-4">
                        <div class="flex-shrink-0">
                            <div class="p-2 bg-teal-100 rounded-lg">
                                <InformationCircleIcon class="h-5 w-5 text-teal-600" />
                            </div>
                        </div>
                        <div class="flex-1">
                            <h3 class="text-sm font-medium text-teal-900 mb-2">About Notification Preferences</h3>
                            <div class="text-sm text-teal-800 space-y-2">
                                <div class="flex items-start gap-2">
                                    <EnvelopeIcon class="h-4 w-4 mt-0.5 flex-shrink-0" />
                                    <p><strong>Email:</strong> Receive notifications via email to {{ $page.props.auth.user.email }}</p>
                                </div>
                                <div class="flex items-start gap-2">
                                    <BellIcon class="h-4 w-4 mt-0.5 flex-shrink-0" />
                                    <p><strong>In-App:</strong> See notifications in the notification bell icon</p>
                                </div>
                                <div class="flex items-start gap-2">
                                    <ComputerDesktopIcon class="h-4 w-4 mt-0.5 flex-shrink-0 text-gray-400" />
                                    <div>
                                        <p><strong>Push:</strong> Browser push notifications (Coming Soon)</p>
                                        <p class="text-xs text-teal-700 mt-1 italic">Requires browser permission to send notifications even when the tab is closed. We're implementing this feature with proper security and privacy controls.</p>
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
import { ref } from 'vue'
import { Head, useForm } from '@inertiajs/vue3'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { BellIcon, EnvelopeIcon, ComputerDesktopIcon, InformationCircleIcon } from '@heroicons/vue/24/outline'

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
