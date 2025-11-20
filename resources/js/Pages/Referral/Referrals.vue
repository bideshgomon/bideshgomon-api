<script setup>
import { Head, Link } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { UserGroupIcon } from '@heroicons/vue/24/outline';
import { useBangladeshFormat } from '@/Composables/useBangladeshFormat';

const props = defineProps({
    referrals: Object,
});

const { formatDate, formatTime } = useBangladeshFormat();
</script>

<template>
    <Head title="My Referrals" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    My Referrals
                </h2>
                <Link 
                    :href="route('referral.index')" 
                    class="flex items-center space-x-2 text-gray-600 hover:text-emerald-600 transition-colors"
                >
                    Back to Referral
                </Link>
            </div>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div v-if="referrals.data.length > 0">
                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-200">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">User</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Phone</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Joined</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Reward Status</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-200">
                                        <tr v-for="referral in referrals.data" :key="referral.id">
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="text-sm font-medium text-gray-900">
                                                    {{ referral.referred_user?.name }}
                                                </div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="text-sm text-gray-500">
                                                    {{ referral.referred_user?.email }}
                                                </div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="text-sm text-gray-500">
                                                    {{ referral.referred_user?.phone || 'N/A' }}
                                                </div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="text-sm text-gray-500">
                                                    {{ formatDate(referral.created_at) }}
                                                    <div class="text-xs text-gray-400">{{ formatTime(referral.created_at) }}</div>
                                                </div>
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
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <span 
                                                    v-if="referral.reward"
                                                    :class="{
                                                        'bg-green-100 text-green-800': referral.reward.status === 'approved' || referral.reward.status === 'paid',
                                                        'bg-yellow-100 text-yellow-800': referral.reward.status === 'pending',
                                                        'bg-red-100 text-red-800': referral.reward.status === 'rejected'
                                                    }"
                                                    class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full"
                                                >
                                                    {{ referral.reward.status.charAt(0).toUpperCase() + referral.reward.status.slice(1) }}
                                                </span>
                                                <span v-else class="text-xs text-gray-400">No reward</span>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>

                            <!-- Pagination -->
                            <div class="mt-6 flex items-center justify-between">
                                <div class="text-sm text-gray-700">
                                    Showing {{ referrals.from }} to {{ referrals.to }} of {{ referrals.total }} referrals
                                </div>
                                <div class="flex space-x-2">
                                    <Link 
                                        v-for="link in referrals.links" 
                                        :key="link.label"
                                        :href="link.url"
                                        :class="{
                                            'bg-indigo-600 text-white': link.active,
                                            'bg-white text-gray-700 hover:bg-gray-50': !link.active,
                                            'opacity-50 cursor-not-allowed': !link.url
                                        }"
                                        class="px-3 py-2 border rounded-md text-sm"
                                        v-html="link.label"
                                    />
                                </div>
                            </div>
                        </div>

                        <div v-else class="text-center py-12">
                            <UserGroupIcon class="mx-auto h-16 w-16 text-gray-400" />
                            <h3 class="mt-4 text-lg font-medium text-gray-900">No referrals yet</h3>
                            <p class="mt-2 text-sm text-gray-500">
                                Share your referral code with friends to get started!
                            </p>
                            <Link 
                                :href="route('referral.index')" 
                                class="mt-6 inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700"
                            >
                                Go to Referral Dashboard
                            </Link>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

