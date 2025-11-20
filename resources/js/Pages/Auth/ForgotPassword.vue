<script setup>
import { Head, useForm, Link } from '@inertiajs/vue3';
import { EnvelopeIcon, ArrowLeftIcon, CheckCircleIcon, ExclamationCircleIcon } from '@heroicons/vue/24/outline';

defineProps({
    status: {
        type: String,
    },
});

const form = useForm({
    email: '',
});

const submit = () => {
    form.post(route('password.email'));
};
</script>

<template>
    <Head title="Forgot Password" />

    <div class="min-h-screen bg-gradient-to-br from-emerald-50 via-white to-green-50 flex flex-col justify-center py-12 sm:px-6 lg:px-8">
        <div class="sm:mx-auto sm:w-full sm:max-w-md">
            <!-- Logo -->
            <Link href="/" class="flex justify-center">
                <div class="flex items-center space-x-2">
                    <div class="w-12 h-12 bg-gradient-to-br from-emerald-600 to-green-600 rounded-xl flex items-center justify-center shadow-lg">
                        <span class="text-white font-bold text-xl">BG</span>
                    </div>
                    <span class="text-2xl font-bold text-gray-900">Bidesh Gomon</span>
                </div>
            </Link>

            <!-- Header -->
            <div class="mt-8 text-center">
                <div class="inline-flex items-center justify-center w-16 h-16 bg-emerald-100 rounded-full mb-4">
                    <EnvelopeIcon class="w-8 h-8 text-emerald-600" />
                </div>
                <h2 class="text-3xl font-bold text-gray-900">Forgot Password?</h2>
                <p class="mt-2 text-sm text-gray-600 max-w-sm mx-auto">
                    No worries! Enter your email address and we'll send you instructions to reset your password.
                </p>
            </div>
        </div>

        <div class="mt-8 sm:mx-auto sm:w-full sm:max-w-md">
            <div class="bg-white py-8 px-4 shadow-xl shadow-emerald-100/50 sm:rounded-2xl sm:px-10 border border-emerald-100">
                <!-- Success Message -->
                <div v-if="status" class="mb-6 rounded-xl bg-green-50 border border-green-200 p-4">
                    <div class="flex">
                        <CheckCircleIcon class="h-5 w-5 text-green-600 mr-3 flex-shrink-0" />
                        <div>
                            <h3 class="text-sm font-medium text-green-800">Email Sent!</h3>
                            <p class="mt-1 text-sm text-green-700">{{ status }}</p>
                        </div>
                    </div>
                </div>

                <!-- Error Message -->
                <div v-if="form.errors.email" class="mb-6 rounded-xl bg-red-50 border border-red-200 p-4">
                    <div class="flex">
                        <ExclamationCircleIcon class="h-5 w-5 text-red-600 mr-3 flex-shrink-0" />
                        <div>
                            <h3 class="text-sm font-medium text-red-800">Error</h3>
                            <p class="mt-1 text-sm text-red-700">{{ form.errors.email }}</p>
                        </div>
                    </div>
                </div>

                <form @submit.prevent="submit" class="space-y-6">
                    <div>
                        <label for="email" class="block text-sm font-semibold text-gray-900 mb-2">
                            Email Address
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <EnvelopeIcon class="h-5 w-5 text-gray-400" />
                            </div>
                            <input
                                id="email"
                                v-model="form.email"
                                type="email"
                                required
                                autofocus
                                autocomplete="email"
                                placeholder="your@email.com"
                                class="block w-full pl-10 pr-3 py-3 border border-gray-300 rounded-xl shadow-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent transition-all duration-200"
                                :class="{ 'border-red-300 focus:ring-red-500': form.errors.email }"
                            />
                        </div>
                    </div>

                    <div>
                        <button
                            type="submit"
                            :disabled="form.processing"
                            class="w-full flex justify-center items-center py-3 px-4 border border-transparent rounded-xl shadow-lg text-sm font-semibold text-white bg-gradient-to-r from-emerald-600 to-green-600 hover:from-emerald-700 hover:to-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500 transition-all duration-200 disabled:opacity-50 disabled:cursor-not-allowed"
                        >
                            <span v-if="form.processing">
                                <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                Sending...
                            </span>
                            <span v-else>
                                <EnvelopeIcon class="w-5 h-5 mr-2 inline" />
                                Send Reset Link
                            </span>
                        </button>
                    </div>
                </form>

                <!-- Back to Login -->
                <div class="mt-6">
                    <Link
                        :href="route('login')"
                        class="flex items-center justify-center text-sm font-medium text-emerald-600 hover:text-emerald-700 transition-colors duration-200"
                    >
                        <ArrowLeftIcon class="w-4 h-4 mr-2" />
                        Back to Login
                    </Link>
                </div>
            </div>

            <!-- Additional Help -->
            <div class="mt-6 text-center">
                <p class="text-sm text-gray-600">
                    Need help? Contact us at 
                    <a href="mailto:support@bideshgomon.com" class="font-medium text-emerald-600 hover:text-emerald-700">
                        support@bideshgomon.com
                    </a>
                </p>
            </div>
        </div>
    </div>
</template>
