<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import { computed } from 'vue';
import { 
    AcademicCapIcon, BriefcaseIcon, DocumentCheckIcon, UserCircleIcon,
    ArrowTrendingUpIcon, SparklesIcon, CurrencyDollarIcon, ClockIcon, ShieldCheckIcon, DocumentTextIcon,
    LightBulbIcon, PhoneIcon, ExclamationCircleIcon, GlobeAltIcon
} from '@heroicons/vue/24/outline';

const props = defineProps({
    profileCompletion: Number,
    stats: Object,
    recentActivity: Array,
    suggestions: Array,
    recommendedServices: Array,
});

const services = [
    {
        id: 'education',
        name: 'Education',
        description: 'Manage your academic records',
        icon: AcademicCapIcon,
        route: 'profile.edit',
        params: { section: 'education' },
        color: 'from-blue-500 to-blue-600',
        badge: props.stats?.education_count || 0
    },
    {
        id: 'experience',
        name: 'Work Experience',
        description: 'Track your employment history',
        icon: BriefcaseIcon,
        route: 'profile.edit',
        params: { section: 'experience' },
        color: 'from-purple-500 to-purple-600',
        badge: props.stats?.experience_count || 0
    },
    {
        id: 'skills',
        name: 'Skills & Expertise',
        description: 'Professional skills and competencies',
        icon: LightBulbIcon,
        route: 'profile.edit',
        params: { section: 'skills' },
        color: 'from-yellow-500 to-orange-600',
        badge: props.stats?.skills_count || 0
    },
    {
        id: 'travel',
        name: 'Travel History',
        description: 'International travel records',
        icon: GlobeAltIcon,
        route: 'profile.edit',
        params: { section: 'travel' },
        color: 'from-cyan-500 to-blue-600',
        badge: props.stats?.travel_count || 0
    },
    {
        id: 'family',
        name: 'Family',
        description: 'Family members & relationships',
        icon: UserCircleIcon,
        route: 'profile.edit',
        params: { section: 'family' },
        color: 'from-pink-500 to-pink-600',
        badge: props.stats?.family_count || 0
    },
    {
        id: 'financial',
        name: 'Financial',
        description: 'Income & financial details',
        icon: CurrencyDollarIcon,
        route: 'profile.edit',
        params: { section: 'financial' },
        color: 'from-green-500 to-green-600',
        badge: null
    },
    {
        id: 'languages',
        name: 'Languages',
        description: 'Language proficiency levels',
        icon: SparklesIcon,
        route: 'profile.edit',
        params: { section: 'languages' },
        color: 'from-indigo-500 to-indigo-600',
        badge: props.stats?.languages_count || 0
    },
    {
        id: 'security',
        name: 'Security',
        description: 'Background & police clearance',
        icon: ShieldCheckIcon,
        route: 'profile.edit',
        params: { section: 'security' },
        color: 'from-red-500 to-red-600',
        badge: null
    }
];

const completionColor = computed(() => {
    const completion = props.profileCompletion || 0;
    if (completion < 30) return 'text-red-600 bg-red-50';
    if (completion < 60) return 'text-yellow-600 bg-yellow-50';
    if (completion < 80) return 'text-blue-600 bg-blue-50';
    return 'text-green-600 bg-green-50';
});

const completionText = computed(() => {
    const completion = props.profileCompletion || 0;
    if (completion < 30) return 'Just Started';
    if (completion < 60) return 'In Progress';
    if (completion < 80) return 'Almost There';
    return 'Complete';
});
</script>

