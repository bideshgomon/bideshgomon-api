<template>
    <Head title="Agency Profile" />

    <AuthenticatedLayout>
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <!-- Profile Header -->
            <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
                <div class="flex items-start justify-between mb-4">
                    <div class="flex items-start gap-6">
                        <!-- Logo -->
                        <div class="w-24 h-24 bg-gray-100 rounded-lg flex items-center justify-center overflow-hidden">
                            <img
                                v-if="agency.logo_path"
                                :src="`/storage/${agency.logo_path}`"
                                :alt="agency.name"
                                class="w-full h-full object-cover"
                            />
                            <BuildingOfficeIcon v-else class="h-12 w-12 text-gray-400" />
                        </div>

                        <!-- Agency Info -->
                        <div class="flex-1">
                            <div class="flex items-center gap-3 mb-2">
                                <h1 class="text-2xl font-bold text-gray-900">{{ agency.name }}</h1>
                                <CheckBadgeIcon
                                    v-if="agency.is_verified"
                                    class="h-6 w-6 text-blue-500"
                                    title="Verified Agency"
                                />
                                <span
                                    v-if="agency.is_featured"
                                    class="px-2 py-1 bg-yellow-100 text-yellow-800 text-xs font-medium rounded"
                                >
                                    Featured
                                </span>
                            </div>
                            <p class="text-gray-600 mb-2">{{ agency.company_name }}</p>
                            <div class="flex items-center gap-4 text-sm text-gray-500">
                                <span v-if="agency.business_type" class="capitalize">{{ agency.business_type }}</span>
                                <span v-if="agency.established_year">Since {{ agency.established_year }}</span>
                                <span v-if="agency.city">{{ agency.city }}, {{ agency.country }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- Actions -->
                    <Link
                        :href="route('agency.profile.edit')"
                        class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700"
                    >
                        Edit Profile
                    </Link>
                </div>

                <!-- Profile Completion -->
                <div v-if="profileCompletion < 100" class="mt-4 p-4 bg-amber-50 border border-amber-200 rounded-lg">
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-sm font-medium text-amber-900">Profile Completion</span>
                        <span class="text-sm text-amber-700">{{ profileCompletion }}%</span>
                    </div>
                    <div class="w-full bg-amber-200 rounded-full h-2">
                        <div
                            class="bg-amber-600 h-2 rounded-full transition-all"
                            :style="`width: ${profileCompletion}%`"
                        ></div>
                    </div>
                </div>
            </div>

            <!-- Stats Row -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
                <div class="bg-white rounded-lg shadow-sm p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-gray-600">Total Clients</p>
                            <p class="text-2xl font-bold text-gray-900">{{ agency.total_clients }}</p>
                        </div>
                        <UsersIcon class="h-10 w-10 text-blue-500" />
                    </div>
                </div>

                <div class="bg-white rounded-lg shadow-sm p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-gray-600">Success Rate</p>
                            <p class="text-2xl font-bold text-gray-900">{{ agency.success_rate }}%</p>
                        </div>
                        <ChartBarIcon class="h-10 w-10 text-green-500" />
                    </div>
                </div>

                <div class="bg-white rounded-lg shadow-sm p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-gray-600">Average Rating</p>
                            <p class="text-2xl font-bold text-gray-900">{{ agency.average_rating }}/5</p>
                        </div>
                        <StarIcon class="h-10 w-10 text-yellow-500" />
                    </div>
                </div>

                <div class="bg-white rounded-lg shadow-sm p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-gray-600">Total Reviews</p>
                            <p class="text-2xl font-bold text-gray-900">{{ agency.total_reviews }}</p>
                        </div>
                        <ChatBubbleLeftIcon class="h-10 w-10 text-purple-500" />
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Main Content -->
                <div class="lg:col-span-2 space-y-6">
                    <!-- About -->
                    <div class="bg-white rounded-lg shadow-sm p-6">
                        <h2 class="text-xl font-semibold text-gray-900 mb-4">About Us</h2>
                        <p class="text-gray-600 whitespace-pre-line">{{ agency.description }}</p>
                    </div>

                    <!-- Services Offered -->
                    <div v-if="agency.services_offered && agency.services_offered.length" class="bg-white rounded-lg shadow-sm p-6">
                        <h2 class="text-xl font-semibold text-gray-900 mb-4">Services Offered</h2>
                        <div class="flex flex-wrap gap-2">
                            <span
                                v-for="service in agency.services_offered"
                                :key="service"
                                class="px-3 py-1 bg-indigo-100 text-indigo-700 rounded-full text-sm"
                            >
                                {{ service }}
                            </span>
                        </div>
                    </div>

                    <!-- Team Members -->
                    <div v-if="agency.team_members && agency.team_members.length" class="bg-white rounded-lg shadow-sm p-6">
                        <div class="flex items-center justify-between mb-4">
                            <h2 class="text-xl font-semibold text-gray-900">Our Team</h2>
                            <Link
                                :href="route('agency.team.index')"
                                class="text-indigo-600 hover:text-indigo-700 text-sm font-medium"
                            >
                                Manage Team →
                            </Link>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div
                                v-for="member in agency.team_members"
                                :key="member.id"
                                class="flex items-start gap-4 p-4 border border-gray-200 rounded-lg"
                            >
                                <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center overflow-hidden flex-shrink-0">
                                    <img
                                        v-if="member.photo"
                                        :src="`/storage/${member.photo}`"
                                        :alt="member.name"
                                        class="w-full h-full object-cover"
                                    />
                                    <UserIcon v-else class="h-8 w-8 text-gray-400" />
                                </div>
                                <div class="flex-1 min-w-0">
                                    <h3 class="font-semibold text-gray-900 truncate">{{ member.name }}</h3>
                                    <p class="text-sm text-gray-600 truncate">{{ member.position }}</p>
                                    <p v-if="member.years_experience" class="text-xs text-gray-500 mt-1">
                                        {{ member.years_experience }} years experience
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Recent Reviews -->
                    <div v-if="agency.reviews && agency.reviews.length" class="bg-white rounded-lg shadow-sm p-6">
                        <h2 class="text-xl font-semibold text-gray-900 mb-4">Recent Reviews</h2>
                        <div class="space-y-4">
                            <div
                                v-for="review in agency.reviews"
                                :key="review.id"
                                class="border-b border-gray-200 last:border-0 pb-4 last:pb-0"
                            >
                                <div class="flex items-center gap-3 mb-2">
                                    <div class="w-10 h-10 bg-indigo-100 rounded-full flex items-center justify-center">
                                        <span class="text-indigo-700 font-semibold text-sm">
                                            {{ (review.user.name || '').charAt(0).toUpperCase() }}
                                        </span>
                                    </div>
                                    <div class="flex-1">
                                        <p class="font-medium text-gray-900">{{ review.user.name }}</p>
                                        <div class="flex items-center gap-1">
                                            <StarIcon
                                                v-for="i in 5"
                                                :key="i"
                                                :class="[
                                                    'h-4 w-4',
                                                    i <= review.rating ? 'text-yellow-400 fill-yellow-400' : 'text-gray-300'
                                                ]"
                                            />
                                        </div>
                                    </div>
                                    <span class="text-sm text-gray-500">{{ formatDate(review.created_at) }}</span>
                                </div>
                                <p class="text-gray-600 text-sm">{{ review.review }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Sidebar -->
                <div class="space-y-6">
                    <!-- Contact Info -->
                    <div class="bg-white rounded-lg shadow-sm p-6">
                        <h2 class="text-lg font-semibold text-gray-900 mb-4">Contact Information</h2>
                        <div class="space-y-3">
                            <div v-if="agency.email" class="flex items-start gap-3">
                                <EnvelopeIcon class="h-5 w-5 text-gray-400 mt-0.5" />
                                <div>
                                    <p class="text-sm text-gray-600">Email</p>
                                    <a :href="`mailto:${agency.email}`" class="text-indigo-600 hover:text-indigo-700">
                                        {{ agency.email }}
                                    </a>
                                </div>
                            </div>
                            <div v-if="agency.phone" class="flex items-start gap-3">
                                <PhoneIcon class="h-5 w-5 text-gray-400 mt-0.5" />
                                <div>
                                    <p class="text-sm text-gray-600">Phone</p>
                                    <a :href="`tel:${agency.phone}`" class="text-indigo-600 hover:text-indigo-700">
                                        {{ agency.phone }}
                                    </a>
                                </div>
                            </div>
                            <div v-if="agency.whatsapp" class="flex items-start gap-3">
                                <ChatBubbleLeftIcon class="h-5 w-5 text-gray-400 mt-0.5" />
                                <div>
                                    <p class="text-sm text-gray-600">WhatsApp</p>
                                    <a :href="`https://wa.me/${agency.whatsapp.replace(/[^0-9]/g, '')}`" target="_blank" class="text-indigo-600 hover:text-indigo-700">
                                        {{ agency.whatsapp }}
                                    </a>
                                </div>
                            </div>
                            <div v-if="agency.address" class="flex items-start gap-3">
                                <MapPinIcon class="h-5 w-5 text-gray-400 mt-0.5" />
                                <div>
                                    <p class="text-sm text-gray-600">Address</p>
                                    <p class="text-gray-900">{{ agency.address }}</p>
                                    <p class="text-gray-600">{{ agency.city }}, {{ agency.country }}</p>
                                </div>
                            </div>
                            <div v-if="agency.website" class="flex items-start gap-3">
                                <GlobeAltIcon class="h-5 w-5 text-gray-400 mt-0.5" />
                                <div>
                                    <p class="text-sm text-gray-600">Website</p>
                                    <a :href="agency.website" target="_blank" class="text-indigo-600 hover:text-indigo-700">
                                        {{ agency.website }}
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Countries Expertise -->
                    <div v-if="agency.countries_expertise && agency.countries_expertise.length" class="bg-white rounded-lg shadow-sm p-6">
                        <h2 class="text-lg font-semibold text-gray-900 mb-4">Countries We Serve</h2>
                        <div class="flex flex-wrap gap-2">
                            <span
                                v-for="country in agency.countries_expertise"
                                :key="country"
                                class="px-2 py-1 bg-gray-100 text-gray-700 rounded text-sm"
                            >
                                {{ country }}
                            </span>
                        </div>
                    </div>

                    <!-- Languages -->
                    <div v-if="agency.languages_spoken && agency.languages_spoken.length" class="bg-white rounded-lg shadow-sm p-6">
                        <h2 class="text-lg font-semibold text-gray-900 mb-4">Languages</h2>
                        <div class="flex flex-wrap gap-2">
                            <span
                                v-for="language in agency.languages_spoken"
                                :key="language"
                                class="px-2 py-1 bg-blue-100 text-blue-700 rounded text-sm"
                            >
                                {{ language }}
                            </span>
                        </div>
                    </div>

                    <!-- Social Links -->
                    <div v-if="agency.facebook_url || agency.linkedin_url || agency.twitter_url || agency.instagram_url" class="bg-white rounded-lg shadow-sm p-6">
                        <h2 class="text-lg font-semibold text-gray-900 mb-4">Follow Us</h2>
                        <div class="flex gap-3">
                            <a v-if="agency.facebook_url" :href="agency.facebook_url" target="_blank" class="text-gray-400 hover:text-blue-600">
                                <span class="sr-only">Facebook</span>
                                <svg class="h-6 w-6" fill="currentColor" viewBox="0 0 24 24"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>
                            </a>
                            <a v-if="agency.linkedin_url" :href="agency.linkedin_url" target="_blank" class="text-gray-400 hover:text-blue-700">
                                <span class="sr-only">LinkedIn</span>
                                <svg class="h-6 w-6" fill="currentColor" viewBox="0 0 24 24"><path d="M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433c-1.144 0-2.063-.926-2.063-2.065 0-1.138.92-2.063 2.063-2.063 1.14 0 2.064.925 2.064 2.063 0 1.139-.925 2.065-2.064 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z"/></svg>
                            </a>
                            <a v-if="agency.twitter_url" :href="agency.twitter_url" target="_blank" class="text-gray-400 hover:text-blue-400">
                                <span class="sr-only">Twitter</span>
                                <svg class="h-6 w-6" fill="currentColor" viewBox="0 0 24 24"><path d="M8.29 20.251c7.547 0 11.675-6.253 11.675-11.675 0-.178 0-.355-.012-.53A8.348 8.348 0 0022 5.92a8.19 8.19 0 01-2.357.646 4.118 4.118 0 001.804-2.27 8.224 8.224 0 01-2.605.996 4.107 4.107 0 00-6.993 3.743 11.65 11.65 0 01-8.457-4.287 4.106 4.106 0 001.27 5.477A4.072 4.072 0 012.8 9.713v.052a4.105 4.105 0 003.292 4.022 4.095 4.095 0 01-1.853.07 4.108 4.108 0 003.834 2.85A8.233 8.233 0 012 18.407a11.616 11.616 0 006.29 1.84"/></svg>
                            </a>
                            <a v-if="agency.instagram_url" :href="agency.instagram_url" target="_blank" class="text-gray-400 hover:text-pink-600">
                                <span class="sr-only">Instagram</span>
                                <svg class="h-6 w-6" fill="currentColor" viewBox="0 0 24 24"><path d="M12.315 2c2.43 0 2.784.013 3.808.06 1.064.049 1.791.218 2.427.465a4.902 4.902 0 011.772 1.153 4.902 4.902 0 011.153 1.772c.247.636.416 1.363.465 2.427.048 1.067.06 1.407.06 4.123v.08c0 2.643-.012 2.987-.06 4.043-.049 1.064-.218 1.791-.465 2.427a4.902 4.902 0 01-1.153 1.772 4.902 4.902 0 01-1.772 1.153c-.636.247-1.363.416-2.427.465-1.067.048-1.407.06-4.123.06h-.08c-2.643 0-2.987-.012-4.043-.06-1.064-.049-1.791-.218-2.427-.465a4.902 4.902 0 01-1.772-1.153 4.902 4.902 0 01-1.153-1.772c-.247-.636-.416-1.363-.465-2.427-.047-1.024-.06-1.379-.06-3.808v-.63c0-2.43.013-2.784.06-3.808.049-1.064.218-1.791.465-2.427a4.902 4.902 0 011.153-1.772A4.902 4.902 0 015.45 2.525c.636-.247 1.363-.416 2.427-.465C8.901 2.013 9.256 2 11.685 2h.63zm-.081 1.802h-.468c-2.456 0-2.784.011-3.807.058-.975.045-1.504.207-1.857.344-.467.182-.8.398-1.15.748-.35.35-.566.683-.748 1.15-.137.353-.3.882-.344 1.857-.047 1.023-.058 1.351-.058 3.807v.468c0 2.456.011 2.784.058 3.807.045.975.207 1.504.344 1.857.182.466.399.8.748 1.15.35.35.683.566 1.15.748.353.137.882.3 1.857.344 1.054.048 1.37.058 4.041.058h.08c2.597 0 2.917-.01 3.96-.058.976-.045 1.505-.207 1.858-.344.466-.182.8-.398 1.15-.748.35-.35.566-.683.748-1.15.137-.353.3-.882.344-1.857.048-1.055.058-1.37.058-4.041v-.08c0-2.597-.01-2.917-.058-3.96-.045-.976-.207-1.505-.344-1.858a3.097 3.097 0 00-.748-1.15 3.098 3.098 0 00-1.15-.748c-.353-.137-.882-.3-1.857-.344-1.023-.047-1.351-.058-3.807-.058zM12 6.865a5.135 5.135 0 110 10.27 5.135 5.135 0 010-10.27zm0 1.802a3.333 3.333 0 100 6.666 3.333 3.333 0 000-6.666zm5.338-3.205a1.2 1.2 0 110 2.4 1.2 1.2 0 010-2.4z"/></svg>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import { Head, Link } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import {
    BuildingOfficeIcon,
    CheckBadgeIcon,
    UsersIcon,
    ChartBarIcon,
    StarIcon,
    ChatBubbleLeftIcon,
    EnvelopeIcon,
    PhoneIcon,
    MapPinIcon,
    GlobeAltIcon,
    UserIcon,
} from '@heroicons/vue/24/outline';

defineProps({
    agency: Object,
    profileCompletion: Number,
});

const formatDate = (dateString) => {
    return new Date(dateString).toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'short',
        day: 'numeric',
    });
};
</script>
