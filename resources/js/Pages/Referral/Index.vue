<script setup>
import { Head, Link } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
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
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Your Referral Code</h3>
                        <div class="bg-gradient-to-r from-indigo-500 to-purple-600 rounded-lg p-6 text-white">
                            <div class="text-center">
                                <p class="text-sm mb-2">Share this code with friends and earn rewards!</p>
                                <p class="text-4xl font-bold tracking-wider mb-4">{{ referralCode }}</p>
                                <div class="flex items-center justify-center space-x-2">
                                    <input 
                                        type="text" 
                                        :value="referralLink" 
                                        readonly 
                                        class="bg-white/20 border-white/30 text-white placeholder-white/50 px-4 py-2 rounded flex-1 max-w-md"
                                    />
                                    <button 
                                        @click="copyToClipboard"
                                        class="bg-white text-indigo-600 px-4 py-2 rounded font-medium hover:bg-indigo-50 transition flex items-center space-x-2"
                                    >
                                        <ClipboardDocumentIcon class="h-5 w-5" />
                                        <span>{{ copied ? 'Copied!' : 'Copy' }}</span>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Stats Cards -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 bg-indigo-500 rounded-md p-3">
                                    <UserGroupIcon class="h-6 w-6 text-white" />
                                </div>
                                <div class="ml-4">
                                    <p class="text-sm font-medium text-gray-500">Total Referrals</p>
                                    <p class="text-2xl font-semibold text-gray-900">{{ stats.totalReferrals }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 bg-green-500 rounded-md p-3">
                                    <CurrencyDollarIcon class="h-6 w-6 text-white" />
                                </div>
                                <div class="ml-4">
                                    <p class="text-sm font-medium text-gray-500">Total Earnings</p>
                                    <p class="text-2xl font-semibold text-gray-900">{{ formatCurrency(stats.totalEarnings) }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 bg-yellow-500 rounded-md p-3">
                                    <ClockIcon class="h-6 w-6 text-white" />
                                </div>
                                <div class="ml-4">
                                    <p class="text-sm font-medium text-gray-500">Pending Rewards</p>
                                    <p class="text-2xl font-semibold text-gray-900">{{ formatCurrency(stats.pendingRewards) }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Recent Referrals -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-lg font-medium text-gray-900">Recent Referrals</h3>
                            <Link 
                                :href="route('referral.referrals')" 
                                class="text-indigo-600 hover:text-indigo-900 text-sm font-medium"
                            >
                                View All
                            </Link>
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
                                            <span 
                                                :class="{
                                                    'bg-green-100 text-green-800': referral.is_completed,
                                                    'bg-yellow-100 text-yellow-800': !referral.is_completed
                                                }"
                                                class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full"
                                            >
                                                {{ referral.is_completed ? 'Completed' : 'Pending' }}
                                            </span>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div v-else class="text-center py-8 text-gray-500">
                            <UserGroupIcon class="mx-auto h-12 w-12 text-gray-400" />
                            <p class="mt-2">No referrals yet. Share your code to get started!</p>
                        </div>
                    </div>
                </div>

                <!-- Recent Rewards -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-lg font-medium text-gray-900">Recent Rewards</h3>
                            <Link 
                                :href="route('referral.rewards')" 
                                class="text-indigo-600 hover:text-indigo-900 text-sm font-medium"
                            >
                                View All
                            </Link>
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
                                    <p class="text-sm font-semibold text-gray-900">{{ formatCurrency(reward.amount) }}</p>
                                    <span 
                                        :class="{
                                            'bg-green-100 text-green-800': reward.status === 'approved' || reward.status === 'paid',
                                            'bg-yellow-100 text-yellow-800': reward.status === 'pending',
                                            'bg-red-100 text-red-800': reward.status === 'rejected'
                                        }"
                                        class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full"
                                    >
                                        {{ reward.status.charAt(0).toUpperCase() + reward.status.slice(1) }}
                                    </span>
                                </div>
                            </div>
                        </div>
                        <div v-else class="text-center py-8 text-gray-500">
                            <CurrencyDollarIcon class="mx-auto h-12 w-12 text-gray-400" />
                            <p class="mt-2">No rewards yet. Refer friends to earn rewards!</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
