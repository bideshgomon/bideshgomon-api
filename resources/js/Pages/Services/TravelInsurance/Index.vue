<script setup>
import { Head, Link } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { ShieldCheckIcon, GlobeAltIcon, ClockIcon, CurrencyDollarIcon } from '@heroicons/vue/24/outline';

const props = defineProps({
    packages: Array,
    popularPackages: Array,
});

const formatCurrency = (amount) => {
    return '৳ ' + Number(amount).toLocaleString('en-BD');
};

const getBadgeClass = (color) => {
    const colors = {
        emerald: 'bg-emerald-100 text-emerald-800',
        blue: 'bg-blue-100 text-blue-800',
        amber: 'bg-amber-100 text-amber-800',
        purple: 'bg-purple-100 text-purple-800',
        indigo: 'bg-indigo-100 text-indigo-800',
        slate: 'bg-slate-100 text-slate-800',
    };
    return colors[color] || 'bg-gray-100 text-gray-800';
};
</script>

<template>
    <Head title="Travel Insurance" />

    <AuthenticatedLayout>
        <!-- Mobile-First Content -->
        <div class="min-h-screen bg-gray-50 pb-20">
            <!-- Header Section with Gradient -->
            <div class="bg-gradient-to-br from-emerald-600 to-emerald-800 text-white px-4 pt-8 pb-32">
                <div class="max-w-4xl mx-auto">
                    <div class="flex items-center space-x-3 mb-4">
                        <ShieldCheckIcon class="h-10 w-10" />
                        <h1 class="text-3xl font-bold">Travel Insurance</h1>
                    </div>
                    <p class="text-emerald-100 text-lg">
                        Protect your journey with comprehensive travel insurance coverage
                    </p>
                </div>
            </div>

            <!-- Main Content -->
            <div class="max-w-4xl mx-auto px-4 -mt-24 space-y-6">
                <!-- Popular Packages -->
                <div v-if="popularPackages.length > 0" class="mb-6">
                    <h2 class="text-xl font-bold text-white mb-4 px-1">⭐ Popular Packages</h2>
                    <div class="space-y-4">
                        <Link
                            v-for="pkg in popularPackages"
                            :key="pkg.id"
                            :href="route('travel-insurance.show', pkg.slug)"
                            class="block bg-white rounded-3xl p-6 shadow-lg hover:shadow-xl transition-all duration-300 active:scale-98"
                        >
                            <!-- Badge -->
                            <div class="flex items-center justify-between mb-4">
                                <span
                                    v-if="pkg.badge_text"
                                    :class="getBadgeClass(pkg.badge_color)"
                                    class="text-xs font-bold px-3 py-1 rounded-full"
                                >
                                    {{ pkg.badge_text }}
                                </span>
                                <div class="text-right">
                                    <div class="text-2xl font-bold text-emerald-600">
                                        {{ formatCurrency(pkg.price_per_day) }}
                                    </div>
                                    <div class="text-xs text-gray-500">per day</div>
                                </div>
                            </div>

                            <!-- Package Name -->
                            <h3 class="text-xl font-bold text-gray-900 mb-2">{{ pkg.name }}</h3>
                            <p class="text-gray-600 text-sm mb-4 line-clamp-2">{{ pkg.description }}</p>

                            <!-- Key Features -->
                            <div class="grid grid-cols-2 gap-3 mb-4">
                                <div class="flex items-center space-x-2">
                                    <ShieldCheckIcon class="h-5 w-5 text-emerald-600 flex-shrink-0" />
                                    <span class="text-sm text-gray-700">
                                        Up to {{ formatCurrency(pkg.max_coverage) }}
                                    </span>
                                </div>
                                <div class="flex items-center space-x-2">
                                    <ClockIcon class="h-5 w-5 text-emerald-600 flex-shrink-0" />
                                    <span class="text-sm text-gray-700">
                                        {{ pkg.max_days }} days max
                                    </span>
                                </div>
                            </div>

                            <!-- CTA Button -->
                            <div class="pt-4 border-t border-gray-200">
                                <div class="flex items-center justify-between">
                                    <span class="text-sm text-gray-500">
                                        {{ pkg.features.length }} benefits included
                                    </span>
                                    <span class="text-emerald-600 font-semibold text-sm">
                                        View Details →
                                    </span>
                                </div>
                            </div>
                        </Link>
                    </div>
                </div>

                <!-- All Packages -->
                <div>
                    <h2 class="text-xl font-bold text-gray-900 mb-4 px-1">All Packages</h2>
                    <div class="space-y-4">
                        <Link
                            v-for="pkg in packages"
                            :key="pkg.id"
                            :href="route('travel-insurance.show', pkg.slug)"
                            class="block bg-white rounded-2xl p-5 shadow-sm hover:shadow-md transition-all duration-300"
                        >
                            <!-- Header -->
                            <div class="flex items-start justify-between mb-3">
                                <div class="flex-1">
                                    <div class="flex items-center space-x-2 mb-1">
                                        <h3 class="text-lg font-bold text-gray-900">{{ pkg.name }}</h3>
                                        <span
                                            v-if="pkg.badge_text"
                                            :class="getBadgeClass(pkg.badge_color)"
                                            class="text-xs font-bold px-2 py-0.5 rounded-full"
                                        >
                                            {{ pkg.badge_text }}
                                        </span>
                                    </div>
                                    <p class="text-gray-600 text-sm line-clamp-1">{{ pkg.description }}</p>
                                </div>
                                <div class="text-right ml-4">
                                    <div class="text-xl font-bold text-emerald-600">
                                        {{ formatCurrency(pkg.price_per_day) }}
                                    </div>
                                    <div class="text-xs text-gray-500">/day</div>
                                </div>
                            </div>

                            <!-- Quick Info -->
                            <div class="flex items-center space-x-4 text-xs text-gray-500">
                                <span class="flex items-center space-x-1">
                                    <ShieldCheckIcon class="h-4 w-4" />
                                    <span>{{ formatCurrency(pkg.max_coverage) }}</span>
                                </span>
                                <span class="flex items-center space-x-1">
                                    <ClockIcon class="h-4 w-4" />
                                    <span>{{ pkg.min_days }}-{{ pkg.max_days }} days</span>
                                </span>
                                <span class="flex items-center space-x-1">
                                    <GlobeAltIcon class="h-4 w-4" />
                                    <span>{{ pkg.covered_countries === 'all' ? 'Worldwide' : 'Select countries' }}</span>
                                </span>
                            </div>
                        </Link>
                    </div>
                </div>

                <!-- Why Travel Insurance -->
                <div class="bg-white rounded-2xl p-6 shadow-sm mt-8">
                    <h3 class="text-lg font-bold text-gray-900 mb-4">Why Travel Insurance?</h3>
                    <div class="space-y-3">
                        <div class="flex items-start space-x-3">
                            <ShieldCheckIcon class="h-6 w-6 text-emerald-600 flex-shrink-0 mt-0.5" />
                            <div>
                                <h4 class="font-semibold text-gray-900">Medical Emergency Coverage</h4>
                                <p class="text-sm text-gray-600">Get covered for unexpected medical expenses abroad</p>
                            </div>
                        </div>
                        <div class="flex items-start space-x-3">
                            <CurrencyDollarIcon class="h-6 w-6 text-emerald-600 flex-shrink-0 mt-0.5" />
                            <div>
                                <h4 class="font-semibold text-gray-900">Trip Cancellation Protection</h4>
                                <p class="text-sm text-gray-600">Recover costs if you need to cancel your trip</p>
                            </div>
                        </div>
                        <div class="flex items-start space-x-3">
                            <GlobeAltIcon class="h-6 w-6 text-emerald-600 flex-shrink-0 mt-0.5" />
                            <div>
                                <h4 class="font-semibold text-gray-900">24/7 Global Assistance</h4>
                                <p class="text-sm text-gray-600">Get help anytime, anywhere in the world</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<style scoped>
.line-clamp-1 {
    display: -webkit-box;
    -webkit-line-clamp: 1;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

.line-clamp-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

.active\:scale-98:active {
    transform: scale(0.98);
}
</style>
