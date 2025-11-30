<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, router } from '@inertiajs/vue3';
import { ref, computed, onMounted, watch } from 'vue';
import { useProfileCompletion } from '@/Composables/useProfileCompletion';
import RhythmicCard from '@/Components/Rhythmic/RhythmicCard.vue';
import ProgressWave from '@/Components/Rhythmic/ProgressWave.vue';
import { 
    CheckCircleIcon, 
    ExclamationCircleIcon,
    UserCircleIcon,
    DocumentTextIcon,
    AcademicCapIcon,
    BriefcaseIcon,
    SparklesIcon,
    GlobeAltIcon,
    UsersIcon,
    BanknotesIcon,
    LanguageIcon,
    ShieldCheckIcon,
    LockClosedIcon,
    TrashIcon,
    ChevronRightIcon,
    PhoneIcon,
    Cog6ToothIcon,
    HeartIcon,
    ClipboardDocumentCheckIcon,
    BuildingLibraryIcon,
    ChatBubbleLeftRightIcon,
    DocumentDuplicateIcon,
    FolderOpenIcon,
    KeyIcon,
    EyeIcon,
    AdjustmentsHorizontalIcon
} from '@heroicons/vue/24/solid';

// Import existing components
import UpdateProfileInformationForm from './Partials/UpdateProfileInformationForm.vue';
import UpdateProfileDetailsForm from './Partials/UpdateProfileDetailsForm.vue';
import UpdatePasswordForm from './Partials/UpdatePasswordForm.vue';
import DeleteUserForm from './Partials/DeleteUserForm.vue';
import FamilySection from './Partials/FamilySection.vue';
import FinancialSection from './Partials/FinancialSection.vue';
import LanguagesSection from './Partials/LanguagesSection.vue';
import SecuritySection from './Partials/SecuritySection.vue';
import EducationSection from './Partials/EducationSection.vue';
import WorkExperienceSection from './Partials/WorkExperienceSection.vue';
import SkillsSection from './Partials/SkillsSection.vue';
import TravelHistorySection from './Partials/TravelHistorySection.vue';
import PhoneNumbersSection from './Partials/PhoneNumbersSection.vue';
import SocialLinksSection from '@/Components/Profile/SocialLinksSection.vue';
import EmergencyContactSection from '@/Components/Profile/EmergencyContactSection.vue';
import MedicalInformationSection from '@/Components/Profile/MedicalInformationSection.vue';
import ReferencesSection from '@/Components/Profile/ReferencesSection.vue';
import CertificationsSection from '@/Components/Profile/CertificationsSection.vue';
import ProfileCompletenessTracker from '@/Components/Profile/ProfileCompletenessTracker.vue';
import PrivacyDataControl from '@/Components/Profile/PrivacyDataControl.vue';
import PreferencesSettings from '@/Components/Profile/PreferencesSettings.vue';
import DocumentsManagement from '@/Components/Profile/DocumentsManagement.vue';
import PassportManagement from '@/Components/Profile/PassportManagement.vue';
import VisaHistoryManagement from '@/Components/Profile/VisaHistoryManagement.vue';

const props = defineProps({
    mustVerifyEmail: Boolean,
    status: String,
    user: Object,
    userProfile: Object,
    familyMembers: Array,
    languages: Array,
    languageTests: Array,
    securityInformation: Object,
    educations: Array,
    workExperiences: Array,
    skills: Array,
    travelHistory: Array,
    phoneNumbers: Array,
    divisions: Array,
    countries: Array,
    degrees: Array,
    serviceCategories: Array,
    currencies: Array,
    section: String, // Section from URL query parameter
});

// Active section management - null means card view, otherwise editing a specific section
const activeSection = ref(null);

// Mobile detection
const isMobile = ref(false);
const checkMobile = () => {
    isMobile.value = window.innerWidth < 768;
};

onMounted(() => {
    checkMobile();
    window.addEventListener('resize', checkMobile);
    
    // Auto-open section from URL on desktop, keep collapsed on mobile
    if (props.section && !isMobile.value) {
        activeSection.value = props.section;
    }
    
    // Handle browser back/forward buttons
    window.addEventListener('popstate', () => {
        const urlParams = new URLSearchParams(window.location.search);
        const sectionParam = urlParams.get('section');
        
        if (sectionParam && sections.find(s => s.id === sectionParam)) {
            activeSection.value = sectionParam;
        } else {
            activeSection.value = null;
        }
    });
});

// Watch for section prop changes (when URL changes)
watch(() => props.section, (newSection) => {
    if (newSection && sections.find(s => s.id === newSection)) {
        activeSection.value = newSection;
    } else if (!newSection) {
        activeSection.value = null;
    }
});

// Profile completion tracking
const userRef = computed(() => props.user);
const userProfileRef = computed(() => props.userProfile);
const { completion, getCompletionColor, getCompletionBgColor } = useProfileCompletion(userRef, userProfileRef);

