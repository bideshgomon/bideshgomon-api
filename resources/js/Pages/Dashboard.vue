<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import { computed } from 'vue';
import RhythmicCard from '@/Components/Rhythmic/RhythmicCard.vue';
import FlowButton from '@/Components/Rhythmic/FlowButton.vue';
import AnimatedSection from '@/Components/Rhythmic/AnimatedSection.vue';
import ProgressWave from '@/Components/Rhythmic/ProgressWave.vue';
import StatusBadge from '@/Components/Rhythmic/StatusBadge.vue';
import { 
    AcademicCapIcon, BriefcaseIcon, DocumentCheckIcon, UserCircleIcon,
    ArrowTrendingUpIcon, SparklesIcon, CurrencyDollarIcon, ClockIcon, ShieldCheckIcon, DocumentTextIcon,
    LightBulbIcon, PhoneIcon, ExclamationCircleIcon, GlobeAltIcon, TrophyIcon, FireIcon, CheckCircleIcon
} from '@heroicons/vue/24/outline';

const props = defineProps({
    profileCompletion: Number,
    stats: Object,
    recentActivity: Array,
    suggestions: Array,
    recommendedServices: Array,
    topReferrers: Array,
    userRank: Object,
});

const services = [
    {
        id: 'education',
        name: 'Education',
        description: 'Manage your academic records',
        icon: AcademicCapIcon,
        route: 'profile.edit',
        params: { section: 'education' },
        variant: 'ocean',
        badge: props.stats?.education_count || 0
    },
    {
        id: 'experience',
        name: 'Work Experience',
        description: 'Track your employment history',
        icon: BriefcaseIcon,
        route: 'profile.edit',
        params: { section: 'experience' },
        variant: 'sky',
        badge: props.stats?.experience_count || 0
    },
    {
        id: 'skills',
        name: 'Skills & Expertise',
        description: 'Professional skills and competencies',
        icon: LightBulbIcon,
        route: 'profile.edit',
        params: { section: 'skills' },
        variant: 'sunrise',
        badge: props.stats?.skills_count || 0
    },
    {
        id: 'travel',
        name: 'Travel History',
        description: 'International travel records',
        icon: GlobeAltIcon,
        route: 'profile.edit',
        params: { section: 'travel' },
        variant: 'growth',
        badge: props.stats?.travel_count || 0
    },
    {
        id: 'family',
        name: 'Family',
        description: 'Family members & relationships',
        icon: UserCircleIcon,
        route: 'profile.edit',
        params: { section: 'family' },
        variant: 'heritage',
        badge: props.stats?.family_count || 0
    },
    {
        id: 'financial',
        name: 'Financial',
        description: 'Income & financial details',
        icon: CurrencyDollarIcon,
        route: 'profile.edit',
        params: { section: 'financial' },
        variant: 'gold',
        badge: null
    },
    {
        id: 'languages',
        name: 'Languages',
        description: 'Language proficiency levels',
        icon: SparklesIcon,
        route: 'profile.edit',
        params: { section: 'languages' },
        variant: 'ocean',
        badge: props.stats?.languages_count || 0
    },
    {
        id: 'security',
        name: 'Security',
        description: 'Background & police clearance',
        icon: ShieldCheckIcon,
        route: 'profile.edit',
        params: { section: 'security' },
        variant: 'sunrise',
        badge: null
    },
];

// Profile completion progress steps
const profileSteps = computed(() => services.map((service, index) => ({
    label: service.name,
    completed: service.badge > 0 || (service.id === 'financial' || service.id === 'security')
})));

const completionColor = computed(() => {
    const completion = props.profileCompletion || 0;
    if (completion < 25) return 'text-red-600';
    if (completion < 50) return 'text-yellow-600';
    if (completion < 75) return 'text-blue-600';
    return 'text-green-600';
});

