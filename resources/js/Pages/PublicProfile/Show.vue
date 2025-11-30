<script setup>
import { Head } from '@inertiajs/vue3';
import { ref } from 'vue';
import {
    UserIcon,
    AcademicCapIcon,
    BriefcaseIcon,
    GlobeAltIcon,
    ChatBubbleLeftRightIcon,
    EyeIcon,
    ShareIcon,
} from '@heroicons/vue/24/outline';
import Modal from '@/Components/Modal.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import SocialQRCode from '@/Components/Profile/SocialQRCode.vue';

const props = defineProps({
    profile: Object,
    totalViews: Number,
});

const hasBasicInfo = !!props.profile.basic_info;
const hasEducation = props.profile.education && props.profile.education.length > 0;
const hasWorkExperience = props.profile.work_experience && props.profile.work_experience.length > 0;
const hasLanguages = props.profile.languages && props.profile.languages.length > 0;
const hasTravelHistory = props.profile.travel_history && props.profile.travel_history.length > 0;
const hasSkills = props.profile.skills && props.profile.skills.length > 0;
const hasSocialLinks = props.profile.social_links && Object.keys(props.profile.social_links).length > 0;

const socialPlatforms = {
    linkedin: { label: 'LinkedIn', icon: '💼', color: 'blue-600', urlPrefix: '' },
    github: { label: 'GitHub', icon: '👨‍💻', color: 'gray-900', urlPrefix: '' },
    facebook: { label: 'Facebook', icon: '👤', color: 'blue-500', urlPrefix: 'https://facebook.com/' },
    twitter: { label: 'Twitter', icon: '🐦', color: 'gray-800', urlPrefix: '' },
    instagram: { label: 'Instagram', icon: '📷', color: 'pink-500', urlPrefix: 'https://instagram.com/' },
    youtube: { label: 'YouTube', icon: '🎥', color: 'red-600', urlPrefix: '' },
    tiktok: { label: 'TikTok', icon: '🎵', color: 'gray-900', urlPrefix: '' },
    whatsapp: { label: 'WhatsApp', icon: '📱', color: 'green-500', urlPrefix: 'https://wa.me/' },
    telegram: { label: 'Telegram', icon: '✈️', color: 'blue-400', urlPrefix: 'https://t.me/' },
    wechat: { label: 'WeChat', icon: '💬', color: 'green-600', urlPrefix: '' },
    skype: { label: 'Skype', icon: '☎️', color: 'blue-400', urlPrefix: 'skype:' },
    discord: { label: 'Discord', icon: '🎮', color: 'indigo-600', urlPrefix: '' },
    medium: { label: 'Medium', icon: '📝', color: 'gray-700', urlPrefix: '' },
    behance: { label: 'Behance', icon: '🎨', color: 'blue-600', urlPrefix: '' },
    dribbble: { label: 'Dribbble', icon: '🏀', color: 'pink-600', urlPrefix: '' },
    website: { label: 'Website', icon: '🌐', color: 'gray-600', urlPrefix: '' },
};

const getSocialUrl = (platform, value) => {
    const config = socialPlatforms[platform];
    if (!config) return value;
    
    // If value already starts with http/https or the prefix, return as is
    if (value.startsWith('http://') || value.startsWith('https://') || value.startsWith(config.urlPrefix)) {
        return value;
    }
    
    // Otherwise, add the prefix
    return config.urlPrefix + value;
};

const showQRModal = ref(false);
const qrPlatform = ref('');
const qrValue = ref('');

