<template>
    <div class="space-y-6">
        <!-- Header -->
        <div class="border-b border-gray-200 pb-4">
            <h3 class="text-lg font-semibold text-gray-900">Preferences & Settings</h3>
            <p class="mt-1 text-sm text-gray-600">
                Customize your experience and manage your preferences
            </p>
        </div>

        <!-- Preferred Destinations -->
        <div class="bg-white rounded-lg border border-gray-200 p-6">
            <div class="flex items-center gap-3 mb-4">
                <div class="p-2 bg-blue-100 rounded-lg">
                    <GlobeAltIcon class="w-6 h-6 text-blue-600" />
                </div>
                <div>
                    <h4 class="font-semibold text-gray-900">Preferred Destinations</h4>
                    <p class="text-sm text-gray-600">Countries you're interested in visiting or working</p>
                </div>
            </div>
            
            <div class="space-y-4">
                <div class="flex flex-wrap gap-2">
                    <button
                        v-for="destination in popularDestinations"
                        :key="destination"
                        @click="toggleDestination(destination)"
                        :class="[
                            'px-4 py-2 rounded-full text-sm font-medium transition-all',
                            form.preferred_destinations?.includes(destination)
                                ? 'bg-blue-600 text-white shadow-md'
                                : 'bg-gray-100 text-gray-700 hover:bg-gray-200'
                        ]"
                    >
                        {{ destination }}
                    </button>
                </div>
                
                <div class="flex gap-2">
                    <input
                        v-model="customDestination"
                        @keyup.enter="addCustomDestination"
                        type="text"
                        placeholder="Add custom destination..."
                        class="flex-1 rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                    />
                    <button
                        @click="addCustomDestination"
                        class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700"
                    >
                        Add
                    </button>
                </div>
            </div>
        </div>

        <!-- Service Interests -->
        <div class="bg-white rounded-lg border border-gray-200 p-6">
            <div class="flex items-center gap-3 mb-4">
                <div class="p-2 bg-purple-100 rounded-lg">
                    <SparklesIcon class="w-6 h-6 text-purple-600" />
                </div>
                <div>
                    <h4 class="font-semibold text-gray-900">Service Interests</h4>
                    <p class="text-sm text-gray-600">Services you're interested in</p>
                </div>
            </div>
            
            <div class="grid grid-cols-2 md:grid-cols-3 gap-3">
                <label
                    v-for="service in availableServices"
                    :key="service.value"
                    class="flex items-center gap-3 p-3 border-2 rounded-lg cursor-pointer transition-all hover:bg-gray-50"
                    :class="form.service_interests?.includes(service.value) ? 'border-purple-500 bg-purple-50' : 'border-gray-200'"
                >
                    <input
                        type="checkbox"
                        :value="service.value"
                        v-model="form.service_interests"
                        class="rounded text-purple-600 focus:ring-purple-500"
                    />
                    <div>
                        <div class="text-sm font-medium text-gray-900">{{ service.emoji }} {{ service.label }}</div>
                    </div>
                </label>
            </div>
        </div>

        <!-- Communication Preferences -->
        <div class="bg-white rounded-lg border border-gray-200 p-6">
            <div class="flex items-center gap-3 mb-4">
                <div class="p-2 bg-green-100 rounded-lg">
                    <EnvelopeIcon class="w-6 h-6 text-green-600" />
                </div>
                <div>
                    <h4 class="font-semibold text-gray-900">Communication Preferences</h4>
                    <p class="text-sm text-gray-600">How you'd like to be contacted</p>
                </div>
            </div>
            
            <div class="space-y-3">
                <label
                    v-for="method in communicationMethods"
                    :key="method.value"
                    class="flex items-center justify-between p-3 border rounded-lg cursor-pointer hover:bg-gray-50"
                >
                    <div class="flex items-center gap-3">
                        <component :is="method.icon" class="w-5 h-5 text-gray-500" />
                        <div>
                            <div class="font-medium text-gray-900">{{ method.label }}</div>
                            <div class="text-sm text-gray-600">{{ method.description }}</div>
                        </div>
                    </div>
                    <input
                        type="checkbox"
                        :value="method.value"
                        v-model="form.communication_preferences"
                        class="rounded text-green-600 focus:ring-green-500 w-5 h-5"
                    />
                </label>
            </div>
        </div>

        <!-- Notifications -->
        <div class="bg-white rounded-lg border border-gray-200 p-6">
            <div class="flex items-center gap-3 mb-4">
                <div class="p-2 bg-yellow-100 rounded-lg">
                    <BellIcon class="w-6 h-6 text-yellow-600" />
                </div>
                <div>
                    <h4 class="font-semibold text-gray-900">Notification Channels</h4>
                    <p class="text-sm text-gray-600">Choose how you receive notifications</p>
                </div>
            </div>
            
            <div class="space-y-3">
                <div
                    v-for="channel in notificationChannels"
                    :key="channel.key"
                    class="flex items-center justify-between p-3 border rounded-lg"
                >
                    <div class="flex items-center gap-3">
                        <component :is="channel.icon" :class="`w-5 h-5 ${channel.color}`" />
                        <div>
                            <div class="font-medium text-gray-900">{{ channel.label }}</div>
                            <div class="text-sm text-gray-600">{{ channel.description }}</div>
                        </div>
                    </div>
                    <button
                        @click="toggleNotification(channel.key)"
                        :class="[
                            'relative inline-flex h-6 w-11 items-center rounded-full transition-colors',
                            form.notifications[channel.key] ? 'bg-yellow-600' : 'bg-gray-200'
                        ]"
                    >
                        <span
                            :class="[
                                'inline-block h-4 w-4 transform rounded-full bg-white transition-transform',
                                form.notifications[channel.key] ? 'translate-x-6' : 'translate-x-1'
                            ]"
                        />
                    </button>
                </div>
            </div>
        </div>

        <!-- Regional Settings -->
        <div class="bg-white rounded-lg border border-gray-200 p-6">
            <div class="flex items-center gap-3 mb-4">
                <div class="p-2 bg-indigo-100 rounded-lg">
                    <GlobeAltIcon class="w-6 h-6 text-indigo-600" />
                </div>
                <div>
                    <h4 class="font-semibold text-gray-900">Regional Settings</h4>
                    <p class="text-sm text-gray-600">Language, timezone, and currency preferences</p>
                </div>
            </div>
            
            <div class="grid md:grid-cols-3 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Language
                    </label>
                    <select
                        v-model="form.language"
                        class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                    >
                        <option value="en">🇬🇧 English</option>
                        <option value="bn">🇧🇩 বাংলা (Bengali)</option>
                        <option value="ar">🇸🇦 العربية (Arabic)</option>
                        <option value="hi">🇮🇳 हिन्दी (Hindi)</option>
                        <option value="ur">🇵🇰 اردو (Urdu)</option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Timezone
                    </label>
                    <select
                        v-model="form.timezone"
                        class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                    >
                        <option value="Asia/Dhaka">Asia/Dhaka (GMT+6)</option>
                        <option value="Asia/Dubai">Asia/Dubai (GMT+4)</option>
                        <option value="Asia/Riyadh">Asia/Riyadh (GMT+3)</option>
                        <option value="Europe/London">Europe/London (GMT+0)</option>
                        <option value="America/New_York">America/New York (GMT-5)</option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Currency
                    </label>
                    <select
                        v-model="form.currency"
                        class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                    >
                        <option value="BDT">🇧🇩 BDT - Bangladeshi Taka</option>
                        <option value="USD">🇺🇸 USD - US Dollar</option>
                        <option value="EUR">🇪🇺 EUR - Euro</option>
                        <option value="GBP">🇬🇧 GBP - British Pound</option>
                        <option value="SAR">🇸🇦 SAR - Saudi Riyal</option>
                        <option value="AED">🇦🇪 AED - UAE Dirham</option>
                    </select>
                </div>
            </div>
        </div>

        <!-- Appearance -->
        <div class="bg-white rounded-lg border border-gray-200 p-6">
            <div class="flex items-center gap-3 mb-4">
                <div class="p-2 bg-pink-100 rounded-lg">
                    <SparklesIcon class="w-6 h-6 text-pink-600" />
                </div>
                <div>
                    <h4 class="font-semibold text-gray-900">Appearance</h4>
                    <p class="text-sm text-gray-600">Customize how the platform looks</p>
                </div>
            </div>
            
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-3">Theme</label>
                    <div class="grid grid-cols-3 gap-3">
                        <button
                            v-for="theme in themes"
                            :key="theme.value"
                            @click="form.theme = theme.value"
                            :class="[
                                'p-4 border-2 rounded-lg text-center transition-all',
                                form.theme === theme.value
                                    ? 'border-pink-500 bg-pink-50'
                                    : 'border-gray-200 hover:border-gray-300'
                            ]"
                        >
                            <div class="text-2xl mb-2">{{ theme.icon }}</div>
                            <div class="font-medium text-gray-900">{{ theme.label }}</div>
                        </button>
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-3">Font Size</label>
                    <div class="grid grid-cols-3 gap-3">
                        <button
                            v-for="size in fontSizes"
                            :key="size.value"
                            @click="form.font_size = size.value"
                            :class="[
                                'p-4 border-2 rounded-lg text-center transition-all',
                                form.font_size === size.value
                                    ? 'border-pink-500 bg-pink-50'
                                    : 'border-gray-200 hover:border-gray-300'
                            ]"
                        >
                            <div :class="`font-bold mb-1 ${size.class}`">Aa</div>
                            <div class="text-sm text-gray-700">{{ size.label }}</div>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Save Button -->
        <div class="flex justify-end">
            <button
                @click="savePreferences"
                :disabled="form.processing"
                class="px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 disabled:opacity-50 disabled:cursor-not-allowed font-medium"
            >
                <span v-if="!form.processing">Save Preferences</span>
                <span v-else>Saving...</span>
            </button>
        </div>
    </div>
