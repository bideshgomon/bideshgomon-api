<template>
    <Head :title="agency.name" />

    <AuthenticatedLayout>
        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <!-- Breadcrumb -->
                <div class="mb-6">
                    <Link :href="route('agencies.index')" class="text-indigo-600 hover:text-indigo-900">
                        ← Back to Agencies
                    </Link>
                </div>

                <!-- Agency Header -->
                <div class="bg-white rounded-lg shadow-sm p-8 mb-6">
                    <div class="flex items-start justify-between">
                        <div class="flex items-start space-x-6">
                            <div class="flex-shrink-0">
                                <img 
                                    v-if="agency.logo" 
                                    :src="`/storage/${agency.logo}`" 
                                    :alt="agency.name"
                                    class="h-24 w-24 object-cover rounded-lg"
                                />
                                <div v-else class="h-24 w-24 bg-indigo-100 rounded-lg flex items-center justify-center">
                                    <BuildingOfficeIcon class="h-12 w-12 text-indigo-600" />
                                </div>
                            </div>
                            <div>
                                <div class="flex items-center space-x-2">
                                    <h1 class="text-3xl font-bold text-gray-900">{{ agency.name }}</h1>
                                    <CheckBadgeIcon v-if="agency.is_verified" class="h-7 w-7 text-blue-500" />
                                    <span v-if="agency.is_featured" class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-yellow-100 text-yellow-800">
                                        Featured
                                    </span>
                                </div>
                                <p v-if="agency.company_name" class="text-lg text-gray-600 mt-1">{{ agency.company_name }}</p>
                                <div class="flex items-center space-x-4 mt-2">
                                    <span v-if="agency.agency_type" :class="[
                                        'inline-flex items-center px-3 py-1 rounded-full text-sm font-medium',
                                        `bg-${agency.agency_type.color}-100 text-${agency.agency_type.color}-800`
                                    ]">
                                        {{ agency.agency_type.name }}
                                    </span>
                                    <span v-else-if="agency.business_type" class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-indigo-100 text-indigo-800">
                                        {{ formatBusinessType(agency.business_type) }}
                                    </span>
                                    <span v-if="agency.established_year" class="text-sm text-gray-500">
                                        Est. {{ agency.established_year }}
                                    </span>
                                    <span v-if="agency.city" class="text-sm text-gray-500 flex items-center">
                                        <MapPinIcon class="h-4 w-4 mr-1" />
                                        {{ agency.city }}{{ agency.country ? `, ${agency.country}` : '' }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Stats Cards -->
                <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
                    <div class="bg-white rounded-lg shadow-sm p-6">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm font-medium text-gray-600">Total Clients</p>
                                <p class="text-2xl font-bold text-gray-900 mt-1">{{ agency.total_clients || 0 }}</p>
                            </div>
                            <UsersIcon class="h-12 w-12 text-blue-500" />
                        </div>
                    </div>

                    <div class="bg-white rounded-lg shadow-sm p-6">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm font-medium text-gray-600">Success Rate</p>
                                <p class="text-2xl font-bold text-gray-900 mt-1">{{ agency.success_rate || 0 }}%</p>
                            </div>
                            <ChartBarIcon class="h-12 w-12 text-green-500" />
                        </div>
                    </div>

                    <div class="bg-white rounded-lg shadow-sm p-6">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm font-medium text-gray-600">Average Rating</p>
                                <div class="flex items-center mt-1">
                                    <p class="text-2xl font-bold text-gray-900">{{ agency.average_rating || 0 }}</p>
                                    <StarIcon class="h-6 w-6 text-yellow-400 fill-current ml-1" />
                                </div>
                            </div>
                            <StarIcon class="h-12 w-12 text-yellow-500" />
                        </div>
                    </div>

                    <div class="bg-white rounded-lg shadow-sm p-6">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm font-medium text-gray-600">Total Reviews</p>
                                <p class="text-2xl font-bold text-gray-900 mt-1">{{ agency.total_reviews || 0 }}</p>
                            </div>
                            <ChatBubbleLeftIcon class="h-12 w-12 text-purple-500" />
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    <!-- Main Content -->
                    <div class="lg:col-span-2 space-y-6">
                        <!-- About -->
                        <div class="bg-white rounded-lg shadow-sm p-6">
                            <h2 class="text-xl font-bold text-gray-900 mb-4">About Us</h2>
                            <p v-if="agency.description" class="text-gray-700 whitespace-pre-line">{{ agency.description }}</p>
                            <p v-else class="text-gray-500 italic">No description available</p>
                        </div>

                        <!-- Services -->
                        <div v-if="agency.services_offered && agency.services_offered.length > 0" class="bg-white rounded-lg shadow-sm p-6">
                            <h2 class="text-xl font-bold text-gray-900 mb-4">Services Offered</h2>
                            <div class="flex flex-wrap gap-2">
                                <span 
                                    v-for="service in agency.services_offered" 
                                    :key="service"
                                    class="inline-flex items-center px-3 py-2 rounded-lg text-sm font-medium bg-indigo-50 text-indigo-700 border border-indigo-200"
                                >
                                    {{ formatServiceName(service) }}
                                </span>
                            </div>
                        </div>

                        <!-- Team Members -->
                        <div v-if="teamMembers.length > 0" class="bg-white rounded-lg shadow-sm p-6">
                            <h2 class="text-xl font-bold text-gray-900 mb-4">Our Team</h2>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div v-for="member in teamMembers" :key="member.id" class="flex items-start space-x-4 p-4 bg-gray-50 rounded-lg">
                                    <div class="flex-shrink-0">
                                        <img 
                                            v-if="member.photo" 
                                            :src="`/storage/${member.photo}`" 
                                            :alt="member.name"
                                            class="h-16 w-16 rounded-full object-cover"
                                        />
                                        <div v-else class="h-16 w-16 rounded-full bg-indigo-100 flex items-center justify-center">
                                            <UserIcon class="h-8 w-8 text-indigo-600" />
                                        </div>
                                    </div>
                                    <div>
                                        <h3 class="font-semibold text-gray-900">{{ member.name }}</h3>
                                        <p class="text-sm text-gray-600">{{ member.position }}</p>
                                        <p v-if="member.years_experience" class="text-xs text-gray-500 mt-1">
                                            {{ member.years_experience }} years experience
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Reviews -->
                        <div class="bg-white rounded-lg shadow-sm p-6">
                            <h2 class="text-xl font-bold text-gray-900 mb-4">Client Reviews</h2>
                            
                            <!-- Review Form -->
                            <div v-if="$page.props.auth.user" class="mb-6 p-4 bg-gray-50 rounded-lg">
                                <h3 class="font-semibold text-gray-900 mb-3">Write a Review</h3>
                                <form @submit.prevent="submitReview" class="space-y-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Rating</label>
                                        <div class="flex space-x-2">
                                            <button
                                                v-for="star in 5"
                                                :key="star"
                                                type="button"
                                                @click="reviewForm.rating = star"
                                                class="focus:outline-none"
                                            >
                                                <StarIcon 
                                                    :class="[
                                                        'h-8 w-8',
                                                        star <= reviewForm.rating ? 'text-yellow-400 fill-current' : 'text-gray-300'
                                                    ]"
                                                />
                                            </button>
                                        </div>
                                        <p v-if="reviewForm.errors.rating" class="mt-1 text-sm text-red-600">{{ reviewForm.errors.rating }}</p>
                                    </div>

                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Your Review</label>
                                        <textarea
                                            v-model="reviewForm.review"
                                            rows="4"
                                            placeholder="Share your experience with this agency..."
                                            class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                        ></textarea>
                                        <p v-if="reviewForm.errors.review" class="mt-1 text-sm text-red-600">{{ reviewForm.errors.review }}</p>
                                    </div>

                                    <button
                                        type="submit"
                                        :disabled="reviewForm.processing"
                                        class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 disabled:opacity-50"
                                    >
                                        {{ reviewForm.processing ? 'Submitting...' : 'Submit Review' }}
                                    </button>
                                </form>
                            </div>

                            <!-- Reviews List -->
                            <div v-if="reviews.length > 0" class="space-y-4">
                                <div v-for="review in reviews" :key="review.id" class="border-b border-gray-200 pb-4 last:border-0">
                                    <div class="flex items-start space-x-3">
                                        <div class="flex-shrink-0">
                                            <div class="h-10 w-10 rounded-full bg-indigo-100 flex items-center justify-center">
                                                <span class="text-sm font-semibold text-indigo-600">
                                                    {{ review.user?.name?.charAt(0) || 'U' }}
                                                </span>
                                            </div>
                                        </div>
                                        <div class="flex-1">
                                            <div class="flex items-center justify-between">
                                                <h4 class="text-sm font-semibold text-gray-900">{{ review.user?.name || 'Anonymous' }}</h4>
                                                <span class="text-xs text-gray-500">{{ formatDate(review.created_at) }}</span>
                                            </div>
                                            <div class="flex items-center mt-1">
                                                <StarIcon 
                                                    v-for="star in 5" 
                                                    :key="star"
                                                    :class="[
                                                        'h-4 w-4',
                                                        star <= review.rating ? 'text-yellow-400 fill-current' : 'text-gray-300'
                                                    ]"
                                                />
                                            </div>
                                            <p class="mt-2 text-sm text-gray-700">{{ review.review }}</p>
                                            <div v-if="review.agency_response" class="mt-3 pl-4 border-l-2 border-indigo-200 bg-indigo-50 p-3 rounded">
                                                <p class="text-xs font-semibold text-indigo-900 mb-1">Agency Response:</p>
                                                <p class="text-sm text-gray-700">{{ review.agency_response }}</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <p v-else class="text-gray-500 text-center py-4">No reviews yet. Be the first to review!</p>
                        </div>
                    </div>

                    <!-- Sidebar -->
                    <div class="space-y-6">
                        <!-- Contact Information -->
                        <div class="bg-white rounded-lg shadow-sm p-6">
                            <h2 class="text-lg font-bold text-gray-900 mb-4">Contact Information</h2>
                            <div class="space-y-3">
                                <a v-if="agency.email" :href="`mailto:${agency.email}`" class="flex items-center text-gray-700 hover:text-indigo-600">
                                    <EnvelopeIcon class="h-5 w-5 mr-3 text-gray-400" />
                                    <span class="text-sm">{{ agency.email }}</span>
                                </a>

                                <a v-if="agency.phone" :href="`tel:${agency.phone}`" class="flex items-center text-gray-700 hover:text-indigo-600">
                                    <PhoneIcon class="h-5 w-5 mr-3 text-gray-400" />
                                    <span class="text-sm">{{ agency.phone }}</span>
                                </a>

                                <a v-if="agency.whatsapp" :href="`https://wa.me/${agency.whatsapp.replace(/[^0-9]/g, '')}`" target="_blank" class="flex items-center text-gray-700 hover:text-indigo-600">
                                    <ChatBubbleLeftIcon class="h-5 w-5 mr-3 text-gray-400" />
                                    <span class="text-sm">WhatsApp</span>
                                </a>

                                <div v-if="agency.address" class="flex items-start text-gray-700">
                                    <MapPinIcon class="h-5 w-5 mr-3 text-gray-400 flex-shrink-0 mt-0.5" />
                                    <span class="text-sm">{{ agency.address }}{{ agency.city ? `, ${agency.city}` : '' }}{{ agency.country ? `, ${agency.country}` : '' }}</span>
                                </div>

                                <a v-if="agency.website" :href="agency.website" target="_blank" class="flex items-center text-gray-700 hover:text-indigo-600">
                                    <GlobeAltIcon class="h-5 w-5 mr-3 text-gray-400" />
                                    <span class="text-sm">Visit Website</span>
                                </a>
                            </div>
                        </div>

                        <!-- Countries Expertise -->
                        <div v-if="agency.countries_expertise && agency.countries_expertise.length > 0" class="bg-white rounded-lg shadow-sm p-6">
                            <h2 class="text-lg font-bold text-gray-900 mb-4">Countries We Serve</h2>
                            <div class="flex flex-wrap gap-2">
                                <span 
                                    v-for="country in agency.countries_expertise" 
                                    :key="country"
                                    class="inline-flex items-center px-2 py-1 rounded text-xs font-medium bg-gray-100 text-gray-800"
                                >
                                    {{ country }}
                                </span>
                            </div>
                        </div>

                        <!-- Languages -->
                        <div v-if="agency.languages_spoken && agency.languages_spoken.length > 0" class="bg-white rounded-lg shadow-sm p-6">
                            <h2 class="text-lg font-bold text-gray-900 mb-4">Languages</h2>
                            <div class="flex flex-wrap gap-2">
                                <span 
                                    v-for="language in agency.languages_spoken" 
                                    :key="language"
                                    class="inline-flex items-center px-2 py-1 rounded text-xs font-medium bg-blue-100 text-blue-800"
                                >
                                    {{ language }}
                                </span>
                            </div>
                        </div>

                        <!-- Social Media -->
                        <div v-if="agency.facebook_url || agency.linkedin_url || agency.twitter_url || agency.instagram_url" class="bg-white rounded-lg shadow-sm p-6">
                            <h2 class="text-lg font-bold text-gray-900 mb-4">Follow Us</h2>
                            <div class="flex space-x-4">
                                <a v-if="agency.facebook_url" :href="agency.facebook_url" target="_blank" class="text-gray-400 hover:text-blue-600">
                                    <svg class="h-6 w-6" fill="currentColor" viewBox="0 0 24 24"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>
                                </a>
                                <a v-if="agency.linkedin_url" :href="agency.linkedin_url" target="_blank" class="text-gray-400 hover:text-blue-700">
                                    <svg class="h-6 w-6" fill="currentColor" viewBox="0 0 24 24"><path d="M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433c-1.144 0-2.063-.926-2.063-2.065 0-1.138.92-2.063 2.063-2.063 1.14 0 2.064.925 2.064 2.063 0 1.139-.925 2.065-2.064 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z"/></svg>
                                </a>
                                <a v-if="agency.twitter_url" :href="agency.twitter_url" target="_blank" class="text-gray-400 hover:text-blue-400">
                                    <svg class="h-6 w-6" fill="currentColor" viewBox="0 0 24 24"><path d="M23.953 4.57a10 10 0 01-2.825.775 4.958 4.958 0 002.163-2.723c-.951.555-2.005.959-3.127 1.184a4.92 4.92 0 00-8.384 4.482C7.69 8.095 4.067 6.13 1.64 3.162a4.822 4.822 0 00-.666 2.475c0 1.71.87 3.213 2.188 4.096a4.904 4.904 0 01-2.228-.616v.06a4.923 4.923 0 003.946 4.827 4.996 4.996 0 01-2.212.085 4.936 4.936 0 004.604 3.417 9.867 9.867 0 01-6.102 2.105c-.39 0-.779-.023-1.17-.067a13.995 13.995 0 007.557 2.209c9.053 0 13.998-7.496 13.998-13.985 0-.21 0-.42-.015-.63A9.935 9.935 0 0024 4.59z"/></svg>
                                </a>
                                <a v-if="agency.instagram_url" :href="agency.instagram_url" target="_blank" class="text-gray-400 hover:text-pink-600">
                                    <svg class="h-6 w-6" fill="currentColor" viewBox="0 0 24 24"><path d="M12.315 2c2.43 0 2.784.013 3.808.06 1.064.049 1.791.218 2.427.465a4.902 4.902 0 011.772 1.153 4.902 4.902 0 011.153 1.772c.247.636.416 1.363.465 2.427.048 1.067.06 1.407.06 4.123v.08c0 2.643-.012 2.987-.06 4.043-.049 1.064-.218 1.791-.465 2.427a4.902 4.902 0 01-1.153 1.772 4.902 4.902 0 01-1.772 1.153c-.636.247-1.363.416-2.427.465-1.067.048-1.407.06-4.123.06h-.08c-2.643 0-2.987-.012-4.043-.06-1.064-.049-1.791-.218-2.427-.465a4.902 4.902 0 01-1.772-1.153 4.902 4.902 0 01-1.153-1.772c-.247-.636-.416-1.363-.465-2.427-.047-1.024-.06-1.379-.06-3.808v-.63c0-2.43.013-2.784.06-3.808.049-1.064.218-1.791.465-2.427a4.902 4.902 0 011.153-1.772A4.902 4.902 0 015.45 2.525c.636-.247 1.363-.416 2.427-.465C8.901 2.013 9.256 2 11.685 2h.63zm-.081 1.802h-.468c-2.456 0-2.784.011-3.807.058-.975.045-1.504.207-1.857.344-.467.182-.8.398-1.15.748-.35.35-.566.683-.748 1.15-.137.353-.3.882-.344 1.857-.047 1.023-.058 1.351-.058 3.807v.468c0 2.456.011 2.784.058 3.807.045.975.207 1.504.344 1.857.182.466.399.8.748 1.15.35.35.683.566 1.15.748.353.137.882.3 1.857.344 1.054.048 1.37.058 4.041.058h.08c2.597 0 2.917-.01 3.96-.058.976-.045 1.505-.207 1.858-.344.466-.182.8-.398 1.15-.748.35-.35.566-.683.748-1.15.137-.353.3-.882.344-1.857.048-1.055.058-1.37.058-4.041v-.08c0-2.597-.01-2.917-.058-3.96-.045-.976-.207-1.505-.344-1.858a3.097 3.097 0 00-.748-1.15 3.098 3.098 0 00-1.15-.748c-.353-.137-.882-.3-1.857-.344-1.023-.047-1.351-.058-3.807-.058zM12 6.865a5.135 5.135 0 110 10.27 5.135 5.135 0 010-10.27zm0 1.802a3.333 3.333 0 100 6.666 3.333 3.333 0 000-6.666zm5.338-3.205a1.2 1.2 0 110 2.4 1.2 1.2 0 010-2.4z"/></svg>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Related Agencies -->
                <div v-if="relatedAgencies.length > 0" class="mt-8">
                    <h2 class="text-2xl font-bold text-gray-900 mb-6">Similar Agencies</h2>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <Link 
                            v-for="related in relatedAgencies" 
                            :key="related.id"
                            :href="route('agencies.show', related.slug)"
                            class="bg-white rounded-lg shadow-sm hover:shadow-lg transition-shadow p-6 border border-gray-200"
                        >
                            <div class="flex items-center space-x-4 mb-3">
                                <img 
                                    v-if="related.logo" 
                                    :src="`/storage/${related.logo}`" 
                                    :alt="related.name"
                                    class="h-12 w-12 object-cover rounded-lg"
                                />
                                <div v-else class="h-12 w-12 bg-indigo-100 rounded-lg flex items-center justify-center">
                                    <BuildingOfficeIcon class="h-6 w-6 text-indigo-600" />
                                </div>
                                <div>
                                    <h3 class="font-semibold text-gray-900">{{ related.name }}</h3>
                                    <div class="flex items-center mt-1">
                                        <StarIcon class="h-4 w-4 text-yellow-400 fill-current" />
                                        <span class="ml-1 text-sm text-gray-600">{{ related.average_rating || 0 }}</span>
                                    </div>
                                </div>
                            </div>
                        </Link>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { 
    BuildingOfficeIcon, CheckBadgeIcon, StarIcon, UsersIcon, ChartBarIcon, 
    ChatBubbleLeftIcon, UserIcon, EnvelopeIcon, PhoneIcon, MapPinIcon, GlobeAltIcon 
} from '@heroicons/vue/24/solid';

const props = defineProps({
    agency: Object,
    teamMembers: Array,
    reviews: Array,
    relatedAgencies: Array,
});

const reviewForm = useForm({
    rating: 5,
    review: '',
});

const formatBusinessType = (type) => {
    const types = {
        recruitment: 'Recruitment Agency',
        education: 'Education Consultancy',
        immigration: 'Immigration Services',
        travel: 'Travel Agency',
        consulting: 'General Consulting'
    };
    return types[type] || type;
};

const formatServiceName = (service) => {
    return service.split('_').map(word => word.charAt(0).toUpperCase() + word.slice(1)).join(' ');
};

const formatDate = (date) => {
    return new Date(date).toLocaleDateString('en-US', { 
        year: 'numeric', 
        month: 'short', 
        day: 'numeric' 
    });
};

const submitReview = () => {
    reviewForm.post(route('agencies.review', props.agency.id), {
        preserveScroll: true,
        onSuccess: () => {
            reviewForm.reset();
        }
    });
};
</script>