const openQRModal = (platform, value) => {
    qrPlatform.value = platform;
    
    if (platform === 'whatsapp') {
        const cleanNumber = value.replace(/\D/g, '');
        qrValue.value = `https://wa.me/${cleanNumber}`;
    } else if (platform === 'telegram') {
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

const canShowQR = (platform) => {
    return ['whatsapp', 'telegram'].includes(platform);
};
</script>

<template>
    <Head :title="`${profile.name} - Public Profile`" />

    <div class="min-h-screen bg-gray-50 py-12">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Header Card -->
            <div class="bg-blue-600 rounded-xl shadow-lg overflow-hidden mb-8 border-2 border-blue-700">
                <div class="p-8 text-white">
                    <div class="flex items-start justify-between">
                        <div>
                            <h1 class="text-4xl font-bold mb-2">{{ profile.name }}</h1>
                            <p v-if="profile.headline" class="text-xl text-blue-100 mb-4">
                                {{ profile.headline }}
                            </p>
                            <div class="flex items-center text-blue-100">
                                <EyeIcon class="h-5 w-5 mr-2" />
                                <span>{{ totalViews }} profile views</span>
                            </div>
                        </div>
                        <div class="bg-white/20 backdrop-blur-sm rounded-full p-4">
                            <UserIcon class="h-16 w-16 text-white" />
                        </div>
                    </div>
                    
                    <p v-if="profile.bio" class="mt-6 text-indigo-50 leading-relaxed">
                        {{ profile.bio }}
                    </p>
                </div>
            </div>

            <!-- Basic Info -->
            <div v-if="hasBasicInfo" class="bg-white rounded-lg shadow mb-6">
                <div class="p-6 border-b border-gray-200">
                    <div class="flex items-center">
                        <UserIcon class="h-6 w-6 text-indigo-600 mr-3" />
                        <h2 class="text-xl font-bold text-gray-900">Basic Information</h2>
                    </div>
                </div>
                <div class="p-6">
                    <dl class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div v-if="profile.basic_info.full_name">
                            <dt class="text-sm font-medium text-gray-500">Full Name</dt>
                            <dd class="mt-1 text-lg text-gray-900">{{ profile.basic_info.full_name }}</dd>
                        </div>
                        <div v-if="profile.basic_info.nationality">
                            <dt class="text-sm font-medium text-gray-500">Nationality</dt>
                            <dd class="mt-1 text-lg text-gray-900">{{ profile.basic_info.nationality }}</dd>
                        </div>
                        <div v-if="profile.basic_info.city">
                            <dt class="text-sm font-medium text-gray-500">Location</dt>
                            <dd class="mt-1 text-lg text-gray-900">{{ profile.basic_info.city }}, {{ profile.basic_info.country }}</dd>
                        </div>
                        <div v-if="profile.basic_info.email">
                            <dt class="text-sm font-medium text-gray-500">Email</dt>
                            <dd class="mt-1 text-lg text-gray-900">{{ profile.basic_info.email }}</dd>
                        </div>
                        <div v-if="profile.basic_info.phone">
                            <dt class="text-sm font-medium text-gray-500">Phone</dt>
                            <dd class="mt-1 text-lg text-gray-900">{{ profile.basic_info.phone }}</dd>
                        </div>
                    </dl>
                </div>
            </div>

            <!-- Education -->
            <div v-if="hasEducation" class="bg-white rounded-lg shadow mb-6">
                <div class="p-6 border-b border-gray-200">
                    <div class="flex items-center">
                        <AcademicCapIcon class="h-6 w-6 text-indigo-600 mr-3" />
                        <h2 class="text-xl font-bold text-gray-900">Education</h2>
                    </div>
                </div>
                <div class="p-6">
                    <div class="space-y-6">
                        <div v-for="(edu, index) in profile.education" :key="index" class="border-l-4 border-indigo-500 pl-4">
                            <h3 class="text-lg font-semibold text-gray-900 capitalize">{{ edu.degree_level }}</h3>
                            <p class="text-indigo-600 font-medium">{{ edu.institution_name }}</p>
                            <p class="text-gray-600">{{ edu.field_of_study }}</p>
                            <div class="mt-2 flex items-center text-sm text-gray-500">
                                <span>{{ edu.start_date }} - {{ edu.end_date }}</span>
                                <span v-if="edu.gpa" class="ml-4">GPA: {{ edu.gpa }}</span>
                            </div>
                            <p v-if="edu.country" class="text-sm text-gray-500">{{ edu.country }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Work Experience -->
            <div v-if="hasWorkExperience" class="bg-white rounded-lg shadow mb-6">
                <div class="p-6 border-b border-gray-200">
                    <div class="flex items-center">
                        <BriefcaseIcon class="h-6 w-6 text-indigo-600 mr-3" />
                        <h2 class="text-xl font-bold text-gray-900">Work Experience</h2>
                    </div>
                </div>
                <div class="p-6">
                    <div class="space-y-6">
                        <div v-for="(work, index) in profile.work_experience" :key="index" class="border-l-4 border-purple-500 pl-4">
                            <h3 class="text-lg font-semibold text-gray-900">{{ work.job_title }}</h3>
                            <p class="text-purple-600 font-medium">{{ work.company_name }}</p>
                            <p class="text-sm text-gray-500">{{ work.start_date }} - {{ work.end_date }}</p>
                            <p v-if="work.city" class="text-sm text-gray-500">{{ work.city }}, {{ work.country }}</p>
                            <p v-if="work.job_description" class="mt-2 text-gray-700 leading-relaxed">{{ work.job_description }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Languages -->
            <div v-if="hasLanguages" class="bg-white rounded-lg shadow mb-6">
                <div class="p-6 border-b border-gray-200">
                    <div class="flex items-center">
                        <ChatBubbleLeftRightIcon class="h-6 w-6 text-indigo-600 mr-3" />
                        <h2 class="text-xl font-bold text-gray-900">Languages</h2>
                    </div>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div v-for="(lang, index) in profile.languages" :key="index" class="border border-gray-200 rounded-lg p-4">
                            <h3 class="font-semibold text-gray-900">{{ lang.language_name }}</h3>
                            <p class="text-sm text-gray-600 capitalize">{{ lang.proficiency_level }}</p>
                            <div v-if="lang.ielts_score || lang.toefl_score" class="mt-2 text-sm">
                                <span v-if="lang.ielts_score" class="inline-block bg-green-100 text-green-800 px-2 py-1 rounded">
                                    IELTS: {{ lang.ielts_score }}
                                </span>
                                <span v-if="lang.toefl_score" class="inline-block bg-blue-100 text-blue-800 px-2 py-1 rounded ml-2">
                                    TOEFL: {{ lang.toefl_score }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Skills -->
            <div v-if="hasSkills" class="bg-white rounded-lg shadow mb-6">
                <div class="p-6 border-b border-gray-200">
                    <h2 class="text-xl font-bold text-gray-900">Skills</h2>
                </div>
                <div class="p-6">
                    <div class="flex flex-wrap gap-2">
                        <span
                            v-for="(skill, index) in profile.skills"
                            :key="index"
                            class="inline-block bg-indigo-100 text-indigo-800 px-3 py-1 rounded-full text-sm font-medium"
                        >
                            {{ skill }}
                        </span>
                    </div>
                </div>
            </div>

            <!-- Social Links -->
            <div v-if="hasSocialLinks" class="bg-white rounded-lg shadow mb-6">
                <div class="p-6 border-b border-gray-200">
                    <div class="flex items-center">
                        <ShareIcon class="h-6 w-6 text-indigo-600 mr-3" />
                        <h2 class="text-xl font-bold text-gray-900">Connect With Me</h2>
                    </div>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4">
                        <div
                            v-for="(value, platform) in profile.social_links"
                            :key="platform"
                            class="relative"
                        >
                            <a
                                :href="getSocialUrl(platform, value)"
                                target="_blank"
                                rel="noopener noreferrer"
                                :class="`flex items-center p-4 border-2 border-${socialPlatforms[platform]?.color} rounded-lg hover:bg-gray-50 transition-colors group`"
                            >
                                <span class="text-2xl mr-3">{{ socialPlatforms[platform]?.icon }}</span>
                                <div class="flex-1 min-w-0">
                                    <p :class="`font-semibold text-${socialPlatforms[platform]?.color} group-hover:underline`">
                                        {{ socialPlatforms[platform]?.label }}
                                    </p>
                                    <p class="text-sm text-gray-600 truncate">{{ value }}</p>
                                </div>
                            </a>
                            <button
                                v-if="canShowQR(platform)"
                                @click="openQRModal(platform, value)"
                                type="button"
                                class="absolute top-2 right-2 p-2 rounded-md bg-white border border-gray-300 shadow-sm hover:bg-gray-50 transition-colors"
                                title="View QR Code"
                            >
                                <svg class="h-4 w-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z" />
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Travel History -->
            <div v-if="hasTravelHistory" class="bg-white rounded-lg shadow mb-6">
                <div class="p-6 border-b border-gray-200">
                    <div class="flex items-center">
                        <GlobeAltIcon class="h-6 w-6 text-indigo-600 mr-3" />
                        <h2 class="text-xl font-bold text-gray-900">Travel History</h2>
                    </div>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                        <div v-for="(travel, index) in profile.travel_history" :key="index" class="text-center">
                            <div class="text-2xl mb-1">🌍</div>
                            <p class="font-semibold text-gray-900">{{ travel.country }}</p>
                            <p class="text-sm text-gray-500">{{ travel.entry_date }}</p>
                            <p class="text-xs text-gray-400 capitalize">{{ travel.purpose }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Footer -->
            <div class="text-center py-8 text-gray-500">
                <p>Powered by <span class="font-semibold text-indigo-600">BideshGomon</span></p>
                <p class="text-sm mt-2">Professional Profile Platform for Bangladesh</p>
            </div>
        </div>

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
                    Scan this QR code to quickly contact {{ profile.name }} via {{ qrPlatform === 'whatsapp' ? 'WhatsApp' : 'Telegram' }}.
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
