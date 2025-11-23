<script setup>
import { computed } from 'vue';
import { Link } from '@inertiajs/vue3';
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
    PhoneIcon
} from '@heroicons/vue/24/solid';

const props = defineProps({
    user: Object,
    userProfile: Object,
    familyMembers: Array,
    languages: Array,
    educations: Array,
    workExperiences: Array,
    skills: Array,
    travelHistory: Array,
    phoneNumbers: Array,
});

const sections = [
    {
        id: 'basic',
        name: 'Basic Information',
        icon: UserCircleIcon,
        color: 'blue',
        fields: ['first_name', 'last_name', 'phone', 'dob', 'gender'],
        weight: 15
    },
    {
        id: 'phone',
        name: 'Phone Numbers',
        icon: PhoneIcon,
        color: 'purple',
        checkArray: 'phoneNumbers',
        weight: 5
    },
    {
        id: 'social',
        name: 'Social Media',
        icon: GlobeAltIcon,
        color: 'pink',
        fields: ['social_links'],
        weight: 5
    },
    {
        id: 'emergency',
        name: 'Emergency Contact',
        icon: PhoneIcon,
        color: 'red',
        fields: ['emergency_contact_name', 'emergency_contact_phone'],
        weight: 10
    },
    {
        id: 'medical',
        name: 'Medical Information',
        icon: ShieldCheckIcon,
        color: 'green',
        fields: ['blood_group'],
        weight: 10
    },
    {
        id: 'profile',
        name: 'Profile Details',
        icon: DocumentTextIcon,
        color: 'indigo',
        fields: ['present_address_line', 'nid', 'passport_number'],
        weight: 15
    },
    {
        id: 'education',
        name: 'Education',
        icon: AcademicCapIcon,
        color: 'yellow',
        checkArray: 'educations',
        weight: 10
    },
    {
        id: 'experience',
        name: 'Work Experience',
        icon: BriefcaseIcon,
        color: 'orange',
        checkArray: 'workExperiences',
        weight: 10
    },
    {
        id: 'skills',
        name: 'Skills',
        icon: SparklesIcon,
        color: 'cyan',
        checkArray: 'skills',
        weight: 5
    },
    {
        id: 'travel',
        name: 'Travel History',
        icon: GlobeAltIcon,
        color: 'teal',
        checkArray: 'travelHistory',
        weight: 5
    },
    {
        id: 'references',
        name: 'References',
        icon: UsersIcon,
        color: 'violet',
        fields: ['references'],
        weight: 5
    },
    {
        id: 'certifications',
        name: 'Certifications',
        icon: ShieldCheckIcon,
        color: 'emerald',
        fields: ['certifications'],
        weight: 5
    },
    {
        id: 'family',
        name: 'Family Information',
        icon: UsersIcon,
        color: 'rose',
        checkArray: 'familyMembers',
        weight: 5
    },
    {
        id: 'languages',
        name: 'Languages',
        icon: LanguageIcon,
        color: 'amber',
        checkArray: 'languages',
        weight: 5
    },
];

const checkSectionCompletion = (section) => {
    if (section.checkArray) {
        const arrayData = props[section.checkArray];
        return arrayData && arrayData.length > 0;
    }
    
    if (section.fields) {
        const profile = props.userProfile || {};
        const user = props.user || {};
        
        return section.fields.every(field => {
            const value = profile[field] || user[field];
            if (Array.isArray(value)) return value.length > 0;
            if (typeof value === 'object' && value !== null) return Object.keys(value).length > 0;
            return value !== null && value !== undefined && value !== '';
        });
    }
    
    return false;
};

const sectionProgress = computed(() => {
    return sections.map(section => ({
        ...section,
        completed: checkSectionCompletion(section),
        progress: checkSectionCompletion(section) ? 100 : 0
    }));
});

const overallProgress = computed(() => {
    const totalWeight = sections.reduce((sum, s) => sum + s.weight, 0);
    const completedWeight = sectionProgress.value
        .filter(s => s.completed)
        .reduce((sum, s) => sum + s.weight, 0);
    return Math.round((completedWeight / totalWeight) * 100);
});

const completedCount = computed(() => 
    sectionProgress.value.filter(s => s.completed).length
);

