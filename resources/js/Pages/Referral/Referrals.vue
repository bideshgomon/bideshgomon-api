<script setup>
import { Head, Link } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import RhythmicCard from '@/Components/Rhythmic/RhythmicCard.vue';
import StatusBadge from '@/Components/Rhythmic/StatusBadge.vue';
import FlowButton from '@/Components/Rhythmic/FlowButton.vue';
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
                <FlowButton
                    :href="route('referral.index')"
                    variant="secondary"
                    size="sm"
                    as="Link"
                >
                    Back to Referral
                </FlowButton>
            </div>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <RhythmicCard variant="light" size="lg" class="animate-fadeIn">
                    <template #default>
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
                                                <StatusBadge :status="referral.is_completed ? 'completed' : 'pending'" />
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <StatusBadge v-if="referral.reward" :status="referral.reward.status" />
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
                                            'bg-ocean-600 text-white': link.active,
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
                            <FlowButton
                                :href="route('referral.index')"
                                variant="primary"
                                size="md"
                                class="mt-6"
                                as="Link"
                            >
                                Go to Referral Dashboard
                            </FlowButton>
                        </div>
                    </template>
                </RhythmicCard>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

