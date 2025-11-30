<script setup>
import { Head, Link } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import RhythmicCard from '@/Components/Rhythmic/RhythmicCard.vue';
import StatusBadge from '@/Components/Rhythmic/StatusBadge.vue';
import FlowButton from '@/Components/Rhythmic/FlowButton.vue';
import { 
    ClipboardDocumentIcon, 
    UserGroupIcon, 
    CurrencyDollarIcon, 
    ClockIcon,
    ShareIcon,
    CheckCircleIcon 
} from '@heroicons/vue/24/outline';
import { useBangladeshFormat } from '@/Composables/useBangladeshFormat';
import { ref } from 'vue';

const props = defineProps({
    referralCode: String,
    referralLink: String,
    stats: Object,
    referrals: Array,
    recentRewards: Array,
});

const { formatCurrency, formatDate, formatTime } = useBangladeshFormat();

const copied = ref(false);

const copyToClipboard = () => {
    navigator.clipboard.writeText(props.referralLink);
    copied.value = true;
    setTimeout(() => {
        copied.value = false;
    }, 2000);
};

const shareViaWhatsApp = () => {
    const message = `Join BideshGomon and start your journey abroad! Use my referral code: ${props.referralCode}\n\n${props.referralLink}`;
    window.open(`https://wa.me/?text=${encodeURIComponent(message)}`, '_blank');
};

const shareViaFacebook = () => {
    window.open(`https://www.facebook.com/sharer/sharer.php?u=${encodeURIComponent(props.referralLink)}`, '_blank');
};

const shareViaEmail = () => {
    const subject = 'Join BideshGomon - Your Journey Abroad Starts Here!';
    const body = `Hi!\n\nI'm using BideshGomon to plan my journey abroad, and I thought you might be interested too!\n\nUse my referral code: ${props.referralCode}\nOr click this link: ${props.referralLink}\n\nYou'll get rewards after registration!\n\nBest regards`;
    window.location.href = `mailto:?subject=${encodeURIComponent(subject)}&body=${encodeURIComponent(body)}`;
};
</script>

