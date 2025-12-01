<template>
    <Head :title="seo.title">
        <meta name="description" :content="seo.description" />
        <meta name="keywords" :content="seo.keywords" />
    </Head>

    <div class="min-h-screen bg-gradient-to-br from-gray-50 to-gray-100">
        <Header />
        
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <!-- Enhanced Breadcrumb -->
            <nav class="flex items-center gap-2 text-sm mb-8 overflow-x-auto">
                <Link 
                    :href="route('directory.index')" 
                    class="flex items-center gap-1 px-3 py-1.5 bg-white rounded-lg hover:bg-blue-50 hover:text-blue-600 transition-all group border border-gray-200"
                >
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                    </svg>
                    <span class="font-medium">Directory</span>
                </Link>
                <svg class="h-4 w-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                </svg>
                <Link
                    v-if="directory.category"
                    :href="route('directory.category', directory.category.slug)"
                    class="flex items-center gap-1 px-3 py-1.5 bg-white rounded-lg hover:bg-blue-50 hover:text-blue-600 transition-all border border-gray-200"
                >
                    <span class="font-medium">{{ directory.category.name }}</span>
                </Link>
                <svg v-if="directory.category" class="h-4 w-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                </svg>
                <span class="px-3 py-1.5 bg-blue-50 text-blue-700 rounded-lg font-semibold border border-blue-200">{{ directory.name }}</span>
            </nav>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Main Content -->
                <div class="lg:col-span-2 space-y-8">
                    <!-- Hero Image with Enhanced Gallery -->
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                        <div class="relative aspect-video bg-gradient-to-br from-gray-100 to-gray-200 overflow-hidden">
                            <img
                                v-if="directory.featured_image"
                                :src="directory.featured_image"
                                :alt="directory.name"
                                class="w-full h-full object-cover"
                            />
                            <div v-else class="w-full h-full flex items-center justify-center bg-gradient-to-br from-blue-500 to-indigo-600">
                                <BuildingOffice2Icon class="h-32 w-32 text-white/30" />
                            </div>
                            
                            <!-- Overlay Badges -->
                            <div class="absolute top-4 left-4 flex items-center gap-2">
                                <span
                                    v-if="directory.category"
                                    class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-sm font-bold backdrop-blur-sm shadow-lg"
                                    :style="{ backgroundColor: directory.category.color + 'E6', color: 'white' }"
                                >
                                    <span v-if="directory.category.icon" v-html="directory.category.icon" class="w-4 h-4"></span>
                                    {{ directory.category.name }}
                                </span>
                                <span v-if="directory.is_verified" class="inline-flex items-center gap-2 px-4 py-2 bg-green-500 text-white rounded-xl text-sm font-bold backdrop-blur-sm shadow-lg">
                                    <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M6.267 3.455a3.066 3.066 0 001.745-.723 3.066 3.066 0 013.976 0 3.066 3.066 0 001.745.723 3.066 3.066 0 012.812 2.812c.051.643.304 1.254.723 1.745a3.066 3.066 0 010 3.976 3.066 3.066 0 00-.723 1.745 3.066 3.066 0 01-2.812 2.812 3.066 3.066 0 00-1.745.723 3.066 3.066 0 01-3.976 0 3.066 3.066 0 00-1.745-.723 3.066 3.066 0 01-2.812-2.812 3.066 3.066 0 00-.723-1.745 3.066 3.066 0 010-3.976 3.066 3.066 0 00.723-1.745 3.066 3.066 0 012.812-2.812zm7.44 5.252a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                    </svg>
                                    Verified
                                </span>
                            </div>
                        </div>

                        <!-- Additional Images Gallery -->
                        <div v-if="directory.images && directory.images.length > 0" class="grid grid-cols-4 gap-2 p-4 bg-gray-50">
                            <div
                                v-for="(image, index) in directory.images"
                                :key="index"
                                class="aspect-square bg-gray-200 rounded-lg overflow-hidden cursor-pointer hover:opacity-75 transition-all hover:scale-105"
                            >
                                <img :src="image" :alt="`${directory.name} - Image ${index + 1}`" class="w-full h-full object-cover" />
                            </div>
                        </div>
                    </div>

                    <!-- Header Section -->
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-8">
                        <h1 class="text-4xl font-bold text-gray-900 mb-4">{{ directory.name }}</h1>

                        <!-- Meta Info Bar -->
                        <div class="flex items-center flex-wrap gap-6 pb-6 border-b border-gray-200">
                            <div class="flex items-center gap-2 text-gray-600">
                                <div class="p-2 bg-blue-50 rounded-lg">
                                    <EyeIcon class="h-5 w-5 text-blue-600" />
                                </div>
                                <div>
                                    <p class="text-xs text-gray-500">Views</p>
                                    <p class="font-semibold">{{ formatNumber(directory.view_count || 0) }}</p>
                                </div>
                            </div>
                            <div v-if="directory.country" class="flex items-center gap-2 text-gray-600">
                                <div class="p-2 bg-green-50 rounded-lg">
                                    <GlobeAltIcon class="h-5 w-5 text-green-600" />
                                </div>
                                <div>
                                    <p class="text-xs text-gray-500">Country</p>
                                    <p class="font-semibold">{{ directory.country.name }}</p>
                                </div>
                            </div>
                            <div v-if="directory.city" class="flex items-center gap-2 text-gray-600">
                                <div class="p-2 bg-purple-50 rounded-lg">
                                    <MapPinIcon class="h-5 w-5 text-purple-600" />
                                </div>
                                <div>
                                    <p class="text-xs text-gray-500">City</p>
                                    <p class="font-semibold">{{ directory.city }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Description -->
                    <div v-if="directory.description" class="bg-white rounded-2xl shadow-sm border border-gray-100 p-8">
                        <div class="flex items-center gap-3 mb-6">
                            <div class="p-2 bg-blue-50 rounded-lg">
                                <svg class="h-6 w-6 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                            <h2 class="text-2xl font-bold text-gray-900">About</h2>
                        </div>
                        <div class="prose prose-blue max-w-none text-gray-700" v-html="directory.description"></div>
                    </div>

                    <!-- Services -->
                    <div v-if="directory.services && directory.services.length > 0" class="bg-white rounded-2xl shadow-sm border border-gray-100 p-8">
                        <div class="flex items-center gap-3 mb-6">
                            <div class="p-2 bg-purple-50 rounded-lg">
                                <svg class="h-6 w-6 text-purple-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                                </svg>
                            </div>
                            <h2 class="text-2xl font-bold text-gray-900">Services Offered</h2>
                        </div>
                        <div class="grid grid-cols-2 gap-3">
                            <div
                                v-for="(service, index) in directory.services"
                                :key="index"
                                class="flex items-center gap-3 p-4 bg-gradient-to-r from-blue-50 to-indigo-50 rounded-xl border border-blue-100"
                            >
                                <svg class="h-5 w-5 text-blue-600 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                                <span class="text-gray-900 font-medium">{{ service }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- Opening Hours -->
                    <div v-if="directory.opening_hours" class="bg-white rounded-2xl shadow-sm border border-gray-100 p-8">
                        <div class="flex items-center gap-3 mb-6">
                            <div class="p-2 bg-green-50 rounded-lg">
                                <svg class="h-6 w-6 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                            <h2 class="text-2xl font-bold text-gray-900">Opening Hours</h2>
                        </div>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <div
                                v-for="(hours, day) in directory.opening_hours"
                                :key="day"
                                class="flex items-center justify-between p-4 bg-gray-50 rounded-xl hover:bg-gray-100 transition-colors"
                            >
                                <span class="font-semibold text-gray-900 capitalize">{{ day }}</span>
                                <span class="text-gray-600 font-medium">{{ hours }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- Map -->
                    <div v-if="directory.gps_latitude && directory.gps_longitude" class="bg-white rounded-2xl shadow-sm border border-gray-100 p-8">
                        <div class="flex items-center gap-3 mb-6">
                            <div class="p-2 bg-red-50 rounded-lg">
                                <MapPinIcon class="h-6 w-6 text-red-600" />
                            </div>
                            <h2 class="text-2xl font-bold text-gray-900">Location Map</h2>
                        </div>
                        <div class="bg-gradient-to-br from-gray-100 to-gray-200 rounded-xl overflow-hidden" style="height: 450px;">
                            <!-- Replace with actual map integration (Google Maps, Mapbox, etc.) -->
                            <div class="w-full h-full flex flex-col items-center justify-center text-gray-500">
                                <div class="p-6 bg-white rounded-2xl shadow-lg text-center">
                                    <MapPinIcon class="h-16 w-16 mx-auto mb-4 text-red-500" />
                                    <p class="text-lg font-semibold text-gray-900 mb-2">Map Location</p>
                                    <p class="text-sm text-gray-600 mb-4">Coordinates: {{ directory.gps_latitude }}, {{ directory.gps_longitude }}</p>
                                    <button
                                        @click="openDirections"
                                        class="inline-flex items-center gap-2 px-6 py-3 bg-blue-600 text-white rounded-xl hover:bg-blue-700 transition-colors font-semibold shadow-lg"
                                    >
                                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7" />
                                        </svg>
                                        Get Directions
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Enhanced Sidebar -->
                <div class="lg:col-span-1 space-y-6">
                    <!-- Contact Card -->
                    <div class="bg-gradient-to-br from-white to-gray-50 rounded-2xl shadow-sm border border-gray-100 p-6 sticky top-4">
                        <div class="flex items-center gap-3 mb-6 pb-6 border-b border-gray-200">
                            <div class="p-3 bg-blue-50 rounded-xl">
                                <svg class="h-7 w-7 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                </svg>
                            </div>
                            <h3 class="text-xl font-bold text-gray-900">Contact Information</h3>
                        </div>
                        
                        <div class="space-y-5">
                            <!-- Address -->
                            <div v-if="directory.address" class="group">
                                <div class="flex items-start gap-4 p-4 bg-white rounded-xl hover:shadow-md transition-all border border-gray-100">
                                    <div class="p-2 bg-red-50 rounded-lg flex-shrink-0">
                                        <MapPinIcon class="h-5 w-5 text-red-600" />
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1">Address</p>
                                        <p class="text-sm font-medium text-gray-900 break-words">{{ directory.address }}</p>
                                        <p class="text-sm text-gray-600">{{ directory.city }}<span v-if="directory.postal_code">, {{ directory.postal_code }}</span></p>
                                    </div>
                                </div>
                            </div>

                            <!-- Phone -->
                            <div v-if="directory.phone">
                                <a 
                                    :href="`tel:${directory.phone}`"
                                    class="flex items-start gap-4 p-4 bg-white rounded-xl hover:shadow-md transition-all border border-gray-100 group"
                                >
                                    <div class="p-2 bg-green-50 rounded-lg flex-shrink-0">
                                        <PhoneIcon class="h-5 w-5 text-green-600" />
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1">Phone</p>
                                        <p class="text-sm font-medium text-gray-900 group-hover:text-green-600">{{ directory.phone }}</p>
                                    </div>
                                    <svg class="h-5 w-5 text-gray-400 group-hover:text-green-600 transition" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                                    </svg>
                                </a>
                            </div>

                            <!-- Email -->
                            <div v-if="directory.email">
                                <a 
                                    :href="`mailto:${directory.email}`"
                                    class="flex items-start gap-4 p-4 bg-white rounded-xl hover:shadow-md transition-all border border-gray-100 group"
                                >
                                    <div class="p-2 bg-blue-50 rounded-lg flex-shrink-0">
                                        <EnvelopeIcon class="h-5 w-5 text-blue-600" />
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1">Email</p>
                                        <p class="text-sm font-medium text-gray-900 group-hover:text-blue-600 break-all">{{ directory.email }}</p>
                                    </div>
                                    <svg class="h-5 w-5 text-gray-400 group-hover:text-blue-600 transition" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                                    </svg>
                                </a>
                            </div>

                            <!-- Website -->
                            <div v-if="directory.website">
                                <a 
                                    :href="directory.website" 
                                    target="_blank"
                                    rel="noopener noreferrer"
                                    class="flex items-start gap-4 p-4 bg-white rounded-xl hover:shadow-md transition-all border border-gray-100 group"
                                >
                                    <div class="p-2 bg-purple-50 rounded-lg flex-shrink-0">
                                        <GlobeAltIcon class="h-5 w-5 text-purple-600" />
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1">Website</p>
                                        <p class="text-sm font-medium text-gray-900 group-hover:text-purple-600 truncate">{{ directory.website }}</p>
                                    </div>
                                    <svg class="h-5 w-5 text-gray-400 group-hover:text-purple-600 transition" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                                    </svg>
                                </a>
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="mt-6 pt-6 border-t border-gray-200 space-y-3">
                            <button
                                v-if="directory.gps_latitude && directory.gps_longitude"
                                @click="openDirections"
                                class="w-full flex items-center justify-center gap-2 px-6 py-3.5 bg-gradient-to-r from-blue-600 to-indigo-600 text-white rounded-xl hover:from-blue-700 hover:to-indigo-700 transition-all font-semibold shadow-lg shadow-blue-500/30"
                            >
                                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7" />
                                </svg>
                                Get Directions
                            </button>
                            
                            <button
                                class="w-full flex items-center justify-center gap-2 px-6 py-3 bg-white text-gray-700 rounded-xl hover:bg-gray-50 transition-all font-semibold border-2 border-gray-200"
                            >
                                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z" />
                                </svg>
                                Share
                            </button>
                        </div>
                    </div>

                    <!-- Related Directories -->
                    <div v-if="relatedDirectories && relatedDirectories.length > 0" class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                        <h3 class="text-lg font-bold text-gray-900 mb-5 flex items-center gap-2">
                            <svg class="h-5 w-5 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                            </svg>
                            Related Listings
                        </h3>
                        <div class="space-y-3">
                            <Link
                                v-for="related in relatedDirectories.slice(0, 5)"
                                :key="related.id"
                                :href="route('directory.show', related.slug)"
                                class="block p-4 bg-gray-50 rounded-xl hover:bg-blue-50 hover:border-blue-200 transition-all border border-gray-100 group"
                            >
                                <h4 class="font-semibold text-gray-900 group-hover:text-blue-600 mb-1 line-clamp-2">{{ related.name }}</h4>
                                <p v-if="related.city" class="text-xs text-gray-600 flex items-center gap-1">
                                    <MapPinIcon class="h-3 w-3" />
                                    {{ related.city }}
                                </p>
                            </Link>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <Footer />
    </div>
</template>

<script setup>
import { Head, Link } from '@inertiajs/vue3';
import Header from '@/Components/Header.vue';
import Footer from '@/Components/Footer.vue';
import {
    BuildingOffice2Icon,
    EyeIcon,
    GlobeAltIcon,
    MapPinIcon,
    PhoneIcon,
    EnvelopeIcon,
} from '@heroicons/vue/24/outline';

const props = defineProps({
    directory: Object,
    relatedDirectories: Array,
    seo: Object,
});

const formatNumber = (num) => {
    if (num >= 1000000) {
        return (num / 1000000).toFixed(1) + 'M';
    } else if (num >= 1000) {
        return (num / 1000).toFixed(1) + 'K';
    }
    return num.toString();
};

const openDirections = () => {
    const lat = props.directory.gps_latitude;
    const lng = props.directory.gps_longitude;
    window.open(`https://www.google.com/maps/dir/?api=1&destination=${lat},${lng}`, '_blank');
};
</script>

<style scoped>
/* Custom scrollbar for better UX */
.scrollbar-hide::-webkit-scrollbar {
    display: none;
}
.scrollbar-hide {
    -ms-overflow-style: none;
    scrollbar-width: none;
}

/* Smooth scroll behavior */
html {
    scroll-behavior: smooth;
}

/* Enhanced prose styles for description */
.prose {
    @apply text-gray-700 leading-relaxed;
}
.prose h1, .prose h2, .prose h3 {
    @apply font-bold text-gray-900 mt-6 mb-4;
}
.prose p {
    @apply mb-4;
}
.prose ul, .prose ol {
    @apply ml-6 mb-4;
}
.prose li {
    @apply mb-2;
}
</style>