const completionText = computed(() => {
    const completion = props.profileCompletion || 0;
    if (completion < 25) return 'Just Started';
    if (completion < 50) return 'In Progress';
    if (completion < 75) return 'Almost There';
    return 'Completed';
});

const getPriorityVariant = (priority) => {
    if (priority === 'high') return 'sunrise';
    if (priority === 'medium') return 'gold';
    return 'sky';
};

const getPriorityStatus = (priority) => {
    if (priority === 'high') return 'error';
    if (priority === 'medium') return 'warning';
    return 'info';
};
</script>

<template>
    <Head title="Dashboard" />

    <AuthenticatedLayout>
        <!-- Page Header -->
        <template #header>
            <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-rhythm-md">
                <div>
                    <h2 class="text-2xl sm:text-3xl font-bold text-gray-900">
                        Welcome Back! 👋
                    </h2>
                    <p class="mt-rhythm-xs text-sm text-gray-600">
                        Track your profile, applications, and referral rewards
                    </p>
                </div>
            </div>
        </template>

        <div class="py-rhythm-2xl">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-rhythm-2xl">
                
                <!-- Profile Completion Banner -->
                <AnimatedSection 
                    variant="ocean" 
                    :show-blobs="true"
                    class="animate-fadeInUp"
                >
                    <div class="relative z-10 text-white">
                        <div class="flex flex-col md:flex-row items-start md:items-center justify-between gap-rhythm-lg">
                            <div class="flex-1">
                                <div class="flex items-center gap-rhythm-sm mb-rhythm-sm">
                                    <DocumentCheckIcon class="h-8 w-8" />
                                    <h3 class="text-xl sm:text-2xl font-bold">Profile Completion</h3>
                                </div>
                                <p class="text-ocean-100 mb-rhythm-md">
                                    Complete your profile to unlock all features and increase your chances of success
                                </p>
                                <div class="flex items-center gap-rhythm-md">
                                    <div class="text-4xl font-bold">{{ profileCompletion }}%</div>
                                    <StatusBadge :status="completionText" size="lg" />
                                </div>
                            </div>
                            <FlowButton 
                                variant="white-outline"
                                :href="route('profile.edit')"
                                size="lg"
                            >
                                Complete Profile
                            </FlowButton>
                        </div>
                        
                        <!-- Progress Wave -->
                        <div class="mt-rhythm-xl">
                            <ProgressWave 
                                :steps="profileSteps"
                                :current-step="Math.floor(profileCompletion / 12.5)"
                                variant="white"
                            />
                        </div>
                    </div>
                </AnimatedSection>

                <!-- Stats Grid -->
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-rhythm-lg animate-fadeIn">
                    <!-- Education Count -->
                    <RhythmicCard 
                        variant="ocean"
                        size="md"
                        hover-lift
                    >
                        <template #icon>
                            <AcademicCapIcon class="h-6 w-6" />
                        </template>
                        <template #default>
                            <div class="text-3xl font-bold text-gray-900">
                                {{ stats?.education_count || 0 }}
                            </div>
                            <p class="text-sm text-gray-600 mt-rhythm-xs">Education Records</p>
                        </template>
                    </RhythmicCard>

                    <!-- Experience Count -->
                    <RhythmicCard 
                        variant="sky"
                        size="md"
                        hover-lift
                    >
                        <template #icon>
                            <BriefcaseIcon class="h-6 w-6" />
                        </template>
                        <template #default>
                            <div class="text-3xl font-bold text-gray-900">
                                {{ stats?.experience_count || 0 }}
                            </div>
                            <p class="text-sm text-gray-600 mt-rhythm-xs">Work Experiences</p>
                        </template>
                    </RhythmicCard>

                    <!-- Profile Strength -->
                    <RhythmicCard 
                        variant="growth"
                        size="md"
                        hover-lift
                    >
                        <template #icon>
                            <ArrowTrendingUpIcon class="h-6 w-6" />
                        </template>
                        <template #default>
                            <div class="text-3xl font-bold text-gray-900">
                                {{ stats?.profile_strength || 0 }}/100
                            </div>
                            <p class="text-sm text-gray-600 mt-rhythm-xs">Profile Strength</p>
                        </template>
                    </RhythmicCard>

                    <!-- Applications -->
                    <RhythmicCard 
                        variant="sunrise"
                        size="md"
                        hover-lift
                    >
                        <template #icon>
                            <DocumentTextIcon class="h-6 w-6" />
                        </template>
                        <template #default>
                            <div class="text-3xl font-bold text-gray-900">
                                {{ stats?.applications_count || 0 }}
                            </div>
                            <p class="text-sm text-gray-600 mt-rhythm-xs">Applications</p>
                        </template>
                    </RhythmicCard>
                </div>

                <!-- Smart Suggestions -->
                <div v-if="suggestions && suggestions.length > 0" class="animate-fadeInUp">
                    <h3 class="text-xl font-bold text-gray-900 mb-rhythm-lg flex items-center gap-rhythm-sm">
                        <SparklesIcon class="h-6 w-6 text-sunrise-500" />
                        Recommended for You
                    </h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-rhythm-lg">
                        <RhythmicCard
                            v-for="suggestion in suggestions"
                            :key="suggestion.id"
                            :variant="getPriorityVariant(suggestion.priority)"
                            hover-lift
                        >
                            <template #badge>
                                <StatusBadge :status="getPriorityStatus(suggestion.priority)" />
                            </template>
                            <template #default>
                                <h4 class="font-semibold text-gray-900 mb-rhythm-sm">
                                    {{ suggestion.title }}
                                </h4>
                                <p class="text-sm text-gray-600">
                                    {{ suggestion.description }}
                                </p>
                            </template>
                            <template #footer>
                                <FlowButton 
                                    :variant="getPriorityVariant(suggestion.priority)"
                                    :href="suggestion.action_url"
                                    size="sm"
                                    full-width
                                >
                                    {{ suggestion.action_text }}
                                </FlowButton>
                            </template>
                        </RhythmicCard>
                    </div>
                </div>

                <!-- Recommended Services -->
                <div v-if="recommendedServices && recommendedServices.length > 0" class="animate-fadeInUp">
                    <h3 class="text-xl font-bold text-gray-900 mb-rhythm-lg flex items-center gap-rhythm-sm">
                        <GlobeAltIcon class="h-6 w-6 text-growth-500" />
                        Services for You
                    </h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-rhythm-lg">
                        <RhythmicCard
                            v-for="service in recommendedServices"
                            :key="service.id"
                            variant="growth"
                            hover-lift
                        >
                            <template #icon>
                                <GlobeAltIcon class="h-6 w-6" />
                            </template>
                            <template #default>
                                <h4 class="font-semibold text-gray-900 mb-rhythm-sm">
                                    {{ service.name }}
                                </h4>
                                <p class="text-sm text-gray-600">
                                    {{ service.description }}
                                </p>
                            </template>
                            <template #footer>
                                <FlowButton 
                                    variant="growth"
                                    :href="service.url"
                                    size="sm"
                                    full-width
                                >
                                    View Details
                                </FlowButton>
                            </template>
                        </RhythmicCard>
                    </div>
                </div>

                <!-- Leaderboard Widget -->
                <div v-if="topReferrers && topReferrers.length > 0" class="animate-fadeInUp">
                    <RhythmicCard variant="gold" size="lg">
                        <template #default>
                            <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-rhythm-md mb-rhythm-lg">
                                <div class="flex items-center gap-rhythm-sm">
                                    <div class="p-rhythm-sm rounded-xl bg-gold-500">
                                        <TrophyIcon class="h-6 w-6 text-white" />
                                    </div>
                                    <div>
                                        <h3 class="text-xl font-bold text-gray-900">Top Referrers</h3>
                                        <p class="text-sm text-gray-600">This month's leaderboard</p>
                                    </div>
                                </div>
                                <FlowButton 
                                    variant="gold"
                                    :href="route('referral.index')"
                                    size="sm"
                                >
                                    <template #icon-right>
                                        <ArrowTrendingUpIcon class="h-4 w-4" />
                                    </template>
                                    View Leaderboard
                                </FlowButton>
                            </div>

                            <!-- User's Rank -->
                            <div v-if="userRank" class="bg-gradient-to-r from-ocean-50 to-sky-50 rounded-xl p-rhythm-md mb-rhythm-lg border border-ocean-200">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center gap-rhythm-sm">
                                        <div class="w-12 h-12 rounded-full bg-gradient-ocean flex items-center justify-center text-white font-bold">
                                            #{{ userRank.rank }}
                                        </div>
                                        <div>
                                            <p class="font-semibold text-gray-900">Your Rank</p>
                                            <p class="text-sm text-gray-600">{{ userRank.referral_count }} referrals</p>
                                        </div>
                                    </div>
                                    <div class="text-right">
                                        <p class="text-lg font-bold text-growth-600">৳{{ userRank.total_earnings?.toFixed(2) || '0.00' }}</p>
                                        <p class="text-xs text-gray-500">Earned</p>
                                    </div>
                                </div>
                            </div>

                            <!-- Top Leaders -->
                            <div class="space-y-rhythm-sm">
                                <div
                                    v-for="(leader, index) in topReferrers.slice(0, 5)"
                                    :key="leader.user_id"
                                    class="bg-white rounded-xl p-rhythm-md flex items-center justify-between hover:shadow-rhythm-md transition-all"
                                >
                                    <div class="flex items-center gap-rhythm-sm flex-1">
                                        <!-- Rank Badge -->
                                        <div
                                            class="w-10 h-10 rounded-full flex items-center justify-center font-bold flex-shrink-0"
                                            :class="{
                                                'bg-gradient-to-br from-gold-400 to-gold-600 text-white': index === 0,
                                                'bg-gradient-to-br from-gray-300 to-gray-500 text-white': index === 1,
                                                'bg-gradient-to-br from-sunrise-400 to-sunrise-600 text-white': index === 2,
                                                'bg-gray-100 text-gray-700': index >= 3
                                            }"
                                        >
                                            <span v-if="index === 0">🥇</span>
                                            <span v-else-if="index === 1">🥈</span>
                                            <span v-else-if="index === 2">🥉</span>
                                            <span v-else>#{{ index + 1 }}</span>
                                        </div>

                                        <!-- User Info -->
                                        <div class="flex-1">
                                            <p class="font-semibold text-gray-900">
                                                {{ leader.user?.name || 'Anonymous' }}
                                            </p>
                                            <p class="text-sm text-gray-600">
                                                {{ leader.referral_count }} referrals
                                            </p>
                                        </div>

                                        <!-- Earnings -->
                                        <div class="text-right">
                                            <p class="font-bold text-growth-600">
                                                ৳{{ leader.total_earnings?.toFixed(2) || '0.00' }}
                                            </p>
                                            <div v-if="index < 3" class="flex items-center gap-1 justify-end">
                                                <FireIcon class="h-3 w-3 text-sunrise-500" />
                                                <span class="text-xs text-sunrise-600 font-semibold">HOT</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- CTA -->
                            <div class="mt-rhythm-lg pt-rhythm-lg border-t border-gold-200">
                                <div class="flex flex-col sm:flex-row items-center justify-between gap-rhythm-md">
                                    <p class="text-sm text-gray-600">
                                        <span class="font-semibold">Refer friends</span> and climb the leaderboard!
                                    </p>
                                    <FlowButton 
                                        variant="gold"
                                        :href="route('referral.index')"
                                        size="sm"
                                    >
                                        <template #icon-left>
                                            <SparklesIcon class="h-4 w-4" />
                                        </template>
                                        Start Referring
                                    </FlowButton>
                                </div>
                            </div>
                        </template>
                    </RhythmicCard>
                </div>

                <!-- Featured Services -->
                <div class="animate-fadeInUp">
                    <h3 class="text-xl font-bold text-gray-900 mb-rhythm-lg">Featured Services</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-rhythm-lg">
                        <!-- Travel Insurance -->
                        <RhythmicCard variant="growth" hover-lift>
                            <template #icon>
                                <ShieldCheckIcon class="h-6 w-6" />
                            </template>
                            <template #badge>
                                <StatusBadge status="new" />
                            </template>
                            <template #default>
                                <h4 class="font-bold text-gray-900 mb-rhythm-xs">Travel Insurance</h4>
                                <p class="text-sm text-gray-600 mb-rhythm-md">Protect your journey worldwide</p>
                                <div class="grid grid-cols-3 gap-rhythm-sm text-xs">
                                    <div>
                                        <div class="font-semibold text-gray-900">6 Packages</div>
                                        <div class="text-gray-500">Available</div>
                                    </div>
                                    <div>
                                        <div class="font-semibold text-gray-900">৳150/day</div>
                                        <div class="text-gray-500">From</div>
                                    </div>
                                    <div>
                                        <div class="font-semibold text-gray-900">24/7</div>
                                        <div class="text-gray-500">Support</div>
                                    </div>
                                </div>
                            </template>
                            <template #footer>
                                <FlowButton 
                                    variant="growth"
                                    href="/travel-insurance"
                                    size="sm"
                                    full-width
                                >
                                    View Packages
                                </FlowButton>
                            </template>
                        </RhythmicCard>

                        <!-- CV Builder -->
                        <RhythmicCard variant="ocean" hover-lift>
                            <template #icon>
                                <DocumentTextIcon class="h-6 w-6" />
                            </template>
                            <template #badge>
                                <StatusBadge status="new" />
                            </template>
                            <template #default>
                                <h4 class="font-bold text-gray-900 mb-rhythm-xs">Professional CV Builder</h4>
                                <p class="text-sm text-gray-600 mb-rhythm-md">Create stunning CVs in minutes</p>
                                <div class="grid grid-cols-3 gap-rhythm-sm text-xs">
                                    <div>
                                        <div class="font-semibold text-gray-900">6 Templates</div>
                                        <div class="text-gray-500">Professional</div>
                                    </div>
                                    <div>
                                        <div class="font-semibold text-gray-900">3 Free</div>
                                        <div class="text-gray-500">Premium ৳300</div>
                                    </div>
                                    <div>
                                        <div class="font-semibold text-gray-900">PDF</div>
                                        <div class="text-gray-500">Export</div>
                                    </div>
                                </div>
                            </template>
                            <template #footer>
                                <FlowButton 
                                    variant="ocean"
                                    href="/cv-builder"
                                    size="sm"
                                    full-width
                                >
                                    Create CV
                                </FlowButton>
                            </template>
                        </RhythmicCard>

                        <!-- Job Opportunities -->
                        <RhythmicCard variant="heritage" hover-lift>
                            <template #icon>
                                <BriefcaseIcon class="h-6 w-6" />
                            </template>
                            <template #badge>
                                <StatusBadge status="active" />
                            </template>
                            <template #default>
                                <h4 class="font-bold text-gray-900 mb-rhythm-xs">Job Opportunities Abroad</h4>
                                <p class="text-sm text-gray-600 mb-rhythm-md">Find your dream job overseas</p>
                                <div class="grid grid-cols-3 gap-rhythm-sm text-xs">
                                    <div>
                                        <div class="font-semibold text-gray-900">10+ Jobs</div>
                                        <div class="text-gray-500">Active</div>
                                    </div>
                                    <div>
                                        <div class="font-semibold text-gray-900">6 Countries</div>
                                        <div class="text-gray-500">UAE, Saudi</div>
                                    </div>
                                    <div>
                                        <div class="font-semibold text-gray-900">Easy</div>
                                        <div class="text-gray-500">Apply</div>
                                    </div>
                                </div>
                            </template>
                            <template #footer>
                                <FlowButton 
                                    variant="heritage"
                                    href="/jobs"
                                    size="sm"
                                    full-width
                                >
                                    Browse Jobs
                                </FlowButton>
                            </template>
                        </RhythmicCard>
                    </div>
                </div>

                <!-- Profile Sections -->
                <div class="animate-fadeInUp">
                    <h3 class="text-xl font-bold text-gray-900 mb-rhythm-lg">Profile Sections</h3>
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-rhythm-lg">
                        <RhythmicCard
                            v-for="service in services"
                            :key="service.id"
                            :variant="service.variant"
                            hover-lift
                        >
                            <template #icon>
                                <component :is="service.icon" class="h-6 w-6" />
                            </template>
                            <template #badge v-if="service.badge !== null">
                                <StatusBadge 
                                    :status="service.badge > 0 ? 'success' : 'pending'"
                                    :label="`${service.badge} items`"
                                />
                            </template>
                            <template #default>
                                <h4 class="font-semibold text-gray-900 mb-rhythm-xs">{{ service.name }}</h4>
                                <p class="text-sm text-gray-600">{{ service.description }}</p>
                            </template>
                            <template #footer>
                                <FlowButton 
                                    :variant="service.variant"
                                    :href="route(service.route, service.params)"
                                    size="sm"
                                    full-width
                                >
                                    Manage
                                </FlowButton>
                            </template>
                        </RhythmicCard>
                    </div>
                </div>

                <!-- Recent Activity -->
                <div v-if="recentActivity && recentActivity.length > 0" class="animate-fadeInUp">
                    <RhythmicCard variant="sky" size="lg">
                        <template #default>
                            <div class="flex items-center gap-rhythm-sm mb-rhythm-lg">
                                <ClockIcon class="h-6 w-6 text-sky-500" />
                                <h3 class="text-xl font-bold text-gray-900">Recent Activity</h3>
                            </div>
                            <div class="space-y-rhythm-md">
                                <div 
                                    v-for="(activity, index) in recentActivity.slice(0, 5)" 
                                    :key="index"
                                    class="flex items-start gap-rhythm-sm pb-rhythm-md border-b border-gray-100 last:border-0"
                                >
                                    <div class="flex-shrink-0 w-10 h-10 rounded-full bg-sky-100 flex items-center justify-center">
                                        <CheckCircleIcon class="h-5 w-5 text-sky-600" />
                                    </div>
                                    <div class="flex-1">
                                        <p class="font-medium text-gray-900">{{ activity.description }}</p>
                                        <p class="text-sm text-gray-500 mt-rhythm-xs">{{ activity.date }}</p>
                                    </div>
                                </div>
                            </div>
                        </template>
                    </RhythmicCard>
                </div>

                <!-- Empty State -->
                <div v-else class="animate-fadeInUp">
                    <RhythmicCard variant="sky" size="lg">
                        <template #default>
                            <div class="text-center py-rhythm-2xl">
                                <SparklesIcon class="mx-auto h-12 w-12 text-gray-400 mb-rhythm-md" />
                                <h3 class="text-lg font-medium text-gray-900 mb-rhythm-sm">No activity yet</h3>
                                <p class="text-gray-600 mb-rhythm-lg">Start by completing your profile sections</p>
                                <FlowButton 
                                    variant="sky"
                                    :href="route('profile.edit')"
                                >
                                    Get Started
                                </FlowButton>
                            </div>
                        </template>
                    </RhythmicCard>
                </div>

            </div>
        </div>
    </AuthenticatedLayout>
</template>
