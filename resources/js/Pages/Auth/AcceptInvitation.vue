<template>
    <Head title="Accept Invitation" />

    <GuestLayout>
        <div class="min-h-screen flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
            <div class="max-w-md w-full space-y-8">
                <!-- Agency Info -->
                <div class="text-center">
                    <div v-if="agency.logo_path" class="mx-auto h-16 w-16 flex items-center justify-center">
                        <img :src="`/storage/${agency.logo_path}`" :alt="agency.name" class="h-16 w-16 object-contain" />
                    </div>
                    <div v-else class="mx-auto h-16 w-16 flex items-center justify-center bg-indigo-100 rounded-full">
                        <BuildingOfficeIcon class="h-8 w-8 text-indigo-600" />
                    </div>
                    <h2 class="mt-4 text-2xl sm:text-3xl font-bold text-gray-900">Join {{ agency.name }}</h2>
                    <p class="mt-2 text-sm text-gray-600">{{ agency.company_name }}</p>
                </div>

                <!-- Invitation Details -->
                <div class="bg-white shadow-sm rounded-lg p-6 border border-gray-200">
                    <div class="space-y-4">
                        <div class="flex items-start">
                            <UserIcon class="h-5 w-5 text-gray-400 mt-0.5 mr-3 flex-shrink-0" />
                            <div>
                                <p class="text-sm font-medium text-gray-700">Your Name</p>
                                <p class="text-base text-gray-900">{{ teamMember.name }}</p>
                            </div>
                        </div>
                        <div class="flex items-start">
                            <EnvelopeIcon class="h-5 w-5 text-gray-400 mt-0.5 mr-3 flex-shrink-0" />
                            <div>
                                <p class="text-sm font-medium text-gray-700">Email Address</p>
                                <p class="text-base text-gray-900">{{ teamMember.email }}</p>
                            </div>
                        </div>
                        <div class="flex items-start">
                            <BriefcaseIcon class="h-5 w-5 text-gray-400 mt-0.5 mr-3 flex-shrink-0" />
                            <div>
                                <p class="text-sm font-medium text-gray-700">Position</p>
                                <p class="text-base text-gray-900">{{ teamMember.position }}</p>
                            </div>
                        </div>
                        <div class="flex items-start">
                            <ShieldCheckIcon class="h-5 w-5 text-gray-400 mt-0.5 mr-3 flex-shrink-0" />
                            <div>
                                <p class="text-sm font-medium text-gray-700">Role</p>
                                <p class="text-base text-gray-900 capitalize">{{ teamMember.role }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Registration Form -->
                <div class="bg-white shadow-sm rounded-lg p-6 border border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Create Your Account</h3>
                    
                    <form @submit.prevent="submit" class="space-y-4">
                        <!-- Password -->
                        <div>
                            <label for="password" class="block text-sm font-medium text-gray-700">
                                Password <span class="text-red-500">*</span>
                            </label>
                            <input
                                id="password"
                                v-model="form.password"
                                type="password"
                                required
                                autocomplete="new-password"
                                class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
                                :class="{ 'border-red-500': form.errors.password }"
                                placeholder="Minimum 8 characters"
                            />
                            <p v-if="form.errors.password" class="mt-1 text-sm text-red-600">{{ form.errors.password }}</p>
                        </div>

                        <!-- Password Confirmation -->
                        <div>
                            <label for="password_confirmation" class="block text-sm font-medium text-gray-700">
                                Confirm Password <span class="text-red-500">*</span>
                            </label>
                            <input
                                id="password_confirmation"
                                v-model="form.password_confirmation"
                                type="password"
                                required
                                autocomplete="new-password"
                                class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
                                :class="{ 'border-red-500': form.errors.password_confirmation }"
                                placeholder="Re-enter your password"
                            />
                            <p v-if="form.errors.password_confirmation" class="mt-1 text-sm text-red-600">{{ form.errors.password_confirmation }}</p>
                        </div>

                        <!-- Terms -->
                        <div class="flex items-start">
                            <input
                                id="terms"
                                v-model="form.terms"
                                type="checkbox"
                                required
                                class="mt-1 h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded"
                            />
                            <label for="terms" class="ml-2 block text-sm text-gray-700">
                                I agree to the <a href="#" class="text-indigo-600 hover:text-indigo-500">Terms of Service</a> and <a href="#" class="text-indigo-600 hover:text-indigo-500">Privacy Policy</a>
                            </label>
                        </div>

                        <!-- Submit Button -->
                        <button
                            type="submit"
                            :disabled="form.processing"
                            class="w-full flex justify-center py-2.5 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 disabled:opacity-50 disabled:cursor-not-allowed"
                        >
                            <span v-if="form.processing">Creating Account...</span>
                            <span v-else>Create Account & Join Team</span>
                        </button>
                    </form>
                </div>

                <!-- Security Note -->
                <div class="text-center text-xs text-gray-500">
                    <p>🔒 Your password is encrypted and secure.</p>
                    <p class="mt-1">You'll be able to log in immediately after account creation.</p>
                </div>
            </div>
        </div>
    </GuestLayout>
</template>

<script setup>
import { Head, useForm } from '@inertiajs/vue3'
import GuestLayout from '@/Layouts/GuestLayout.vue'
import { BuildingOfficeIcon, UserIcon, EnvelopeIcon, BriefcaseIcon, ShieldCheckIcon } from '@heroicons/vue/24/outline'

const props = defineProps({
    teamMember: Object,
    agency: Object,
    token: String,
})

const form = useForm({
    password: '',
    password_confirmation: '',
    terms: false,
})

function submit() {
    form.post(route('consultant.complete-invitation', props.token))
}
</script>
