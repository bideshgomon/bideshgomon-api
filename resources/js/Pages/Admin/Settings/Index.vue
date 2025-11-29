<script setup>
import { ref, computed } from 'vue';
import { Head, useForm, Link } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { 
    CogIcon, 
    EnvelopeIcon, 
    BriefcaseIcon, 
    WalletIcon, 
    FlagIcon, 
    ShareIcon,
    CheckIcon,
    XMarkIcon,
    PlusIcon,
    ArrowPathIcon,
    KeyIcon,
    EyeIcon,
    EyeSlashIcon,
    MapIcon,
    CreditCardIcon,
    CloudIcon,
    ShieldCheckIcon,
    ChatBubbleLeftRightIcon,
    SparklesIcon,
    ExclamationTriangleIcon
} from '@heroicons/vue/24/outline';

const props = defineProps({
    settings: Array,          // Array of all settings
    currentGroup: String,      // Active group
    groups: Object,            // Available groups {key: label}
});

const activeTab = ref(props.currentGroup || 'general');
const visiblePasswords = ref({});

const togglePasswordVisibility = (key) => {
    visiblePasswords.value[key] = !visiblePasswords.value[key];
};

const groupConfig = {
    general: { icon: CogIcon, label: 'General', color: 'indigo' },
    branding: { icon: SparklesIcon, label: 'Branding', color: 'purple' },
    seo: { icon: ShieldCheckIcon, label: 'SEO', color: 'blue' },
    email: { icon: EnvelopeIcon, label: 'Email', color: 'blue' },
    contact: { icon: ChatBubbleLeftRightIcon, label: 'Contact', color: 'green' },
    jobs: { icon: BriefcaseIcon, label: 'Jobs', color: 'purple' },
    wallet: { icon: WalletIcon, label: 'Wallet', color: 'green' },
    features: { icon: FlagIcon, label: 'Features', color: 'orange' },
    social: { icon: ShareIcon, label: 'Social Media', color: 'pink' },
    api: { icon: KeyIcon, label: 'API Keys', color: 'red' },
    advanced: { icon: CogIcon, label: 'Advanced', color: 'gray' },
};

// API service configuration with icons and colors
const apiServices = {
    google_maps: { icon: MapIcon, color: 'green', label: 'Google Maps' },
    google_oauth: { icon: ShieldCheckIcon, color: 'blue', label: 'Google OAuth' },
    facebook: { icon: ShareIcon, color: 'indigo', label: 'Facebook' },
    stripe: { icon: CreditCardIcon, color: 'purple', label: 'Stripe' },
    paypal: { icon: WalletIcon, color: 'blue', label: 'PayPal' },
    sslcommerz: { icon: CreditCardIcon, color: 'green', label: 'SSLCommerz' },
    bkash: { icon: WalletIcon, color: 'pink', label: 'bKash' },
    nagad: { icon: WalletIcon, color: 'orange', label: 'Nagad' },
    aws: { icon: CloudIcon, color: 'orange', label: 'AWS' },
    pusher: { icon: ChatBubbleLeftRightIcon, color: 'purple', label: 'Pusher' },
    mailgun: { icon: EnvelopeIcon, color: 'red', label: 'Mailgun' },
    twilio: { icon: ChatBubbleLeftRightIcon, color: 'red', label: 'Twilio' },
    openai: { icon: SparklesIcon, color: 'green', label: 'OpenAI' },
    recaptcha: { icon: ShieldCheckIcon, color: 'blue', label: 'reCAPTCHA' },
};

const getApiServiceConfig = (key) => {
    for (const [service, config] of Object.entries(apiServices)) {
        if (key.toLowerCase().includes(service.replace('_', ''))) {
            return config;
        }
    }
    return null;
};

const form = useForm({
    settings: props.settings.map(setting => ({
        key: setting.key,
        value: setting.type === 'boolean' 
            ? setting.value === '1' || setting.value === true 
            : setting.value,
        type: setting.type,
    })),
    group: activeTab.value,
});

const activeSettings = computed(() => {
    return props.settings.filter(s => s.group === activeTab.value);
});

const submit = () => {
    // Only submit settings for the active tab
    const activeTabSettings = form.settings.filter(s => {
        const originalSetting = props.settings.find(ps => ps.key === s.key);
        return originalSetting && originalSetting.group === activeTab.value;
    });
    
    console.log('Submitting settings for group:', activeTab.value);
    console.log('Number of settings:', activeTabSettings.length);
    
    const submitForm = useForm({
        settings: activeTabSettings
    });
    
    submitForm.post(route('admin.settings.update', { group: activeTab.value }), {
        preserveScroll: true,
        onSuccess: () => {
            console.log('Settings saved successfully');
        },
        onError: (errors) => {
            console.error('Save errors:', errors);
        },
    });
};

const switchTab = (group) => {
    window.location.href = route('admin.settings.index', { group });
};

const getInputType = (type) => {
    const typeMap = {
        'text': 'text',
        'email': 'email',
        'url': 'url',
        'number': 'number',
        'boolean': 'checkbox',
        'password': 'password',
    };
    return typeMap[type] || 'text';
};