const getColorClasses = (color, type = 'bg') => {
    const colors = {
        blue: { bg: 'bg-blue-500', text: 'text-blue-600', border: 'border-blue-500', ring: 'ring-blue-500' },
        purple: { bg: 'bg-purple-500', text: 'text-purple-600', border: 'border-purple-500', ring: 'ring-purple-500' },
        pink: { bg: 'bg-pink-500', text: 'text-pink-600', border: 'border-pink-500', ring: 'ring-pink-500' },
        red: { bg: 'bg-red-500', text: 'text-red-600', border: 'border-red-500', ring: 'ring-red-500' },
        green: { bg: 'bg-green-500', text: 'text-green-600', border: 'border-green-500', ring: 'ring-green-500' },
        indigo: { bg: 'bg-indigo-500', text: 'text-indigo-600', border: 'border-indigo-500', ring: 'ring-indigo-500' },
        yellow: { bg: 'bg-yellow-500', text: 'text-yellow-600', border: 'border-yellow-500', ring: 'ring-yellow-500' },
        orange: { bg: 'bg-orange-500', text: 'text-orange-600', border: 'border-orange-500', ring: 'ring-orange-500' },
        cyan: { bg: 'bg-cyan-500', text: 'text-cyan-600', border: 'border-cyan-500', ring: 'ring-cyan-500' },
        teal: { bg: 'bg-teal-500', text: 'text-teal-600', border: 'border-teal-500', ring: 'ring-teal-500' },
        violet: { bg: 'bg-violet-500', text: 'text-violet-600', border: 'border-violet-500', ring: 'ring-violet-500' },
        emerald: { bg: 'bg-emerald-500', text: 'text-emerald-600', border: 'border-emerald-500', ring: 'ring-emerald-500' },
        rose: { bg: 'bg-rose-500', text: 'text-rose-600', border: 'border-rose-500', ring: 'ring-rose-500' },
        amber: { bg: 'bg-amber-500', text: 'text-amber-600', border: 'border-amber-500', ring: 'ring-amber-500' },
    };
    return colors[color]?.[type] || colors.blue[type];
};

const getProgressColor = () => {
    if (overallProgress.value >= 80) return 'bg-green-500';
    if (overallProgress.value >= 50) return 'bg-yellow-500';
    return 'bg-orange-500';
};
</script>