// Calculate completion per section for dynamic card colors
const getSectionCompletion = (sectionId) => {
    const user = props.user;
    const profile = props.userProfile;
    
    const completionMap = {
        'basic': () => {
            let completed = 0;
            let total = 4;
            if (profile?.first_name) completed++;
            if (profile?.last_name) completed++;
            if (profile?.name_as_per_passport) completed++;
            if (user?.email) completed++;
            return Math.round((completed / total) * 100);
        },
        'profile': () => {
            let completed = 0;
            let total = 5;
            if (profile?.date_of_birth) completed++;
            if (profile?.gender) completed++;
            if (profile?.nationality) completed++;
            if (profile?.nid) completed++;
            if (profile?.present_address_line && profile?.present_division) completed++;
            return Math.round((completed / total) * 100);
        },
        'phone': () => {
            return props.phoneNumbers?.length > 0 ? 100 : 0;
        },
        'social': () => {
            let completed = 0;
            let total = 4;
            if (profile?.facebook_url) completed++;
            if (profile?.linkedin_url) completed++;
            if (profile?.whatsapp_number) completed++;
            if (profile?.telegram_username) completed++;
            return Math.round((completed / total) * 100);
        },
        'education': () => {
            return props.educations?.length > 0 ? 100 : 0;
        },
        'experience': () => {
            return props.workExperiences?.length > 0 ? 100 : 0;
        },
        'skills': () => {
            return props.skills?.length > 0 ? 100 : 0;
        },
        'languages': () => {
            return props.languages?.length > 0 ? 100 : 0;
        },
        'certifications': () => {
            // Check if certifications data exists in profile
            return profile?.certifications?.length > 0 ? 100 : 0;
        },
        'references': () => {
            // Check if references data exists in profile
            return profile?.references?.length > 0 ? 100 : 0;
        },
        'emergency': () => {
            let completed = 0;
            let total = 3;
            if (profile?.emergency_contact_name) completed++;
            if (profile?.emergency_contact_phone) completed++;
            if (profile?.emergency_contact_relationship) completed++;
            return Math.round((completed / total) * 100);
        },
        'medical': () => {
            let completed = 0;
            let total = 4;
            if (profile?.blood_group) completed++;
            if (profile?.medical_conditions) completed++;
            if (profile?.vaccinations) completed++;
            if (profile?.health_insurance_number) completed++;
            return Math.round((completed / total) * 100);
        },
        'travel': () => {
            return props.travelHistory?.length > 0 ? 100 : 0;
        },
        'documents': () => {
            // Documents section now tracks uploaded files, not passport fields
            // Passport fields moved to dedicated Passport Management section
            return 0; // TODO: implement document upload tracking
        },
        'family': () => {
            return props.familyMembers?.length > 0 ? 100 : 0;
        },
        'financial': () => {
            let completed = 0;
            let total = 3;
            if (profile?.monthly_income_bdt) completed++;
            if (profile?.bank_account_number) completed++;
            if (profile?.bank_name) completed++;
            return Math.round((completed / total) * 100);
        },
        'security': () => {
            return props.securityInformation ? 100 : 0;
        },
        'completeness': () => completion.value.percentage,
        'privacy': () => 50, // Default for settings sections
        'preferences': () => 50,
        'password': () => 100, // Always complete if account exists
        'delete': () => 100
    };
    
    const calculator = completionMap[sectionId];
    return calculator ? calculator() : 50;
};

// Get dynamic solid color based on completion
const getSectionGradient = (sectionId) => {
    const percentage = getSectionCompletion(sectionId);
    
    if (percentage >= 80) {
        return 'bg-green-600'; // Complete
    } else if (percentage >= 50) {
        return 'bg-yellow-600'; // Partial
    } else if (percentage > 0) {
        return 'bg-orange-600'; // Started but incomplete
    } else {
        return 'bg-gray-500'; // Not started
    }
};

// Get dynamic hover border color
const getSectionBorderColor = (sectionId) => {
    const percentage = getSectionCompletion(sectionId);
    
    if (percentage >= 80) {
        return 'hover:border-green-500 dark:hover:border-green-400';
    } else if (percentage >= 50) {
        return 'hover:border-yellow-500 dark:hover:border-yellow-400';
    } else if (percentage > 0) {
        return 'hover:border-orange-500 dark:hover:border-orange-400';
    } else {
        return 'hover:border-gray-500 dark:hover:border-gray-400';
    }
};

// Get dynamic text hover color
const getSectionTextColor = (sectionId) => {
    const percentage = getSectionCompletion(sectionId);
    
    if (percentage >= 80) {
        return 'group-hover:text-green-600 dark:group-hover:text-green-400';
    } else if (percentage >= 50) {
        return 'group-hover:text-yellow-600 dark:group-hover:text-yellow-400';
    } else if (percentage > 0) {
        return 'group-hover:text-orange-600 dark:group-hover:text-orange-400';
    } else {
        return 'group-hover:text-gray-600 dark:group-hover:text-gray-400';
    }
};

