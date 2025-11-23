<script setup>
import { ref, computed } from 'vue';
import { useForm, router } from '@inertiajs/vue3';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import { 
    LockClosedIcon, 
    ShieldCheckIcon, 
    EyeIcon, 
    EyeSlashIcon,
    ArrowDownTrayIcon,
    CheckCircleIcon
} from '@heroicons/vue/24/solid';

const props = defineProps({
    userProfile: Object,
});

const defaultSettings = {
    profile_visibility: 'public',
    show_email: false,
    show_phone: false,
    show_address: false,
    show_dob: false,
    show_social_links: true,
    allow_search_engines: true,
    show_in_directory: true,
};

const form = useForm({
    privacy_settings: props.userProfile?.privacy_settings || defaultSettings
});

const downloading = ref(false);
const lastDownload = computed(() => props.userProfile?.data_downloaded_at);

const visibilityOptions = [
    { value: 'public', label: 'Public', icon: '🌍', description: 'Anyone can view your profile' },
    { value: 'connections', label: 'Connections Only', icon: '👥', description: 'Only your connections can view' },
    { value: 'private', label: 'Private', icon: '🔒', description: 'Only you can view your profile' },
];

const submit = () => {
    form.post(route('profile.privacy-settings.update'), {
        preserveScroll: true,
    });
};

const downloadData = () => {
    downloading.value = true;
    window.location.href = route('profile.download-data');
    setTimeout(() => {
        downloading.value = false;
    }, 3000);
};

const getVisibilityInfo = (value) => {
    return visibilityOptions.find(o => o.value === value) || visibilityOptions[0];
};
</script>