</template>

<script setup>
import { ref, reactive, onMounted } from 'vue';
import { useForm } from '@inertiajs/vue3';
import {
    GlobeAltIcon,
    SparklesIcon,
    EnvelopeIcon,
    BellIcon,
    PhoneIcon,
    ChatBubbleLeftIcon,
    DevicePhoneMobileIcon
} from '@heroicons/vue/24/outline';

const props = defineProps({
    userProfile: {
        type: Object,
        required: true
    }
});

const customDestination = ref('');

const popularDestinations = [
    '🇸🇦 Saudi Arabia',
    '🇦🇪 UAE',
    '🇶🇦 Qatar',
    '🇴🇲 Oman',
    '🇰🇼 Kuwait',
    '🇧🇭 Bahrain',
    '🇲🇾 Malaysia',
    '🇸🇬 Singapore',
    '🇬🇧 UK',
    '🇺🇸 USA',
    '🇨🇦 Canada',
    '🇦🇺 Australia'
];

const availableServices = [
    { emoji: '✈️', label: 'Visa Processing', value: 'visa_processing' },
    { emoji: '🎫', label: 'Air Ticketing', value: 'air_ticketing' },
    { emoji: '🏨', label: 'Hotel Booking', value: 'hotel_booking' },
    { emoji: '💼', label: 'Job Placement', value: 'job_placement' },
    { emoji: '🎓', label: 'Education', value: 'education' },
    { emoji: '🚗', label: 'Transport', value: 'transport' },
    { emoji: '🏥', label: 'Medical', value: 'medical' },
    { emoji: '🕌', label: 'Hajj & Umrah', value: 'hajj_umrah' },
    { emoji: '📋', label: 'Documentation', value: 'documentation' }
];