const sections = [
    // Personal Information
    { 
        id: 'basic', 
        name: 'Basic Information', 
        icon: UserCircleIcon, 
        description: 'Name, email, and contact information',
        category: 'personal' 
    },
    { 
        id: 'profile', 
        name: 'Profile Details', 
        icon: ClipboardDocumentCheckIcon, 
        description: 'Address, NID, passport, and personal details',
        category: 'personal' 
    },
    { 
        id: 'phone', 
        name: 'Phone Numbers', 
        icon: PhoneIcon, 
        description: 'Manage and verify your phone numbers',
        category: 'personal' 
    },
    { 
        id: 'social', 
        name: 'Social Media & Contact', 
        icon: ChatBubbleLeftRightIcon, 
        description: 'Social profiles and additional contact methods',
        category: 'personal' 
    },
    
    // Professional Profile
    { 
        id: 'education', 
        name: 'Education & Qualifications', 
        icon: AcademicCapIcon, 
        description: 'Academic history and qualifications',
        category: 'professional' 
    },
    { 
        id: 'experience', 
        name: 'Work Experience', 
        icon: BriefcaseIcon, 
        description: 'Employment history and career details',
        category: 'professional' 
    },
    { 
        id: 'skills', 
        name: 'Skills & Expertise', 
        icon: SparklesIcon, 
        description: 'Professional skills and competencies',
        category: 'professional' 
    },
    { 
        id: 'languages', 
        name: 'Language Proficiency', 
        icon: LanguageIcon, 
        description: 'Languages and proficiency levels',
        category: 'professional' 
    },
    { 
        id: 'certifications', 
        name: 'Certifications & Licenses', 
        icon: ClipboardDocumentCheckIcon, 
        description: 'Professional certifications, licenses, and credentials',
        category: 'professional' 
    },
    { 
        id: 'references', 
        name: 'References', 
        icon: UsersIcon, 
        description: 'Professional, academic, and personal references',
        category: 'professional' 
    },
    
    // Safety & Health
    { 
        id: 'emergency', 
        name: 'Emergency Contact', 
        icon: HeartIcon, 
        description: 'Emergency contact person details',
        category: 'safety' 
    },
    { 
        id: 'medical', 
        name: 'Medical Information', 
        icon: ShieldCheckIcon, 
        description: 'Health records, vaccinations, and insurance',
        category: 'safety' 
    },
    
    // Immigration & Documents
    { 
        id: 'travel', 
        name: 'Travel History', 
        icon: GlobeAltIcon, 
        description: 'International travel records',
        category: 'immigration' 
    },
    { 
        id: 'passports', 
        name: 'Passport Management', 
        icon: DocumentTextIcon, 
        description: 'Manage passport details and validity',
        category: 'immigration' 
    },
    { 
        id: 'visa-history', 
        name: 'Visa History', 
        icon: DocumentDuplicateIcon, 
        description: 'Track visa applications and status',
        category: 'immigration' 
    },
    { 
        id: 'documents', 
        name: 'Documents Management', 
        icon: FolderOpenIcon, 
        description: 'Upload and manage important documents',
        category: 'immigration' 
    },
    
    // Family & Financial
    { 
        id: 'family', 
        name: 'Family Information', 
        icon: UsersIcon, 
        description: 'Family members and relationships',
        category: 'family-financial' 
    },
    { 
        id: 'financial', 
        name: 'Financial Information', 
        icon: BanknotesIcon, 
        description: 'Income, assets, and financial details',
        category: 'family-financial' 
    },
    
    // Background & Security
    { 
        id: 'security', 
        name: 'Background Check', 
        icon: BuildingLibraryIcon, 
        description: 'Criminal records and police clearance',
        category: 'background' 
    },
    
    // Account & Settings
    { 
        id: 'completeness', 
        name: 'Profile Completeness', 
        icon: CheckCircleIcon, 
        description: 'Track your profile completion progress',
        category: 'settings' 
    },
    { 
        id: 'public-profile', 
        name: 'Public Profile & Sharing', 
        icon: UserCircleIcon, 
        description: 'Share your profile and generate QR code',
        category: 'settings' 
    },
    { 
        id: 'privacy', 
        name: 'Privacy & Data Control', 
        icon: EyeIcon, 
        description: 'Privacy settings and data control',
        category: 'settings' 
    },
    { 
        id: 'preferences', 
        name: 'Preferences & Settings', 
        icon: AdjustmentsHorizontalIcon, 
        description: 'Personalize your experience',
        category: 'settings' 
    },
    { 
        id: 'password', 
        name: 'Change Password', 
        icon: KeyIcon, 
        description: 'Update your account password',
        category: 'settings' 
    },
    { 
        id: 'delete', 
        name: 'Delete Account', 
        icon: TrashIcon, 
        description: 'Permanently delete your account',
        category: 'settings' 
    },
];

const currentSection = computed(() => sections.find(s => s.id === activeSection.value));

// Function to change active section
const changeSection = (sectionId) => {
    activeSection.value = sectionId;
    
    // Update URL to reflect the current section
    router.visit(route('profile.edit', { section: sectionId }), {
        preserveState: true,
        preserveScroll: false,
        only: ['section'],
        replace: true // Use replace to avoid polluting browser history
    });
    
    // Scroll to top on mobile
    if (window.innerWidth < 1024) {
        window.scrollTo({ top: 0, behavior: 'smooth' });
    }
};

// Function to go back to card view
const backToCards = () => {
    activeSection.value = null;
    
    // Update URL to remove section parameter
    router.visit(route('profile.edit'), {
        preserveState: true,
        preserveScroll: false,
        replace: true
    });
};


</script>

