<template>
    <Head :title="seo.title">
        <meta name="description" :content="seo.description" />
        <meta name="keywords" :content="seo.keywords" />
    </Head>

    <AuthenticatedLayout>
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <!-- Breadcrumb -->
            <nav class="flex items-center gap-2 text-sm text-gray-600 mb-6">
                <Link :href="route('directory.index')" class="hover:text-blue-600">Directory</Link>
                <ChevronRightIcon class="h-4 w-4" />
                <Link
                    v-if="directory.category"
                    :href="route('directory.category', directory.category.slug)"
                    class="hover:text-blue-600"
                >
                    {{ directory.category.name }}
                </Link>
                <ChevronRightIcon class="h-4 w-4" />
                <span class="text-gray-900 font-medium">{{ directory.name }}</span>
            </nav>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Main Content -->
                <div class="lg:col-span-2">
                    <!-- Image Gallery -->
                    <div class="mb-8">
                        <div class="aspect-video bg-gray-200 rounded-lg overflow-hidden mb-4">
                            <img
                                v-if="directory.featured_image"
                                :src="directory.featured_image"
                                :alt="directory.name"
                                class="w-full h-full object-cover"
                            />
                            <div v-else class="w-full h-full flex items-center justify-center bg-gradient-to-br from-blue-500 to-blue-600">
                                <BuildingOffice2Icon class="h-24 w-24 text-white/50" />
                            </div>
                        </div>

                        <!-- Additional Images -->
                        <div v-if="directory.images && directory.images.length > 0" class="grid grid-cols-4 gap-4">
                            <div
                                v-for="(image, index) in directory.images"
                                :key="index"
                                class="aspect-square bg-gray-200 rounded-lg overflow-hidden cursor-pointer hover:opacity-75 transition-opacity"
                            >
                                <img :src="image" :alt="`${directory.name} - Image ${index + 1}`" class="w-full h-full object-cover" />
                            </div>
                        </div>
                    </div>

                    <!-- Header -->
                    <div class="mb-8">
                        <!-- Category Badge -->
                        <span
                            v-if="directory.category"
                            class="inline-block px-3 py-1 rounded-full text-sm font-medium mb-3"
                            :style="{ backgroundColor: directory.category.color + '20', color: directory.category.color }"
                        >
                            {{ directory.category.name }}
                        </span>

                        <h1 class="text-3xl font-bold text-gray-900 mb-4">{{ directory.name }}</h1>

                        <!-- Meta Info -->
                        <div class="flex items-center gap-4 text-sm text-gray-600">
                            <div class="flex items-center gap-1">
                                <EyeIcon class="h-5 w-5" />
                                <span>{{ directory.view_count || 0 }} views</span>
                            </div>
                            <div v-if="directory.country" class="flex items-center gap-1">
                                <GlobeAltIcon class="h-5 w-5" />
                                <span>{{ directory.country.name }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- Description -->
                    <div v-if="directory.description" class="prose max-w-none mb-8">
                        <h2 class="text-xl font-semibold text-gray-900 mb-4">About</h2>
                        <div class="text-gray-700" v-html="directory.description"></div>
                    </div>

                    <!-- Services -->
                    <div v-if="directory.services && directory.services.length > 0" class="mb-8">
                        <h2 class="text-xl font-semibold text-gray-900 mb-4">Services</h2>
                        <div class="flex flex-wrap gap-2">
                            <span
                                v-for="(service, index) in directory.services"
                                :key="index"
                                class="px-3 py-1 bg-blue-50 text-blue-700 rounded-full text-sm"
                            >
                                {{ service }}
                            </span>
                        </div>
                    </div>

                    <!-- Opening Hours -->
                    <div v-if="directory.opening_hours" class="mb-8">
                        <h2 class="text-xl font-semibold text-gray-900 mb-4">Opening Hours</h2>
                        <div class="bg-gray-50 rounded-lg p-6 space-y-2">
                            <div
                                v-for="(hours, day) in directory.opening_hours"
                                :key="day"
                                class="flex justify-between items-center py-2 border-b border-gray-200 last:border-0"
                            >
                                <span class="font-medium text-gray-900 capitalize">{{ day }}</span>
                                <span class="text-gray-600">{{ hours }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- Map -->
                    <div v-if="directory.gps_latitude && directory.gps_longitude" class="mb-8">
                        <h2 class="text-xl font-semibold text-gray-900 mb-4">Location</h2>
                        <div class="bg-gray-200 rounded-lg overflow-hidden" style="height: 400px;">
                            <!-- Replace with actual map integration (Google Maps, Mapbox, etc.) -->
                            <div class="w-full h-full flex items-center justify-center text-gray-500">
                                <div class="text-center">
                                    <MapPinIcon class="h-12 w-12 mx-auto mb-2" />
                                    <p>Map Location</p>
                                    <p class="text-sm">{{ directory.gps_latitude }}, {{ directory.gps_longitude }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Sidebar -->
                <div class="lg:col-span-1">
                    <div class="bg-white rounded-lg shadow-sm p-6 sticky top-4 space-y-6">
                        <!-- Contact Information -->
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900 mb-4">Contact Information</h3>
                            
                            <div class="space-y-4">
                                <!-- Address -->
                                <div v-if="directory.address">
                                    <div class="flex items-start gap-3">
                                        <MapPinIcon class="h-5 w-5 text-gray-400 mt-0.5 flex-shrink-0" />
                                        <div>
                                            <p class="text-sm font-medium text-gray-900">Address</p>
                                            <p class="text-sm text-gray-600">{{ directory.address }}</p>
                                            <p class="text-sm text-gray-600">{{ directory.city }}, {{ directory.postal_code }}</p>
                                        </div>
                                    </div>
                                </div>

                                <!-- Phone -->
                                <div v-if="directory.phone">
                                    <div class="flex items-start gap-3">
                                        <PhoneIcon class="h-5 w-5 text-gray-400 mt-0.5 flex-shrink-0" />
                                        <div>
                                            <p class="text-sm font-medium text-gray-900">Phone</p>
                                            <a :href="`tel:${directory.phone}`" class="text-sm text-blue-600 hover:underline">
                                                {{ directory.phone }}
                                            </a>
                                        </div>
                                    </div>
                                </div>

                                <!-- Email -->
                                <div v-if="directory.email">
                                    <div class="flex items-start gap-3">
                                        <EnvelopeIcon class="h-5 w-5 text-gray-400 mt-0.5 flex-shrink-0" />
                                        <div>
                                            <p class="text-sm font-medium text-gray-900">Email</p>
                                            <a :href="`mailto:${directory.email}`" class="text-sm text-blue-600 hover:underline">
                                                {{ directory.email }}
                                            </a>
                                        </div>
                                    </div>
                                </div>

                                <!-- Website -->
                                <div v-if="directory.website">
                                    <div class="flex items-start gap-3">
                                        <GlobeAltIcon class="h-5 w-5 text-gray-400 mt-0.5 flex-shrink-0" />
                                        <div>
                                            <p class="text-sm font-medium text-gray-900">Website</p>
                                            <a :href="directory.website" target="_blank" class="text-sm text-blue-600 hover:underline">
                                                Visit Website →
                                            </a>
                                        </div>
                                    </div>
                                </div>

                                <!-- Social Media -->
                                <div v-if="directory.facebook || directory.twitter || directory.linkedin">
                                    <p class="text-sm font-medium text-gray-900 mb-2">Social Media</p>
                                    <div class="flex items-center gap-3">
                                        <a v-if="directory.facebook" :href="directory.facebook" target="_blank" class="text-gray-600 hover:text-blue-600">
                                            <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 24 24">
                                                <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
                                            </svg>
                                        </a>
                                        <a v-if="directory.twitter" :href="directory.twitter" target="_blank" class="text-gray-600 hover:text-blue-600">
                                            <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 24 24">
                                                <path d="M23.953 4.57a10 10 0 01-2.825.775 4.958 4.958 0 002.163-2.723c-.951.555-2.005.959-3.127 1.184a4.92 4.92 0 00-8.384 4.482C7.69 8.095 4.067 6.13 1.64 3.162a4.822 4.822 0 00-.666 2.475c0 1.71.87 3.213 2.188 4.096a4.904 4.904 0 01-2.228-.616v.06a4.923 4.923 0 003.946 4.827 4.996 4.996 0 01-2.212.085 4.936 4.936 0 004.604 3.417 9.867 9.867 0 01-6.102 2.105c-.39 0-.779-.023-1.17-.067a13.995 13.995 0 007.557 2.209c9.053 0 13.998-7.496 13.998-13.985 0-.21 0-.42-.015-.63A9.935 9.935 0 0024 4.59z"/>
                                            </svg>
                                        </a>
                                        <a v-if="directory.linkedin" :href="directory.linkedin" target="_blank" class="text-gray-600 hover:text-blue-600">
                                            <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 24 24">
                                                <path d="M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433c-1.144 0-2.063-.926-2.063-2.065 0-1.138.92-2.063 2.063-2.063 1.14 0 2.064.925 2.064 2.063 0 1.139-.925 2.065-2.064 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z"/>
                                            </svg>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="space-y-3 pt-6 border-t border-gray-200">
                            <a
                                v-if="directory.phone"
                                :href="`tel:${directory.phone}`"
                                class="w-full flex items-center justify-center gap-2 px-4 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors font-medium"
                            >
                                <PhoneIcon class="h-5 w-5" />
                                Call Now
                            </a>
                            <a
                                v-if="directory.website"
                                :href="directory.website"
                                target="_blank"
                                class="w-full flex items-center justify-center gap-2 px-4 py-3 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors font-medium"
                            >
                                <GlobeAltIcon class="h-5 w-5" />
                                Visit Website
                            </a>
                            <button
                                v-if="directory.gps_latitude && directory.gps_longitude"
                                @click="openDirections"
                                class="w-full flex items-center justify-center gap-2 px-4 py-3 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors font-medium"
                            >
                                <MapPinIcon class="h-5 w-5" />
                                Get Directions
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Related Directories -->
            <div v-if="relatedDirectories.length > 0" class="mt-12">
                <h2 class="text-2xl font-bold text-gray-900 mb-6">Related Directories</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                    <Link
                        v-for="related in relatedDirectories"
                        :key="related.id"
                        :href="route('directory.show', related.slug)"
                        class="bg-white rounded-lg shadow-sm hover:shadow-lg transition-all overflow-hidden group"
                    >
                        <div class="aspect-video bg-gray-200 overflow-hidden">
                            <img
                                v-if="related.featured_image"
                                :src="related.featured_image"
                                :alt="related.name"
                                class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300"
                            />
                            <div v-else class="w-full h-full flex items-center justify-center bg-gradient-to-br from-blue-500 to-blue-600">
                                <BuildingOffice2Icon class="h-12 w-12 text-white/50" />
                            </div>
                        </div>
                        <div class="p-4">
                            <h3 class="font-semibold text-gray-900 group-hover:text-blue-600 transition-colors line-clamp-2">
                                {{ related.name }}
                            </h3>
                            <p v-if="related.city" class="text-sm text-gray-600 mt-1">{{ related.city }}</p>
                        </div>
                    </Link>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import { Head, Link } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import {
    ChevronRightIcon,
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

const openDirections = () => {
    const lat = props.directory.gps_latitude;
    const lng = props.directory.gps_longitude;
    window.open(`https://www.google.com/maps/dir/?api=1&destination=${lat},${lng}`, '_blank');
};
</script>
