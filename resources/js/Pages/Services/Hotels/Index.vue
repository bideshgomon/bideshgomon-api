<script setup>
import { ref, computed } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';

const props = defineProps({
    hotels: Object,
    cities: Array,
    featured: Array,
    filters: Object,
});

const search = ref({
    city: props.filters.city || '',
    star_rating: props.filters.star_rating || '',
    min_price: props.filters.min_price || '',
    max_price: props.filters.max_price || '',
    amenities: props.filters.amenities || [],
    sort: props.filters.sort || 'popular',
});

const amenitiesList = [
    { value: 'wifi', label: 'Wi-Fi' },
    { value: 'pool', label: 'Swimming Pool' },
    { value: 'gym', label: 'Fitness Center' },
    { value: 'restaurant', label: 'Restaurant' },
    { value: 'parking', label: 'Parking' },
    { value: 'spa', label: 'Spa' },
    { value: 'beach_access', label: 'Beach Access' },
];

const applyFilters = () => {
    router.get(route('hotels.index'), search.value, {
        preserveState: true,
        preserveScroll: true,
    });
};

const resetFilters = () => {
    search.value = {
        city: '',
        star_rating: '',
        min_price: '',
        max_price: '',
        amenities: [],
        sort: 'popular',
    };
    applyFilters();
};

const formatPrice = (price) => {
    return new Intl.NumberFormat('en-BD', { 
        style: 'currency', 
        currency: 'BDT',
        minimumFractionDigits: 0,
    }).format(price);
};
</script>

