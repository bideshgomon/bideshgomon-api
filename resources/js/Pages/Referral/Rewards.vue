<script setup>
import { Head, Link } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
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
            <div class="flex items-center justify-between">
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    My Rewards
                </h2>
                <Link 
                    :href="route('referral.index')" 
                    class="flex items-center space-x-2 text-gray-600 hover:text-emerald-600 transition-colors"
                >
                    Back to Dashboard
                </Link>
            </div>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div v-if="rewards.data.length > 0">
                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-200">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Description</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Amount</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Approved On</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-200">
                                        <tr v-for="reward in rewards.data" :key="reward.id">
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="text-sm font-medium text-gray-900">
                                                    {{ reward.type }}
                                                </div>
                                            </td>
                                            <td class="px-6 py-4">
                                                <div class="text-sm text-gray-900">
                                                    {{ reward.description }}
                                                </div>
                                                <div v-if="reward.status === 'rejected' && reward.metadata?.rejection_reason" class="text-xs text-red-600 mt-1">
                                                    Reason: {{ reward.metadata.rejection_reason }}
                                                </div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="text-sm font-semibold text-gray-900">
                                                    {{ formatCurrency(reward.amount) }}
                                                </div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
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
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="text-sm text-gray-500">
                                                    {{ formatDate(reward.created_at) }}
                                                    <div class="text-xs text-gray-400">{{ formatTime(reward.created_at) }}</div>
                                                </div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
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
                            <div class="mt-6 flex items-center justify-between">
                                <div class="text-sm text-gray-700">
                                    Showing {{ rewards.from }} to {{ rewards.to }} of {{ rewards.total }} rewards
                                </div>
                                <div class="flex space-x-2">
                                    <Link 
                                        v-for="link in rewards.links" 
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
                            <CurrencyDollarIcon class="mx-auto h-16 w-16 text-gray-400" />
                            <h3 class="mt-4 text-lg font-medium text-gray-900">No rewards yet</h3>
                            <p class="mt-2 text-sm text-gray-500">
                                Refer friends to earn rewards!
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
