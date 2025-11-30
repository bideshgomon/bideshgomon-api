<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import { useBangladeshFormat } from '@/Composables/useBangladeshFormat';
import { ref, onMounted, computed } from 'vue';
import RhythmicCard from '@/Components/Rhythmic/RhythmicCard.vue';
import FlowButton from '@/Components/Rhythmic/FlowButton.vue';
import AnimatedSection from '@/Components/Rhythmic/AnimatedSection.vue';
import StatusBadge from '@/Components/Rhythmic/StatusBadge.vue';
import { 
    UserCircleIcon, 
    PhoneIcon, 
    AcademicCapIcon, 
    BriefcaseIcon, 
    SparklesIcon, 
    GlobeAltIcon, 
    UsersIcon, 
    BanknotesIcon, 
    ShieldCheckIcon,
    MapPinIcon,
    DocumentTextIcon,
    PencilSquareIcon,
    PlusIcon
} from '@heroicons/vue/24/solid';

const props = defineProps({
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
    profileCompletion: Number,
});

const activeSection = ref(null);
const showNavigation = ref(false);

// Section definitions for navigation
const sections = computed(() => [
    { id: 'basic', name: 'Basic Info', icon: '👤', hasData: true },
    { id: 'phones', name: 'Phone Numbers', icon: '📱', hasData: props.phoneNumbers?.length > 0 },
    { id: 'education', name: 'Education', icon: '🎓', hasData: props.educations?.length > 0 },
    { id: 'experience', name: 'Experience', icon: '💼', hasData: props.workExperiences?.length > 0 },
    { id: 'skills', name: 'Skills', icon: '⚡', hasData: props.skills?.length > 0 },
    { id: 'travel', name: 'Travel', icon: '✈️', hasData: props.travelHistory?.length > 0 },
    { id: 'address', name: 'Address', icon: '📍', hasData: props.userProfile?.present_address_line },
    { id: 'documents', name: 'Documents', icon: '📄', hasData: props.userProfile?.nid || props.userProfile?.passport_number },
    { id: 'family', name: 'Family', icon: '👨‍👩‍👧‍👦', hasData: props.familyMembers?.length > 0 },
    { id: 'financial', name: 'Financial', icon: '💰', hasData: props.userProfile?.monthly_income_bdt },
    { id: 'languages', name: 'Languages', icon: '🗣️', hasData: props.languages?.length > 0 },
    { id: 'security', name: 'Security', icon: '🛡️', hasData: props.securityInformation },
]);

// Smooth scroll to section
const scrollToSection = (sectionId) => {
    const element = document.getElementById(sectionId);
    if (element) {
        const offset = 80; // Account for sticky header
        const elementPosition = element.getBoundingClientRect().top;
        const offsetPosition = elementPosition + window.pageYOffset - offset;
        
        window.scrollTo({
            top: offsetPosition,
            behavior: 'smooth'
        });
        
        activeSection.value = sectionId;
        showNavigation.value = false;
    }
};

// Detect active section on scroll
const handleScroll = () => {
    const scrollPosition = window.pageYOffset + 100;
    
    for (const section of sections.value) {
        const element = document.getElementById(section.id);
        if (element) {
            const { offsetTop, offsetHeight } = element;
            if (scrollPosition >= offsetTop && scrollPosition < offsetTop + offsetHeight) {
                activeSection.value = section.id;
                break;
            }
        }
    }
};

onMounted(() => {
    window.addEventListener('scroll', handleScroll);
    handleScroll();
});

const { formatCurrency, formatPhone, formatDate, divisions } = useBangladeshFormat();

const completionColor = () => {
    if (props.profileCompletion >= 80) return 'text-green-600';
    if (props.profileCompletion >= 50) return 'text-yellow-600';
    return 'text-red-600';
};

const getRiskLevel = () => {
    if (!props.securityInformation) return null;
    const score = props.securityInformation.risk_score || 0;
    if (score >= 75) return { text: 'High Risk', color: 'text-red-600 bg-red-50' };
    if (score >= 50) return { text: 'Medium Risk', color: 'text-yellow-600 bg-yellow-50' };
    if (score >= 25) return { text: 'Low Risk', color: 'text-blue-600 bg-blue-50' };
    return { text: 'Minimal Risk', color: 'text-green-600 bg-green-50' };
};

const getProficiencyLabel = (level) => {
    const levels = {
        'native': 'Native',
        'fluent': 'Fluent',
        'advanced': 'Advanced',
        'intermediate': 'Intermediate',
        'beginner': 'Beginner',
    };
    return levels[level] || level;
};
</script>

