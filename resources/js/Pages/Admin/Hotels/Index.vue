<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { ref } from 'vue';

const props = defineProps({
    hotels: Object,
    stats: Object,
    filters: Object,
});

const search = ref(props.filters.search || '');
const city = ref(props.filters.city || '');
const status = ref(props.filters.status || '');

const applyFilters = () => {
    router.get(route('admin.hotels.index'), {
        search: search.value,
        city: city.value,
        status: status.value,
    }, {
        preserveState: true,
        preserveScroll: true,
    });
};

const deleteHotel = (id) => {
    if (confirm('Are you sure you want to delete this hotel?')) {
        router.delete(route('admin.hotels.destroy', id));
    }
};

const toggleStatus = (id) => {
    router.post(route('admin.hotels.toggle-status', id));
};
</script>

<template>
    <Head title="Hotel Management - Admin" />

    <AdminLayout>
        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <!-- Header -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                    <div class="p-6 border-b border-gray-200">
                        <div class="flex justify-between items-center">
                            <div>
                                <h2 class="text-2xl font-bold text-gray-800">Hotel Management</h2>
                                <p class="text-gray-600 mt-1">Manage hotels and room inventory</p>
                            </div>
                            <Link
                                :href="route('admin.hotels.create')"
                                class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg"
                            >
                                + Add Hotel
                            </Link>
                        </div>
                    </div>

                    <!-- Stats -->
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 p-6">
                        <div class="bg-blue-50 p-4 rounded-lg">
                            <div class="text-blue-600 text-sm font-medium">Total Hotels</div>
                            <div class="text-2xl font-bold text-blue-900">{{ stats.total }}</div>
                        </div>
                        <div class="bg-green-50 p-4 rounded-lg">
                            <div class="text-green-600 text-sm font-medium">Active</div>
                            <div class="text-2xl font-bold text-green-900">{{ stats.active }}</div>
                        </div>
                        <div class="bg-yellow-50 p-4 rounded-lg">
                            <div class="text-yellow-600 text-sm font-medium">Total Rooms</div>
                            <div class="text-2xl font-bold text-yellow-900">{{ stats.total_rooms }}</div>
                        </div>
                        <div class="bg-purple-50 p-4 rounded-lg">
                            <div class="text-purple-600 text-sm font-medium">Avg Price</div>
                            <div class="text-2xl font-bold text-purple-900">৳{{ stats.avg_price }}</div>
                        </div>
                    </div>
                </div>

                <!-- Filters -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                    <div class="p-6">
                        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                            <input
                                v-model="search"
                                @keyup.enter="applyFilters"
                                type="text"
                                placeholder="Search hotels..."
                                class="rounded-lg border-gray-300"
                            />
                            <input
                                v-model="city"
                                @keyup.enter="applyFilters"
                                type="text"
                                placeholder="City"
                                class="rounded-lg border-gray-300"
                            />
                            <select
                                v-model="status"
                                @change="applyFilters"
                                class="rounded-lg border-gray-300"
                            >
                                <option value="">All Status</option>
                                <option value="active">Active</option>
                                <option value="inactive">Inactive</option>
                            </select>
                            <button
                                @click="applyFilters"
                                class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg"
                            >
                                Apply Filters
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Hotels List -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div v-if="hotels.data.length === 0" class="text-center py-12 text-gray-500">
                            No hotels found
                        </div>

                        <div v-else class="space-y-4">
                            <div
                                v-for="hotel in hotels.data"
                                :key="hotel.id"
                                class="border border-gray-200 rounded-lg p-4 hover:shadow-md transition"
                            >
                                <div class="flex justify-between items-start">
                                    <div class="flex-1">
                                        <div class="flex items-center gap-3">
                                            <h3 class="text-lg font-semibold text-gray-800">{{ hotel.name }}</h3>
                                            <span
                                                :class="hotel.is_active ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800'"
                                                class="px-2 py-1 rounded text-xs"
                                            >
                                                {{ hotel.is_active ? 'Active' : 'Inactive' }}
                                            </span>
                                            <span class="text-yellow-500">
                                                ★ {{ hotel.rating }} ({{ hotel.total_reviews }} reviews)
                                            </span>
                                        </div>
                                        <p class="text-gray-600 mt-1">{{ hotel.city }}, {{ hotel.country }}</p>
                                        <p class="text-gray-500 text-sm mt-1">{{ hotel.address }}</p>
                                        <div class="flex gap-4 mt-3 text-sm">
                                            <span class="text-gray-600">🛏️ {{ hotel.rooms_count }} rooms</span>
                                            <span class="text-gray-600">💰 From ৳{{ hotel.price_per_night }}/night</span>
                                            <span class="text-gray-600">⭐ {{ hotel.star_rating }} Star</span>
                                        </div>
                                    </div>
                                    <div class="flex gap-2">
                                        <Link
                                            :href="route('admin.hotels.rooms', hotel.id)"
                                            class="text-blue-600 hover:text-blue-800 px-3 py-1 rounded border border-blue-600"
                                        >
                                            Rooms
                                        </Link>
                                        <Link
                                            :href="route('admin.hotels.edit', hotel.id)"
                                            class="text-indigo-600 hover:text-indigo-800 px-3 py-1 rounded border border-indigo-600"
                                        >
                                            Edit
                                        </Link>
                                        <button
                                            @click="toggleStatus(hotel.id)"
                                            class="text-yellow-600 hover:text-yellow-800 px-3 py-1 rounded border border-yellow-600"
                                        >
                                            {{ hotel.is_active ? 'Deactivate' : 'Activate' }}
                                        </button>
                                        <button
                                            @click="deleteHotel(hotel.id)"
                                            class="text-red-600 hover:text-red-800 px-3 py-1 rounded border border-red-600"
                                        >
                                            Delete
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Pagination -->
                        <div v-if="hotels.links.length > 3" class="mt-6 flex justify-center gap-2">
                            <Link
                                v-for="(link, index) in hotels.links"
                                :key="index"
                                :href="link.url"
                                :class="link.active ? 'bg-indigo-600 text-white' : 'bg-white text-gray-700 hover:bg-gray-100'"
                                class="px-3 py-2 rounded border"
                                v-html="link.label"
                            />
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>
