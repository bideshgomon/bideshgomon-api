<script setup>
import { Head, Link } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { 
    ShieldCheckIcon, 
    ClockIcon, 
    GlobeAltIcon, 
    CheckCircleIcon,
    UserGroupIcon,
    CurrencyDollarIcon,
    ArrowLeftIcon
} from '@heroicons/vue/24/outline';

const props = defineProps({
    package: Object,
    countries: Array,
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
    <Head :title="package.name" />

    <AuthenticatedLayout>
        <!-- Mobile-First Content -->
        <div class="min-h-screen bg-gray-50 pb-24">
            <!-- Header with Back Button -->
            <div class="bg-gradient-to-br from-emerald-600 to-emerald-800 text-white px-4 pt-6 pb-32">
                <div class="max-w-4xl mx-auto">
                    <Link
                        :href="route('travel-insurance.index')"
                        class="inline-flex items-center space-x-2 text-emerald-100 hover:text-white mb-6 touch-manipulation"
                    >
                        <ArrowLeftIcon class="h-5 w-5" />
                        <span class="text-sm font-medium">Back to Packages</span>
                    </Link>
                    
                    <div class="flex items-start justify-between">
                        <div class="flex-1">
                            <div class="flex items-center space-x-2 mb-2">
                                <h1 class="text-2xl sm:text-3xl font-bold">{{ package.name }}</h1>
                                <span
                                    v-if="package.badge_text"
                                    :class="getBadgeClass(package.badge_color)"
                                    class="text-xs font-bold px-2 py-1 rounded-full"
                                >
                                    {{ package.badge_text }}
                                </span>
                            </div>
                            <p class="text-emerald-100 text-lg">{{ package.description }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Main Content -->
            <div class="max-w-4xl mx-auto px-4 -mt-24 space-y-6">
                <!-- Price Card -->
                <div class="bg-white rounded-3xl p-6 shadow-xl">
                    <div class="text-center mb-6">
                        <div class="text-4xl font-bold text-emerald-600 mb-2">
                            {{ formatCurrency(package.price_per_day) }}
                        </div>
                        <div class="text-gray-600">per day, per traveler</div>
                        <div class="text-sm text-gray-500 mt-2">
                            Minimum price: {{ formatCurrency(package.min_price) }}
                        </div>
                    </div>

                    <!-- Quick Stats -->
                    <div class="grid grid-cols-2 gap-4 mb-6">
                        <div class="bg-emerald-50 rounded-2xl p-4 text-center">
                            <ShieldCheckIcon class="h-8 w-8 text-emerald-600 mx-auto mb-2" />
                            <div class="text-sm text-gray-600">Max Coverage</div>
                            <div class="text-lg font-bold text-gray-900">{{ formatCurrency(package.max_coverage) }}</div>
                        </div>
                        <div class="bg-blue-50 rounded-2xl p-4 text-center">
                            <ClockIcon class="h-8 w-8 text-blue-600 mx-auto mb-2" />
                            <div class="text-sm text-gray-600">Duration</div>
                            <div class="text-lg font-bold text-gray-900">{{ package.min_days }}-{{ package.max_days }} days</div>
                        </div>
                        <div class="bg-purple-50 rounded-2xl p-4 text-center">
                            <UserGroupIcon class="h-8 w-8 text-purple-600 mx-auto mb-2" />
                            <div class="text-sm text-gray-600">Age Range</div>
                            <div class="text-lg font-bold text-gray-900">{{ package.min_age }}-{{ package.max_age }} years</div>
                        </div>
                        <div class="bg-amber-50 rounded-2xl p-4 text-center">
                            <GlobeAltIcon class="h-8 w-8 text-amber-600 mx-auto mb-2" />
                            <div class="text-sm text-gray-600">Coverage</div>
                            <div class="text-lg font-bold text-gray-900">
                                {{ package.covered_countries === 'all' ? 'Worldwide' : 'Select Countries' }}
                            </div>
                        </div>
                    </div>

                    <!-- Book Now Button -->
                    <Link
                        :href="route('travel-insurance.booking-form', package.slug)"
                        class="block w-full bg-gradient-to-r from-emerald-600 to-emerald-700 text-white text-center font-bold py-4 rounded-2xl hover:from-emerald-700 hover:to-emerald-800 transition-all shadow-lg hover:shadow-xl touch-manipulation"
                    >
                        Book This Package
                    </Link>
                </div>

                <!-- Features -->
                <div class="bg-white rounded-2xl p-6 shadow-sm">
                    <h2 class="text-xl font-bold text-gray-900 mb-4 flex items-center">
                        <CheckCircleIcon class="h-6 w-6 text-emerald-600 mr-2" />
                        What's Included
                    </h2>
                    <div class="space-y-3">
                        <div
                            v-for="(feature, index) in package.features"
                            :key="index"
                            class="flex items-start space-x-3"
                        >
                            <CheckCircleIcon class="h-5 w-5 text-emerald-600 flex-shrink-0 mt-0.5" />
                            <span class="text-gray-700">{{ feature }}</span>
                        </div>
                    </div>
                </div>

                <!-- Coverage Details -->
                <div v-if="package.coverage_details" class="bg-white rounded-2xl p-6 shadow-sm">
                    <h2 class="text-xl font-bold text-gray-900 mb-4 flex items-center">
                        <CurrencyDollarIcon class="h-6 w-6 text-emerald-600 mr-2" />
                        Coverage Breakdown
                    </h2>
                    <div class="space-y-3">
                        <div
                            v-if="package.coverage_details.medical_expenses"
                            class="flex items-center justify-between p-3 bg-gray-50 rounded-xl"
                        >
                            <span class="text-gray-700">Medical Expenses</span>
                            <span class="font-bold text-gray-900">{{ formatCurrency(package.coverage_details.medical_expenses) }}</span>
                        </div>
                        <div
                            v-if="package.coverage_details.baggage_loss"
                            class="flex items-center justify-between p-3 bg-gray-50 rounded-xl"
                        >
                            <span class="text-gray-700">Baggage Loss</span>
                            <span class="font-bold text-gray-900">{{ formatCurrency(package.coverage_details.baggage_loss) }}</span>
                        </div>
                        <div
                            v-if="package.coverage_details.trip_cancellation"
                            class="flex items-center justify-between p-3 bg-gray-50 rounded-xl"
                        >
                            <span class="text-gray-700">Trip Cancellation</span>
                            <span class="font-bold text-gray-900">{{ formatCurrency(package.coverage_details.trip_cancellation) }}</span>
                        </div>
                        <div
                            v-if="package.coverage_details.personal_accident"
                            class="flex items-center justify-between p-3 bg-gray-50 rounded-xl"
                        >
                            <span class="text-gray-700">Personal Accident</span>
                            <span class="font-bold text-gray-900">{{ formatCurrency(package.coverage_details.personal_accident) }}</span>
                        </div>
                        <div
                            v-if="package.coverage_details.trip_delay"
                            class="flex items-center justify-between p-3 bg-gray-50 rounded-xl"
                        >
                            <span class="text-gray-700">Trip Delay</span>
                            <span class="font-bold text-gray-900">{{ formatCurrency(package.coverage_details.trip_delay) }}</span>
                        </div>
                        <div
                            v-if="package.coverage_details.emergency_evacuation"
                            class="flex items-center justify-between p-3 bg-emerald-50 rounded-xl"
                        >
                            <span class="text-gray-700">Emergency Evacuation</span>
                            <span class="font-bold text-emerald-600">✓ Included</span>
                        </div>
                    </div>
                </div>

                <!-- Important Notes -->
                <div class="bg-blue-50 border-2 border-blue-200 rounded-2xl p-6">
                    <h3 class="font-bold text-blue-900 mb-3 text-lg">📋 Important Information</h3>
                    <ul class="space-y-2 text-blue-800 text-sm">
                        <li>• Coverage is valid from the start date of your trip</li>
                        <li>• Policy will be issued immediately after payment</li>
                        <li>• 24/7 emergency assistance available worldwide</li>
                        <li>• Pre-existing conditions may not be covered</li>
                        <li>• Read full terms and conditions before booking</li>
                    </ul>
                </div>

                <!-- CTA Button (Sticky on Mobile) -->
                <div class="fixed bottom-0 left-0 right-0 bg-white border-t border-gray-200 p-4 lg:hidden safe-area-inset-bottom">
                    <Link
                        :href="route('travel-insurance.booking-form', package.slug)"
                        class="block w-full bg-gradient-to-r from-emerald-600 to-emerald-700 text-white text-center font-bold py-4 rounded-2xl hover:from-emerald-700 hover:to-emerald-800 transition-all shadow-lg touch-manipulation"
                    >
                        Book Now - {{ formatCurrency(package.price_per_day) }}/day
                    </Link>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<style scoped>
.safe-area-inset-bottom {
    padding-bottom: env(safe-area-inset-bottom);
}

.touch-manipulation {
    touch-action: manipulation;
}
</style>
