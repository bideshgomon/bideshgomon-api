<script setup>
import { ref, computed } from 'vue';
import { useForm } from '@inertiajs/vue3';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import InputLabel from '@/Components/InputLabel.vue';
import Modal from '@/Components/Modal.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import SocialQRCode from '@/Components/Profile/SocialQRCode.vue';

const props = defineProps({
    socialLinks: {
        type: Object,
        default: () => ({})
    }
});

const emit = defineEmits(['saved']);

const showQRModal = ref(false);
const qrPlatform = ref('');
const qrValue = ref('');

const socialPlatforms = [
    { 
        key: 'linkedin', 
        label: 'LinkedIn', 
        icon: '💼',
        placeholder: 'https://linkedin.com/in/username',
        color: 'bg-blue-600'
    },
    { 
        key: 'github', 
        label: 'GitHub', 
        icon: '👨‍💻',
        placeholder: 'https://github.com/username',
        color: 'bg-gray-900'
    },
    { 
        key: 'facebook', 
        label: 'Facebook', 
        icon: '👤',
        placeholder: 'https://facebook.com/username',
        color: 'bg-blue-500'
    },
    { 
        key: 'twitter', 
        label: 'Twitter/X', 
        icon: '🐦',
        placeholder: 'https://twitter.com/username',
        color: 'bg-gray-800'
    },
    { 
        key: 'instagram', 
        label: 'Instagram', 
        icon: '📷',
        placeholder: 'https://instagram.com/username',
        color: 'bg-pink-500'
    },
    { 
        key: 'youtube', 
        label: 'YouTube', 
        icon: '🎥',
        placeholder: 'https://youtube.com/@username',
        color: 'bg-red-600'
    },
    { 
        key: 'tiktok', 
        label: 'TikTok', 
        icon: '🎵',
        placeholder: 'https://tiktok.com/@username',
        color: 'bg-black'
    },
    { 
        key: 'whatsapp', 
        label: 'WhatsApp', 
        icon: '📱',
        placeholder: '+8801XXXXXXXXX',
        color: 'bg-green-500'
    },
    { 
        key: 'telegram', 
        label: 'Telegram', 
        icon: '✈️',
        placeholder: '@username',
        color: 'bg-blue-400'
    },
    { 
        key: 'wechat', 
        label: 'WeChat', 
        icon: '💬',
        placeholder: 'WeChat ID',
        color: 'bg-green-600'
    },
    { 
        key: 'skype', 
        label: 'Skype', 
        icon: '☎️',
        placeholder: 'live:username',
        color: 'bg-blue-400'
    },
    { 
        key: 'discord', 
        label: 'Discord', 
        icon: '🎮',
        placeholder: 'username#1234',
        color: 'bg-indigo-600'
    },
    { 
        key: 'medium', 
        label: 'Medium', 
        icon: '📝',
        placeholder: 'https://medium.com/@username',
        color: 'bg-gray-700'
    },
    { 
        key: 'behance', 
        label: 'Behance', 
        icon: '🎨',
        placeholder: 'https://behance.net/username',
        color: 'bg-blue-600'
    },
    { 
        key: 'dribbble', 
        label: 'Dribbble', 
        icon: '🏀',
        placeholder: 'https://dribbble.com/username',
        color: 'bg-pink-600'
    },
    { 
        key: 'website', 
        label: 'Personal Website', 
        icon: '🌐',
        placeholder: 'https://yourwebsite.com',
        color: 'bg-gray-600'
    },
];

const form = useForm({
    social_links: { ...props.socialLinks }
});

const submit = () => {
    form.post(route('profile.social-links.update'), {
        preserveScroll: true,
        onSuccess: () => {
            emit('saved');
        },
    });
};

const hasAnyLinks = computed(() => {
    return Object.values(form.social_links || {}).some(link => link && link.trim() !== '');
});

const openQRModal = (platform, value) => {
    qrPlatform.value = platform;
    
    // Format the value for QR code
    if (platform === 'whatsapp') {
        // Remove any non-numeric characters and ensure it starts with country code
        const cleanNumber = value.replace(/\D/g, '');
        qrValue.value = `https://wa.me/${cleanNumber}`;
    } else if (platform === 'telegram') {
        // Remove @ if present and create telegram link
        const username = value.replace('@', '');
        qrValue.value = `https://t.me/${username}`;
    }
    
    showQRModal.value = true;
};

const closeQRModal = () => {
    showQRModal.value = false;
    qrPlatform.value = '';
    qrValue.value = '';
};

const canGenerateQR = (platform) => {
    return ['whatsapp', 'telegram'].includes(platform) && form.social_links[platform];
};
</script>