<template>
    <Head title="Profile" />

    <AuthenticatedLayout>
        <!-- Hero Section -->
        <AnimatedSection variant="heritage" :show-blobs="true" class="mb-rhythm-2xl">
            <div class="relative z-10">
                <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-rhythm-md">
                    <div class="flex items-center gap-rhythm-md flex-1">
                        <button
                            @click="showNavigation = !showNavigation"
                            class="p-rhythm-sm rounded-xl bg-heritage-600 hover:bg-heritage-700 transition-colors touch-manipulation text-white shadow-md"
                            aria-label="Toggle navigation"
                        >
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                            </svg>
                        </button>
                        <div class="w-16 h-16 sm:w-20 sm:h-20 rounded-2xl bg-heritage-600 shadow-lg flex items-center justify-center text-white font-bold text-3xl sm:text-4xl">
                            {{ user.name.charAt(0).toUpperCase() }}
                        </div>
                        <div class="flex-1">
                            <h1 class="text-2xl sm:text-3xl font-bold text-heritage-900">{{ user.name }}</h1>
                            <p class="text-sm sm:text-base text-heritage-700 mt-1">{{ user.email }}</p>
                            <p class="text-xs text-heritage-600 mt-1 capitalize flex items-center gap-2">
                                <span class="w-2 h-2 rounded-full bg-green-500"></span>
                                {{ user.role?.name || 'User' }}
                            </p>
                        </div>
                    </div>
                    <FlowButton
                        variant="heritage"
                        size="md"
                        @click="$inertia.visit(route('profile.edit'))"
                    >
                        <template #icon-left>
                            <PencilSquareIcon class="h-5 w-5" />
                        </template>
                        Edit Profile
                    </FlowButton>
                </div>
                
                <!-- Profile Completion -->
                <div class="mt-rhythm-lg bg-white border-2 border-heritage-200 rounded-xl p-rhythm-md shadow-sm">
                    <div class="flex items-center justify-between mb-rhythm-sm">
                        <span class="text-sm font-medium text-heritage-900">Profile Completion</span>
                        <span class="text-xl font-bold text-heritage-900">{{ profileCompletion }}%</span>
                    </div>
                    <div class="w-full bg-heritage-100 rounded-full h-3">
                        <div 
                            class="h-3 rounded-full transition-all duration-500"
                            :class="{
                                'bg-green-500': profileCompletion >= 80,
                                'bg-yellow-500': profileCompletion >= 50 && profileCompletion < 80,
                                'bg-red-400': profileCompletion < 50
                            }"
                            :style="{ width: profileCompletion + '%' }"
                        ></div>
                    </div>
                </div>
            </div>
        </AnimatedSection>

        <!-- Section Navigation - Mobile Dropdown -->
        <Transition
            enter-active-class="transition-opacity duration-300"
            enter-from-class="opacity-0"
            enter-to-class="opacity-100"
            leave-active-class="transition-opacity duration-200"
            leave-from-class="opacity-100"
            leave-to-class="opacity-0"
        >
            <div
                v-if="showNavigation"
                class="fixed inset-0 bg-black bg-opacity-50 z-40"
                @click="showNavigation = false"
            >
                <div class="bg-white dark:bg-gray-800 rounded-t-3xl absolute bottom-0 left-0 right-0 max-h-[70vh] overflow-y-auto" @click.stop style="animation: slide-up 0.3s ease-out">
                    <div class="p-rhythm-lg">
                        <div class="flex justify-between items-center mb-rhythm-md">
                            <h3 class="text-lg font-bold text-gray-900 dark:text-gray-100">Jump to Section</h3>
                            <button @click="showNavigation = false" class="p-rhythm-sm hover:bg-gray-100 dark:hover:bg-gray-700 rounded-xl">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                            </button>
                        </div>
                        <nav class="space-y-rhythm-xs">
                            <button
                                v-for="section in sections"
                                :key="section.id"
                                @click="scrollToSection(section.id)"
                                :class="[
                                    'w-full flex items-center gap-rhythm-sm p-rhythm-sm rounded-xl text-left transition-colors touch-manipulation',
                                    activeSection === section.id 
                                        ? 'bg-heritage-100 dark:bg-heritage-900 text-heritage-700 dark:text-heritage-300' 
                                        : 'hover:bg-gray-100 dark:hover:bg-gray-700 text-gray-700 dark:text-gray-300',
                                    !section.hasData && 'opacity-50'
                                ]"
                            >
                                <span class="text-2xl">{{ section.icon }}</span>
                                <span class="font-medium">{{ section.name }}</span>
                                <span v-if="!section.hasData" class="ml-auto text-xs text-gray-500 dark:text-gray-400">Empty</span>
                            </button>
                        </nav>
                    </div>
                </div>
            </div>
        </Transition>

        <div class="py-rhythm-2xl">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-rhythm-lg">

                <!-- Basic Information -->
                <RhythmicCard id="basic" variant="ocean" size="lg" class="scroll-mt-20">
                    <template #icon>
                        <UserCircleIcon class="h-6 w-6" />
                    </template>
                    <template #default>
                        <h3 class="text-xl font-bold text-gray-900 mb-rhythm-md">Basic Information</h3>
                        <dl class="grid grid-cols-1 sm:grid-cols-2 gap-rhythm-md">
                            <div class="bg-ocean-50 rounded-xl p-rhythm-sm border-2 border-ocean-100">
                                <dt class="text-xs font-semibold text-ocean-700 uppercase tracking-wide">Name</dt>
                                <dd class="mt-1 text-sm text-gray-900 font-medium">{{ user.name }}</dd>
                            </div>
                            <div class="bg-ocean-50 rounded-xl p-rhythm-sm border-2 border-ocean-100">
                                <dt class="text-xs font-semibold text-ocean-700 uppercase tracking-wide">Email</dt>
                                <dd class="mt-1 text-sm text-gray-900 break-all">{{ user.email }}</dd>
                            </div>
                            <div class="bg-ocean-50 rounded-xl p-rhythm-sm border-2 border-ocean-100">
                                <dt class="text-xs font-semibold text-ocean-700 uppercase tracking-wide">Role</dt>
                                <dd class="mt-1 text-sm text-gray-900 capitalize">{{ user.role?.name || 'N/A' }}</dd>
                            </div>
                            <div class="bg-ocean-50 rounded-xl p-rhythm-sm border-2 border-ocean-100">
                                <dt class="text-xs font-semibold text-ocean-700 uppercase tracking-wide">Date of Birth</dt>
                                <dd class="mt-1 text-sm text-gray-900">
                                    {{ userProfile?.dob ? formatDate(userProfile.dob) : 'Not provided' }}
                                </dd>
                            </div>
                            <div class="bg-ocean-50 rounded-xl p-rhythm-sm border-2 border-ocean-100">
                                <dt class="text-xs font-semibold text-ocean-700 uppercase tracking-wide">Gender</dt>
                                <dd class="mt-1 text-sm text-gray-900 capitalize">
                                    {{ userProfile?.gender || 'Not provided' }}
                                </dd>
                            </div>
                            <div class="bg-ocean-50 rounded-xl p-rhythm-sm border-2 border-ocean-100">
                                <dt class="text-xs font-semibold text-ocean-700 uppercase tracking-wide">Nationality</dt>
                                <dd class="text-sm text-gray-900">
                                    {{ userProfile?.nationality || 'Not provided' }}
                                </dd>
                            </div>
                            <div class="bg-ocean-50 rounded-xl p-rhythm-sm border-2 border-ocean-100 sm:col-span-2">
                                <dt class="text-xs font-semibold text-ocean-700 uppercase tracking-wide">Bio</dt>
                                <dd class="mt-1 text-sm text-gray-900">
                                    {{ userProfile?.bio || 'No bio provided' }}
                                </dd>
                            </div>
                        </dl>
                    </template>
                </RhythmicCard>

                <!-- Phone Numbers -->
                <div id="phones" class="bg-white dark:bg-gray-800 shadow-md sm:rounded-xl overflow-hidden border border-gray-200 dark:border-gray-700 scroll-mt-20">
                    <div class="h-1 bg-sky-500"></div>
                    <div class="p-4 sm:p-6">
                        <h3 class="text-base sm:text-lg font-bold text-gray-900 dark:text-gray-100 mb-4 flex items-center gap-2">
                            <span class="text-2xl">📱</span>
                            <span>Phone Numbers</span>
                        </h3>
                        <div v-if="phoneNumbers && phoneNumbers.length > 0" class="space-y-3">
                            <div v-for="phone in phoneNumbers" :key="phone.id" class="bg-sky-50 dark:bg-sky-900/20 border border-sky-200 dark:border-sky-800 rounded-lg p-4">
                                <div class="flex items-start justify-between gap-3">
                                    <div class="flex items-center gap-3 flex-1 min-w-0">
                                        <div class="w-10 h-10 rounded-full bg-sky-100 dark:bg-sky-800 flex items-center justify-center flex-shrink-0">
                                            <span class="text-xl">
                                                {{ phone.phone_type === 'mobile' ? '📱' : phone.phone_type === 'work' ? '💼' : phone.phone_type === 'whatsapp' ? '💬' : '🏠' }}
                                            </span>
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <a :href="`tel:${phone.country_code}${phone.phone_number}`" class="text-base font-semibold text-gray-900 dark:text-gray-100 hover:text-sky-600 dark:hover:text-sky-400">
                                                {{ phone.country_code }} {{ phone.phone_number }}
                                            </a>
                                            <div class="text-xs text-gray-600 dark:text-gray-400 capitalize mt-0.5">{{ phone.phone_type }}</div>
                                        </div>
                                    </div>
                                    <div class="flex flex-col items-end gap-1">
                                        <span v-if="phone.is_primary" class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-indigo-600 text-white">
                                            Primary
                                        </span>
                                        <span v-if="phone.is_verified" class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-green-600 text-white">
                                            ✓ Verified
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div v-else class="text-center py-12 bg-gray-50 dark:bg-gray-900/50 rounded-lg border-2 border-dashed border-gray-300 dark:border-gray-700">
                            <span class="text-5xl block mb-3">📱</span>
                            <p class="text-gray-600 dark:text-gray-400 font-medium mb-3">No phone numbers added yet</p>
                            <Link :href="route('profile.edit', { section: 'phone' })" class="inline-flex items-center gap-2 px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors touch-manipulation">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                </svg>
                                Add Phone Number
                            </Link>
                        </div>
                    </div>
                </div>

                <!-- Education & Qualifications -->
                <div id="education" class="bg-white dark:bg-gray-800 shadow-md sm:rounded-xl overflow-hidden border border-gray-200 dark:border-gray-700 scroll-mt-20">
                    <div class="h-1 bg-blue-500"></div>
                    <div class="p-4 sm:p-6">
                        <h3 class="text-base sm:text-lg font-bold text-gray-900 dark:text-gray-100 mb-4 flex items-center gap-2">
                            <span class="text-2xl">🎓</span>
                            <span>Education & Qualifications</span>
                        </h3>
                        <div v-if="educations && educations.length > 0" class="space-y-3">
                            <div v-for="education in educations" :key="education.id" class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                                <div class="flex justify-between items-start gap-3 mb-3">
                                    <div class="flex-1 min-w-0">
                                        <h4 class="font-semibold text-gray-900 text-sm">{{ education.institution_name }}</h4>
                                        <p class="text-sm text-gray-700 font-medium mt-1">{{ education.degree }}</p>
                                        <p v-if="education.field_of_study" class="text-xs text-gray-600 mt-0.5">{{ education.field_of_study }}</p>
                                    </div>
                                    <span v-if="education.is_completed" class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-600 text-white flex-shrink-0">
                                        Completed
                                    </span>
                                    <span v-else class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-yellow-600 text-white flex-shrink-0">
                                        In Progress
                                    </span>
                                </div>
                                <div class="grid grid-cols-2 gap-3 text-sm">
                                    <div class="bg-white/60 rounded p-2">
                                        <dt class="text-xs text-gray-600">Start Date</dt>
                                        <dd class="text-gray-900 font-medium mt-0.5">{{ formatDate(education.start_date) }}</dd>
                                    </div>
                                    <div v-if="education.end_date" class="bg-white/60 rounded p-2">
                                        <dt class="text-xs text-gray-600">End Date</dt>
                                        <dd class="text-gray-900 font-medium mt-0.5">{{ formatDate(education.end_date) }}</dd>
                                    </div>
                                    <div v-if="education.gpa_or_grade" class="bg-white/60 rounded p-2">
                                        <dt class="text-xs text-gray-600">GPA/Grade</dt>
                                        <dd class="text-gray-900 font-bold mt-0.5">{{ education.gpa_or_grade }}</dd>
                                    </div>
                                    <div v-if="education.country" class="bg-white/60 rounded p-2">
                                        <dt class="text-xs text-gray-600">Location</dt>
                                        <dd class="text-gray-900 font-medium mt-0.5">{{ education.city }}, {{ education.country }}</dd>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div v-else class="text-center py-12 bg-gray-50 dark:bg-gray-900/50 rounded-lg border-2 border-dashed border-gray-300 dark:border-gray-700">
                            <span class="text-5xl block mb-3">📚</span>
                            <p class="text-gray-600 dark:text-gray-400 font-medium mb-3">No education records added yet</p>
                            <Link :href="route('profile.edit', { section: 'education' })" class="inline-flex items-center gap-2 px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors touch-manipulation">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                </svg>
                                Add Education
                            </Link>
                        </div>
                    </div>
                </div>

                <!-- Work Experience -->
                <div id="experience" class="bg-white dark:bg-gray-800 shadow-md sm:rounded-xl overflow-hidden border border-gray-200 dark:border-gray-700 scroll-mt-20">
                    <div class="h-1 bg-orange-500"></div>
                    <div class="p-4 sm:p-6">
                        <h3 class="text-base sm:text-lg font-bold text-gray-900 dark:text-gray-100 mb-4 flex items-center gap-2">
                            <span class="text-2xl">💼</span>
                            <span>Work Experience</span>
                        </h3>
                        <div v-if="workExperiences && workExperiences.length > 0" class="space-y-3">
                            <div v-for="work in workExperiences" :key="work.id" class="bg-orange-50 border border-orange-200 rounded-lg p-4">
                                <div class="flex justify-between items-start mb-3">
                                    <div class="flex-1 min-w-0">
                                        <h4 class="font-semibold text-gray-900 text-sm">{{ work.position }}</h4>
                                        <p class="text-xs text-gray-600 mt-0.5">{{ work.company_name }}</p>
                                    </div>
                                    <span v-if="work.is_current" class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-600 text-white flex-shrink-0">
                                        Current
                                    </span>
                                </div>
                                <dl class="grid grid-cols-2 gap-2 text-sm">
                                    <div class="bg-white/60 rounded p-2">
                                        <dt class="text-xs text-gray-600">Start Date</dt>
                                        <dd class="text-gray-900 font-medium mt-0.5">{{ formatDate(work.start_date) }}</dd>
                                    </div>
                                    <div v-if="work.end_date" class="bg-white/60 rounded p-2">
                                        <dt class="text-xs text-gray-600">End Date</dt>
                                        <dd class="text-gray-900 font-medium mt-0.5">{{ formatDate(work.end_date) }}</dd>
                                    </div>
                                    <div v-if="work.country" class="col-span-2 bg-white/60 rounded p-2">
                                        <dt class="text-xs text-gray-600">Location</dt>
                                        <dd class="text-gray-900 font-medium mt-0.5">{{ work.city }}, {{ work.country }}</dd>
                                    </div>
                                </dl>
                                <div v-if="work.responsibilities" class="text-sm text-gray-700 bg-white/60 p-3 rounded mt-2">
                                    {{ work.responsibilities }}
                                </div>
                            </div>
                        </div>
                        <div v-else class="text-center py-12 bg-gray-50 dark:bg-gray-900/50 rounded-lg border-2 border-dashed border-gray-300 dark:border-gray-700">
                        <span class="text-5xl block mb-3">💼</span>
                        <p class="text-gray-600 dark:text-gray-400 font-medium mb-3">No work experience added yet</p>
                        <Link :href="route('profile.edit', { section: 'experience' })" class="inline-flex items-center gap-2 px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors touch-manipulation">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                            </svg>
                            Add Experience
                        </Link>
                    </div>
                </div>

                <!-- Skills & Expertise -->
                <div id="skills" class="bg-white dark:bg-gray-800 shadow-md sm:rounded-xl overflow-hidden border border-gray-200 dark:border-gray-700 scroll-mt-20">
                    <div class="h-1 bg-teal-500"></div>
                    <div class="p-4 sm:p-6">
                        <h3 class="text-base sm:text-lg font-bold text-gray-900 dark:text-gray-100 mb-4 flex items-center gap-2">
                            <span class="text-2xl">⚡</span>
                            <span>Skills & Expertise</span>
                        </h3>
                        <div v-if="skills && skills.length > 0" class="space-y-3">
                            <div v-for="userSkill in skills" :key="userSkill.id" class="bg-teal-50 border border-teal-200 rounded-lg p-4">
                                <div class="flex justify-between items-start mb-2">
                                    <h4 class="font-semibold text-gray-900 text-sm">{{ userSkill.skill?.name || 'N/A' }}</h4>
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-teal-600 text-white capitalize">
                                        {{ userSkill.proficiency_level }}
                                    </span>
                                </div>
                                <div class="flex items-center space-x-4 text-sm">
                                    <div>
                                        <span class="text-gray-600">Experience:</span>
                                        <span class="text-gray-900 font-medium ml-1">{{ userSkill.years_of_experience }} years</span>
                                    </div>
                                    <div v-if="userSkill.skill?.category" class="text-gray-600">
                                        • {{ userSkill.skill.category }}
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div v-else class="text-center py-12 bg-gray-50 dark:bg-gray-900/50 rounded-lg border-2 border-dashed border-gray-300 dark:border-gray-700">
                            <span class="text-5xl block mb-3">🛠️</span>
                            <p class="text-gray-600 dark:text-gray-400 font-medium mb-3">No skills added yet</p>
                            <Link :href="route('profile.edit', { section: 'skills' })" class="inline-flex items-center gap-2 px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors touch-manipulation">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                </svg>
                                Add Skills
                            </Link>
                        </div>
                    </div>
                </div>

                <!-- Travel History -->
                <div id="travel" class="bg-white dark:bg-gray-800 shadow-md sm:rounded-xl overflow-hidden border border-gray-200 dark:border-gray-700 scroll-mt-20">
                    <div class="h-1 bg-sky-500"></div>
                    <div class="p-4 sm:p-6">
                        <h3 class="text-base sm:text-lg font-bold text-gray-900 dark:text-gray-100 mb-4 flex items-center gap-2">
                            <span class="text-2xl">✈️</span>
                            <span>Travel History</span>
                        </h3>
                        <div v-if="travelHistory && travelHistory.length > 0" class="space-y-3">
                            <div v-for="travel in travelHistory" :key="travel.id" class="bg-sky-50 border border-sky-200 rounded-lg p-4">
                                <div class="flex justify-between items-start mb-3">
                                    <div class="flex-1 min-w-0">
                                        <h4 class="font-semibold text-gray-900 text-sm">{{ travel.country_visited }}</h4>
                                        <p class="text-xs text-gray-600 mt-0.5">{{ travel.city_visited }}</p>
                                    </div>
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-sky-600 text-white capitalize flex-shrink-0">
                                        {{ travel.purpose }}
                                    </span>
                                </div>
                                <dl class="grid grid-cols-2 gap-2 text-sm">
                                    <div class="bg-white/60 rounded p-2">
                                        <dt class="text-xs text-gray-600">Entry Date</dt>
                                        <dd class="text-gray-900 font-medium mt-0.5">{{ formatDate(travel.entry_date) }}</dd>
                                    </div>
                                    <div v-if="travel.exit_date" class="bg-white/60 rounded p-2">
                                        <dt class="text-xs text-gray-600">Exit Date</dt>
                                        <dd class="text-gray-900 font-medium mt-0.5">{{ formatDate(travel.exit_date) }}</dd>
                                    </div>
                                    <div v-if="travel.duration_days" class="bg-white/60 rounded p-2">
                                        <dt class="text-xs text-gray-600">Duration</dt>
                                        <dd class="text-gray-900 font-medium mt-0.5">{{ travel.duration_days }} days</dd>
                                    </div>
                                    <div v-if="travel.accommodation_type" class="bg-white/60 rounded p-2">
                                        <dt class="text-xs text-gray-600">Accommodation</dt>
                                        <dd class="text-gray-900 font-medium mt-0.5 capitalize">{{ travel.accommodation_type }}</dd>
                                    </div>
                                    <div v-if="travel.transportation_mode" class="bg-white/60 rounded p-2">
                                        <dt class="text-xs text-gray-600">Transportation</dt>
                                        <dd class="text-gray-900 font-medium mt-0.5 capitalize">{{ travel.transportation_mode }}</dd>
                                    </div>
                                    <div v-if="travel.visa_type_used" class="bg-white/60 rounded p-2">
                                        <dt class="text-xs text-gray-600">Visa Type</dt>
                                        <dd class="text-gray-900 font-medium mt-0.5">{{ travel.visa_type_used }}</dd>
                                    </div>
                                </dl>
                            </div>
                        </div>
                        <div v-else class="text-center py-12 bg-gray-50 dark:bg-gray-900/50 rounded-lg border-2 border-dashed border-gray-300 dark:border-gray-700">
                            <span class="text-5xl block mb-3">🌍</span>
                            <p class="text-gray-600 dark:text-gray-400 font-medium mb-3">No travel history recorded yet</p>
                            <Link :href="route('profile.edit', { section: 'travel' })" class="inline-flex items-center gap-2 px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors touch-manipulation">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                </svg>
                                Add Travel History
                            </Link>
                        </div>
                    </div>
                </div>

                <!-- Address Information -->
                <div id="address" class="bg-white dark:bg-gray-800 shadow-md sm:rounded-xl overflow-hidden border border-gray-200 dark:border-gray-700 scroll-mt-20">
                    <div class="h-1 bg-green-500"></div>
                    <div class="p-4 sm:p-6">
                        <h3 class="text-base sm:text-lg font-bold text-gray-900 dark:text-gray-100 mb-6 flex items-center gap-2">
                            <span class="text-2xl">📍</span>
                            <span>Address Information</span>
                        </h3>
                        <div v-if="userProfile?.present_address_line || userProfile?.permanent_address_line" class="space-y-6">
                            <div class="bg-green-50 border border-green-200 rounded-lg p-4">
                                <h4 class="text-sm font-bold text-gray-900 dark:text-gray-100 mb-3">🏠 Present Address</h4>
                                <dl class="grid grid-cols-1 gap-3 sm:grid-cols-3">
                                    <div class="bg-white/60 rounded p-2">
                                        <dt class="text-xs font-medium text-gray-600">Division</dt>
                                        <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100 font-medium">
                                            {{ userProfile?.present_division || 'Not provided' }}
                                        </dd>
                                    </div>
                                    <div class="bg-white/60 rounded p-2">
                                        <dt class="text-xs font-medium text-gray-600">District</dt>
                                        <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100 font-medium">
                                            {{ userProfile?.present_district || 'Not provided' }}
                                        </dd>
                                    </div>
                                    <div class="sm:col-span-3 bg-white/60 rounded p-2">
                                        <dt class="text-xs font-medium text-gray-600">Full Address</dt>
                                        <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100 font-medium">
                                            {{ userProfile?.present_address_line || 'Not provided' }}
                                        </dd>
                                    </div>
                                </dl>
                            </div>
                            <div class="bg-teal-50 border border-teal-200 rounded-lg p-4">
                                <h4 class="text-sm font-bold text-gray-900 dark:text-gray-100 mb-3">🏚️ Permanent Address</h4>
                                <dl class="grid grid-cols-1 gap-3 sm:grid-cols-3">
                                    <div class="bg-white/60 rounded p-2">
                                        <dt class="text-xs font-medium text-gray-600">Division</dt>
                                        <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100 font-medium">
                                            {{ userProfile?.permanent_division || 'Not provided' }}
                                        </dd>
                                    </div>
                                    <div class="bg-white/60 rounded p-2">
                                        <dt class="text-xs font-medium text-gray-600">District</dt>
                                        <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100 font-medium">
                                            {{ userProfile?.permanent_district || 'Not provided' }}
                                        </dd>
                                    </div>
                                    <div class="sm:col-span-3 bg-white/60 rounded p-2">
                                        <dt class="text-xs font-medium text-gray-600">Full Address</dt>
                                        <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100 font-medium">
                                            {{ userProfile?.permanent_address_line || 'Not provided' }}
                                        </dd>
                                    </div>
                                </dl>
                            </div>
                        </div>
                        <div v-else class="text-center py-12 bg-gray-50 dark:bg-gray-900/50 rounded-lg border-2 border-dashed border-gray-300 dark:border-gray-700">
                            <span class="text-5xl block mb-3">🏠</span>
                            <p class="text-gray-600 dark:text-gray-400 font-medium mb-3">No address information added yet</p>
                            <Link :href="route('profile.edit', { section: 'address' })" class="inline-flex items-center gap-2 px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors touch-manipulation">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                </svg>
                                Add Address
                            </Link>
                        </div>
                    </div>
                </div>

                <!-- Documents -->
                <div id="documents" class="bg-white dark:bg-gray-800 shadow-md sm:rounded-xl overflow-hidden border border-gray-200 dark:border-gray-700 scroll-mt-20">
                    <div class="h-1 bg-blue-500"></div>
                    <div class="p-4 sm:p-6">
                        <h3 class="text-base sm:text-lg font-bold text-gray-900 dark:text-gray-100 mb-4 flex items-center gap-2">
                            <span class="text-2xl">📄</span>
                            <span>Documents</span>
                        </h3>
                        <div v-if="userProfile?.nid || userProfile?.passport_number" class="space-y-3">
                            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                                <dl class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                                    <div class="bg-white/60 rounded p-3">
                                        <dt class="text-xs font-medium text-gray-600">🇯 National ID (NID)</dt>
                                        <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100 font-semibold">
                                            {{ userProfile?.nid || 'Not provided' }}
                                        </dd>
                                    </div>
                                    <div class="bg-white/60 rounded p-3">
                                        <dt class="text-xs font-medium text-gray-600">📜 Passport Number</dt>
                                        <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100 font-semibold">
                                            {{ userProfile?.passport_number || 'Not provided' }}
                                        </dd>
                                    </div>
                                    <div class="bg-white/60 rounded p-3">
                                        <dt class="text-xs font-medium text-gray-600">Passport Issue Date</dt>
                                        <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100 font-medium">
                                            {{ userProfile?.passport_issue_date ? formatDate(userProfile.passport_issue_date) : 'Not provided' }}
                                        </dd>
                                    </div>
                                    <div class="bg-white/60 rounded p-3">
                                        <dt class="text-xs font-medium text-gray-600">Passport Expiry Date</dt>
                                        <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100 font-medium">
                                            {{ userProfile?.passport_expiry_date ? formatDate(userProfile.passport_expiry_date) : 'Not provided' }}        
                                        </dd>
                                    </div>
                                </dl>
                            </div>
                        </div>
                        <div v-else class="text-center py-12 bg-gray-50 dark:bg-gray-900/50 rounded-lg border-2 border-dashed border-gray-300 dark:border-gray-700">
                            <span class="text-5xl block mb-3">📝</span>
                            <p class="text-gray-600 dark:text-gray-400 font-medium mb-3">No documents added yet</p>
                            <Link :href="route('profile.edit', { section: 'documents' })" class="inline-flex items-center gap-2 px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors touch-manipulation">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                </svg>
                                Add Documents
                            </Link>
                        </div>
                    </div>
                </div>

                <!-- Family Members -->
                <div id="family" class="bg-white dark:bg-gray-800 shadow-md sm:rounded-xl overflow-hidden border border-gray-200 dark:border-gray-700 scroll-mt-20">
                    <div class="h-1 bg-red-500"></div>
                    <div class="p-4 sm:p-6">
                        <h3 class="text-base sm:text-lg font-bold text-gray-900 dark:text-gray-100 mb-4 flex items-center gap-2">
                            <span class="text-2xl">👨‍👩‍👧‍👦</span>
                            <span>Family Members</span>
                        </h3>
                        <div v-if="familyMembers && familyMembers.length > 0" class="space-y-3">
                            <div v-for="member in familyMembers" :key="member.id" class="bg-red-50 border border-red-200 rounded-lg p-4">
                                <div class="flex justify-between items-start mb-3">
                                    <div class="flex-1 min-w-0">
                                        <h4 class="font-semibold text-gray-900 text-sm">{{ member.full_name }}</h4>
                                        <p class="text-xs text-gray-600 capitalize mt-0.5">{{ member.relationship }}</p>
                                    </div>
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 border border-red-300 text-red-700 capitalize flex-shrink-0">
                                        {{ member.relationship }}
                                    </span>
                                </div>
                                <dl class="grid grid-cols-2 gap-2 text-sm">
                                    <div class="bg-white/60 rounded p-2">
                                        <dt class="text-xs text-gray-600">Date of Birth</dt>
                                        <dd class="text-gray-900 font-medium mt-0.5">{{ member.date_of_birth ? formatDate(member.date_of_birth) : 'N/A' }}</dd>
                                    </div>
                                    <div class="bg-white/60 rounded p-2">
                                        <dt class="text-xs text-gray-600">Gender</dt>
                                        <dd class="text-gray-900 font-medium mt-0.5 capitalize">{{ member.gender || 'N/A' }}</dd>
                                    </div>
                                    <div class="bg-white/60 rounded p-2">
                                        <dt class="text-xs text-gray-600">Nationality</dt>
                                        <dd class="text-gray-900 font-medium mt-0.5">{{ member.nationality || 'N/A' }}</dd>
                                    </div>
                                    <div class="bg-white/60 rounded p-2">
                                        <dt class="text-xs text-gray-600">Phone</dt>
                                        <dd class="text-gray-900 font-medium mt-0.5">{{ member.contact_phone ? formatPhone(member.contact_phone) : 'N/A' }}</dd>
                                    </div>
                                    <div v-if="member.occupation" class="col-span-2 bg-white/60 rounded p-2">
                                        <dt class="text-xs text-gray-600">Occupation</dt>
                                        <dd class="text-gray-900 font-medium mt-0.5">{{ member.occupation }}</dd>
                                    </div>
                                </dl>
                            </div>
                        </div>
                        <div v-else class="text-center py-12 bg-gray-50 dark:bg-gray-900/50 rounded-lg border-2 border-dashed border-gray-300 dark:border-gray-700">
                            <span class="text-5xl block mb-3">👪</span>
                            <p class="text-gray-600 dark:text-gray-400 font-medium mb-3">No family members added yet</p>
                            <Link :href="route('profile.edit', { section: 'family' })" class="inline-flex items-center gap-2 px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors touch-manipulation">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                </svg>
                                Add Family Member
                            </Link>
                        </div>
                    </div>
                </div>

                <!-- Financial Information -->
                <div id="financial" class="bg-white dark:bg-gray-800 shadow-md sm:rounded-xl overflow-hidden border border-gray-200 dark:border-gray-700 scroll-mt-20">
                    <div class="h-1 bg-green-500"></div>
                    <div class="p-4 sm:p-6">
                        <h3 class="text-base sm:text-lg font-bold text-gray-900 dark:text-gray-100 mb-4 flex items-center gap-2">
                            <span class="text-2xl">💰</span>
                            <span>Financial Information</span>
                        </h3>
                        
                        <!-- Financial Summary -->
                        <div v-if="userProfile?.monthly_income_bdt || userProfile?.bank_balance_bdt || userProfile?.net_worth_bdt" class="space-y-4">
                            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                                <div class="bg-green-100 rounded-lg p-4 border border-green-300">
                                    <dt class="text-xs font-medium text-gray-700">Monthly Income</dt>
                                    <dd class="text-2xl font-bold text-green-700 mt-1">
                                        {{ userProfile?.monthly_income_bdt ? formatCurrency(userProfile.monthly_income_bdt) : '৳0' }}
                                    </dd>
                                </div>
                                <div class="bg-blue-100 rounded-lg p-4 border border-blue-300">
                                    <dt class="text-xs font-medium text-gray-700">Bank Balance</dt>
                                    <dd class="text-2xl font-bold text-blue-700 mt-1">
                                        {{ userProfile?.bank_balance_bdt ? formatCurrency(userProfile.bank_balance_bdt) : '৳০' }}
                                    </dd>
                                </div>
                                <div class="bg-green-100 rounded-lg p-4 border border-green-300">
                                    <dt class="text-xs font-medium text-gray-700">Net Worth</dt>
                                    <dd class="text-2xl font-bold text-green-700 mt-1">
                                        {{ userProfile?.net_worth_bdt ? formatCurrency(userProfile.net_worth_bdt) : '৳0' }}
                                    </dd>
                                </div>
                            </div>

                            <!-- Employment Details -->
                            <div v-if="userProfile?.employer_name" class="bg-green-50 border border-green-200 rounded-lg p-4">
                                <h4 class="font-semibold text-gray-900 text-sm mb-3 flex items-center gap-2">
                                    <span>🏢</span> Employment
                                </h4>
                                <dl class="grid grid-cols-2 gap-2 text-sm">
                                    <div class="bg-white/60 rounded p-2">
                                        <dt class="text-xs text-gray-600">Employer</dt>
                                        <dd class="text-gray-900 font-medium mt-0.5">{{ userProfile.employer_name }}</dd>
                                    </div>
                                    <div class="bg-white/60 rounded p-2">
                                        <dt class="text-xs text-gray-600">Annual Income</dt>
                                        <dd class="text-gray-900 font-medium mt-0.5">{{ userProfile.annual_income_bdt ? formatCurrency(userProfile.annual_income_bdt) : 'N/A' }}</dd>
                                    </div>
                                </dl>
                            </div>

                            <!-- Assets -->
                            <div v-if="userProfile?.owns_property || userProfile?.owns_vehicle" class="bg-green-50 border border-green-200 rounded-lg p-4">
                                <h4 class="font-semibold text-gray-900 text-sm mb-3 flex items-center gap-2">
                                    <span>🏡</span> Assets
                                </h4>
                                <div class="space-y-2 text-sm">
                                    <div v-if="userProfile?.owns_property" class="bg-white/60 rounded p-2">
                                        <dt class="text-xs text-gray-600">Property</dt>
                                        <dd class="text-gray-900 font-medium mt-0.5">{{ userProfile.property_type }} - {{ formatCurrency(userProfile.property_value_bdt || 0) }}</dd>
                                    </div>
                                    <div v-if="userProfile?.owns_vehicle" class="bg-white/60 rounded p-2">
                                        <dt class="text-xs text-gray-600">Vehicle</dt>
                                        <dd class="text-gray-900 font-medium mt-0.5">{{ userProfile.vehicle_make_model }} ({{ userProfile.vehicle_year }}) - {{ formatCurrency(userProfile.vehicle_value_bdt || 0) }}</dd>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div v-else class="text-center py-12 bg-gray-50 dark:bg-gray-900/50 rounded-lg border-2 border-dashed border-gray-300 dark:border-gray-700">
                            <span class="text-5xl block mb-3">💳</span>
                            <p class="text-gray-600 dark:text-gray-400 font-medium mb-3">No financial information provided yet</p>
                            <Link :href="route('profile.edit', { section: 'financial' })" class="inline-flex items-center gap-2 px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors touch-manipulation">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                </svg>
                                Add Financial Info
                            </Link>
                        </div>
                    </div>
                </div>

                <!-- Language Proficiency -->
                <div id="languages" class="bg-white dark:bg-gray-800 shadow-md sm:rounded-xl overflow-hidden border border-gray-200 dark:border-gray-700 scroll-mt-20">
                    <div class="h-1 bg-blue-500"></div>
                    <div class="p-4 sm:p-6">
                        <h3 class="text-base sm:text-lg font-bold text-gray-900 dark:text-gray-100 mb-4 flex items-center gap-2">
                            <span class="text-2xl">🗣️</span>
                            <span>Language Proficiency</span>
                        </h3>
                        <div v-if="languages && languages.length > 0" class="space-y-3">
                            <div v-for="language in languages" :key="language.id" class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                                <div class="flex justify-between items-start mb-3">
                                    <div class="flex-1 min-w-0">
                                        <h4 class="font-semibold text-gray-900 text-sm">{{ language.language_name }}</h4>
                                        <p class="text-xs text-gray-600 mt-0.5">{{ getProficiencyLabel(language.proficiency) }}</p>
                                    </div>
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-600 text-white capitalize flex-shrink-0">
                                        {{ language.proficiency }}
                                    </span>
                                </div>
                                <div v-if="language.test_taken && language.test_taken !== 'none'" class="mt-3 pt-3 border-t border-blue-200">
                                    <dl class="grid grid-cols-2 gap-2 text-sm">
                                        <div class="bg-white/60 rounded p-2">
                                            <dt class="text-xs text-gray-600">Test</dt>
                                            <dd class="text-gray-900 font-semibold uppercase mt-0.5">{{ language.test_taken }}</dd>
                                        </div>
                                        <div class="bg-white/60 rounded p-2">
                                            <dt class="text-xs text-gray-600">Overall Score</dt>
                                            <dd class="text-gray-900 font-bold mt-0.5">{{ language.overall_score || 'N/A' }}</dd>
                                        </div>
                                        <div v-if="language.reading_score" class="bg-white/60 rounded p-2">
                                            <dt class="text-xs text-gray-600">Reading</dt>
                                            <dd class="text-gray-900 font-medium mt-0.5">{{ language.reading_score }}</dd>
                                        </div>
                                        <div v-if="language.writing_score" class="bg-white/60 rounded p-2">
                                            <dt class="text-xs text-gray-600">Writing</dt>
                                            <dd class="text-gray-900 font-medium mt-0.5">{{ language.writing_score }}</dd>
                                        </div>
                                        <div v-if="language.listening_score" class="bg-white/60 rounded p-2">
                                            <dt class="text-xs text-gray-600">Listening</dt>
                                            <dd class="text-gray-900 font-medium mt-0.5">{{ language.listening_score }}</dd>
                                        </div>
                                        <div v-if="language.speaking_score" class="bg-white/60 rounded p-2">
                                            <dt class="text-xs text-gray-600">Speaking</dt>
                                            <dd class="text-gray-900 font-medium mt-0.5">{{ language.speaking_score }}</dd>
                                        </div>
                                        <div v-if="language.test_date" class="col-span-2 bg-white/60 rounded p-2">
                                            <dt class="text-xs text-gray-600">Test Date</dt>
                                            <dd class="text-gray-900 font-medium mt-0.5">{{ formatDate(language.test_date) }}</dd>
                                        </div>
                                    </dl>
                                </div>
                            </div>
                        </div>
                        <div v-else class="text-center py-12 bg-gray-50 dark:bg-gray-900/50 rounded-lg border-2 border-dashed border-gray-300 dark:border-gray-700">
                            <span class="text-5xl block mb-3">🌐</span>
                            <p class="text-gray-600 dark:text-gray-400 font-medium mb-3">No languages added yet</p>
                            <Link :href="route('profile.edit', { section: 'languages' })" class="inline-flex items-center gap-2 px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors touch-manipulation">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                </svg>
                                Add Language
                            </Link>
                        </div>
                    </div>
                </div>

                <!-- Security & Background -->
                <div id="security" class="bg-white dark:bg-gray-800 shadow-md sm:rounded-xl overflow-hidden border border-gray-200 dark:border-gray-700 scroll-mt-20">
                    <div class="h-1 bg-red-500"></div>
                    <div class="p-4 sm:p-6">
                        <div class="flex justify-between items-center mb-4">
                            <h3 class="text-base sm:text-lg font-bold text-gray-900 dark:text-gray-100 flex items-center gap-2">
                                <span class="text-2xl">🛡️</span>
                                <span>Security & Background</span>
                            </h3>
                            <span v-if="getRiskLevel()" :class="[getRiskLevel().color, 'inline-flex items-center px-3 py-1 rounded-full text-sm font-medium']">
                                {{ getRiskLevel().text }}
                            </span>
                        </div>
                    
                        <div v-if="securityInformation" class="space-y-3">
                            <!-- Criminal Records -->
                            <div class="bg-red-50 border border-red-200 rounded-lg p-4">
                                <h4 class="font-semibold text-gray-900 text-sm mb-3 flex items-center gap-2">
                                    <span>⚠️</span> Criminal Records
                                </h4>
                                <dl class="grid grid-cols-2 gap-2 text-sm">
                                    <div class="bg-white/60 rounded p-2">
                                        <dt class="text-xs text-gray-600">Has Criminal Record</dt>
                                        <dd class="text-gray-900 font-semibold mt-0.5">
                                            <span :class="securityInformation.has_criminal_record ? 'text-red-600 font-bold' : 'text-green-600'">
                                                {{ securityInformation.has_criminal_record ? 'Yes' : 'No' }}
                                            </span>
                                        </dd>
                                    </div>
                                    <div v-if="securityInformation.criminal_record_details" class="bg-white/60 rounded p-2">
                                        <dt class="text-xs text-gray-600">Details</dt>
                                        <dd class="text-gray-900 font-medium mt-0.5">{{ securityInformation.criminal_record_details }}</dd>
                                    </div>
                                </dl>
                            </div>

                            <!-- Police Clearance -->
                            <div class="bg-orange-50 border border-orange-200 rounded-lg p-4">
                                <h4 class="font-semibold text-gray-900 text-sm mb-3 flex items-center gap-2">
                                    <span>✔️</span> Police Clearance
                                </h4>
                                <dl class="grid grid-cols-2 gap-2 text-sm">
                                    <div class="bg-white/60 rounded p-2">
                                        <dt class="text-xs text-gray-600">Has Clearance</dt>
                                        <dd class="text-gray-900 font-semibold mt-0.5">
                                            <span :class="securityInformation.has_police_clearance ? 'text-green-600 font-bold' : 'text-gray-600'">
                                                {{ securityInformation.has_police_clearance ? 'Yes' : 'No' }}
                                            </span>
                                        </dd>
                                    </div>
                                    <div v-if="securityInformation.police_clearance_date" class="bg-white/60 rounded p-2">
                                        <dt class="text-xs text-gray-600">Clearance Date</dt>
                                        <dd class="text-gray-900 font-medium mt-0.5">{{ formatDate(securityInformation.police_clearance_date) }}</dd>
                                    </div>
                                    <div v-if="securityInformation.police_clearance_country" class="bg-white/60 rounded p-2">
                                        <dt class="text-xs text-gray-600">Country</dt>
                                        <dd class="text-gray-900 font-medium mt-0.5">{{ securityInformation.police_clearance_country }}</dd>
                                    </div>
                                    <div v-if="securityInformation.police_clearance_expiry_date" class="bg-white/60 rounded p-2">
                                        <dt class="text-xs text-gray-600">Expiry Date</dt>
                                        <dd class="text-gray-900 font-medium mt-0.5">{{ formatDate(securityInformation.police_clearance_expiry_date) }}</dd>
                                    </div>
                                </dl>
                            </div>

                            <!-- Risk Assessment -->
                            <div v-if="securityInformation.risk_score" class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                                <h4 class="font-semibold text-gray-900 text-sm mb-3 flex items-center gap-2">
                                    <span>📊</span> Risk Assessment
                                </h4>
                                <div class="flex items-center space-x-4">
                                    <div class="flex-1">
                                        <div class="flex justify-between text-sm mb-2">
                                            <span class="text-gray-700 font-medium">Risk Score</span>
                                            <span class="font-bold text-gray-900">{{ securityInformation.risk_score }}/100</span>
                                        </div>
                                        <div class="w-full bg-gray-200 rounded-full h-3 overflow-hidden">
                                            <div 
                                                class="h-3 rounded-full transition-all duration-500"
                                                :class="{
                                                    'bg-green-500': securityInformation.risk_score < 25,
                                                    'bg-blue-500': securityInformation.risk_score >= 25 && securityInformation.risk_score < 50,
                                                    'bg-yellow-500': securityInformation.risk_score >= 50 && securityInformation.risk_score < 75,
                                                    'bg-red-500': securityInformation.risk_score >= 75
                                                }"
                                                :style="{ width: securityInformation.risk_score + '%' }"
                                            ></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div v-else class="text-center py-12 bg-gray-50 dark:bg-gray-900/50 rounded-lg border-2 border-dashed border-gray-300 dark:border-gray-700">
                            <span class="text-5xl block mb-3">🔒</span>
                            <p class="text-gray-600 dark:text-gray-400 font-medium mb-3">No security information provided yet</p>
                            <Link :href="route('profile.edit', { section: 'security' })" class="inline-flex items-center gap-2 px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors touch-manipulation">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                </svg>
                                Add Security Info
                            </Link>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        </div>
    </AuthenticatedLayout>
</template>

<style scoped>
@keyframes slide-up {
    from {
        transform: translateY(100%);
        opacity: 0;
    }
    to {
        transform: translateY(0);
        opacity: 1;
    }
}

.animate-slide-up {
    animation: slide-up 0.3s ease-out;
}

html {
    scroll-behavior: smooth;
}

.scroll-mt-20 {
    scroll-margin-top: 5rem;
}
</style>
