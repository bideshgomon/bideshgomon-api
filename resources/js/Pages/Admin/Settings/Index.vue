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
    EyeSlashIcon
} from '@heroicons/vue/24/outline';

const props = defineProps({
    settings: Object,
    groups: Array,
});

const activeTab = ref('general');
const visiblePasswords = ref({});

const togglePasswordVisibility = (key) => {
    visiblePasswords.value[key] = !visiblePasswords.value[key];
};

const groupConfig = {
    general: { icon: CogIcon, label: 'General', color: 'indigo' },
    email: { icon: EnvelopeIcon, label: 'Email', color: 'blue' },
    jobs: { icon: BriefcaseIcon, label: 'Jobs', color: 'purple' },
    wallet: { icon: WalletIcon, label: 'Wallet', color: 'green' },
    features: { icon: FlagIcon, label: 'Features', color: 'orange' },
    social: { icon: ShareIcon, label: 'Social Media', color: 'pink' },
    api: { icon: KeyIcon, label: 'API Keys', color: 'red' },
};

const form = useForm({
    settings: Object.keys(props.settings).flatMap(group =>
        props.settings[group].map(setting => ({
            key: setting.key,
            value: setting.type === 'boolean' 
                ? setting.value === '1' || setting.value === true 
                : setting.value,
            type: setting.type,
        }))
    ),
});

const activeSettings = computed(() => {
    return props.settings[activeTab.value] || [];
});

const submit = () => {
    form.post(route('admin.settings.update'), {
        preserveScroll: true,
        onSuccess: () => {
            // Success message handled by backend
        },
    });
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
                                v-for="group in groups"
                                :key="group"
                                @click="activeTab = group"
                                :class="[
                                    activeTab === group
                                        ? `border-${groupConfig[group]?.color || 'indigo'}-500 text-${groupConfig[group]?.color || 'indigo'}-600`
                                        : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300',
                                    'whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm flex items-center gap-2 transition-colors'
                                ]"
                            >
                                <component :is="groupConfig[group]?.icon || CogIcon" class="h-5 w-5" />
                                {{ groupConfig[group]?.label || group }}
                            </button>
                        </nav>
                    </div>

                    <!-- Settings Form -->
                    <form @submit.prevent="submit" class="p-6">
                        <div class="space-y-6">
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
                                                    :placeholder="form.settings.find(s => s.key === setting.key)?.value ? '••••••••••••' : 'Enter API key'"
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
                                class="inline-flex items-center px-6 py-3 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-700 hover:to-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 disabled:opacity-50 disabled:cursor-not-allowed transition-all"
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