<template>
    <Head title="Edit Profile" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <h2 class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">
                    {{ activeSection ? currentSection?.name : 'Edit Profile' }}
                </h2>
                <button
                    v-if="activeSection"
                    @click="backToCards"
                    class="inline-flex items-center px-4 py-2 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-md font-semibold text-xs text-gray-700 dark:text-gray-300 uppercase tracking-widest shadow-sm hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150"
                >
                    ← Back to Sections
                </button>
            </div>
        </template>

        <div class="py-6 md:py-8">
            <div class="mx-auto max-w-7xl px-2 sm:px-4 lg:px-8">
                <!-- Mobile Back Button (Sticky) -->
                <div 
                    v-if="activeSection && isMobile"
                    class="sticky top-0 z-20 bg-white dark:bg-gray-900 border-b border-gray-200 dark:border-gray-700 -mx-2 px-4 py-3 mb-4 shadow-sm"
                >
                    <button
                        @click="backToCards"
                        class="flex items-center gap-2 text-indigo-600 dark:text-indigo-400 font-semibold text-base active:scale-95 transition-transform"
                    >
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                        </svg>
                        Back to All Sections
                    </button>
                </div>

                <!-- Profile Completion Progress -->
                <RhythmicCard variant="gradient" class="mb-6">
                    <div class="p-6">
                        <div class="flex items-center justify-between mb-4">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-lg bg-white border border-gray-200 flex items-center justify-center">
                                    <CheckCircleIcon v-if="completion.isComplete" class="w-6 h-6 text-gray-400" />
                                    <ExclamationCircleIcon v-else class="w-6 h-6 text-gray-400" />
                                </div>
                                <h3 class="font-display font-bold text-lg text-gray-800">Profile Completion</h3>
                            </div>
                            <span :class="[
                                'text-2xl md:text-3xl font-display font-bold',
                                getCompletionColor(completion.percentage)
                            ]">
                                {{ completion.percentage }}%
                            </span>
                        </div>
                        <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-3 mb-3 relative overflow-hidden">
                            <div
                                class="h-3 rounded-full transition-all duration-500 bg-indigo-600 relative overflow-hidden"
                                :style="{ width: completion.percentage + '%' }"
                            >
                                <div class="absolute inset-0 bg-white/20 animate-shimmer"></div>
                            </div>
                        </div>
                        <div class="flex items-start text-sm">
                            <span class="text-gray-600 dark:text-gray-400 leading-snug">
                                {{ completion.completed }} of {{ completion.total }} essential fields completed
                                <span v-if="!completion.isComplete" class="block text-gray-500 dark:text-gray-500 mt-1">
                                    Complete your profile to unlock all features
                                </span>
                            </span>
                        </div>
                    </div>
                </RhythmicCard>

                <!-- Card View: Show when no section is active -->
                <div v-if="!activeSection" class="space-y-8">
                    <!-- Personal Information -->
                    <div>
                        <div class="flex items-center gap-3 mb-4 px-1">
                            <div class="w-8 h-8 rounded-lg bg-white border border-gray-200 flex items-center justify-center">
                                <UserCircleIcon class="w-5 h-5 text-gray-400" />
                            </div>
                            <h3 class="font-display font-bold text-xl text-gray-800">
                                Personal Information
                            </h3>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3 md:gap-4">
                            <button
                                v-for="section in sections.filter(s => s.category === 'personal')"
                                :key="section.id"
                                @click="changeSection(section.id)"
                                :class="[
                                    'group bg-white dark:bg-gray-800 rounded-lg shadow hover:shadow-md transition-all duration-200 p-4 md:p-5 text-left border-2 border-transparent touch-manipulation',
                                    getSectionBorderColor(section.id)
                                ]"
                            >
                                <div class="flex items-start justify-between">
                                    <div class="flex items-start space-x-3 md:space-x-4 flex-1">
                                        <div :class="[
                                            'flex-shrink-0 w-10 h-10 md:w-12 md:h-12 rounded-lg flex items-center justify-center border border-gray-200 transition-colors duration-200',
                                            getSectionGradient(section.id)
                                        ]">
                                            <component :is="section.icon" class="w-5 h-5 md:w-6 md:h-6 text-white" />
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <div class="flex items-center gap-2 mb-1">
                                                <h4 :class="[
                                                    'text-sm md:text-base font-semibold text-gray-900 dark:text-white transition-colors',
                                                    getSectionTextColor(section.id)
                                                ]">
                                                    {{ section.name }}
                                                </h4>
                                                <span :class="[
                                                    'text-xs font-bold px-2 py-0.5 rounded-full',
                                                    getCompletionColor(getSectionCompletion(section.id))
                                                ]">
                                                    {{ getSectionCompletion(section.id) }}%
                                                </span>
                                            </div>
                                            <p class="text-xs md:text-sm text-gray-600 dark:text-gray-400 line-clamp-2 leading-snug">
                                                {{ section.description }}
                                            </p>
                                        </div>
                                    </div>
                                    <ChevronRightIcon :class="[
                                        'w-5 h-5 text-gray-400 transition-colors flex-shrink-0 ml-2',
                                        getSectionTextColor(section.id)
                                    ]" />
                                </div>
                            </button>
                        </div>
                    </div>

                    <!-- Professional Profile -->
                    <div>
                        <div class="flex items-center gap-3 mb-4 px-1">
                            <div class="w-8 h-8 rounded-lg bg-white border border-gray-200 flex items-center justify-center">
                                <BriefcaseIcon class="w-5 h-5 text-gray-400" />
                            </div>
                            <h3 class="font-display font-bold text-xl text-gray-800">
                                Professional Profile
                            </h3>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3 md:gap-4">
                            <button
                                v-for="section in sections.filter(s => s.category === 'professional')"
                                :key="section.id"
                                @click="changeSection(section.id)"
                                :class="[
                                    'group bg-white dark:bg-gray-800 rounded-lg shadow hover:shadow-md transition-all duration-200 p-4 md:p-5 text-left border-2 border-transparent touch-manipulation',
                                    getSectionBorderColor(section.id)
                                ]"
                            >
                                <div class="flex items-start justify-between">
                                    <div class="flex items-start space-x-3 md:space-x-4 flex-1">
                                        <div :class="[
                                            'flex-shrink-0 w-10 h-10 md:w-12 md:h-12 rounded-lg flex items-center justify-center border border-gray-200 transition-colors duration-200',
                                            getSectionGradient(section.id)
                                        ]">
                                            <component :is="section.icon" class="w-5 h-5 md:w-6 md:h-6 text-white" />
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <div class="flex items-center gap-2 mb-1">
                                                <h4 :class="[
                                                    'text-sm md:text-base font-semibold text-gray-900 dark:text-white transition-colors',
                                                    getSectionTextColor(section.id)
                                                ]">
                                                    {{ section.name }}
                                                </h4>
                                                <span :class="[
                                                    'text-xs font-bold px-2 py-0.5 rounded-full',
                                                    getCompletionColor(getSectionCompletion(section.id))
                                                ]">
                                                    {{ getSectionCompletion(section.id) }}%
                                                </span>
                                            </div>
                                            <p class="text-xs md:text-sm text-gray-600 dark:text-gray-400 line-clamp-2 leading-snug">
                                                {{ section.description }}
                                            </p>
                                        </div>
                                    </div>
                                    <ChevronRightIcon :class="[
                                        'w-5 h-5 text-gray-400 transition-colors flex-shrink-0 ml-2',
                                        getSectionTextColor(section.id)
                                    ]" />
                                </div>
                            </button>
                        </div>
                    </div>

                    <!-- Safety & Health -->
                    <div>
                        <div class="flex items-center gap-3 mb-4 px-1">
                            <div class="w-8 h-8 rounded-lg bg-white border border-gray-200 flex items-center justify-center">
                                <HeartIcon class="w-5 h-5 text-gray-400" />
                            </div>
                            <h3 class="font-display font-bold text-xl text-gray-800">
                                Safety & Health
                            </h3>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3 md:gap-4">
                            <button
                                v-for="section in sections.filter(s => s.category === 'safety')"
                                :key="section.id"
                                @click="changeSection(section.id)"
                                :class="[
                                    'group bg-white dark:bg-gray-800 rounded-lg shadow hover:shadow-md transition-all duration-200 p-4 md:p-5 text-left border-2 border-transparent touch-manipulation',
                                    getSectionBorderColor(section.id)
                                ]"
                            >
                                <div class="flex items-start justify-between">
                                    <div class="flex items-start space-x-3 md:space-x-4 flex-1">
                                        <div :class="[
                                            'flex-shrink-0 w-10 h-10 md:w-12 md:h-12 rounded-lg flex items-center justify-center border border-gray-200 transition-colors duration-200',
                                            getSectionGradient(section.id)
                                        ]">
                                            <component :is="section.icon" class="w-5 h-5 md:w-6 md:h-6 text-white" />
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <div class="flex items-center gap-2 mb-1">
                                                <h4 :class="[
                                                    'text-sm md:text-base font-semibold text-gray-900 dark:text-white transition-colors',
                                                    getSectionTextColor(section.id)
                                                ]">
                                                    {{ section.name }}
                                                </h4>
                                                <span :class="[
                                                    'text-xs font-bold px-2 py-0.5 rounded-full',
                                                    getCompletionColor(getSectionCompletion(section.id))
                                                ]">
                                                    {{ getSectionCompletion(section.id) }}%
                                                </span>
                                            </div>
                                            <p class="text-xs md:text-sm text-gray-600 dark:text-gray-400 line-clamp-2 leading-snug">
                                                {{ section.description }}
                                            </p>
                                        </div>
                                    </div>
                                    <ChevronRightIcon :class="[
                                        'w-5 h-5 text-gray-400 transition-colors flex-shrink-0 ml-2',
                                        getSectionTextColor(section.id)
                                    ]" />
                                </div>
                            </button>
                        </div>
                    </div>

                    <!-- Immigration & Documents -->
                    <div>
                        <div class="flex items-center gap-3 mb-4 px-1">
                            <div class="w-8 h-8 rounded-lg bg-white border border-gray-200 flex items-center justify-center">
                                <GlobeAltIcon class="w-5 h-5 text-gray-400" />
                            </div>
                            <h3 class="font-display font-bold text-xl text-gray-800">
                                Immigration & Documents
                            </h3>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3 md:gap-4">
                            <button
                                v-for="section in sections.filter(s => s.category === 'immigration')"
                                :key="section.id"
                                @click="changeSection(section.id)"
                                :class="[
                                    'group bg-white dark:bg-gray-800 rounded-lg shadow hover:shadow-md transition-all duration-200 p-4 md:p-5 text-left border-2 border-transparent touch-manipulation',
                                    getSectionBorderColor(section.id)
                                ]"
                            >
                                <div class="flex items-start justify-between">
                                    <div class="flex items-start space-x-3 md:space-x-4 flex-1">
                                        <div :class="[
                                            'flex-shrink-0 w-10 h-10 md:w-12 md:h-12 rounded-lg flex items-center justify-center border border-gray-200 transition-colors duration-200',
                                            getSectionGradient(section.id)
                                        ]">
                                            <component :is="section.icon" class="w-5 h-5 md:w-6 md:h-6 text-white" />
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <div class="flex items-center gap-2 mb-1">
                                                <h4 :class="[
                                                    'text-sm md:text-base font-semibold text-gray-900 dark:text-white transition-colors',
                                                    getSectionTextColor(section.id)
                                                ]">
                                                    {{ section.name }}
                                                </h4>
                                                <span :class="[
                                                    'text-xs font-bold px-2 py-0.5 rounded-full',
                                                    getCompletionColor(getSectionCompletion(section.id))
                                                ]">
                                                    {{ getSectionCompletion(section.id) }}%
                                                </span>
                                            </div>
                                            <p class="text-xs md:text-sm text-gray-600 dark:text-gray-400 line-clamp-2 leading-snug">
                                                {{ section.description }}
                                            </p>
                                        </div>
                                    </div>
                                    <ChevronRightIcon :class="[
                                        'w-5 h-5 text-gray-400 transition-colors flex-shrink-0 ml-2',
                                        getSectionTextColor(section.id)
                                    ]" />
                                </div>
                            </button>
                        </div>
                    </div>

                    <!-- Family & Financial -->
                    <div>
                        <div class="flex items-center gap-3 mb-4 px-1">
                            <div class="w-8 h-8 rounded-lg bg-white border border-gray-200 flex items-center justify-center">
                                <UsersIcon class="w-5 h-5 text-gray-400" />
                            </div>
                            <h3 class="font-display font-bold text-xl text-gray-800">
                                Family & Financial
                            </h3>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3 md:gap-4">
                            <button
                                v-for="section in sections.filter(s => s.category === 'family-financial')"
                                :key="section.id"
                                @click="changeSection(section.id)"
                                :class="[
                                    'group bg-white dark:bg-gray-800 rounded-lg shadow hover:shadow-md transition-all duration-200 p-4 md:p-5 text-left border-2 border-transparent touch-manipulation',
                                    getSectionBorderColor(section.id)
                                ]"
                            >
                                <div class="flex items-start justify-between">
                                    <div class="flex items-start space-x-3 md:space-x-4 flex-1">
                                        <div :class="[
                                            'flex-shrink-0 w-10 h-10 md:w-12 md:h-12 rounded-lg flex items-center justify-center border border-gray-200 transition-colors duration-200',
                                            getSectionGradient(section.id)
                                        ]">
                                            <component :is="section.icon" class="w-5 h-5 md:w-6 md:h-6 text-white" />
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <div class="flex items-center gap-2 mb-1">
                                                <h4 :class="[
                                                    'text-sm md:text-base font-semibold text-gray-900 dark:text-white transition-colors',
                                                    getSectionTextColor(section.id)
                                                ]">
                                                    {{ section.name }}
                                                </h4>
                                                <span :class="[
                                                    'text-xs font-bold px-2 py-0.5 rounded-full',
                                                    getCompletionColor(getSectionCompletion(section.id))
                                                ]">
                                                    {{ getSectionCompletion(section.id) }}%
                                                </span>
                                            </div>
                                            <p class="text-xs md:text-sm text-gray-600 dark:text-gray-400 line-clamp-2 leading-snug">
                                                {{ section.description }}
                                            </p>
                                        </div>
                                    </div>
                                    <ChevronRightIcon :class="[
                                        'w-5 h-5 text-gray-400 transition-colors flex-shrink-0 ml-2',
                                        getSectionTextColor(section.id)
                                    ]" />
                                </div>
                            </button>
                        </div>
                    </div>

                    <!-- Background & Security -->
                    <div>
                        <div class="flex items-center gap-3 mb-4 px-1">
                            <div class="w-8 h-8 rounded-lg bg-white border border-gray-200 flex items-center justify-center">
                                <ShieldCheckIcon class="w-5 h-5 text-gray-400" />
                            </div>
                            <h3 class="font-display font-bold text-xl text-gray-800">
                                Background & Security
                            </h3>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3 md:gap-4">
                            <button
                                v-for="section in sections.filter(s => s.category === 'background')"
                                :key="section.id"
                                @click="changeSection(section.id)"
                                :class="[
                                    'group bg-white dark:bg-gray-800 rounded-lg shadow hover:shadow-md transition-all duration-200 p-4 md:p-5 text-left border-2 border-transparent touch-manipulation',
                                    getSectionBorderColor(section.id)
                                ]"
                            >
                                <div class="flex items-start justify-between">
                                    <div class="flex items-start space-x-3 md:space-x-4 flex-1">
                                        <div :class="[
                                            'flex-shrink-0 w-10 h-10 md:w-12 md:h-12 rounded-lg flex items-center justify-center border border-gray-200 transition-colors duration-200',
                                            getSectionGradient(section.id)
                                        ]">
                                            <component :is="section.icon" class="w-5 h-5 md:w-6 md:h-6 text-white" />
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <div class="flex items-center gap-2 mb-1">
                                                <h4 :class="[
                                                    'text-sm md:text-base font-semibold text-gray-900 dark:text-white transition-colors',
                                                    getSectionTextColor(section.id)
                                                ]">
                                                    {{ section.name }}
                                                </h4>
                                                <span :class="[
                                                    'text-xs font-bold px-2 py-0.5 rounded-full',
                                                    getCompletionColor(getSectionCompletion(section.id))
                                                ]">
                                                    {{ getSectionCompletion(section.id) }}%
                                                </span>
                                            </div>
                                            <p class="text-xs md:text-sm text-gray-600 dark:text-gray-400 line-clamp-2 leading-snug">
                                                {{ section.description }}
                                            </p>
                                        </div>
                                    </div>
                                    <ChevronRightIcon :class="[
                                        'w-5 h-5 text-gray-400 transition-colors flex-shrink-0 ml-2',
                                        getSectionTextColor(section.id)
                                    ]" />
                                </div>
                            </button>
                        </div>
                    </div>

                    <!-- Account & Settings -->
                    <div>
                        <div class="flex items-center gap-3 mb-4 px-1">
                            <div class="w-8 h-8 rounded-lg bg-white border border-gray-200 flex items-center justify-center">
                                <Cog6ToothIcon class="w-5 h-5 text-gray-400" />
                            </div>
                            <h3 class="font-display font-bold text-xl text-gray-800">
                                Account & Settings
                            </h3>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3 md:gap-4">
                            <button
                                v-for="section in sections.filter(s => s.category === 'settings')"
                                :key="section.id"
                                @click="changeSection(section.id)"
                                :class="[
                                    'group rounded-lg shadow hover:shadow-md transition-all duration-200 p-4 md:p-5 text-left border-2 border-transparent touch-manipulation',
                                    section.id === 'delete' 
                                        ? 'bg-red-50 dark:bg-red-900/20 hover:border-red-500 dark:hover:border-red-400' 
                                        : 'bg-white dark:bg-gray-800 ' + getSectionBorderColor(section.id)
                                ]"
                            >
                                <div class="flex items-start justify-between">
                                    <div class="flex items-start space-x-3 md:space-x-4 flex-1">
                                        <div :class="[
                                            'flex-shrink-0 w-10 h-10 md:w-12 md:h-12 rounded-lg flex items-center justify-center border transition-colors duration-200',
                                            section.id === 'delete' 
                                                ? 'bg-red-600 border-red-700' 
                                                : 'border-gray-200 ' + getSectionGradient(section.id)
                                        ]">
                                            <component :is="section.icon" class="w-5 h-5 md:w-6 md:h-6 text-white" />
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <div class="flex items-center gap-2 mb-1">
                                                <h4 :class="[
                                                    'text-sm md:text-base font-semibold transition-colors',
                                                    section.id === 'delete'
                                                        ? 'text-red-900 dark:text-red-200 group-hover:text-red-600 dark:group-hover:text-red-400'
                                                        : 'text-gray-900 dark:text-white ' + getSectionTextColor(section.id)
                                                ]">
                                                    {{ section.name }}
                                                </h4>
                                                <span v-if="section.id !== 'delete'" :class="[
                                                    'text-xs font-bold px-2 py-0.5 rounded-full',
                                                    getCompletionColor(getSectionCompletion(section.id))
                                                ]">
                                                    {{ getSectionCompletion(section.id) }}%
                                                </span>
                                            </div>
                                            <p class="text-xs md:text-sm text-gray-600 dark:text-gray-400 line-clamp-2 leading-snug">
                                                {{ section.description }}
                                            </p>
                                        </div>
                                    </div>
                                    <ChevronRightIcon :class="[
                                        'w-5 h-5 transition-colors flex-shrink-0 ml-2',
                                        section.id === 'delete'
                                            ? 'text-red-400 group-hover:text-red-600 dark:group-hover:text-red-400'
                                            : 'text-gray-400 ' + getSectionTextColor(section.id)
                                    ]" />
                                </div>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Section Detail View: Show when a section is active -->
                <div v-else class="bg-white dark:bg-gray-800 shadow-lg sm:rounded-lg overflow-hidden">
                    <!-- Section Content -->
                    <div class="p-4 md:p-6">
                        <!-- Basic Information -->
                        <UpdateProfileInformationForm
                            v-if="activeSection === 'basic'"
                            :must-verify-email="mustVerifyEmail"
                            :status="status"
                            :user-profile="userProfile"
                        />

                        <!-- Phone Numbers -->
                        <PhoneNumbersSection
                            v-if="activeSection === 'phone'"
                        />

                        <!-- Social Media & Contact -->
                        <SocialLinksSection
                            v-if="activeSection === 'social'"
                            :social-links="userProfile?.social_links || {}"
                        />

                        <!-- Emergency Contact -->
                        <EmergencyContactSection
                            v-if="activeSection === 'emergency'"
                            :emergency-contact="userProfile"
                        />

                        <!-- Medical Information -->
                        <MedicalInformationSection
                            v-if="activeSection === 'medical'"
                            :medical-info="userProfile"
                        />

                        <!-- Profile Details -->
                        <UpdateProfileDetailsForm
                            v-if="activeSection === 'profile'"
                            :user-profile="userProfile"
                            :divisions="divisions"
                            :countries="countries"
                        />

                        <!-- Education Section -->
                        <EducationSection
                            v-if="activeSection === 'education'"
                            :user-profile="userProfile"
                            :educations="educations"
                            :degrees="degrees"
                            :countries="countries"
                        />

                        <!-- Work Experience Section -->
                        <WorkExperienceSection
                            v-if="activeSection === 'experience'"
                            :user-profile="userProfile"
                            :work-experiences="workExperiences"
                            :countries="countries"
                        />

                        <!-- Skills Section -->
                        <SkillsSection
                            v-if="activeSection === 'skills'"
                            :user-profile="userProfile"
                            :user-skills="skills"
                        />

                        <!-- Travel History Section -->
                        <TravelHistorySection
                            v-if="activeSection === 'travel'"
                            :travel-history="travelHistory"
                        />

                        <!-- References Section -->
                        <ReferencesSection
                            v-if="activeSection === 'references'"
                            :references="userProfile"
                        />

                        <!-- Certifications Section -->
                        <CertificationsSection
                            v-if="activeSection === 'certifications'"
                            :certifications="userProfile"
                        />

                        <!-- Profile Completeness Tracker -->
                        <ProfileCompletenessTracker
                            v-if="activeSection === 'completeness'"
                            :user="user"
                            :user-profile="userProfile"
                            :family-members="familyMembers"
                            :languages="languages"
                            :educations="educations"
                            :work-experiences="workExperiences"
                            :skills="skills"
                            :travel-history="travelHistory"
                            :phone-numbers="phoneNumbers"
                        />

                        <!-- Family Section -->
                        <FamilySection
                            v-if="activeSection === 'family'"
                            :user-profile="userProfile"
                        />

                        <!-- Financial Section -->
                        <FinancialSection
                            v-if="activeSection === 'financial'"
                            :user-profile="userProfile"
                        />

                        <!-- Languages Section -->
                        <LanguagesSection
                            v-if="activeSection === 'languages'"
                            :user-profile="userProfile"
                        />

                        <!-- Privacy & Data Control -->
                        <PrivacyDataControl
                            v-if="activeSection === 'privacy'"
                            :user-profile="userProfile"
                        />

                        <!-- Preferences & Settings -->
                        <PreferencesSettings
                            v-if="activeSection === 'preferences'"
                            :user-profile="userProfile"
                            :countries="countries"
                            :service-categories="serviceCategories"
                            :currencies="currencies"
                            :languages="languages"
                        />

                        <!-- Documents Management -->
                        <DocumentsManagement
                            v-if="activeSection === 'documents'"
                            :user-profile="userProfile"
                        />

                        <!-- Passport Management -->
                        <PassportManagement
                            v-if="activeSection === 'passports'"
                            :user-profile="userProfile"
                        />

                        <!-- Visa History Management -->
                        <VisaHistoryManagement
                            v-if="activeSection === 'visa-history'"
                            :user-profile="userProfile"
                        />

                        <!-- Security Section -->
                        <SecuritySection
                            v-if="activeSection === 'security'"
                            :user-profile="userProfile"
                        />

                        <!-- Public Profile & Sharing -->
                        <div v-if="activeSection === 'public-profile'" class="space-y-6">
                            <div class="bg-white dark:bg-gray-800 shadow sm:rounded-lg">
                                <div class="px-4 py-5 sm:p-6">
                                    <h3 class="text-lg font-medium text-gray-900 dark:text-white">
                                        Public Profile & Sharing
                                    </h3>
                                    <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                                        Share your profile with others and generate a QR code for easy access.
                                    </p>
                                    <div class="mt-6">
                                        <a
                                            :href="route('profile.public.settings')"
                                            class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150"
                                        >
                                            Manage Public Profile
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Privacy & Data Control -->
                        <PrivacyDataControl
                            v-if="activeSection === 'privacy'"
                            :user="user"
                            :user-profile="userProfile"
                        />

                        <!-- Preferences & Settings -->
                        <PreferencesSettings
                            v-if="activeSection === 'preferences'"
                            :user-profile="userProfile"
                        />

                        <!-- Password -->
                        <UpdatePasswordForm
                            v-if="activeSection === 'password'"
                        />

                        <!-- Delete Account -->
                        <DeleteUserForm
                            v-if="activeSection === 'delete'"
                        />
                    </div>

                    <!-- Bottom Back Button (Mobile Friendly) -->
                    <div class="border-t border-gray-200 dark:border-gray-700 px-4 py-4 bg-gray-50 dark:bg-gray-900/50 md:hidden">
                        <button
                            @click="backToCards"
                            class="w-full inline-flex items-center justify-center px-4 py-3 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-lg font-semibold text-sm text-gray-700 dark:text-gray-300 uppercase tracking-widest shadow-sm hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150"
                        >
                            ← Back to All Sections
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<style scoped>
/* Mobile-specific improvements */
@media (max-width: 640px) {
    /* Ensure modals don't overflow on mobile */
    :deep(.max-w-2xl),
    :deep(.max-w-3xl),
    :deep(.max-w-xl) {
        max-width: 100% !important;
        margin: 0 !important;
    }
    
    /* Better modal padding on mobile */
    :deep(.modal-content) {
        padding: 1rem !important;
    }
    
    /* Stack form grids on mobile */
    :deep(.grid.grid-cols-2),
    :deep(.grid.grid-cols-3),
    :deep(.md\\:grid-cols-2),
    :deep(.md\\:grid-cols-3) {
        grid-template-columns: 1fr !important;
    }
    
    /* Better button sizing on mobile */
    :deep(button),
    :deep(.btn) {
        min-height: 44px;
        font-size: 0.875rem;
    }
    
    /* Better input sizing */
    :deep(input),
    :deep(textarea),
    :deep(select) {
        font-size: 16px !important; /* Prevents zoom on iOS */
    }
}

/* Improve section card spacing */
:deep(.space-y-4 > *) {
    margin-top: 1rem;
}

@media (min-width: 768px) {
    :deep(.space-y-4 > *) {
        margin-top: 1rem;
    }
}
</style>

