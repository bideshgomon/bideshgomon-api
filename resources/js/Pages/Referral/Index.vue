<script setup>
import { Head, Link } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import RhythmicCard from '@/Components/Rhythmic/RhythmicCard.vue';
import StatusBadge from '@/Components/Rhythmic/StatusBadge.vue';
import FlowButton from '@/Components/Rhythmic/FlowButton.vue';
import { ClipboardDocumentIcon, UserGroupIcon, CurrencyDollarIcon, ClockIcon } from '@heroicons/vue/24/outline';
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
</script>

<template>
    <Head title="Referrals" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Referrals
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
                <!-- Referral Code Card -->
                <RhythmicCard variant="gradient" size="xl" class="animate-fadeInUp">
                    <template #default>
                        <h3 class="text-lg font-medium text-white mb-4">Your Referral Code</h3>
                        <div class="text-center">
                            <p class="text-sm text-white/90 mb-2">Share this code with friends and earn rewards!</p>
                            <p class="text-4xl font-bold tracking-wider mb-4 text-white">{{ referralCode }}</p>
                            <div class="flex items-center justify-center space-x-2">
                                <input 
                                    type="text" 
                                    :value="referralLink" 
                                    readonly 
                                    class="bg-white/20 border-white/30 text-white placeholder-white/50 px-4 py-2 rounded flex-1 max-w-md"
                                />
                                <FlowButton
                                    @click="copyToClipboard"
                                    variant="white-outline"
                                    size="md"
                                >
                                    <template #icon-left>
                                        <ClipboardDocumentIcon class="h-5 w-5" />
                                    </template>
                                    {{ copied ? 'Copied!' : 'Copy' }}
                                </FlowButton>
                            </div>
                        </div>
                    </template>
                </RhythmicCard>

                <!-- Stats Cards -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <RhythmicCard variant="ocean" size="md" class="animate-fadeIn" style="animation-delay: 100ms;">
                        <template #icon>
                            <UserGroupIcon class="h-6 w-6" />
                        </template>
                        <template #default>
                            <p class="text-sm font-medium mb-1">Total Referrals</p>
                            <p class="text-2xl font-semibold">{{ stats.totalReferrals }}</p>
                        </template>
                    </RhythmicCard>

                    <RhythmicCard variant="growth" size="md" class="animate-fadeIn" style="animation-delay: 200ms;">
                        <template #icon>
                            <CurrencyDollarIcon class="h-6 w-6" />
                        </template>
                        <template #default>
                            <p class="text-sm font-medium mb-1">Total Earnings</p>
                            <p class="text-2xl font-semibold">{{ formatCurrency(stats.totalEarnings) }}</p>
                        </template>
                    </RhythmicCard>

                    <RhythmicCard variant="gold" size="md" class="animate-fadeIn" style="animation-delay: 300ms;">
                        <template #icon>
                            <ClockIcon class="h-6 w-6" />
                        </template>
                        <template #default>
                            <p class="text-sm font-medium mb-1">Pending Rewards</p>
                            <p class="text-2xl font-semibold">{{ formatCurrency(stats.pendingRewards) }}</p>
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
