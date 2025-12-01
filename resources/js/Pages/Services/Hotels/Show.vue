<script setup>
import { ref } from 'vue';
import { Head, Link, router, useForm } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';

const props = defineProps({
    hotel: Object,
    similarHotels: Array,
    checkIn: String,
    checkOut: String,
    rooms: Number,
});

const bookingForm = useForm({
    check_in: props.checkIn || '',
    check_out: props.checkOut || '',
    rooms: props.rooms || 1,
    adults: 2,
    children: 0,
});

const selectedRoom = ref(null);

const formatPrice = (price) => {
    return new Intl.NumberFormat('en-BD', { 
        style: 'currency', 
        currency: 'BDT',
        minimumFractionDigits: 0,
    }).format(price);
};

const bookRoom = (room) => {
    if (!bookingForm.check_in || !bookingForm.check_out) {
        alert('Please select check-in and check-out dates');
        return;
    }
    
    router.get(route('hotels.book', { hotel: props.hotel.id, room: room.id }), bookingForm.data());
};
</script>

<template>
    <Head :title="hotel.name" />

    <AuthenticatedLayout>
        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <!-- Hotel Header -->
                <div class="bg-white rounded-lg shadow-md overflow-hidden mb-8">
                    <div class="relative h-96">
                        <img 
                            :src="hotel.featured_image || 'https://via.placeholder.com/1200x400'" 
                            :alt="hotel.name"
                            class="w-full h-full object-cover"
                        />
                    </div>
                    
                    <div class="p-8">
                        <div class="flex justify-between items-start mb-4">
                            <div>
                                <h1 class="text-3xl font-bold text-gray-900 mb-2">{{ hotel.name }}</h1>
                                <p class="text-lg text-gray-600">📍 {{ hotel.address }}, {{ hotel.city }}, {{ hotel.country }}</p>
                                <div class="flex items-center mt-2 space-x-4">
                                    <div class="text-yellow-500">{{ '⭐'.repeat(hotel.star_rating) }} {{ hotel.star_rating }}-Star</div>
                                    <div v-if="hotel.rating > 0" class="text-gray-600">
                                        Rating: {{ hotel.rating }}/5 ({{ hotel.total_reviews }} reviews)
                                    </div>
                                </div>
                            </div>
                            <div class="text-right">
                                <span class="text-sm text-gray-500">From</span>
                                <div class="text-3xl font-bold text-indigo-600">{{ formatPrice(hotel.price_from) }}</div>
                                <p class="text-sm text-gray-500">per night</p>
                            </div>
                        </div>

                        <div class="prose max-w-none mb-6">
                            <p class="text-gray-700">{{ hotel.description }}</p>
                        </div>

                        <!-- Amenities -->
                        <div v-if="hotel.amenities && hotel.amenities.length > 0" class="mb-6">
                            <h3 class="text-lg font-semibold text-gray-900 mb-3">Amenities</h3>
                            <div class="flex flex-wrap gap-2">
                                <span v-for="amenity in hotel.amenities" :key="amenity" 
                                    class="bg-indigo-50 text-indigo-700 px-3 py-1 rounded-full text-sm">
                                    {{ (amenity || '').replace('_', ' ').replace(/\b\w/g, l => l.toUpperCase()) }}
                                </span>
                            </div>
                        </div>

                        <!-- Hotel Info -->
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 p-4 bg-gray-50 rounded-lg">
                            <div>
                                <span class="text-sm text-gray-600">Check-in</span>
                                <p class="font-semibold">{{ hotel.check_in_time }}</p>
                            </div>
                            <div>
                                <span class="text-sm text-gray-600">Check-out</span>
                                <p class="font-semibold">{{ hotel.check_out_time }}</p>
                            </div>
                            <div>
                                <span class="text-sm text-gray-600">Property Type</span>
                                <p class="font-semibold capitalize">{{ hotel.property_type }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Booking Form -->
                <div class="bg-white rounded-lg shadow-md p-6 mb-8">
                    <h2 class="text-2xl font-semibold text-gray-900 mb-4">Check Availability</h2>
                    <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Check-in</label>
                            <input v-model="bookingForm.check_in" type="date" :min="new Date().toISOString().split('T')[0]" 
                                class="w-full border-gray-300 rounded-lg" />
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Check-out</label>
                            <input v-model="bookingForm.check_out" type="date" :min="bookingForm.check_in" 
                                class="w-full border-gray-300 rounded-lg" />
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Rooms</label>
                            <input v-model="bookingForm.rooms" type="number" min="1" 
                                class="w-full border-gray-300 rounded-lg" />
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Adults</label>
                            <input v-model="bookingForm.adults" type="number" min="1" 
                                class="w-full border-gray-300 rounded-lg" />
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Children</label>
                            <input v-model="bookingForm.children" type="number" min="0" 
                                class="w-full border-gray-300 rounded-lg" />
                        </div>
                    </div>
                </div>

                <!-- Available Rooms -->
                <div class="bg-white rounded-lg shadow-md p-6 mb-8">
                    <h2 class="text-2xl font-semibold text-gray-900 mb-6">Available Rooms</h2>
                    <div class="space-y-6">
                        <div v-for="room in hotel.rooms" :key="room.id" 
                            class="border border-gray-200 rounded-lg p-6 hover:border-indigo-500 transition">
                            <div class="flex justify-between items-start">
                                <div class="flex-1">
                                    <h3 class="text-xl font-semibold text-gray-900 mb-2">{{ room.room_type }}</h3>
                                    <p v-if="room.description" class="text-gray-600 mb-3">{{ room.description }}</p>
                                    
                                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-sm text-gray-600 mb-4">
                                        <div>👥 Max: {{ room.max_adults }} Adults</div>
                                        <div v-if="room.max_children">👶 {{ room.max_children }} Children</div>
                                        <div v-if="room.size_sqm">📐 {{ room.size_sqm }} m²</div>
                                        <div>🛏️ {{ room.bed_count }} {{ room.bed_type }} Bed</div>
                                    </div>

                                    <div v-if="room.amenities && room.amenities.length" class="flex flex-wrap gap-2">
                                        <span v-for="amenity in room.amenities.slice(0, 6)" :key="amenity" 
                                            class="text-xs bg-gray-100 text-gray-600 px-2 py-1 rounded">
                                            {{ (amenity || '').replace('_', ' ') }}
                                        </span>
                                    </div>
                                </div>
                                
                                <div class="ml-6 text-right">
                                    <div class="text-2xl font-bold text-indigo-600 mb-2">
                                        {{ formatPrice(room.price_per_night) }}
                                    </div>
                                    <p class="text-sm text-gray-500 mb-4">per night</p>
                                    <button @click="bookRoom(room)" 
                                        class="bg-indigo-600 text-white px-6 py-2 rounded-lg hover:bg-indigo-700 transition w-full">
                                        Book Now
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Similar Hotels -->
                <div v-if="similarHotels.length > 0" class="mb-8">
                    <h2 class="text-2xl font-semibold text-gray-900 mb-6">Similar Hotels in {{ hotel.city }}</h2>
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                        <Link 
                            v-for="similar in similarHotels" 
                            :key="similar.id" 
                            :href="route('hotels.show', similar.id)"
                            class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-xl transition"
                        >
                            <img :src="similar.featured_image" :alt="similar.name" class="w-full h-32 object-cover" />
                            <div class="p-4">
                                <h3 class="font-semibold text-gray-900 mb-1">{{ similar.name }}</h3>
                                <div class="text-yellow-500 text-sm mb-2">{{ '⭐'.repeat(similar.star_rating) }}</div>
                                <div class="text-lg font-bold text-indigo-600">{{ formatPrice(similar.price_from) }}</div>
                            </div>
                        </Link>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
