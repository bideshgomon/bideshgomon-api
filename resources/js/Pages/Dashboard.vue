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
    suggestions: Array,
    recommendedServices: Array,
    topReferrers: Array,
    userRank: [Number, null],
    availableServices: Array
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

// Always show profile shortcuts first
const profileShortcuts = [
    { 
        name: 'Edit Profile', 
        icon: 'user', 
        route: 'profile.edit', 
        description: 'Update your information',
        color: 'blue'
    },
    { 
        name: 'Education', 
        icon: 'academic', 
        route: 'profile.edit', 
        params: { section: 'education' }, 
        badge: props.stats?.education_count, 
        description: 'Manage academic records',
        color: 'green'
    },
    { 
        name: 'Work Experience', 
        icon: 'briefcase', 
        route: 'profile.edit', 
        params: { section: 'experience' }, 
        badge: props.stats?.experience_count, 
        description: 'Track employment history',
        color: 'purple'
    }
];

// Featured services from database
const featuredServices = computed(() => {
    if (!props.availableServices?.length) return [];
    return props.availableServices
        .filter(s => s.is_featured)
        .slice(0, 3)
        .map(s => ({
            name: s.name,
            icon: s.icon,
            route: s.route || 'services.show',
            params: s.route_params,
            description: s.description,
            color: 'orange'
        }));
});

// Other services from database
const otherServices = computed(() => {
    if (!props.availableServices?.length) return [];
    return props.availableServices
        .filter(s => !s.is_featured)
        .map(s => ({
            name: s.name,
            icon: s.icon,
            route: s.route || 'services.show',
            params: s.route_params,
            description: s.description,
            color: 'gray'
        }));
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
                                    <div class="p-3 rounded-lg" :class="`bg-${service.color}-100`">
                                        <component 
                                            :is="getIcon(service.icon)" 
                                            class="h-6 w-6 sm:h-7 sm:w-7" 
                                            :class="`text-${service.color}-600`"
                                        />
                                    </div>
                                    <span 
                                        v-if="service.badge" 
                                        class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-gray-100 text-gray-800"
                                    >
                                        {{ service.badge }}
                                    </span>
                                </div>
                                <h3 class="text-lg font-semibold text-gray-900 mb-2 group-hover:text-blue-600 transition-colors">
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

                <!-- Featured Services -->
                <section v-if="featuredServices.length > 0" class="mb-8 sm:mb-12">
                    <div class="flex items-center mb-4 sm:mb-6 px-1">
                        <FireIcon class="h-6 w-6 text-orange-500 mr-2" />
                        <h2 class="text-xl sm:text-2xl font-bold text-gray-900">Featured Services</h2>
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 sm:gap-6">
                        <Link 
                            v-for="service in featuredServices" 
                            :key="service.name"
                            :href="route(service.route, service.params || {})"
                            class="bg-gradient-to-br from-orange-50 to-white rounded-xl shadow-sm hover:shadow-lg transition-all duration-300 overflow-hidden group border border-orange-100"
                        >
                            <div class="p-6">
                                <div class="p-3 bg-orange-100 rounded-lg inline-block mb-4">
                                    <component 
                                        :is="getIcon(service.icon)" 
                                        class="h-6 w-6 sm:h-7 sm:w-7 text-orange-600"
                                    />
                                </div>
                                <h3 class="text-lg font-semibold text-gray-900 mb-2 group-hover:text-orange-600 transition-colors">
                                    {{ service.name }}
                                </h3>
                                <p class="text-sm text-gray-600 mb-4">{{ service.description }}</p>
                                <div class="inline-flex items-center px-4 py-2 rounded-lg text-sm font-semibold text-white bg-orange-600 group-hover:bg-orange-700 transition-colors">
                                    <span>Explore</span>
                                    <ChevronRightIcon class="h-4 w-4 ml-1 group-hover:translate-x-1 transition-transform" />
                                </div>
                            </div>
                        </Link>
                    </div>
                </section>

                <!-- All Services -->
                <section v-if="otherServices.length > 0">
                    <h2 class="text-xl sm:text-2xl font-bold text-gray-900 mb-4 sm:mb-6 px-1">All Services</h2>
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4 sm:gap-6">
                        <Link 
                            v-for="service in otherServices" 
                            :key="service.name"
                            :href="route(service.route, service.params || {})"
                            class="bg-white rounded-xl shadow-sm hover:shadow-lg transition-all duration-300 overflow-hidden group"
                        >
                            <div class="p-6">
                                <div class="p-3 bg-gray-100 rounded-lg inline-block mb-4">
                                    <component 
                                        :is="getIcon(service.icon)" 
                                        class="h-6 w-6 text-gray-600"
                                    />
                                </div>
                                <h3 class="text-base font-semibold text-gray-900 mb-2 group-hover:text-blue-600 transition-colors">
                                    {{ service.name }}
                                </h3>
                                <p class="text-sm text-gray-600 mb-4 line-clamp-2">{{ service.description }}</p>
                                <div class="flex items-center text-sm font-medium text-blue-600 group-hover:text-blue-700">
                                    <span>View</span>
                                    <ChevronRightIcon class="h-4 w-4 ml-1 group-hover:translate-x-1 transition-transform" />
                                </div>
                            </div>
                        </Link>
                    </div>
                </section>

                <!-- Suggestions Section -->
                <section v-if="suggestions && suggestions.length > 0" class="mt-8 sm:mt-12">
                    <h2 class="text-xl sm:text-2xl font-bold text-gray-900 mb-4 sm:mb-6 px-1">Suggestions for You</h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 sm:gap-6">
                        <div 
                            v-for="suggestion in suggestions" 
                            :key="suggestion.title"
                            class="bg-blue-50 border border-blue-200 rounded-xl p-6 hover:shadow-md transition-shadow"
                        >
                            <h3 class="text-lg font-semibold text-gray-900 mb-2">{{ suggestion.title }}</h3>
                            <p class="text-sm text-gray-600 mb-4">{{ suggestion.message }}</p>
                            <Link 
                                v-if="suggestion.action_route"
                                :href="route(suggestion.action_route)" 
                                class="inline-flex items-center px-4 py-2 rounded-lg text-sm font-semibold text-white bg-orange-600 hover:bg-orange-700 transition-colors"
                            >
                                {{ suggestion.action_text }}
                                <ChevronRightIcon class="h-4 w-4 ml-1" />
                            </Link>
                        </div>
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
