<script setup>
import { ref } from 'vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { UserIcon, EnvelopeIcon, LockClosedIcon, EyeIcon, EyeSlashIcon, GiftIcon, PhoneIcon, RocketLaunchIcon } from '@heroicons/vue/24/outline';
import Header from '@/Components/Header.vue';
import Footer from '@/Components/Footer.vue';
import ApplicationLogo from '@/Components/ApplicationLogo.vue';

const props = defineProps({
    referralCode: String,
});

const form = useForm({
    first_name: '',
    middle_name: '',
    last_name: '',
    email: '',
    phone: '',
    password: '',
    password_confirmation: '',
    referral_code: props.referralCode || '',
});

const showPassword = ref(false);
const showConfirmPassword = ref(false);

const submit = () => {
    form.post(route('register'), {
        onFinish: () => form.reset('password', 'password_confirmation'),
    });
};
</script>

<template>
    <Head title="Register | Bidesh Gomon" />

    <div class="min-h-screen bg-white flex flex-col">
        <!-- Header -->
        <Header :can-login="true" :can-register="false" />

        <!-- Main Content -->
        <div class="flex-1 flex items-center justify-center px-4 py-24 sm:px-6 lg:px-8 bg-gradient-to-br from-emerald-50 via-white to-blue-50">
            <div class="w-full max-w-md">
                <!-- Logo -->
                <div class="flex justify-center mb-8">
                    <Link :href="route('welcome')">
                        <ApplicationLogo class="h-16 w-auto" />
                    </Link>
                </div>

                <!-- Welcome Text -->
                <div class="text-center mb-8">
                    <div class="inline-flex items-center px-4 py-2 rounded-full bg-emerald-50 border border-emerald-200 mb-4">
                        <RocketLaunchIcon class="h-4 w-4 text-emerald-600 mr-2" />
                        <span class="text-xs font-medium text-emerald-900">Start Your Journey</span>
                    </div>
                    <h1 class="text-3xl font-bold text-gray-900 mb-2">
                        Create Account 🚀
                    </h1>
                    <p class="text-gray-600">
                        Join <span class="font-semibold text-emerald-600">50,000+</span> users going abroad with confidence
                    </p>
                </div>

                <!-- Referral Badge -->
                <div v-if="referralCode" class="mb-6 p-4 bg-gradient-to-r from-amber-50 to-orange-50 border-2 border-amber-200 rounded-2xl">
                    <div class="flex items-center space-x-3">
                        <div class="flex-shrink-0">
                            <GiftIcon class="h-6 w-6 text-amber-600" />
                        </div>
                        <div class="flex-1">
                            <p class="text-sm font-semibold text-amber-900">Referral Bonus! 🎉</p>
                            <p class="text-xs text-amber-700 mt-1">You were referred! Get rewards after registration.</p>
                        </div>
                    </div>
                </div>

                <!-- Register Form Card -->
                <div class="bg-white rounded-3xl shadow-xl p-6 sm:p-8 space-y-5 border border-gray-100">
                    <form @submit.prevent="submit" class="space-y-4">
                        <!-- Passport Name Notice -->
                        <div class="p-3 bg-blue-50 border border-blue-200 rounded-xl">
                            <p class="text-xs text-blue-800">
                                <span class="font-semibold">📝 Enter your name as it appears on your passport</span>
                            </p>
                        </div>

                        <!-- Name Fields Grid -->
                        <div class="space-y-3">
                            <!-- First Name -->
                            <div>
                                <label for="first_name" class="block text-sm font-semibold text-gray-700 mb-2">
                                    First Name *
                                </label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                        <UserIcon class="h-5 w-5 text-gray-400" />
                                    </div>
                                    <input
                                        id="first_name"
                                        type="text"
                                        v-model="form.first_name"
                                        required
                                        autofocus
                                        autocomplete="given-name"
                                        placeholder="FIRST"
                                        class="block w-full pl-11 pr-4 py-4 text-base border-2 border-gray-200 rounded-2xl focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-colors uppercase"
                                        style="font-size: 16px"
                                        :class="{ 'border-red-300 focus:border-red-500 focus:ring-red-500': form.errors.first_name }"
                                    />
                                </div>
                                <p v-if="form.errors.first_name" class="mt-2 text-sm text-red-600 flex items-center">
                                    <span class="inline-block w-1 h-1 bg-red-600 rounded-full mr-2"></span>
                                    {{ form.errors.first_name }}
                                </p>
                            </div>

                            <!-- Middle Name -->
                            <div>
                                <label for="middle_name" class="block text-sm font-semibold text-gray-700 mb-2">
                                    Middle Name (Optional)
                                </label>
                                <input
                                    id="middle_name"
                                    type="text"
                                    v-model="form.middle_name"
                                    autocomplete="additional-name"
                                    placeholder="MIDDLE"
                                    class="block w-full px-4 py-4 text-base border-2 border-gray-200 rounded-2xl focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-colors uppercase"
                                    style="font-size: 16px"
                                    :class="{ 'border-red-300 focus:border-red-500 focus:ring-red-500': form.errors.middle_name }"
                                />
                                <p v-if="form.errors.middle_name" class="mt-2 text-sm text-red-600 flex items-center">
                                    <span class="inline-block w-1 h-1 bg-red-600 rounded-full mr-2"></span>
                                    {{ form.errors.middle_name }}
                                </p>
                            </div>

                            <!-- Last Name -->
                            <div>
                                <label for="last_name" class="block text-sm font-semibold text-gray-700 mb-2">
                                    Last Name *
                                </label>
                                <input
                                    id="last_name"
                                    type="text"
                                    v-model="form.last_name"
                                    required
                                    autocomplete="family-name"
                                    placeholder="LAST"
                                    class="block w-full px-4 py-4 text-base border-2 border-gray-200 rounded-2xl focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-colors uppercase"
                                    style="font-size: 16px"
                                    :class="{ 'border-red-300 focus:border-red-500 focus:ring-red-500': form.errors.last_name }"
                                />
                                <p v-if="form.errors.last_name" class="mt-2 text-sm text-red-600 flex items-center">
                                    <span class="inline-block w-1 h-1 bg-red-600 rounded-full mr-2"></span>
                                    {{ form.errors.last_name }}
                                </p>
                            </div>
                        </div>

                        <!-- Email Input -->
                        <div>
                            <label for="email" class="block text-sm font-semibold text-gray-700 mb-2">
                                Email
                            </label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                    <EnvelopeIcon class="h-5 w-5 text-gray-400" />
                                </div>
                                <input
                                    id="email"
                                    type="email"
                                    v-model="form.email"
                                    required
                                    autocomplete="username"
                                    placeholder="your@email.com"
                                    class="block w-full pl-11 pr-4 py-4 text-base border-2 border-gray-200 rounded-2xl focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-colors"
                                    :class="{ 'border-red-300 focus:border-red-500 focus:ring-red-500': form.errors.email }"
                                />
                            </div>
                            <p v-if="form.errors.email" class="mt-2 text-sm text-red-600 flex items-center">
                                <span class="inline-block w-1 h-1 bg-red-600 rounded-full mr-2"></span>
                                {{ form.errors.email }}
                            </p>
                        </div>

                        <!-- Phone Number Input -->
                        <div>
                            <label for="phone" class="block text-sm font-semibold text-gray-700 mb-2">
                                Mobile Number *
                            </label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                    <PhoneIcon class="h-5 w-5 text-gray-400" />
                                </div>
                                <input
                                    id="phone"
                                    type="tel"
                                    v-model="form.phone"
                                    required
                                    autocomplete="tel"
                                    placeholder="+880 1712345678"
                                    class="block w-full pl-11 pr-4 py-4 text-base border-2 border-gray-200 rounded-2xl focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-colors"
                                    :class="{ 'border-red-300 focus:border-red-500 focus:ring-red-500': form.errors.phone }"
                                />
                            </div>
                            <p class="mt-1 text-xs text-gray-500">Include country code (e.g., +880 for Bangladesh)</p>
                            <p v-if="form.errors.phone" class="mt-2 text-sm text-red-600 flex items-center">
                                <span class="inline-block w-1 h-1 bg-red-600 rounded-full mr-2"></span>
                                {{ form.errors.phone }}
                            </p>
                        </div>

                        <!-- Referral Code (if provided) -->
                        <div v-if="referralCode">
                            <label for="referral_code" class="block text-sm font-semibold text-gray-700 mb-2">
                                Referral Code
                            </label>
                            <input
                                id="referral_code"
                                type="text"
                                v-model="form.referral_code"
                                readonly
                                class="block w-full px-4 py-4 text-base border-2 border-amber-200 bg-amber-50 rounded-2xl font-mono font-semibold text-amber-900"
                            />
                        </div>

                        <!-- Password Input -->
                        <div>
                            <label for="password" class="block text-sm font-semibold text-gray-700 mb-2">
                                Password
                            </label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                    <LockClosedIcon class="h-5 w-5 text-gray-400" />
                                </div>
                                <input
                                    id="password"
                                    :type="showPassword ? 'text' : 'password'"
                                    v-model="form.password"
                                    required
                                    autocomplete="new-password"
                                    placeholder="At least 8 characters"
                                    class="block w-full pl-11 pr-12 py-4 text-base border-2 border-gray-200 rounded-2xl focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-colors"
                                    :class="{ 'border-red-300 focus:border-red-500 focus:ring-red-500': form.errors.password }"
                                />
                                <button
                                    type="button"
                                    @click="showPassword = !showPassword"
                                    class="absolute inset-y-0 right-0 pr-4 flex items-center touch-manipulation"
                                >
                                    <EyeSlashIcon v-if="showPassword" class="h-5 w-5 text-gray-400 hover:text-gray-600" />
                                    <EyeIcon v-else class="h-5 w-5 text-gray-400 hover:text-gray-600" />
                                </button>
                            </div>
                            <p v-if="form.errors.password" class="mt-2 text-sm text-red-600 flex items-center">
                                <span class="inline-block w-1 h-1 bg-red-600 rounded-full mr-2"></span>
                                {{ form.errors.password }}
                            </p>
                        </div>

                        <!-- Confirm Password Input -->
                        <div>
                            <label for="password_confirmation" class="block text-sm font-semibold text-gray-700 mb-2">
                                Confirm Password
                            </label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                    <LockClosedIcon class="h-5 w-5 text-gray-400" />
                                </div>
                                <input
                                    id="password_confirmation"
                                    :type="showConfirmPassword ? 'text' : 'password'"
                                    v-model="form.password_confirmation"
                                    required
                                    autocomplete="new-password"
                                    placeholder="Re-enter your password"
                                    class="block w-full pl-11 pr-12 py-4 text-base border-2 border-gray-200 rounded-2xl focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-colors"
                                    :class="{ 'border-red-300 focus:border-red-500 focus:ring-red-500': form.errors.password_confirmation }"
                                />
                                <button
                                    type="button"
                                    @click="showConfirmPassword = !showConfirmPassword"
                                    class="absolute inset-y-0 right-0 pr-4 flex items-center touch-manipulation"
                                >
                                    <EyeSlashIcon v-if="showConfirmPassword" class="h-5 w-5 text-gray-400 hover:text-gray-600" />
                                    <EyeIcon v-else class="h-5 w-5 text-gray-400 hover:text-gray-600" />
                                </button>
                            </div>
                            <p v-if="form.errors.password_confirmation" class="mt-2 text-sm text-red-600 flex items-center">
                                <span class="inline-block w-1 h-1 bg-red-600 rounded-full mr-2"></span>
                                {{ form.errors.password_confirmation }}
                            </p>
                        </div>

                        <!-- Register Button - Touch Optimized (48px height) -->
                        <button
                            type="submit"
                            :disabled="form.processing"
                            class="w-full flex justify-center items-center py-4 px-6 text-base font-semibold text-white bg-gradient-to-r from-emerald-600 to-emerald-700 rounded-2xl shadow-lg hover:shadow-xl hover:from-emerald-700 hover:to-emerald-800 focus:outline-none focus:ring-4 focus:ring-emerald-500 focus:ring-offset-2 disabled:opacity-50 disabled:cursor-not-allowed transition-all duration-200 touch-manipulation min-h-[48px] mt-6"
                        >
                            <svg v-if="form.processing" class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            <span v-if="form.processing">Creating account...</span>
                            <span v-else>Create Account</span>
                        </button>
                    </form>

                    <!-- Terms & Privacy -->
                    <div class="text-center text-xs text-gray-500 mt-4">
                        <p>
                            By registering, you agree to our
                            <a href="#" class="text-emerald-600 hover:underline">Terms of Service</a> and
                            <a href="#" class="text-emerald-600 hover:underline">Privacy Policy</a>
                        </p>
                    </div>

                    <!-- Divider -->
                    <div class="relative my-6">
                        <div class="absolute inset-0 flex items-center">
                            <div class="w-full border-t border-gray-200"></div>
                        </div>
                        <div class="relative flex justify-center text-sm">
                            <span class="px-4 bg-white text-gray-500 font-medium">or continue with</span>
                        </div>
                    </div>

                    <!-- Social Login Buttons -->
                    <div class="grid grid-cols-1 gap-3">
                        <a
                            :href="route('auth.google')"
                            class="w-full flex items-center justify-center px-4 py-3 border-2 border-gray-200 rounded-2xl shadow-sm bg-white text-sm font-medium text-gray-700 hover:bg-gray-50 hover:border-gray-300 transition-all"
                        >
                            <svg class="w-5 h-5 mr-3" viewBox="0 0 24 24">
                                <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
                                <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
                                <path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z"/>
                                <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"/>
                            </svg>
                            Continue with Google
                        </a>
                    </div>

                    <!-- Another Divider -->
                    <div class="relative my-6">
                        <div class="absolute inset-0 flex items-center">
                            <div class="w-full border-t border-gray-200"></div>
                        </div>
                    </div>

                    <!-- Login Link -->
                    <div class="text-center">
                        <p class="text-sm text-gray-600">
                            Already have an account?
                            <Link
                                :href="route('login')"
                                class="font-semibold text-emerald-600 hover:text-emerald-700 touch-manipulation"
                            >
                                Sign in
                            </Link>
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Footer -->
        <Footer />
    </div>
</template>