<template>
    <div class="space-y-6">
        <div>
            <h3 class="text-lg font-medium text-gray-900">Profile Completeness</h3>
            <p class="mt-1 text-sm text-gray-600">
                Track your profile completion and improve your chances for visa applications and job opportunities.
            </p>
        </div>

        <!-- Overall Progress -->
        <div class="rounded-lg border border-gray-200 bg-white p-6 shadow-sm">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <h4 class="text-lg font-semibold text-gray-900">Overall Completion</h4>
                    <p class="text-sm text-gray-600">{{ completedCount }} of {{ sections.length }} sections completed</p>
                </div>
                <div class="text-right">
                    <div class="text-4xl font-bold" :class="overallProgress >= 80 ? 'text-green-600' : overallProgress >= 50 ? 'text-yellow-600' : 'text-orange-600'">
                        {{ overallProgress }}%
                    </div>
                </div>
            </div>

            <!-- Progress Bar -->
            <div class="relative h-4 overflow-hidden rounded-full bg-gray-200">
                <div 
                    class="h-full transition-all duration-500 ease-out"
                    :class="getProgressColor()"
                    :style="{ width: `${overallProgress}%` }"
                ></div>
            </div>

            <!-- Milestones -->
            <div class="mt-4 grid gap-3 md:grid-cols-3">
                <div class="rounded-lg bg-orange-50 p-3 text-center">
                    <div class="text-2xl font-bold text-orange-600">Basic</div>
                    <div class="text-xs text-orange-800">0-49% Complete</div>
                </div>
                <div class="rounded-lg bg-yellow-50 p-3 text-center">
                    <div class="text-2xl font-bold text-yellow-600">Good</div>
                    <div class="text-xs text-yellow-800">50-79% Complete</div>
                </div>
                <div class="rounded-lg bg-green-50 p-3 text-center">
                    <div class="text-2xl font-bold text-green-600">Excellent</div>
                    <div class="text-xs text-green-800">80-100% Complete</div>
                </div>
            </div>
        </div>

        <!-- Benefits Notice -->
        <div class="rounded-lg border border-blue-200 bg-blue-50 p-4">
            <div class="flex items-start">
                <svg class="h-5 w-5 text-blue-600 mr-3 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <div>
                    <h4 class="text-sm font-medium text-blue-900">Why Complete Your Profile?</h4>
                    <ul class="mt-2 text-sm text-blue-800 space-y-1">
                        <li>✓ Higher approval rates for visa applications</li>
                        <li>✓ Better job matching and opportunities</li>
                        <li>✓ Faster application processing</li>
                        <li>✓ Increased credibility with agencies and employers</li>
                        <li>✓ Access to premium services and features</li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Sections Grid -->
        <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-3">
            <Link
                v-for="section in sectionProgress"
                :key="section.id"
                :href="route('profile.edit', { section: section.id })"
                class="group relative rounded-lg border bg-white p-4 shadow-sm transition-all hover:shadow-md hover:-translate-y-1"
                :class="section.completed ? 'border-gray-200' : 'border-gray-300'"
            >
                <div class="flex items-start justify-between">
                    <div class="flex items-start gap-3">
                        <div 
                            class="flex h-10 w-10 flex-shrink-0 items-center justify-center rounded-lg transition-colors"
                            :class="section.completed ? getColorClasses(section.color, 'bg') : 'bg-gray-200'"
                        >
                            <component 
                                :is="section.icon" 
                                class="h-5 w-5"
                                :class="section.completed ? 'text-white' : 'text-gray-500'"
                            />
                        </div>
                        <div class="flex-1">
                            <h5 class="font-medium text-gray-900 group-hover:text-indigo-600 transition-colors">
                                {{ section.name }}
                            </h5>
                            <p class="mt-1 text-xs text-gray-500">
                                Weight: {{ section.weight }}%
                            </p>
                        </div>
                    </div>

                    <!-- Status Icon -->
                    <div v-if="section.completed" class="flex-shrink-0">
                        <CheckCircleIcon :class="`h-6 w-6 ${getColorClasses(section.color, 'text')}`" />
                    </div>
                    <div v-else class="flex-shrink-0">
                        <ExclamationCircleIcon class="h-6 w-6 text-gray-400" />
                    </div>
                </div>

                <!-- Progress Indicator -->
                <div class="mt-3">
                    <div class="h-2 overflow-hidden rounded-full bg-gray-200">
                        <div 
                            class="h-full transition-all duration-300"
                            :class="getColorClasses(section.color, 'bg')"
                            :style="{ width: `${section.progress}%` }"
                        ></div>
                    </div>
                </div>

                <!-- Status Badge -->
                <div class="mt-2">
                    <span 
                        v-if="section.completed"
                        class="inline-flex items-center gap-1 rounded-full px-2 py-1 text-xs font-medium"
                        :class="`${getColorClasses(section.color, 'bg')} text-white`"
                    >
                        <CheckCircleIcon class="h-3 w-3" />
                        Completed
                    </span>
                    <span 
                        v-else
                        class="inline-flex items-center gap-1 rounded-full bg-gray-100 px-2 py-1 text-xs font-medium text-gray-600"
                    >
                        <ExclamationCircleIcon class="h-3 w-3" />
                        Incomplete
                    </span>
                </div>

                <!-- Hover Arrow -->
                <div class="absolute bottom-4 right-4 opacity-0 transition-opacity group-hover:opacity-100">
                    <svg class="h-5 w-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                </div>
            </Link>
        </div>

        <!-- Recommendations -->
        <div v-if="overallProgress < 100" class="rounded-lg border border-amber-200 bg-amber-50 p-4">
            <h4 class="mb-3 flex items-center gap-2 text-sm font-medium text-amber-900">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z" />
                </svg>
                Next Steps to Improve Your Profile
            </h4>
            <div class="space-y-2">
                <div
                    v-for="section in sectionProgress.filter(s => !s.completed).slice(0, 3)"
                    :key="section.id"
                    class="flex items-center justify-between rounded-lg bg-white p-3"
                >
                    <div class="flex items-center gap-3">
                        <div 
                            class="flex h-8 w-8 items-center justify-center rounded-lg"
                            :class="`${getColorClasses(section.color, 'bg')} bg-opacity-20`"
                        >
                            <component 
                                :is="section.icon" 
                                class="h-4 w-4"
                                :class="getColorClasses(section.color, 'text')"
                            />
                        </div>
                        <div>
                            <div class="text-sm font-medium text-gray-900">{{ section.name }}</div>
                            <div class="text-xs text-gray-500">+{{ section.weight }}% completion</div>
                        </div>
                    </div>
                    <Link
                        :href="route('profile.edit', { section: section.id })"
                        class="rounded-lg px-3 py-1 text-xs font-medium text-white transition-colors"
                        :class="getColorClasses(section.color, 'bg')"
                    >
                        Complete Now
                    </Link>
                </div>
            </div>
        </div>

        <!-- Achievement Badge -->
        <div v-if="overallProgress === 100" class="rounded-lg border-2 border-green-300 bg-green-50 p-6 text-center">
            <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-full bg-green-500">
                <CheckCircleIcon class="h-10 w-10 text-white" />
            </div>
            <h4 class="mt-4 text-xl font-bold text-green-900">🎉 Profile Complete!</h4>
            <p class="mt-2 text-sm text-green-800">
                Congratulations! You've completed your profile. You now have the best chance for successful applications.
            </p>
        </div>
    </div>
</template>
