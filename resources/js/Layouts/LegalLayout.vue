<script setup>
import { Head, Link } from '@inertiajs/vue3';

defineProps({
    title: String,
    headerIcon: Function,
    headerTitle: String,
    lastUpdated: String,
    headerGradient: String,
    headerColors: Object, // e.g., { text: 'text-white', subtext: 'text-purple-100' }
    summaryText: String,
    summaryColors: Object, // e.g., { bg: 'bg-purple-50', border: 'border-purple-100', text: 'text-purple-800' }
});
</script>

<template>
    <div>
        <Head :title="`${title} - BideshGomon`" />

        <div class="min-h-screen bg-gray-50 py-12">
            <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
                <!-- Header -->
                <div class="bg-white shadow rounded-lg overflow-hidden mb-6">
                    <div class="px-6 py-8" :class="headerGradient">
                        <div class="flex items-center justify-center mb-4">
                            <component :is="headerIcon" class="h-12 w-12" :class="headerColors.text" />
                        </div>
                        <h1 class="text-3xl font-bold text-center" :class="headerColors.text">{{ headerTitle }}</h1>
                        <p class="text-center mt-2" :class="headerColors.subtext">Last Updated: {{ lastUpdated }}</p>
                    </div>

                    <div class="px-6 py-4 border-b" :class="[summaryColors.bg, summaryColors.border]">
                        <p class="text-sm text-center" :class="summaryColors.text">
                            {{ summaryText }}
                        </p>
                    </div>
                </div>

                <!-- Content -->
                <div class="bg-white shadow rounded-lg p-8 space-y-8">
                    <slot />
                </div>
            </div>
        </div>
    </div>
</template>