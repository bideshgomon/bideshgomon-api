<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';
import { ref, computed, onMounted } from 'vue';
import { useProfileCompletion } from '@/Composables/useProfileCompletion';
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
    ChevronRightIcon
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

const props = defineProps({
    mustVerifyEmail: Boolean,
    status: String,
    user: Object,
    userProfile: Object,
    familyMembers: Array,
    languages: Array,
    securityInformation: Object,
    educations: Array,
    workExperiences: Array,
    skills: Array,
    travelHistory: Array,
    phoneNumbers: Array,
    divisions: Array,
    section: String, // Section from URL query parameter
});

// Active section management - null means card view, otherwise editing a specific section
const activeSection = ref(null);

// Profile completion tracking
const userRef = computed(() => props.user);
const userProfileRef = computed(() => props.userProfile);
const { completion, getCompletionColor, getCompletionBgColor } = useProfileCompletion(userRef, userProfileRef);

const sections = [
    { 
        id: 'basic', 
        name: 'Basic Information', 
        icon: UserCircleIcon, 
        description: 'Name, email, and contact information',
        category: 'essential' 
    },
    { 
        id: 'profile', 
        name: 'Profile Details', 
        icon: DocumentTextIcon, 
        description: 'Address, NID, passport, and personal details',
        category: 'essential' 
    },
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
        id: 'travel', 
        name: 'Travel History', 
        icon: GlobeAltIcon, 
        description: 'International travel and visa records',
        category: 'professional' 
    },
    { 
        id: 'family', 
        name: 'Family Information', 
        icon: UsersIcon, 
        description: 'Family members and relationships',
        category: 'additional' 
    },
    { 
        id: 'financial', 
        name: 'Financial Information', 
        icon: BanknotesIcon, 
        description: 'Income, assets, and financial details',
        category: 'additional' 
    },
    { 
        id: 'languages', 
        name: 'Language Proficiency', 
        icon: LanguageIcon, 
        description: 'Languages and proficiency levels',
        category: 'professional' 
    },
    { 
        id: 'security', 
        name: 'Background & Security', 
        icon: ShieldCheckIcon, 
        description: 'Criminal records and police clearance',
        category: 'additional' 
    },
    { 
        id: 'password', 
        name: 'Password', 
        icon: LockClosedIcon, 
        description: 'Change your account password',
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
    // Scroll to top on mobile
    if (window.innerWidth < 1024) {
        window.scrollTo({ top: 0, behavior: 'smooth' });
    }
};

// Function to go back to card view
const backToCards = () => {
    activeSection.value = null;
};