<template>
    <div class="space-y-6">
        <div>
            <h3 class="text-lg font-medium text-gray-900">Privacy & Data Control</h3>
            <p class="mt-1 text-sm text-gray-600">
                Manage your privacy settings, control who can see your information, and download your data.
            </p>
        </div>

        <!-- GDPR Notice -->
        <div class="rounded-lg border border-blue-200 bg-blue-50 p-4">
            <div class="flex items-start">
                <ShieldCheckIcon class="h-5 w-5 text-blue-600 mr-3 mt-0.5" />
                <div>
                    <h4 class="text-sm font-medium text-blue-900">Your Privacy Rights</h4>
                    <p class="mt-1 text-sm text-blue-800">
                        You have complete control over your data. You can view, download, or delete your information at any time. We comply with GDPR and international data protection standards.
                    </p>
                </div>
            </div>
        </div>

        <form @submit.prevent="submit" class="space-y-6">
            <!-- Profile Visibility -->
            <div class="rounded-lg border border-gray-200 bg-white p-6 shadow-sm">
                <h4 class="mb-4 flex items-center gap-2 text-base font-semibold text-gray-900">
                    <EyeIcon class="h-5 w-5 text-indigo-600" />
                    Profile Visibility
                </h4>

                <div class="space-y-3">
                    <div
                        v-for="option in visibilityOptions"
                        :key="option.value"
                        class="relative"
                    >
                        <label
                            class="flex cursor-pointer items-start rounded-lg border-2 p-4 transition-colors"
                            :class="form.privacy_settings.profile_visibility === option.value 
                                ? 'border-indigo-600 bg-indigo-50' 
                                : 'border-gray-200 hover:border-gray-300'"
                        >
                            <input
                                type="radio"
                                v-model="form.privacy_settings.profile_visibility"
                                :value="option.value"
                                class="mt-1 h-4 w-4 text-indigo-600 focus:ring-indigo-500"
                            />
                            <div class="ml-3 flex-1">
                                <div class="flex items-center gap-2">
                                    <span class="text-xl">{{ option.icon }}</span>
                                    <span class="font-medium text-gray-900">{{ option.label }}</span>
                                    <CheckCircleIcon 
                                        v-if="form.privacy_settings.profile_visibility === option.value"
                                        class="h-5 w-5 text-indigo-600"
                                    />
                                </div>
                                <p class="mt-1 text-sm text-gray-600">{{ option.description }}</p>
                            </div>
                        </label>
                    </div>
                </div>

                <p v-if="form.errors.privacy_settings?.profile_visibility" class="mt-2 text-sm text-red-600">
                    {{ form.errors.privacy_settings.profile_visibility }}
                </p>
            </div>

            <!-- Information Visibility -->
            <div class="rounded-lg border border-gray-200 bg-white p-6 shadow-sm">
                <h4 class="mb-4 flex items-center gap-2 text-base font-semibold text-gray-900">
                    <LockClosedIcon class="h-5 w-5 text-green-600" />
                    Information Visibility
                </h4>

                <p class="mb-4 text-sm text-gray-600">
                    Choose what information is visible to others on your profile
                </p>

                <div class="space-y-3">
                    <!-- Show Email -->
                    <div class="flex items-start justify-between rounded-lg border border-gray-200 p-4">
                        <div class="flex items-start gap-3">
                            <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-purple-100">
                                <svg class="h-5 w-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                </svg>
                            </div>
                            <div>
                                <div class="font-medium text-gray-900">Email Address</div>
                                <div class="text-sm text-gray-600">Allow others to see your email</div>
                            </div>
                        </div>
                        <label class="relative inline-flex cursor-pointer items-center">
                            <input
                                type="checkbox"
                                v-model="form.privacy_settings.show_email"
                                class="peer sr-only"
                            />
                            <div class="peer h-6 w-11 rounded-full bg-gray-200 after:absolute after:left-[2px] after:top-[2px] after:h-5 after:w-5 after:rounded-full after:border after:border-gray-300 after:bg-white after:transition-all after:content-[''] peer-checked:bg-indigo-600 peer-checked:after:translate-x-full peer-checked:after:border-white peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-indigo-300"></div>
                        </label>
                    </div>

                    <!-- Show Phone -->
                    <div class="flex items-start justify-between rounded-lg border border-gray-200 p-4">
                        <div class="flex items-start gap-3">
                            <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-blue-100">
                                <svg class="h-5 w-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                                </svg>
                            </div>
                            <div>
                                <div class="font-medium text-gray-900">Phone Number</div>
                                <div class="text-sm text-gray-600">Allow others to see your phone number</div>
                            </div>
                        </div>
                        <label class="relative inline-flex cursor-pointer items-center">
                            <input
                                type="checkbox"
                                v-model="form.privacy_settings.show_phone"
                                class="peer sr-only"
                            />
                            <div class="peer h-6 w-11 rounded-full bg-gray-200 after:absolute after:left-[2px] after:top-[2px] after:h-5 after:w-5 after:rounded-full after:border after:border-gray-300 after:bg-white after:transition-all after:content-[''] peer-checked:bg-indigo-600 peer-checked:after:translate-x-full peer-checked:after:border-white peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-indigo-300"></div>
                        </label>
                    </div>

                    <!-- Show Address -->
                    <div class="flex items-start justify-between rounded-lg border border-gray-200 p-4">
                        <div class="flex items-start gap-3">
                            <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-green-100">
                                <svg class="h-5 w-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                </svg>
                            </div>
                            <div>
                                <div class="font-medium text-gray-900">Address</div>
                                <div class="text-sm text-gray-600">Allow others to see your address</div>
                            </div>
                        </div>
                        <label class="relative inline-flex cursor-pointer items-center">
                            <input
                                type="checkbox"
                                v-model="form.privacy_settings.show_address"
                                class="peer sr-only"
                            />
                            <div class="peer h-6 w-11 rounded-full bg-gray-200 after:absolute after:left-[2px] after:top-[2px] after:h-5 after:w-5 after:rounded-full after:border after:border-gray-300 after:bg-white after:transition-all after:content-[''] peer-checked:bg-indigo-600 peer-checked:after:translate-x-full peer-checked:after:border-white peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-indigo-300"></div>
                        </label>
                    </div>

                    <!-- Show Date of Birth -->
                    <div class="flex items-start justify-between rounded-lg border border-gray-200 p-4">
                        <div class="flex items-start gap-3">
                            <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-pink-100">
                                <svg class="h-5 w-5 text-pink-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                            </div>
                            <div>
                                <div class="font-medium text-gray-900">Date of Birth</div>
                                <div class="text-sm text-gray-600">Allow others to see your birth date</div>
                            </div>
                        </div>
                        <label class="relative inline-flex cursor-pointer items-center">
                            <input
                                type="checkbox"
                                v-model="form.privacy_settings.show_dob"
                                class="peer sr-only"
                            />
                            <div class="peer h-6 w-11 rounded-full bg-gray-200 after:absolute after:left-[2px] after:top-[2px] after:h-5 after:w-5 after:rounded-full after:border after:border-gray-300 after:bg-white after:transition-all after:content-[''] peer-checked:bg-indigo-600 peer-checked:after:translate-x-full peer-checked:after:border-white peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-indigo-300"></div>
                        </label>
                    </div>

                    <!-- Show Social Links -->
                    <div class="flex items-start justify-between rounded-lg border border-gray-200 p-4">
                        <div class="flex items-start gap-3">
                            <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-cyan-100">
                                <svg class="h-5 w-5 text-cyan-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1" />
                                </svg>
                            </div>
                            <div>
                                <div class="font-medium text-gray-900">Social Media Links</div>
                                <div class="text-sm text-gray-600">Allow others to see your social profiles</div>
                            </div>
                        </div>
                        <label class="relative inline-flex cursor-pointer items-center">
                            <input
                                type="checkbox"
                                v-model="form.privacy_settings.show_social_links"
                                class="peer sr-only"
                            />
                            <div class="peer h-6 w-11 rounded-full bg-gray-200 after:absolute after:left-[2px] after:top-[2px] after:h-5 after:w-5 after:rounded-full after:border after:border-gray-300 after:bg-white after:transition-all after:content-[''] peer-checked:bg-indigo-600 peer-checked:after:translate-x-full peer-checked:after:border-white peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-indigo-300"></div>
                        </label>
                    </div>
                </div>
            </div>

            <!-- Platform Settings -->
            <div class="rounded-lg border border-gray-200 bg-white p-6 shadow-sm">
                <h4 class="mb-4 flex items-center gap-2 text-base font-semibold text-gray-900">
                    <svg class="h-5 w-5 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                    Platform Settings
                </h4>

                <div class="space-y-3">
                    <!-- Allow Search Engines -->
                    <div class="flex items-start justify-between rounded-lg border border-gray-200 p-4">
                        <div class="flex items-start gap-3">
                            <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-yellow-100">
                                <svg class="h-5 w-5 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                </svg>
                            </div>
                            <div>
                                <div class="font-medium text-gray-900">Search Engine Indexing</div>
                                <div class="text-sm text-gray-600">Allow search engines to index your profile</div>
                            </div>
                        </div>
                        <label class="relative inline-flex cursor-pointer items-center">
                            <input
                                type="checkbox"
                                v-model="form.privacy_settings.allow_search_engines"
                                class="peer sr-only"
                            />
                            <div class="peer h-6 w-11 rounded-full bg-gray-200 after:absolute after:left-[2px] after:top-[2px] after:h-5 after:w-5 after:rounded-full after:border after:border-gray-300 after:bg-white after:transition-all after:content-[''] peer-checked:bg-indigo-600 peer-checked:after:translate-x-full peer-checked:after:border-white peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-indigo-300"></div>
                        </label>
                    </div>

                    <!-- Show in Directory -->
                    <div class="flex items-start justify-between rounded-lg border border-gray-200 p-4">
                        <div class="flex items-start gap-3">
                            <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-indigo-100">
                                <svg class="h-5 w-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                                </svg>
                            </div>
                            <div>
                                <div class="font-medium text-gray-900">Show in Directory</div>
                                <div class="text-sm text-gray-600">Appear in user directory and search results</div>
                            </div>
                        </div>
                        <label class="relative inline-flex cursor-pointer items-center">
                            <input
                                type="checkbox"
                                v-model="form.privacy_settings.show_in_directory"
                                class="peer sr-only"
                            />
                            <div class="peer h-6 w-11 rounded-full bg-gray-200 after:absolute after:left-[2px] after:top-[2px] after:h-5 after:w-5 after:rounded-full after:border after:border-gray-300 after:bg-white after:transition-all after:content-[''] peer-checked:bg-indigo-600 peer-checked:after:translate-x-full peer-checked:after:border-white peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-indigo-300"></div>
                        </label>
                    </div>
                </div>
            </div>

            <!-- Save Button -->
            <div class="flex items-center justify-between border-t border-gray-200 pt-6">
                <p v-if="form.recentlySuccessful" class="text-sm text-green-600">
                    ✓ Privacy settings saved successfully!
                </p>
                <div class="ml-auto">
                    <PrimaryButton :disabled="form.processing">
                        {{ form.processing ? 'Saving...' : 'Save Privacy Settings' }}
                    </PrimaryButton>
                </div>
            </div>
        </form>

        <!-- Data Download Section -->
        <div class="rounded-lg border border-gray-200 bg-white p-6 shadow-sm">
            <h4 class="mb-4 flex items-center gap-2 text-base font-semibold text-gray-900">
                <ArrowDownTrayIcon class="h-5 w-5 text-blue-600" />
                Download Your Data
            </h4>

            <p class="mb-4 text-sm text-gray-600">
                Download a complete copy of your data in JSON format. This includes your profile, education, work experience, and all other information.
            </p>

            <div v-if="lastDownload" class="mb-4 rounded-lg bg-gray-50 p-3 text-sm text-gray-700">
                <strong>Last downloaded:</strong> {{ new Date(lastDownload).toLocaleString() }}
            </div>

            <SecondaryButton 
                @click="downloadData" 
                :disabled="downloading"
                class="flex items-center gap-2"
            >
                <ArrowDownTrayIcon class="h-4 w-4" />
                {{ downloading ? 'Preparing Download...' : 'Download My Data' }}
            </SecondaryButton>
        </div>

        <!-- Security Notice -->
        <div class="rounded-lg border border-amber-200 bg-amber-50 p-4">
            <h4 class="mb-2 flex items-center gap-2 text-sm font-medium text-amber-900">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                </svg>
                Privacy Best Practices
            </h4>
            <ul class="space-y-1 text-sm text-amber-800">
                <li>• Review your privacy settings regularly</li>
                <li>• Be cautious about making sensitive information public</li>
                <li>• Download your data periodically for backup</li>
                <li>• Limit personal contact details visibility to trusted connections</li>
                <li>• Consider making your profile private during job searches</li>
            </ul>
        </div>
    </div>
</template>
