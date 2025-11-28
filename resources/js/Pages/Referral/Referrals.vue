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
            <div class="flex items-center justify-between animate-fadeIn">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-ocean-500 flex items-center justify-center shadow-rhythmic-md">
                        <UserGroupIcon class="h-5 w-5 text-white" />
                    </div>
                    <div>
                        <h2 class="font-display font-bold text-xl text-gray-800 leading-tight">
                            My Referrals
                        </h2>
                        <p class="text-xs text-gray-500">Track your referral network</p>
                    </div>
                </div>
                <FlowButton
                    :href="route('referral.index')"
                    variant="secondary"
                    size="sm"
                    as="Link"
                >
                    Back to Dashboard
                </FlowButton>
            </div>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <RhythmicCard variant="light" size="lg" class="animate-fadeIn">
                    <template #default>
                        <div v-if="referrals.data.length > 0">
                            <div class="overflow-x-auto rounded-xl">
                                <table class="min-w-full divide-y divide-gray-200">
                                    <thead class="bg-gradient-to-r from-ocean-50 to-sky-50">
                                        <tr>
                                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">User</th>
                                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Email</th>
                                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider hidden sm:table-cell">Phone</th>
                                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider hidden md:table-cell">Joined</th>
                                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Status</th>
                                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider hidden lg:table-cell">Reward Status</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-100">
                                        <tr v-for="referral in referrals.data" :key="referral.id" class="hover:bg-gray-50 transition-colors">
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
                                            <td class="px-6 py-4 whitespace-nowrap hidden sm:table-cell">
                                                <div class="text-sm text-gray-500">
                                                    {{ referral.referred_user?.phone || 'N/A' }}
                                                </div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap hidden md:table-cell">
                                                <div class="text-sm text-gray-500">
                                                    {{ formatDate(referral.created_at) }}
                                                    <div class="text-xs text-gray-400">{{ formatTime(referral.created_at) }}</div>
                                                </div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <StatusBadge :status="referral.is_completed ? 'completed' : 'pending'" />
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap hidden lg:table-cell">
                                                <StatusBadge v-if="referral.reward" :status="referral.reward.status" />
                                                <span v-else class="text-xs text-gray-400">No reward</span>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>

                            <!-- Pagination -->
                            <div class="mt-6 flex flex-col sm:flex-row items-center justify-between gap-4 pt-4 border-t border-gray-200">
                                <div class="text-sm text-gray-600 font-medium">
                                    <span class="text-ocean-600">{{ referrals.from }}</span> to <span class="text-ocean-600">{{ referrals.to }}</span> of <span class="font-bold">{{ referrals.total }}</span> referrals
                                </div>
                                <div class="flex space-x-1">
                                    <Link 
                                        v-for="link in referrals.links" 
                                        :key="link.label"
                                        :href="link.url"
                                        :class="{
                                            'bg-gradient-to-r from-ocean-600 to-sky-600 text-white shadow-rhythmic-md': link.active,
                                            'bg-white text-gray-700 hover:bg-gray-50 border border-gray-300': !link.active,
                                            'opacity-50 cursor-not-allowed': !link.url
                                        }"
                                        class="px-3 py-2 rounded-lg text-sm font-medium transition-all duration-200 hover:shadow-rhythmic-sm"
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

