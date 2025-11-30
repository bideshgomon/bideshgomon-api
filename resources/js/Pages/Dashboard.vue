<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import { 
    SparklesIcon, 
    CurrencyDollarIcon, 
    TrophyIcon, 
    ShieldCheckIcon, 
    ClockIcon, 
    LightBulbIcon, 
    GlobeAltIcon, 
    DocumentTextIcon,
    UserCircleIcon,
    AcademicCapIcon,
    BriefcaseIcon,
    DocumentCheckIcon,
    ChevronRightIcon,
    FireIcon
} from '@heroicons/vue/24/outline';
import { computed } from 'vue';

const props = defineProps({
    stats: Object,
    profileCompletion: Number,
    recentActivity: Array,
    suggestions: Array,  // Profile completion suggestions (context-aware)
    recommendedServices: Array,
    topReferrers: Array,
    userRank: [Number, null],
    availableServices: Array, // platform_tool and support services for dashboard
    extendedProfileServices: Array // No longer used - kept for backward compatibility
});

// Icon mapping for dynamic service icons
const iconMap = {
    'sparkles': SparklesIcon,
    'currency': CurrencyDollarIcon,
    'trophy': TrophyIcon,
    'shield': ShieldCheckIcon,
    'clock': ClockIcon,
    'lightbulb': LightBulbIcon,
    'globe': GlobeAltIcon,
    'document': DocumentTextIcon,
    'user': UserCircleIcon,
    'academic': AcademicCapIcon,
    'briefcase': BriefcaseIcon,
    'check': DocumentCheckIcon,
    'fire': FireIcon
};

// Get icon component by name, fallback to DocumentCheckIcon
const getIcon = (iconName) => iconMap[iconName?.toLowerCase()] || DocumentCheckIcon;

// Profile completion color
const completionColor = computed(() => {
    if (props.profileCompletion >= 80) return 'bg-green-500';
    if (props.profileCompletion >= 50) return 'bg-orange-500';
    return 'bg-red-500';
});

// CORE PROFILE: Always visible, hardcoded (not from database)
// These are essential profile actions that every user needs
const profileShortcuts = [
    { 
        name: 'Edit Profile', 
        icon: 'user', 
        route: 'profile.edit', 
        description: 'Update your basic information',
        color: 'blue',
        bgColor: 'bg-blue-100',
        textColor: 'text-blue-600',
        hoverColor: 'group-hover:text-blue-600'
    },
    { 
        name: 'Education', 
        icon: 'academic', 
        route: 'profile.edit', 
        params: { section: 'education' }, 
        badge: props.stats?.education_count, 
        description: 'Manage academic records',
        color: 'green',
        bgColor: 'bg-emerald-100',
        textColor: 'text-emerald-600',
        hoverColor: 'group-hover:text-emerald-600'
    },
    { 
        name: 'Work Experience', 
        icon: 'briefcase', 
        route: 'profile.edit', 
        params: { section: 'experience' }, 
        badge: props.stats?.experience_count, 
        description: 'Track employment history',
        color: 'purple',
        bgColor: 'bg-purple-100',
        textColor: 'text-purple-600',
        hoverColor: 'group-hover:text-purple-600'
    }
];

// PLATFORM SERVICES: Utilities and tools available to users
// Shown in dashboard for easy access (wallet, cv builder, ai assessment, etc.)
const platformServices = computed(() => {
    if (!props.availableServices?.length) return [];
    
    // Color mapping for different service types
    const serviceColors = {
        'wallet': { bg: 'bg-green-100', text: 'text-green-600' },
        'referrals': { bg: 'bg-pink-100', text: 'text-pink-600' },
        'ai-profile-assessment': { bg: 'bg-purple-100', text: 'text-purple-600' },
        'cv-builder': { bg: 'bg-indigo-100', text: 'text-indigo-600' },
        'document-scanner': { bg: 'bg-cyan-100', text: 'text-cyan-600' },
        'public-profile': { bg: 'bg-teal-100', text: 'text-teal-600' },
        'payments': { bg: 'bg-amber-100', text: 'text-amber-600' },
        'support': { bg: 'bg-rose-100', text: 'text-rose-600' },
        'appointments': { bg: 'bg-violet-100', text: 'text-violet-600' },
        'faqs': { bg: 'bg-sky-100', text: 'text-sky-600' },
        'events': { bg: 'bg-orange-100', text: 'text-orange-600' }
    };
    
    return props.availableServices.map(s => {
        const colors = serviceColors[s.slug] || { bg: 'bg-gray-100', text: 'text-gray-600' };
        return {
            name: s.name,
            icon: s.icon,
            route: s.route || 'services.show',
            params: s.route_params,
            description: s.description,
            color: s.is_featured ? 'orange' : 'gray',
            is_featured: s.is_featured,
            service_type: s.service_type,
            bgColor: s.is_featured ? 'bg-orange-100' : colors.bg,
            textColor: s.is_featured ? 'text-orange-600' : colors.text
        };
    });
});