<template>
    <Head title="Referrals" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between animate-fadeIn">
                <div class="flex items-center gap-3">
                    <div class="p-3 bg-red-100 rounded-xl border-2 border-red-200 relative">
                        <ShareIcon class="h-8 w-8 text-red-600" />
                        <div class="absolute -top-1 -right-1 w-3 h-3 bg-green-500 rounded-full"></div>
                    </div>
                    <div>
                        <h2 class="text-2xl font-bold text-gray-900">
                            Referral Dashboard
                        </h2>
                        <p class="text-sm text-gray-600">Share & earn rewards together</p>
                    </div>
                </div>
            </div>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
                <!-- Referral Code Card - Improved Design -->
                <RhythmicCard variant="gradient" size="xl" class="animate-fadeInUp relative overflow-hidden">
                    <template #default>
                        <!-- Decorative background elements -->
                        <div class="absolute -top-10 -right-10 w-40 h-40 bg-white/10 rounded-full blur-2xl"></div>
                        <div class="absolute -bottom-10 -left-10 w-60 h-60 bg-white/5 rounded-full blur-3xl"></div>
                        
                        <div class="relative z-10">
                            <div class="flex items-center justify-center mb-4">
                                <ShareIcon class="h-8 w-8 text-white mr-3" />
                                <h3 class="text-2xl font-bold text-white">Your Referral Code</h3>
                            </div>
                            
                            <div class="text-center mb-6">
                                <p class="text-white/90 mb-4 text-lg">Share with friends and family to earn rewards!</p>
                                
                                <!-- Large Referral Code Display -->
                                <div class="bg-white/20 backdrop-blur-sm border-2 border-white/30 rounded-2xl p-6 mb-6">
                                    <p class="text-5xl md:text-6xl font-black tracking-widest text-white mb-2">
                                        {{ referralCode }}
                                    </p>
                                    <p class="text-white/80 text-sm">Click to copy your unique code</p>
                                </div>

                                <!-- Referral Link -->
                                <div class="max-w-2xl mx-auto mb-6">
                                    <div class="flex items-center bg-white/10 backdrop-blur-sm border border-white/20 rounded-xl overflow-hidden">
                                        <input 
                                            type="text" 
                                            :value="referralLink" 
                                            readonly 
                                            class="flex-1 bg-transparent text-white placeholder-white/50 px-4 py-3 text-sm font-mono border-0 focus:ring-0"
                                        />
                                        <button
                                            @click="copyToClipboard"
                                            class="flex items-center gap-2 px-6 py-3 bg-white text-purple-600 hover:bg-gray-50 font-semibold transition-all duration-200"
                                        >
                                            <CheckCircleIcon v-if="copied" class="h-5 w-5 text-green-600" />
                                            <ClipboardDocumentIcon v-else class="h-5 w-5" />
                                            {{ copied ? 'Copied!' : 'Copy Link' }}
                                        </button>
                                    </div>
                                </div>

                                <!-- Share Buttons -->
                                <div class="flex flex-col sm:flex-row items-center justify-center gap-3">
                                    <p class="text-white/90 font-semibold mr-2 w-full sm:w-auto text-center">Quick Share:</p>
                                    <div class="flex flex-wrap justify-center gap-3">
                                        <!-- WhatsApp -->
                                        <button
                                            @click="shareViaWhatsApp"
                                            class="group flex items-center gap-2 px-6 py-3 bg-green-500 hover:bg-green-600 text-white rounded-xl font-semibold transition-all duration-200 shadow-lg hover:shadow-xl transform hover:-translate-y-0.5"
                                        >
                                            <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 24 24">
                                                <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413Z"/>
                                            </svg>
                                            WhatsApp
                                        </button>

                                        <!-- Facebook -->
                                        <button
                                            @click="shareViaFacebook"
                                            class="group flex items-center gap-2 px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white rounded-xl font-semibold transition-all duration-200 shadow-lg hover:shadow-xl transform hover:-translate-y-0.5"
                                        >
                                            <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 24 24">
                                                <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
                                            </svg>
                                            Facebook
                                        </button>

                                        <!-- Email -->
                                        <button
                                            @click="shareViaEmail"
                                            class="group flex items-center gap-2 px-6 py-3 bg-gray-700 hover:bg-gray-800 text-white rounded-xl font-semibold transition-all duration-200 shadow-lg hover:shadow-xl transform hover:-translate-y-0.5"
                                        >
                                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                            </svg>
                                            Email
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </template>
                </RhythmicCard>

                <!-- Stats Cards - Improved -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <!-- Total Referrals -->
                    <RhythmicCard variant="light" size="md" class="animate-fadeIn border-l-4 border-blue-500" style="animation-delay: 100ms;">
                        <template #default>
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-sm font-medium text-gray-600 mb-1">Total Referrals</p>
                                    <p class="text-3xl font-bold text-gray-900">{{ stats.totalReferrals }}</p>
                                    <p class="text-xs text-gray-500 mt-1">Friends you've invited</p>
                                </div>
                                <div class="p-4 bg-blue-100 rounded-xl">
                                    <UserGroupIcon class="h-8 w-8 text-blue-600" />
                                </div>
                            </div>
                        </template>
                    </RhythmicCard>

                    <!-- Total Earnings -->
                    <RhythmicCard variant="light" size="md" class="animate-fadeIn border-l-4 border-green-500" style="animation-delay: 200ms;">
                        <template #default>
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-sm font-medium text-gray-600 mb-1">Total Earnings</p>
                                    <p class="text-3xl font-bold text-green-600">{{ formatCurrency(stats.totalEarnings) }}</p>
                                    <p class="text-xs text-gray-500 mt-1">Paid rewards received</p>
                                </div>
                                <div class="p-4 bg-green-100 rounded-xl">
                                    <CurrencyDollarIcon class="h-8 w-8 text-green-600" />
                                </div>
                            </div>
                        </template>
                    </RhythmicCard>

                    <!-- Pending Rewards -->
                    <RhythmicCard variant="light" size="md" class="animate-fadeIn border-l-4 border-amber-500" style="animation-delay: 300ms;">
                        <template #default>
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-sm font-medium text-gray-600 mb-1">Pending Rewards</p>
                                    <p class="text-3xl font-bold text-amber-600">{{ formatCurrency(stats.pendingRewards) }}</p>
                                    <p class="text-xs text-gray-500 mt-1">Awaiting approval</p>
                                </div>
                                <div class="p-4 bg-amber-100 rounded-xl">
                                    <ClockIcon class="h-8 w-8 text-amber-600" />
                                </div>
                            </div>
                        </template>
                    </RhythmicCard>
                </div>

                <!-- Recent Referrals -->
                <RhythmicCard variant="light" size="lg" class="animate-fadeIn" style="animation-delay: 400ms;">
                    <template #default>
                        <div class="flex items-center justify-between mb-6">
                            <h3 class="text-lg font-medium">Recent Referrals</h3>
                            <FlowButton 
                                :href="route('referral.referrals')"
                                variant="secondary"
                                size="sm"
                                as="Link"
                            >
                                View All
                            </FlowButton>
                        </div>
                        
                        <div v-if="referrals.length > 0" class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">User</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Joined</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    <tr v-for="referral in referrals" :key="referral.id">
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                            {{ referral.referred_user?.name }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ referral.referred_user?.email }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ formatDate(referral.created_at) }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <StatusBadge :status="referral.is_completed ? 'completed' : 'pending'" />
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div v-else class="text-center py-8 text-gray-500">
                            <UserGroupIcon class="mx-auto h-12 w-12 text-gray-400" />
                            <p class="mt-2">No referrals yet. Share your code to get started!</p>
                        </div>
                    </template>
                </RhythmicCard>

                <!-- Recent Rewards -->
                <RhythmicCard variant="light" size="lg" class="animate-fadeIn" style="animation-delay: 500ms;">
                    <template #default>
                        <div class="flex items-center justify-between mb-6">
                            <h3 class="text-lg font-medium">Recent Rewards</h3>
                            <FlowButton 
                                :href="route('referral.rewards')"
                                variant="secondary"
                                size="sm"
                                as="Link"
                            >
                                View All
                            </FlowButton>
                        </div>
                        
                        <div v-if="recentRewards.length > 0" class="space-y-4">
                            <div 
                                v-for="reward in recentRewards" 
                                :key="reward.id"
                                class="flex items-center justify-between py-3 border-b last:border-0"
                            >
                                <div>
                                    <p class="text-sm font-medium text-gray-900">{{ reward.type }}</p>
                                    <p class="text-xs text-gray-500">{{ reward.description }}</p>
                                    <p class="text-xs text-gray-400">{{ formatDate(reward.created_at) }} at {{ formatTime(reward.created_at) }}</p>
                                </div>
                                <div class="text-right">
                                    <p class="text-sm font-semibold">{{ formatCurrency(reward.amount) }}</p>
                                    <StatusBadge :status="reward.status" />
                                </div>
                            </div>
                        </div>
                        <div v-else class="text-center py-8 text-gray-500">
                            <CurrencyDollarIcon class="mx-auto h-12 w-12 text-gray-400" />
                            <p class="mt-2">No rewards yet. Refer friends to earn rewards!</p>
                        </div>
                    </template>
                </RhythmicCard>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