<template>
    <div class="space-y-6">
        <div>
            <h3 class="text-lg font-medium text-gray-900">Social Media & Contact</h3>
            <p class="mt-1 text-sm text-gray-600">
                Add your social media profiles and additional contact methods. These help verify your identity and make it easier for agencies to reach you.
            </p>
        </div>

        <form @submit.prevent="submit" class="space-y-6">
            <div class="grid gap-6 md:grid-cols-2">
                <div v-for="platform in socialPlatforms" :key="platform.key" class="space-y-2">
                    <InputLabel :for="`social-${platform.key}`" class="flex items-center justify-between">
                        <span class="flex items-center gap-2">
                            <span :class="[platform.color, 'inline-flex h-6 w-6 items-center justify-center rounded text-white text-sm']">
                                {{ platform.icon }}
                            </span>
                            {{ platform.label }}
                        </span>
                        <button
                            v-if="canGenerateQR(platform.key)"
                            @click="openQRModal(platform.key, form.social_links[platform.key])"
                            type="button"
                            class="inline-flex items-center gap-1 rounded-md bg-indigo-100 px-2 py-1 text-xs font-medium text-indigo-700 hover:bg-indigo-200"
                            title="Generate QR Code"
                        >
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z" />
                            </svg>
                            QR
                        </button>
                    </InputLabel>
                    <TextInput
                        :id="`social-${platform.key}`"
                        v-model="form.social_links[platform.key]"
                        type="text"
                        class="mt-1 block w-full"
                        :placeholder="platform.placeholder"
                        autocomplete="off"
                    />
                </div>
            </div>

            <!-- Preview Section -->
            <div v-if="hasAnyLinks" class="rounded-lg bg-gray-50 p-4">
                <h4 class="mb-3 text-sm font-medium text-gray-700">Preview</h4>
                <div class="flex flex-wrap gap-2">
                    <a
                        v-for="platform in socialPlatforms"
                        :key="platform.key"
                        v-show="form.social_links[platform.key]"
                        :href="form.social_links[platform.key]"
                        target="_blank"
                        rel="noopener noreferrer"
                        :class="[platform.color, 'inline-flex items-center gap-1 rounded-full px-3 py-1 text-xs font-medium text-white hover:opacity-90']"
                    >
                        <span>{{ platform.icon }}</span>
                        <span>{{ platform.label }}</span>
                    </a>
                </div>
            </div>

            <!-- Tips -->
            <div class="rounded-lg border border-blue-200 bg-blue-50 p-4">
                <h4 class="mb-2 flex items-center gap-2 text-sm font-medium text-blue-900">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    Tips
                </h4>
                <ul class="space-y-1 text-sm text-blue-800">
                    <li>• <strong>LinkedIn:</strong> Helps verify your professional background</li>
                    <li>• <strong>WhatsApp/Telegram:</strong> Faster communication with agencies (click QR button to generate scannable code)</li>
                    <li>• <strong>Facebook:</strong> Common verification method in Bangladesh</li>
                    <li>• All links are optional - add only what you're comfortable sharing</li>
                </ul>
            </div>

            <div class="flex items-center justify-end gap-4">
                <p v-if="form.recentlySuccessful" class="text-sm text-green-600">
                    ✓ Social links saved successfully!
                </p>
                <PrimaryButton :disabled="form.processing">
                    {{ form.processing ? 'Saving...' : 'Save Social Links' }}
                </PrimaryButton>
            </div>
        </form>

        <!-- QR Code Modal -->
        <Modal :show="showQRModal" @close="closeQRModal">
            <div class="p-6">
                <div class="mb-4 flex items-center justify-between">
                    <h3 class="text-lg font-medium text-gray-900">
                        {{ qrPlatform === 'whatsapp' ? '📱 WhatsApp' : '✈️ Telegram' }} QR Code
                    </h3>
                    <button
                        @click="closeQRModal"
                        type="button"
                        class="rounded-md text-gray-400 hover:text-gray-500"
                    >
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
                
                <p class="mb-6 text-sm text-gray-600">
                    Share this QR code with agencies or save it to your device. They can scan it to quickly contact you via {{ qrPlatform === 'whatsapp' ? 'WhatsApp' : 'Telegram' }}.
                </p>

                <div class="flex justify-center">
                    <SocialQRCode
                        v-if="qrValue"
                        :value="qrValue"
                        :platform="qrPlatform"
                        :size="250"
                    />
                </div>

                <div class="mt-6 flex justify-end">
                    <SecondaryButton @click="closeQRModal">
                        Close
                    </SecondaryButton>
                </div>
            </div>
        </Modal>
    </div>
</template>