const communicationMethods = [
    {
        icon: EnvelopeIcon,
        label: 'Email',
        description: 'Receive updates via email',
        value: 'email'
    },
    {
        icon: PhoneIcon,
        label: 'Phone Call',
        description: 'Contact via phone calls',
        value: 'phone'
    },
    {
        icon: DevicePhoneMobileIcon,
        label: 'SMS',
        description: 'Text message notifications',
        value: 'sms'
    },
    {
        icon: ChatBubbleLeftIcon,
        label: 'WhatsApp',
        description: 'WhatsApp messaging',
        value: 'whatsapp'
    }
];

const notificationChannels = [
    {
        key: 'email',
        icon: EnvelopeIcon,
        label: 'Email Notifications',
        description: 'Receive notifications via email',
        color: 'text-blue-600'
    },
    {
        key: 'sms',
        icon: DevicePhoneMobileIcon,
        label: 'SMS Notifications',
        description: 'Get SMS alerts for important updates',
        color: 'text-green-600'
    },
    {
        key: 'push',
        icon: BellIcon,
        label: 'Push Notifications',
        description: 'Browser push notifications',
        color: 'text-purple-600'
    },
    {
        key: 'whatsapp',
        icon: ChatBubbleLeftIcon,
        label: 'WhatsApp Notifications',
        description: 'Receive updates on WhatsApp',
        color: 'text-green-500'
    }
];