// Get color classes for buttons
const getColorClasses = (color) => {
    const colors = {
        blue: 'bg-blue-600 hover:bg-blue-700 focus:ring-blue-500',
        orange: 'bg-orange-600 hover:bg-orange-700 focus:ring-orange-500',
        green: 'bg-green-600 hover:bg-green-700 focus:ring-green-500',
        purple: 'bg-purple-600 hover:bg-purple-700 focus:ring-purple-500',
        gray: 'bg-gray-600 hover:bg-gray-700 focus:ring-gray-500'
    };
    return colors[color] || colors.gray;
};
</script>

<template>
    <Head title="Dashboard" />

    <AuthenticatedLayout>
        <div class="py-6 sm:py-12">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                
                <!-- Profile Completion Card -->
                <div class="bg-white rounded-2xl shadow-sm overflow-hidden mb-6 sm:mb-8">
                    <div class="p-6 sm:p-8">
                        <div class="flex items-center justify-between mb-4">
                            <h2 class="text-xl sm:text-2xl font-bold text-gray-900">Profile Strength</h2>
                            <span class="text-2xl sm:text-3xl font-bold" :class="profileCompletion >= 80 ? 'text-green-600' : 'text-orange-600'">
                                {{ profileCompletion }}%
                            </span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-3 sm:h-4 mb-4">
                            <div 
                                :class="completionColor" 
                                class="h-3 sm:h-4 rounded-full transition-all duration-500"
                                :style="{ width: profileCompletion + '%' }"
                            ></div>
                        </div>
                        <p class="text-sm text-gray-600">
                            {{ profileCompletion >= 80 ? 'Great! Your profile is strong.' : 'Complete your profile to improve your chances.' }}
                        </p>
                    </div>
                </div>

                <!-- Profile Management -->
                <section class="mb-8 sm:mb-12">
                    <h2 class="text-xl sm:text-2xl font-bold text-gray-900 mb-4 sm:mb-6 px-1">Profile Management</h2>
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 sm:gap-6">
                        <Link 
                            v-for="service in profileShortcuts" 
                            :key="service.route"
                            :href="route(service.route, service.params || {})"
                            class="bg-white rounded-xl shadow-sm hover:shadow-lg transition-all duration-300 overflow-hidden group"
                        >
                            <div class="p-6">
                                <div class="flex items-start justify-between mb-4">
                                    <div class="p-3 rounded-lg" :class="service.bgColor">
                                        <component 
                                            :is="getIcon(service.icon)" 
                                            class="h-6 w-6 sm:h-7 sm:w-7" 
                                            :class="service.textColor"
                                        />
                                    </div>
                                    <span 
                                        v-if="service.badge" 
                                        class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-gray-100 text-gray-800"
                                    >
                                        {{ service.badge }}
                                    </span>
                                </div>
                                <h3 class="text-lg font-semibold text-gray-900 mb-2 transition-colors" :class="service.hoverColor">
                                    {{ service.name }}
                                </h3>
                                <p class="text-sm text-gray-600 mb-4">{{ service.description }}</p>
                                <div class="flex items-center text-sm font-medium text-blue-600 group-hover:text-blue-700">
                                    <span>Manage</span>
                                    <ChevronRightIcon class="h-4 w-4 ml-1 group-hover:translate-x-1 transition-transform" />
                                </div>
                            </div>
                        </Link>
                    </div>
                </section>

                <!-- Suggestions Section (Context-Aware Profile Completion) -->
                <section v-if="suggestions && suggestions.length > 0" class="mb-8 sm:mb-12">
                    <div class="flex items-center mb-4 sm:mb-6 px-1">
                        <LightBulbIcon class="h-6 w-6 text-yellow-500 mr-2" />
                        <h2 class="text-xl sm:text-2xl font-bold text-gray-900">Improve Your Profile</h2>
                    </div>
                    <p class="text-sm text-gray-600 mb-4 px-1">Complete these sections to strengthen your profile for visa applications</p>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 sm:gap-6">
                        <div 
                            v-for="suggestion in suggestions" 
                            :key="suggestion.title"
                            class="bg-yellow-50 border-2 border-yellow-200 rounded-xl p-6 hover:shadow-lg transition-shadow group"
                        >
                            <div class="flex items-start mb-4">
                                <div class="p-3 bg-yellow-100 rounded-lg">
                                    <component :is="getIcon(suggestion.icon)" class="h-6 w-6 text-yellow-600" />
                                </div>
                            </div>
                            <h3 class="text-lg font-semibold text-gray-900 mb-2">{{ suggestion.title }}</h3>
                            <p class="text-sm text-gray-600 mb-4">{{ suggestion.message }}</p>
                            <Link 
                                v-if="suggestion.action_route"
                                :href="route(suggestion.action_route)" 
                                class="inline-flex items-center px-4 py-2 rounded-lg text-sm font-semibold text-white bg-yellow-600 hover:bg-yellow-700 transition-colors group-hover:shadow-md"
                            >
                                {{ suggestion.action_text }}
                                <ChevronRightIcon class="h-4 w-4 ml-1 group-hover:translate-x-1 transition-transform" />
                            </Link>
                        </div>
                    </div>
                </section>

                <!-- Platform Services -->
                <section v-if="platformServices.length > 0" class="mb-8 sm:mb-12">
                    <h2 class="text-xl sm:text-2xl font-bold text-gray-900 mb-4 sm:mb-6 px-1">Your Services & Tools</h2>
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4 sm:gap-6">
                        <Link 
                            v-for="service in platformServices" 
                            :key="service.name"
                            :href="route(service.route, service.params || {})"
                            class="bg-white rounded-xl shadow-sm hover:shadow-lg transition-all duration-300 overflow-hidden group"
                            :class="service.is_featured ? 'border-2 border-orange-200' : ''"
                        >
                            <div class="p-6">
                                <div class="p-3 rounded-lg inline-block mb-4" :class="service.bgColor">
                                    <component 
                                        :is="getIcon(service.icon)" 
                                        class="h-6 w-6"
                                        :class="service.textColor"
                                    />
                                </div>
                                <h3 class="text-base font-semibold text-gray-900 mb-2 group-hover:text-blue-600 transition-colors">
                                    {{ service.name }}
                                </h3>
                                <p class="text-sm text-gray-600 mb-4 line-clamp-2">{{ service.description }}</p>
                                <div class="flex items-center text-sm font-medium" 
                                    :class="service.is_featured ? 'text-orange-600 group-hover:text-orange-700' : 'text-blue-600 group-hover:text-blue-700'">
                                    <span>{{ service.is_featured ? 'Explore' : 'Open' }}</span>
                                    <ChevronRightIcon class="h-4 w-4 ml-1 group-hover:translate-x-1 transition-transform" />
                                </div>
                            </div>
                        </Link>
                    </div>
                </section>

                <!-- Browse Revenue Services -->
                <section class="mb-8 sm:mb-12">
                    <div class="bg-blue-50 border-2 border-blue-200 rounded-2xl p-8 text-center">
                        <div class="inline-flex items-center justify-center p-4 bg-blue-100 rounded-full mb-4">
                            <GlobeAltIcon class="h-10 w-10 text-blue-600" />
                        </div>
                        <h2 class="text-2xl font-bold text-gray-900 mb-3">Looking for Visa Services?</h2>
                        <p class="text-gray-600 mb-6 max-w-2xl mx-auto">
                            Explore our comprehensive visa services, travel insurance, flight bookings, and more on our dedicated services page.
                        </p>
                        <Link 
                            :href="route('services.index')"
                            class="inline-flex items-center px-6 py-3 rounded-xl text-base font-semibold text-white bg-blue-600 hover:bg-blue-700 transition-all shadow-lg hover:shadow-xl"
                        >
                            Browse All Services
                            <ChevronRightIcon class="h-5 w-5 ml-2" />
                        </Link>
                    </div>
                </section>

                <!-- Leaderboard Section -->
                <section v-if="topReferrers && topReferrers.length > 0" class="mt-8 sm:mt-12">
                    <h2 class="text-xl sm:text-2xl font-bold text-gray-900 mb-4 sm:mb-6 px-1">Top Referrers This Month</h2>
                    <div class="bg-white rounded-xl shadow-sm overflow-hidden">
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Rank</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Referrals</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    <tr v-for="(referrer, index) in topReferrers" :key="referrer.id" :class="userRank && userRank.user_id === referrer.id ? 'bg-blue-50' : ''">
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                            <span v-if="index === 0" class="text-yellow-500">🥇</span>
                                            <span v-else-if="index === 1" class="text-gray-400">🥈</span>
                                            <span v-else-if="index === 2" class="text-orange-600">🥉</span>
                                            <span v-else>{{ index + 1 }}</span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ referrer.name }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ referrer.total_referrals }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </section>

            </div>
        </div>
    </AuthenticatedLayout>
</template>