<template>
    <Head title="Hotel Booking" />

    <AuthenticatedLayout>
        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <!-- Header -->
                <div class="mb-8">
                    <h1 class="text-3xl font-bold text-gray-900">Hotel Booking</h1>
                    <p class="mt-2 text-gray-600">Find and book the perfect hotel for your stay</p>
                </div>

                <!-- Featured Hotels -->
                <div v-if="featured.length > 0" class="mb-12">
                    <h2 class="text-2xl font-semibold text-gray-900 mb-6">Featured Hotels</h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                        <Link 
                            v-for="hotel in featured" 
                            :key="hotel.id" 
                            :href="route('hotels.show', hotel.id)"
                            class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-xl transition group"
                        >
                            <div class="relative h-48">
                                <img 
                                    :src="hotel.featured_image || 'https://via.placeholder.com/400x300'" 
                                    :alt="hotel.name"
                                    class="w-full h-full object-cover group-hover:scale-105 transition duration-300"
                                />
                                <div class="absolute top-3 right-3 bg-indigo-600 text-white px-3 py-1 rounded-full text-sm font-semibold">
                                    ⭐ {{ hotel.star_rating }}-Star
                                </div>
                            </div>
                            <div class="p-4">
                                <h3 class="text-lg font-semibold text-gray-900 mb-1">{{ hotel.name }}</h3>
                                <p class="text-sm text-gray-600 mb-2">📍 {{ hotel.city }}, {{ hotel.country }}</p>
                                <div class="flex items-center justify-between">
                                    <span class="text-sm text-gray-500">From</span>
                                    <span class="text-xl font-bold text-indigo-600">{{ formatPrice(hotel.price_from) }}</span>
                                </div>
                                <p class="text-xs text-gray-500 mt-1">per night</p>
                            </div>
                        </Link>
                    </div>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
                    <!-- Filters Sidebar -->
                    <div class="lg:col-span-1">
                        <div class="bg-white rounded-lg shadow p-6 sticky top-6">
                            <div class="flex justify-between items-center mb-4">
                                <h3 class="text-lg font-semibold text-gray-900">Filters</h3>
                                <button @click="resetFilters" class="text-sm text-indigo-600 hover:text-indigo-800">
                                    Reset
                                </button>
                            </div>

                            <!-- City Filter -->
                            <div class="mb-6">
                                <label class="block text-sm font-medium text-gray-700 mb-2">City</label>
                                <select v-model="search.city" @change="applyFilters" class="w-full border-gray-300 rounded-lg">
                                    <option value="">All Cities</option>
                                    <option v-for="city in cities" :key="city" :value="city">{{ city }}</option>
                                </select>
                            </div>

                            <!-- Star Rating -->
                            <div class="mb-6">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Star Rating</label>
                                <select v-model="search.star_rating" @change="applyFilters" class="w-full border-gray-300 rounded-lg">
                                    <option value="">Any Rating</option>
                                    <option value="5">5 Stars</option>
                                    <option value="4">4 Stars</option>
                                    <option value="3">3 Stars</option>
                                </select>
                            </div>

                            <!-- Price Range -->
                            <div class="mb-6">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Price Range (BDT)</label>
                                <div class="grid grid-cols-2 gap-2">
                                    <input 
                                        v-model="search.min_price" 
                                        type="number" 
                                        placeholder="Min" 
                                        class="border-gray-300 rounded-lg text-sm"
                                    />
                                    <input 
                                        v-model="search.max_price" 
                                        type="number" 
                                        placeholder="Max" 
                                        class="border-gray-300 rounded-lg text-sm"
                                    />
                                </div>
                            </div>

                            <!-- Amenities -->
                            <div class="mb-6">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Amenities</label>
                                <div class="space-y-2">
                                    <label v-for="amenity in amenitiesList" :key="amenity.value" class="flex items-center">
                                        <input 
                                            v-model="search.amenities" 
                                            :value="amenity.value"
                                            type="checkbox" 
                                            class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500"
                                        />
                                        <span class="ml-2 text-sm text-gray-700">{{ amenity.label }}</span>
                                    </label>
                                </div>
                            </div>

                            <button @click="applyFilters" class="w-full bg-indigo-600 text-white py-2 px-4 rounded-lg hover:bg-indigo-700 transition">
                                Apply Filters
                            </button>
                        </div>
                    </div>

                    <!-- Hotels List -->
                    <div class="lg:col-span-3">
                        <!-- Sort Options -->
                        <div class="flex justify-between items-center mb-6">
                            <p class="text-gray-600">{{ hotels.total }} hotels found</p>
                            <select v-model="search.sort" @change="applyFilters" class="border-gray-300 rounded-lg">
                                <option value="popular">Most Popular</option>
                                <option value="price_low">Price: Low to High</option>
                                <option value="price_high">Price: High to Low</option>
                                <option value="rating">Highest Rated</option>
                            </select>
                        </div>

                        <!-- Hotels Grid -->
                        <div class="space-y-6">
                            <Link 
                                v-for="hotel in hotels.data" 
                                :key="hotel.id" 
                                :href="route('hotels.show', hotel.id)"
                                class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-xl transition flex flex-col sm:flex-row group"
                            >
                                <div class="sm:w-1/3 h-48 sm:h-auto relative overflow-hidden">
                                    <img 
                                        :src="hotel.featured_image || 'https://via.placeholder.com/400x300'" 
                                        :alt="hotel.name"
                                        class="w-full h-full object-cover group-hover:scale-105 transition duration-300"
                                    />
                                </div>
                                <div class="sm:w-2/3 p-6 flex flex-col justify-between">
                                    <div>
                                        <div class="flex justify-between items-start mb-2">
                                            <div>
                                                <h3 class="text-xl font-semibold text-gray-900 mb-1">{{ hotel.name }}</h3>
                                                <p class="text-sm text-gray-600">📍 {{ hotel.city }}, {{ hotel.country }}</p>
                                            </div>
                                            <div class="text-right">
                                                <div class="text-yellow-500 text-sm">{{ '⭐'.repeat(hotel.star_rating) }}</div>
                                                <div v-if="hotel.rating > 0" class="text-sm text-gray-600">{{ hotel.rating }}/5</div>
                                            </div>
                                        </div>
                                        <p v-if="hotel.description" class="text-sm text-gray-700 mb-3 line-clamp-2">{{ hotel.description }}</p>
                                        
                                        <!-- Amenities -->
                                        <div v-if="hotel.amenities && hotel.amenities.length > 0" class="flex flex-wrap gap-2 mb-3">
                                            <span v-for="amenity in hotel.amenities.slice(0, 5)" :key="amenity" 
                                                class="text-xs bg-gray-100 text-gray-600 px-2 py-1 rounded">
                                                {{ (amenity || '').replace('_', ' ') }}
                                            </span>
                                        </div>
                                    </div>
                                    
                                    <div class="flex items-end justify-between">
                                        <div>
                                            <span class="text-sm text-gray-500">From</span>
                                            <div class="text-2xl font-bold text-indigo-600">{{ formatPrice(hotel.price_from) }}</div>
                                            <p class="text-xs text-gray-500">per night</p>
                                        </div>
                                        <button class="bg-indigo-600 text-white px-6 py-2 rounded-lg hover:bg-indigo-700 transition">
                                            View Details
                                        </button>
                                    </div>
                                </div>
                            </Link>
                        </div>

                        <!-- Pagination -->
                        <div v-if="hotels.links && hotels.links.length > 3" class="mt-8 flex justify-center">
                            <nav class="flex space-x-2">
                                <Link 
                                    v-for="(link, index) in hotels.links" 
                                    :key="index"
                                    :href="link.url"
                                    :class="[
                                        'px-4 py-2 rounded-lg border transition',
                                        link.active 
                                            ? 'bg-indigo-600 text-white border-indigo-600' 
                                            : 'bg-white text-gray-700 border-gray-300 hover:bg-gray-50',
                                        !link.url && 'opacity-50 cursor-not-allowed'
                                    ]"
                                    :disabled="!link.url"
                                    v-html="link.label"
                                />
                            </nav>
                        </div>

                        <!-- No Results -->
                        <div v-if="hotels.data.length === 0" class="text-center py-12 bg-white rounded-lg">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                            </svg>
                            <h3 class="mt-4 text-lg font-semibold text-gray-900">No hotels found</h3>
                            <p class="mt-2 text-gray-600">Try adjusting your filters or search criteria</p>
                            <button @click="resetFilters" class="mt-4 bg-indigo-600 text-white px-6 py-2 rounded-lg hover:bg-indigo-700">
                                Reset Filters
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
