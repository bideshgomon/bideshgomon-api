<script setup>
import { Head, Link } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import RhythmicCard from '@/Components/Rhythmic/RhythmicCard.vue';
import StatusBadge from '@/Components/Rhythmic/StatusBadge.vue';
import FlowButton from '@/Components/Rhythmic/FlowButton.vue';
import { CurrencyDollarIcon } from '@heroicons/vue/24/outline';
import { useBangladeshFormat } from '@/Composables/useBangladeshFormat';

const props = defineProps({
    rewards: Object,
});

const { formatCurrency, formatDate, formatTime } = useBangladeshFormat();
</script>

<template>
    <Head title="My Rewards" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between animate-fadeIn">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-gold-500 flex items-center justify-center shadow-rhythmic-md">
                        <CurrencyDollarIcon class="h-5 w-5 text-white" />
                    </div>
                    <div>
                        <h2 class="font-display font-bold text-xl text-gray-800 leading-tight">
                            My Rewards
                        </h2>
                        <p class="text-xs text-gray-500">Track your earnings and bonuses</p>
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
                        <div v-if="rewards.data.length > 0">
                            <div class="overflow-x-auto rounded-xl">
                                <table class="min-w-full divide-y divide-gray-200">
                                    <thead class="bg-gold-50">
                                        <tr>
                                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider hidden md:table-cell">Type</th>
                                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Description</th>
                                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Amount</th>
                                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Status</th>
                                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider hidden lg:table-cell">Date</th>
                                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider hidden xl:table-cell">Approved On</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-100">
                                        <tr v-for="reward in rewards.data" :key="reward.id" class="hover:bg-gray-50 transition-colors">
                                            <td class="px-6 py-4 whitespace-nowrap hidden md:table-cell">
                                                <div class="text-sm font-medium text-gray-900">
                                                    {{ reward.type }}
                                                </div>
                                            </td>
                                            <td class="px-6 py-4">
                                                <div class="text-sm text-gray-900">
                                                    {{ reward.description }}
                                                </div>
                                                <div v-if="reward.status === 'rejected' && reward.metadata?.rejection_reason" class="text-xs text-red-600 mt-1 bg-red-50 px-2 py-1 rounded">
                                                    ⚠️ {{ reward.metadata.rejection_reason }}
                                                </div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="text-sm font-bold text-gold-600">
                                                    {{ formatCurrency(reward.amount) }}
                                                </div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <StatusBadge :status="reward.status" />
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap hidden lg:table-cell">
                                                <div class="text-sm text-gray-500">
                                                    {{ formatDate(reward.created_at) }}
                                                    <div class="text-xs text-gray-400">{{ formatTime(reward.created_at) }}</div>
                                                </div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap hidden xl:table-cell">
                                                <div v-if="reward.approved_at" class="text-sm text-gray-500">
                                                    {{ formatDate(reward.approved_at) }}
                                                    <div class="text-xs text-gray-400">{{ formatTime(reward.approved_at) }}</div>
                                                </div>
                                                <span v-else class="text-xs text-gray-400">-</span>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>

                            <!-- Pagination -->
                            <div class="mt-6 flex flex-col sm:flex-row items-center justify-between gap-4 pt-4 border-t border-gray-200">
                                <div class="text-sm text-gray-600 font-medium">
                                    <span class="text-gold-600">{{ rewards.from }}</span> to <span class="text-gold-600">{{ rewards.to }}</span> of <span class="font-bold">{{ rewards.total }}</span> rewards
                                </div>
                                <div class="flex space-x-1">
                                    <Link 
                                        v-for="link in rewards.links" 
                                        :key="link.label"
                                        :href="link.url"
                                        :class="{
                                            'bg-gold-600 text-white shadow-rhythmic-md': link.active,
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
                            <CurrencyDollarIcon class="mx-auto h-16 w-16 text-gray-400" />
                            <h3 class="mt-4 text-lg font-medium text-gray-900">No rewards yet</h3>
                            <p class="mt-2 text-sm text-gray-500">
                                Refer friends to earn rewards!
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