const updateSetting = (key, value) => {
    const index = form.settings.findIndex(s => s.key === key);
    if (index !== -1) {
        form.settings[index].value = value;
    }
};
</script>

<template>
    <Head title="Settings" />

    <AdminLayout>
        <div class="py-6">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <!-- Header -->
                <div class="mb-8">
                    <div class="flex justify-between items-center">
                        <div>
                            <h1 class="text-3xl font-bold text-gray-900">Platform Settings</h1>
                            <p class="mt-2 text-sm text-gray-600">
                                Configure your platform settings and preferences
                            </p>
                        </div>
                        <div class="flex gap-3">
                            <Link
                                :href="route('admin.settings.seed')"
                                method="post"
                                as="button"
                                class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-lg shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 transition-colors"
                            >
                                <ArrowPathIcon class="h-5 w-5 mr-2" />
                                Reset to Defaults
                            </Link>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-xl shadow-sm border border-gray-200">
                    <!-- Tabs -->
                    <div class="border-b border-gray-200">
                        <nav class="flex space-x-4 px-6 overflow-x-auto" aria-label="Tabs">
                            <button
                                v-for="(label, key) in groups"
                                :key="key"
                                @click="switchTab(key)"
                                :class="[
                                    activeTab === key
                                        ? 'border-indigo-500 text-indigo-600'
                                        : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300',
                                    'whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm flex items-center gap-2 transition-colors'
                                ]"
                            >
                                <component :is="groupConfig[key]?.icon || CogIcon" class="h-5 w-5" />
                                {{ label }}
                            </button>
                        </nav>
                    </div>

                    <!-- Settings Form -->
                    <form @submit.prevent="submit" class="p-6">
                        <!-- API Keys Section - Enhanced Layout -->
                        <div v-if="activeTab === 'api'" class="space-y-4">
                            <!-- Warning Banner -->
                            <div class="bg-amber-50 border border-amber-200 rounded-lg p-4 mb-6">
                                <div class="flex">
                                    <ExclamationTriangleIcon class="h-5 w-5 text-amber-400 flex-shrink-0" />
                                    <div class="ml-3">
                                        <h3 class="text-sm font-medium text-amber-800">Security Notice</h3>
                                        <p class="mt-1 text-sm text-amber-700">
                                            API keys are sensitive credentials. Never share them publicly or commit them to version control.
                                            These values are stored securely in your .env file.
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <!-- Group API keys by service -->
                            <div
                                v-for="setting in activeSettings"
                                :key="setting.key"
                                class="bg-white border border-gray-200 rounded-lg p-5 hover:shadow-md transition-shadow"
                            >
                                <div class="flex items-start gap-4">
                                    <!-- Service Icon -->
                                    <div class="flex-shrink-0 rounded-lg p-3 bg-indigo-100">
                                        <component 
                                            :is="getApiServiceConfig(setting.key)?.icon || KeyIcon" 
                                            class="h-6 w-6 text-indigo-600"
                                        />
                                    </div>

                                    <!-- Setting Details -->
                                    <div class="flex-1 min-w-0">
                                        <div class="flex items-center gap-2 mb-1">
                                            <label :for="setting.key" class="text-sm font-semibold text-gray-900">
                                                {{ setting.key.split('_').map(w => w.charAt(0).toUpperCase() + w.slice(1)).join(' ') }}
                                            </label>
                                            <span
                                                v-if="form.settings.find(s => s.key === setting.key)?.value"
                                                class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800"
                                            >
                                                <CheckIcon class="h-3 w-3 mr-1" />
                                                Configured
                                            </span>
                                            <span
                                                v-else
                                                class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-600"
                                            >
                                                Not Set
                                            </span>
                                        </div>
                                        
                                        <p class="text-sm text-gray-600 mb-3">
                                            {{ setting.description }}
                                        </p>

                                        <!-- Password Input with Toggle -->
                                        <div class="relative">
                                            <input
                                                :id="setting.key"
                                                :type="visiblePasswords[setting.key] ? 'text' : 'password'"
                                                :value="form.settings.find(s => s.key === setting.key)?.value"
                                                @input="updateSetting(setting.key, $event.target.value)"
                                                :placeholder="form.settings.find(s => s.key === setting.key)?.value ? '••••••••••••••••••••' : `Enter ${setting.key.split('_').pop()}`"
                                                class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm pr-10 font-mono text-sm"
                                            >
                                            <button
                                                type="button"
                                                @click="togglePasswordVisibility(setting.key)"
                                                class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-gray-600 transition-colors"
                                            >
                                                <EyeIcon v-if="!visiblePasswords[setting.key]" class="h-5 w-5" />
                                                <EyeSlashIcon v-else class="h-5 w-5" />
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Standard Settings Layout for Other Tabs -->
                        <div v-else class="space-y-6">
                            <div
                                v-for="setting in activeSettings"
                                :key="setting.key"
                                class="pb-6 border-b border-gray-100 last:border-0"
                            >
                                <div class="flex items-start justify-between gap-4">
                                    <div class="flex-1">
                                        <label :for="setting.key" class="block text-sm font-medium text-gray-900">
                                            {{ setting.key.split('_').map(w => w.charAt(0).toUpperCase() + w.slice(1)).join(' ') }}
                                            <span
                                                v-if="setting.is_public"
                                                class="ml-2 inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-800"
                                            >
                                                Public
                                            </span>
                                        </label>
                                        <p v-if="setting.description" class="mt-1 text-sm text-gray-500">
                                            {{ setting.description }}
                                        </p>

                                        <!-- Boolean Input -->
                                        <div v-if="setting.type === 'boolean'" class="mt-3">
                                            <label class="relative inline-flex items-center cursor-pointer">
                                                <input
                                                    :id="setting.key"
                                                    type="checkbox"
                                                    :checked="form.settings.find(s => s.key === setting.key)?.value === true || form.settings.find(s => s.key === setting.key)?.value === '1'"
                                                    @change="updateSetting(setting.key, $event.target.checked)"
                                                    class="sr-only peer"
                                                >
                                                <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-indigo-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-indigo-600"></div>
                                                <span class="ml-3 text-sm font-medium text-gray-900">
                                                    {{ form.settings.find(s => s.key === setting.key)?.value ? 'Enabled' : 'Disabled' }}
                                                </span>
                                            </label>
                                        </div>

                                        <!-- Textarea for long content -->
                                        <div v-else-if="setting.type === 'textarea'" class="mt-3">
                                            <textarea
                                                :id="setting.key"
                                                :value="form.settings.find(s => s.key === setting.key)?.value"
                                                @input="updateSetting(setting.key, $event.target.value)"
                                                rows="4"
                                                class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                                            ></textarea>
                                        </div>

                                        <!-- Color picker -->
                                        <div v-else-if="setting.type === 'color'" class="mt-3">
                                            <div class="flex items-center gap-3">
                                                <input
                                                    :id="setting.key"
                                                    type="color"
                                                    :value="form.settings.find(s => s.key === setting.key)?.value || '#3B82F6'"
                                                    @input="updateSetting(setting.key, $event.target.value)"
                                                    class="h-10 w-20 rounded border-gray-300 cursor-pointer"
                                                >
                                                <input
                                                    type="text"
                                                    :value="form.settings.find(s => s.key === setting.key)?.value"
                                                    @input="updateSetting(setting.key, $event.target.value)"
                                                    placeholder="#3B82F6"
                                                    class="flex-1 rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm font-mono"
                                                >
                                            </div>
                                        </div>

                                        <!-- Text/Number/Email/URL Inputs -->
                                        <div v-else-if="setting.type !== 'password'" class="mt-3">
                                            <input
                                                :id="setting.key"
                                                :type="getInputType(setting.type)"
                                                :value="form.settings.find(s => s.key === setting.key)?.value"
                                                @input="updateSetting(setting.key, $event.target.value)"
                                                :step="setting.type === 'number' ? 'any' : undefined"
                                                class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                                            >
                                        </div>

                                        <!-- Password Input with Toggle -->
                                        <div v-else class="mt-3">
                                            <div class="relative">
                                                <input
                                                    :id="setting.key"
                                                    :type="visiblePasswords[setting.key] ? 'text' : 'password'"
                                                    :value="form.settings.find(s => s.key === setting.key)?.value"
                                                    @input="updateSetting(setting.key, $event.target.value)"
                                                    :placeholder="form.settings.find(s => s.key === setting.key)?.value ? '••••••••••••' : 'Enter secure value'"
                                                    class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm pr-10"
                                                >
                                                <button
                                                    type="button"
                                                    @click="togglePasswordVisibility(setting.key)"
                                                    class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-gray-600"
                                                >
                                                    <EyeIcon v-if="!visiblePasswords[setting.key]" class="h-5 w-5" />
                                                    <EyeSlashIcon v-else class="h-5 w-5" />
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Submit Button -->
                        <div class="mt-8 flex justify-end gap-3">
                            <button
                                type="submit"
                                :disabled="form.processing"
                                class="inline-flex items-center px-6 py-3 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 disabled:opacity-50 disabled:cursor-not-allowed transition-all"
                            >
                                <CheckIcon class="h-5 w-5 mr-2" />
                                {{ form.processing ? 'Saving...' : 'Save Settings' }}
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Info Panel -->
                <div class="mt-6 bg-blue-50 border border-blue-200 rounded-lg p-4">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <CogIcon class="h-5 w-5 text-blue-400" />
                        </div>
                        <div class="ml-3">
                            <h3 class="text-sm font-medium text-blue-800">Settings Information</h3>
                            <div class="mt-2 text-sm text-blue-700">
                                <ul class="list-disc list-inside space-y-1">
                                    <li>Settings marked as "Public" can be accessed by the frontend application</li>
                                    <li>Changes are cached for performance - clear cache if needed</li>
                                    <li>Use "Reset to Defaults" to restore original platform settings</li>
                                    <li>Boolean settings use toggles for easier management</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>