// Initialize section from URL parameter on mount
onMounted(() => {
    if (props.section && sections.find(s => s.id === props.section)) {
        activeSection.value = props.section;
    }
});
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

        <div class="py-6 md:py-12">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <!-- Profile Completion Progress -->
                <div class="mb-6 bg-white dark:bg-gray-800 shadow-lg sm:rounded-xl overflow-hidden">
                    <div class="p-4 md:p-6">
                        <div class="flex items-center justify-between mb-2">
                            <h3 class="text-sm font-semibold text-gray-900 dark:text-white">Profile Completion</h3>
                            <span :class="[
                                'text-2xl font-bold',
                                getCompletionColor(completion.percentage)
                            ]">
                                {{ completion.percentage }}%
                            </span>
                        </div>
                        <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-3 mb-4">
                            <div
                                :class="[
                                    'h-3 rounded-full transition-all duration-500',
                                    getCompletionBgColor(completion.percentage)
                                ]"
                                :style="{ width: completion.percentage + '%' }"
                            ></div>
                        </div>
                        <div class="flex items-center text-sm">
                            <CheckCircleIcon v-if="completion.isComplete" class="w-5 h-5 text-green-600 mr-2 flex-shrink-0" />
                            <ExclamationCircleIcon v-else class="w-5 h-5 text-yellow-600 mr-2 flex-shrink-0" />
                            <span class="text-gray-600 dark:text-gray-400">
                                {{ completion.completed }} of {{ completion.total }} essential fields completed
                                <span v-if="!completion.isComplete" class="block sm:inline text-gray-500 dark:text-gray-500 mt-1 sm:mt-0">
                                    Complete your profile to unlock all features
                                </span>
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Card View: Show when no section is active -->
                <div v-if="!activeSection" class="space-y-6">
                    <!-- Essential Sections -->
                    <div>
                        <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4 px-2">
                            Essential Information
                        </h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                            <button
                                v-for="section in sections.filter(s => s.category === 'essential')"
                                :key="section.id"
                                @click="changeSection(section.id)"
                                class="group bg-white dark:bg-gray-800 rounded-xl shadow-md hover:shadow-xl transition-all duration-200 p-5 text-left border-2 border-transparent hover:border-indigo-500 dark:hover:border-indigo-400"
                            >
                                <div class="flex items-start justify-between">
                                    <div class="flex items-start space-x-4 flex-1">
                                        <div class="flex-shrink-0 w-12 h-12 bg-gradient-to-br from-indigo-500 to-purple-600 rounded-xl flex items-center justify-center shadow-lg group-hover:scale-110 transition-transform duration-200">
                                            <component :is="section.icon" class="w-6 h-6 text-white" />
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <h4 class="text-base font-semibold text-gray-900 dark:text-white mb-1 group-hover:text-indigo-600 dark:group-hover:text-indigo-400 transition-colors">
                                                {{ section.name }}
                                            </h4>
                                            <p class="text-sm text-gray-600 dark:text-gray-400 line-clamp-2">
                                                {{ section.description }}
                                            </p>
                                        </div>
                                    </div>
                                    <ChevronRightIcon class="w-5 h-5 text-gray-400 group-hover:text-indigo-600 dark:group-hover:text-indigo-400 transition-colors flex-shrink-0 ml-2" />
                                </div>
                            </button>
                        </div>
                    </div>

                    <!-- Professional Sections -->
                    <div>
                        <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4 px-2">
                            Professional Details
                        </h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                            <button
                                v-for="section in sections.filter(s => s.category === 'professional')"
                                :key="section.id"
                                @click="changeSection(section.id)"
                                class="group bg-white dark:bg-gray-800 rounded-xl shadow-md hover:shadow-xl transition-all duration-200 p-5 text-left border-2 border-transparent hover:border-blue-500 dark:hover:border-blue-400"
                            >
                                <div class="flex items-start justify-between">
                                    <div class="flex items-start space-x-4 flex-1">
                                        <div class="flex-shrink-0 w-12 h-12 bg-gradient-to-br from-blue-500 to-cyan-600 rounded-xl flex items-center justify-center shadow-lg group-hover:scale-110 transition-transform duration-200">
                                            <component :is="section.icon" class="w-6 h-6 text-white" />
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <h4 class="text-base font-semibold text-gray-900 dark:text-white mb-1 group-hover:text-blue-600 dark:group-hover:text-blue-400 transition-colors">
                                                {{ section.name }}
                                            </h4>
                                            <p class="text-sm text-gray-600 dark:text-gray-400 line-clamp-2">
                                                {{ section.description }}
                                            </p>
                                        </div>
                                    </div>
                                    <ChevronRightIcon class="w-5 h-5 text-gray-400 group-hover:text-blue-600 dark:group-hover:text-blue-400 transition-colors flex-shrink-0 ml-2" />
                                </div>
                            </button>
                        </div>
                    </div>

                    <!-- Additional Information -->
                    <div>
                        <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4 px-2">
                            Additional Information
                        </h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                            <button
                                v-for="section in sections.filter(s => s.category === 'additional')"
                                :key="section.id"
                                @click="changeSection(section.id)"
                                class="group bg-white dark:bg-gray-800 rounded-xl shadow-md hover:shadow-xl transition-all duration-200 p-5 text-left border-2 border-transparent hover:border-emerald-500 dark:hover:border-emerald-400"
                            >
                                <div class="flex items-start justify-between">
                                    <div class="flex items-start space-x-4 flex-1">
                                        <div class="flex-shrink-0 w-12 h-12 bg-gradient-to-br from-emerald-500 to-teal-600 rounded-xl flex items-center justify-center shadow-lg group-hover:scale-110 transition-transform duration-200">
                                            <component :is="section.icon" class="w-6 h-6 text-white" />
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <h4 class="text-base font-semibold text-gray-900 dark:text-white mb-1 group-hover:text-emerald-600 dark:group-hover:text-emerald-400 transition-colors">
                                                {{ section.name }}
                                            </h4>
                                            <p class="text-sm text-gray-600 dark:text-gray-400 line-clamp-2">
                                                {{ section.description }}
                                            </p>
                                        </div>
                                    </div>
                                    <ChevronRightIcon class="w-5 h-5 text-gray-400 group-hover:text-emerald-600 dark:group-hover:text-emerald-400 transition-colors flex-shrink-0 ml-2" />
                                </div>
                            </button>
                        </div>
                    </div>

                    <!-- Settings -->
                    <div>
                        <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4 px-2">
                            Account Settings
                        </h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                            <button
                                v-for="section in sections.filter(s => s.category === 'settings')"
                                :key="section.id"
                                @click="changeSection(section.id)"
                                :class="[
                                    'group rounded-xl shadow-md hover:shadow-xl transition-all duration-200 p-5 text-left border-2 border-transparent',
                                    section.id === 'delete' 
                                        ? 'bg-red-50 dark:bg-red-900/20 hover:border-red-500 dark:hover:border-red-400' 
                                        : 'bg-white dark:bg-gray-800 hover:border-gray-500 dark:hover:border-gray-400'
                                ]"
                            >
                                <div class="flex items-start justify-between">
                                    <div class="flex items-start space-x-4 flex-1">
                                        <div :class="[
                                            'flex-shrink-0 w-12 h-12 rounded-xl flex items-center justify-center shadow-lg group-hover:scale-110 transition-transform duration-200',
                                            section.id === 'delete' 
                                                ? 'bg-gradient-to-br from-red-500 to-pink-600' 
                                                : 'bg-gradient-to-br from-gray-500 to-gray-700'
                                        ]">
                                            <component :is="section.icon" class="w-6 h-6 text-white" />
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <h4 :class="[
                                                'text-base font-semibold mb-1 transition-colors',
                                                section.id === 'delete'
                                                    ? 'text-red-900 dark:text-red-200 group-hover:text-red-600 dark:group-hover:text-red-400'
                                                    : 'text-gray-900 dark:text-white group-hover:text-gray-600 dark:group-hover:text-gray-400'
                                            ]">
                                                {{ section.name }}
                                            </h4>
                                            <p class="text-sm text-gray-600 dark:text-gray-400 line-clamp-2">
                                                {{ section.description }}
                                            </p>
                                        </div>
                                    </div>
                                    <ChevronRightIcon :class="[
                                        'w-5 h-5 transition-colors flex-shrink-0 ml-2',
                                        section.id === 'delete'
                                            ? 'text-red-400 group-hover:text-red-600 dark:group-hover:text-red-400'
                                            : 'text-gray-400 group-hover:text-gray-600 dark:group-hover:text-gray-400'
                                    ]" />
                                </div>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Section Detail View: Show when a section is active -->
                <div v-else class="bg-white dark:bg-gray-800 shadow-xl sm:rounded-xl overflow-hidden">
                    <!-- Section Content -->
                    <div class="p-4 md:p-6">
                        <!-- Basic Information -->
                        <UpdateProfileInformationForm
                            v-if="activeSection === 'basic'"
                            :must-verify-email="mustVerifyEmail"
                            :status="status"
                        />

                        <!-- Profile Details -->
                        <UpdateProfileDetailsForm
                            v-if="activeSection === 'profile'"
                            :user-profile="userProfile"
                            :divisions="divisions"
                        />

                        <!-- Education Section -->
                        <EducationSection
                            v-if="activeSection === 'education'"
                            :user-profile="userProfile"
                            :educations="educations"
                        />

                        <!-- Work Experience Section -->
                        <WorkExperienceSection
                            v-if="activeSection === 'experience'"
                            :user-profile="userProfile"
                            :work-experiences="workExperiences"
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

                        <!-- Security Section -->
                        <SecuritySection
                            v-if="activeSection === 'security'"
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