const themes = [
    { value: 'light', label: 'Light', icon: '☀️' },
    { value: 'dark', label: 'Dark', icon: '🌙' },
    { value: 'system', label: 'System', icon: '💻' }
];

const fontSizes = [
    { value: 'small', label: 'Small', class: 'text-sm' },
    { value: 'medium', label: 'Medium', class: 'text-base' },
    { value: 'large', label: 'Large', class: 'text-lg' }
];

const form = useForm({
    preferences: {
        preferred_destinations: props.userProfile.preferences?.preferred_destinations || [],
        service_interests: props.userProfile.preferences?.service_interests || [],
        communication_preferences: props.userProfile.preferences?.communication_preferences || [],
        language: props.userProfile.preferences?.language || 'en',
        timezone: props.userProfile.preferences?.timezone || 'Asia/Dhaka',
        currency: props.userProfile.preferences?.currency || 'BDT',
        notifications: {
            email: props.userProfile.preferences?.notifications?.email ?? true,
            sms: props.userProfile.preferences?.notifications?.sms ?? false,
            push: props.userProfile.preferences?.notifications?.push ?? true,
            whatsapp: props.userProfile.preferences?.notifications?.whatsapp ?? false
        },
        theme: props.userProfile.preferences?.theme || 'system',
        font_size: props.userProfile.preferences?.font_size || 'medium'
    }
});

// Create reactive proxies for easier binding
const form_data = reactive({
    preferred_destinations: form.preferences.preferred_destinations,
    service_interests: form.preferences.service_interests,
    communication_preferences: form.preferences.communication_preferences,
    language: form.preferences.language,
    timezone: form.preferences.timezone,
    currency: form.preferences.currency,
    notifications: form.preferences.notifications,
    theme: form.preferences.theme,
    font_size: form.preferences.font_size
});

// Sync reactive data back to form
Object.keys(form_data).forEach(key => {
    Object.defineProperty(form, key, {
        get() { return form_data[key]; },
        set(value) { form_data[key] = value; }
    });
});

const toggleDestination = (destination) => {
    const index = form.preferred_destinations.indexOf(destination);
    if (index > -1) {
        form.preferred_destinations.splice(index, 1);
    } else {
        form.preferred_destinations.push(destination);
    }
};

const addCustomDestination = () => {
    if (customDestination.value.trim() && !form.preferred_destinations.includes(customDestination.value.trim())) {
        form.preferred_destinations.push(customDestination.value.trim());
        customDestination.value = '';
    }
};

const toggleNotification = (key) => {
    form.notifications[key] = !form.notifications[key];
};

const savePreferences = () => {
    form.post(route('profile.preferences.update'), {
        preserveScroll: true,
        onSuccess: () => {
            // Success handled by Inertia
        }
    });
};
</script>