<template>
    <Head title="Dashboard" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                Dashboard
            </h2>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <!-- Welcome Banner -->
                <div class="mb-8 overflow-hidden rounded-lg bg-gradient-to-r from-indigo-600 to-purple-600 p-8 text-white shadow-lg">
                    <div class="flex flex-col sm:flex-row items-center justify-between gap-4">
                        <div>
                            <h1 class="text-3xl font-bold mb-2">Welcome back!</h1>
                            <p class="text-indigo-100 text-lg">Complete your profile to unlock all features</p>
                        </div>
                        <Link 
                            :href="route('profile.edit')" 
                            class="rounded-lg bg-white px-6 py-3 text-indigo-600 font-semibold hover:bg-indigo-50 transition-colors whitespace-nowrap"
                        >
                            Complete Profile
                        </Link>
                    </div>
                </div>

                <!-- Stats Grid -->
                <div class="grid gap-6 mb-8 md:grid-cols-2 lg:grid-cols-4">
                    <!-- Profile Completion -->
                    <div class="bg-white rounded-lg shadow p-6">
                        <div class="flex items-center justify-between mb-4">
                            <div>
                                <p class="text-sm font-medium text-gray-600">Profile Completion</p>
                                <p class="text-3xl font-bold text-gray-900">{{ profileCompletion || 0 }}%</p>
                            </div>
                            <div :class="[completionColor, 'p-3 rounded-full']">
                                <UserCircleIcon class="h-8 w-8" />
                            </div>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-2">
                            <div 
                                class="bg-gradient-to-r from-indigo-500 to-purple-500 h-2 rounded-full transition-all duration-500"
                                :style="{ width: `${profileCompletion || 0}%` }"
                            ></div>
                        </div>
                        <p class="text-xs text-gray-500 mt-2">{{ completionText }}</p>
                    </div>

                    <!-- Education Records -->
                    <div class="bg-white rounded-lg shadow p-6">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm font-medium text-gray-600">Education</p>
                                <p class="text-3xl font-bold text-gray-900">{{ stats?.education_count || 0 }}</p>
                                <p class="text-xs text-gray-500 mt-1">Academic records</p>
                            </div>
                            <div class="bg-blue-50 p-3 rounded-full">
                                <AcademicCapIcon class="h-8 w-8 text-blue-600" />
                            </div>
                        </div>
                    </div>

                    <!-- Work Experience -->
                    <div class="bg-white rounded-lg shadow p-6">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm font-medium text-gray-600">Experience</p>
                                <p class="text-3xl font-bold text-gray-900">{{ stats?.experience_count || 0 }}</p>
                                <p class="text-xs text-gray-500 mt-1">Work history</p>
                            </div>
                            <div class="bg-purple-50 p-3 rounded-full">
                                <BriefcaseIcon class="h-8 w-8 text-purple-600" />
                            </div>
                        </div>
                    </div>

                    <!-- Profile Strength -->
                    <div class="bg-white rounded-lg shadow p-6">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm font-medium text-gray-600">Profile Strength</p>
                                <p class="text-3xl font-bold text-gray-900">
                                    {{ stats?.profile_strength || 'Basic' }}
                                </p>
                                <p class="text-xs text-gray-500 mt-1">Current level</p>
                            </div>
                            <div class="bg-green-50 p-3 rounded-full">
                                <ArrowTrendingUpIcon class="h-8 w-8 text-green-600" />
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Smart Suggestions -->
                <div v-if="suggestions && suggestions.length > 0" class="mb-8">
                    <div class="flex items-center gap-2 mb-4">
                        <LightBulbIcon class="h-6 w-6 text-yellow-500" />
                        <h3 class="text-lg font-semibold text-gray-900">Recommended for You</h3>
                    </div>
                    <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-4">
                        <div
                            v-for="(suggestion, index) in suggestions"
                            :key="index"
                            class="bg-white rounded-lg p-5 border-l-4 hover:shadow-lg transition-shadow"
                            :class="{
                                'border-red-500': suggestion.priority === 'high',
                                'border-yellow-500': suggestion.priority === 'medium',
                                'border-blue-500': suggestion.priority === 'low'
                            }"
                        >
                            <div class="flex items-start justify-between mb-3">
                                <div
                                    class="p-2 rounded-lg"
                                    :class="{
                                        'bg-red-50': suggestion.priority === 'high',
                                        'bg-yellow-50': suggestion.priority === 'medium',
                                        'bg-blue-50': suggestion.priority === 'low'
                                    }"
                                >
                                    <AcademicCapIcon
                                        v-if="suggestion.icon === 'academic'"
                                        class="h-5 w-5"
                                        :class="{
                                            'text-red-600': suggestion.priority === 'high',
                                            'text-yellow-600': suggestion.priority === 'medium',
                                            'text-blue-600': suggestion.priority === 'low'
                                        }"
                                    />
                                    <BriefcaseIcon
                                        v-else-if="suggestion.icon === 'briefcase'"
                                        class="h-5 w-5"
                                        :class="{
                                            'text-red-600': suggestion.priority === 'high',
                                            'text-yellow-600': suggestion.priority === 'medium',
                                            'text-blue-600': suggestion.priority === 'low'
                                        }"
                                    />
                                    <DocumentTextIcon
                                        v-else-if="suggestion.icon === 'document'"
                                        class="h-5 w-5"
                                        :class="{
                                            'text-red-600': suggestion.priority === 'high',
                                            'text-yellow-600': suggestion.priority === 'medium',
                                            'text-blue-600': suggestion.priority === 'low'
                                        }"
                                    />
                                    <PhoneIcon
                                        v-else-if="suggestion.icon === 'phone'"
                                        class="h-5 w-5"
                                        :class="{
                                            'text-red-600': suggestion.priority === 'high',
                                            'text-yellow-600': suggestion.priority === 'medium',
                                            'text-blue-600': suggestion.priority === 'low'
                                        }"
                                    />
                                    <SparklesIcon
                                        v-else
                                        class="h-5 w-5"
                                        :class="{
                                            'text-red-600': suggestion.priority === 'high',
                                            'text-yellow-600': suggestion.priority === 'medium',
                                            'text-blue-600': suggestion.priority === 'low'
                                        }"
                                    />
                                </div>
                                <span
                                    v-if="suggestion.priority === 'high'"
                                    class="text-xs font-semibold px-2 py-1 bg-red-100 text-red-700 rounded-full"
                                >
                                    Priority
                                </span>
                            </div>
                            <h4 class="font-bold text-gray-900 mb-2">{{ suggestion.title }}</h4>
                            <p class="text-sm text-gray-600 mb-4">{{ suggestion.description }}</p>
                            <Link
                                :href="route(suggestion.route)"
                                class="inline-flex items-center text-sm font-semibold hover:underline"
                                :class="{
                                    'text-red-600': suggestion.priority === 'high',
                                    'text-yellow-600': suggestion.priority === 'medium',
                                    'text-blue-600': suggestion.priority === 'low'
                                }"
                            >
                                Complete Now →
                            </Link>
                        </div>
                    </div>
                </div>

                <!-- Recommended Services -->
                <div v-if="recommendedServices && recommendedServices.length > 0" class="mb-8">
                    <div class="flex items-center gap-2 mb-4">
                        <SparklesIcon class="h-6 w-6 text-indigo-500" />
                        <h3 class="text-lg font-semibold text-gray-900">Based on Your Profile</h3>
                    </div>
                    <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-4">
                        <Link
                            v-for="(service, index) in recommendedServices"
                            :key="index"
                            :href="route(service.route)"
                            class="bg-white rounded-lg p-5 border-2 border-transparent hover:border-indigo-500 hover:shadow-lg transition-all"
                        >
                            <div
                                class="w-10 h-10 rounded-lg flex items-center justify-center mb-3"
                                :class="{
                                    'bg-blue-100': service.color === 'blue',
                                    'bg-purple-100': service.color === 'purple',
                                    'bg-green-100': service.color === 'green'
                                }"
                            >
                                <SparklesIcon
                                    class="h-5 w-5"
                                    :class="{
                                        'text-blue-600': service.color === 'blue',
                                        'text-purple-600': service.color === 'purple',
                                        'text-green-600': service.color === 'green'
                                    }"
                                />
                            </div>
                            <h4 class="font-bold text-gray-900 mb-2">{{ service.name }}</h4>
                            <p class="text-sm text-gray-600">{{ service.description }}</p>
                        </Link>
                    </div>
                </div>

                <!-- Service Cards Grid -->
                <div class="mb-8">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Our Services</h3>
                    
                    <!-- Travel Insurance Feature Card -->
                    <Link
                        :href="route('travel-insurance.index')"
                        class="block mb-4 bg-gradient-to-br from-emerald-600 to-emerald-800 rounded-2xl p-6 shadow-xl hover:shadow-2xl transition-all transform hover:-translate-y-1"
                    >
                        <div class="flex items-center justify-between text-white mb-4">
                            <div class="flex items-center space-x-3">
                                <div class="bg-white/20 p-3 rounded-xl">
                                    <ShieldCheckIcon class="h-8 w-8" />
                                </div>
                                <div>
                                    <h3 class="text-xl font-bold">Travel Insurance</h3>
                                    <p class="text-emerald-100 text-sm">Protect your journey worldwide</p>
                                </div>
                            </div>
                            <div class="bg-white/20 px-3 py-1 rounded-full text-xs font-semibold">
                                NEW
                            </div>
                        </div>
                        <div class="grid grid-cols-3 gap-3 text-white/90 text-xs">
                            <div>
                                <div class="font-semibold">6 Packages</div>
                                <div class="text-white/70">Available now</div>
                            </div>
                            <div>
                                <div class="font-semibold">From ৳150/day</div>
                                <div class="text-white/70">Affordable rates</div>
                            </div>
                            <div>
                                <div class="font-semibold">24/7 Support</div>
                                <div class="text-white/70">Global coverage</div>
                            </div>
                        </div>
                    </Link>

                    <!-- CV Builder Feature Card -->
                    <Link
                        :href="route('cv-builder.index')"
                        class="block mb-6 bg-gradient-to-br from-blue-600 to-indigo-800 rounded-2xl p-6 shadow-xl hover:shadow-2xl transition-all transform hover:-translate-y-1"
                    >
                        <div class="flex items-center justify-between text-white mb-4">
                            <div class="flex items-center space-x-3">
                                <div class="bg-white/20 p-3 rounded-xl">
                                    <DocumentTextIcon class="h-8 w-8" />
                                </div>
                                <div>
                                    <h3 class="text-xl font-bold">Professional CV Builder</h3>
                                    <p class="text-blue-100 text-sm">Create stunning CVs in minutes</p>
                                </div>
                            </div>
                            <div class="bg-white/20 px-3 py-1 rounded-full text-xs font-semibold">
                                NEW
                            </div>
                        </div>
                        <div class="grid grid-cols-3 gap-3 text-white/90 text-xs">
                            <div>
                                <div class="font-semibold">6 Templates</div>
                                <div class="text-white/70">Professional designs</div>
                            </div>
                            <div>
                                <div class="font-semibold">3 Free</div>
                                <div class="text-white/70">Premium from ৳300</div>
                            </div>
                            <div>
                                <div class="font-semibold">PDF Export</div>
                                <div class="text-white/70">Instant download</div>
                            </div>
                        </div>
                    </Link>

                    <!-- Job Opportunities Feature Card -->
                    <Link
                        :href="route('jobs.index')"
                        class="block mb-6 bg-gradient-to-br from-purple-600 to-pink-700 rounded-2xl p-6 shadow-xl hover:shadow-2xl transition-all transform hover:-translate-y-1"
                    >
                        <div class="flex items-center justify-between text-white mb-4">
                            <div class="flex items-center space-x-3">
                                <div class="bg-white/20 p-3 rounded-xl">
                                    <BriefcaseIcon class="h-8 w-8" />
                                </div>
                                <div>
                                    <h3 class="text-xl font-bold">Job Opportunities Abroad</h3>
                                    <p class="text-purple-100 text-sm">Find your dream job overseas</p>
                                </div>
                            </div>
                            <div class="bg-white/20 px-3 py-1 rounded-full text-xs font-semibold">
                                HOT
                            </div>
                        </div>
                        <div class="grid grid-cols-3 gap-3 text-white/90 text-xs">
                            <div>
                                <div class="font-semibold">10+ Jobs</div>
                                <div class="text-white/70">Active listings</div>
                            </div>
                            <div>
                                <div class="font-semibold">6 Countries</div>
                                <div class="text-white/70">UAE, Saudi & more</div>
                            </div>
                            <div>
                                <div class="font-semibold">Easy Apply</div>
                                <div class="text-white/70">One-click process</div>
                            </div>
                        </div>
                    </Link>
                </div>

                <!-- Profile Sections -->
                <div class="mb-8">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Profile Sections</h3>
                    <div class="grid gap-6 md:grid-cols-2 lg:grid-cols-3">
                        <Link
                            v-for="service in services"
                            :key="service.id"
                            :href="route(service.route, service.params)"
                            class="group relative bg-white rounded-lg shadow-md hover:shadow-xl transition-all duration-300 overflow-hidden"
                        >
                            <!-- Gradient Background -->
                            <div :class="['absolute inset-0 bg-gradient-to-br opacity-0 group-hover:opacity-10 transition-opacity', service.color]"></div>
                            
                            <div class="relative p-6">
                                <div class="flex items-start justify-between mb-4">
                                    <div :class="['p-3 rounded-lg bg-gradient-to-br', service.color]">
                                        <component :is="service.icon" class="h-6 w-6 text-white" />
                                    </div>
                                    <span 
                                        v-if="service.badge !== null"
                                        class="bg-gray-100 text-gray-700 text-xs font-semibold px-2.5 py-1 rounded-full"
                                    >
                                        {{ service.badge }} items
                                    </span>
                                </div>
                                
                                <h4 class="text-lg font-semibold text-gray-900 mb-2">{{ service.name }}</h4>
                                <p class="text-sm text-gray-600">{{ service.description }}</p>
                                
                                <div class="mt-4 flex items-center text-sm font-medium text-indigo-600 group-hover:text-indigo-700">
                                    Manage
                                    <svg class="ml-1 h-4 w-4 transition-transform group-hover:translate-x-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                    </svg>
                                </div>
                            </div>
                        </Link>
                    </div>
                </div>

                <!-- Recent Activity -->
                <div v-if="recentActivity && recentActivity.length > 0" class="bg-white rounded-lg shadow">
                    <div class="border-b border-gray-200 px-6 py-4">
                        <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                            <ClockIcon class="h-5 w-5 mr-2 text-gray-400" />
                            Recent Activity
                        </h3>
                    </div>
                    <div class="p-6">
                        <div class="space-y-4">
                            <div 
                                v-for="(activity, index) in recentActivity.slice(0, 5)" 
                                :key="index"
                                class="flex items-start space-x-3 pb-4 border-b border-gray-100 last:border-0"
                            >
                                <div class="flex-shrink-0 h-8 w-8 rounded-full bg-indigo-100 flex items-center justify-center">
                                    <SparklesIcon class="h-4 w-4 text-indigo-600" />
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-medium text-gray-900">{{ activity.description }}</p>
                                    <p class="text-xs text-gray-500">{{ activity.date }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Empty State -->
                <div v-else class="bg-white rounded-lg shadow p-12 text-center">
                    <SparklesIcon class="mx-auto h-12 w-12 text-gray-400 mb-4" />
                    <h3 class="text-lg font-medium text-gray-900 mb-2">No activity yet</h3>
                    <p class="text-gray-600 mb-6">Start by completing your profile sections</p>
                    <Link 
                        :href="route('profile.edit')" 
                        class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors"
                    >
                        Get Started
                    </Link>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
