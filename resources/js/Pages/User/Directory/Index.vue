<template>
    <Head title="Directory - Find Embassies, Airlines & Training Centers" />

    <AuthenticatedLayout>
        <!-- Hero Section -->
        <div class="bg-gradient-to-r from-blue-600 to-blue-800 text-white">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
                <h1 class="text-4xl font-bold mb-4">Directory</h1>
                <p class="text-xl text-blue-100 mb-8">Find embassies, airlines, training centers, and more</p>
                
                <!-- Search Bar -->
                <div class="max-w-3xl">
                    <div class="relative">
                        <MagnifyingGlassIcon class="absolute left-4 top-1/2 -translate-y-1/2 h-6 w-6 text-gray-400" />
                        <input
                            v-model="searchQuery"
                            @input="debouncedSearch"
                            type="text"
                            placeholder="Search directories..."
                            class="w-full pl-12 pr-4 py-4 rounded-lg text-gray-900 placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500"
                        />
                    </div>
                </div>
            </div>
        </div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <!-- Category Filter Pills -->
            <div class="mb-8">
                <div class="flex items-center gap-3 overflow-x-auto pb-2">
                    <button
                        @click="filterByCategory(null)"
                        :class="[
                            'px-5 py-2 rounded-full text-sm font-medium whitespace-nowrap transition-all',
                            !selectedCategory 
                                ? 'bg-blue-600 text-white' 
                                : 'bg-gray-100 text-gray-700 hover:bg-gray-200'
                        ]"
                    >
                        All Categories
                    </button>
                    <button
                        v-for="category in categories"
                        :key="category.id"
                        @click="filterByCategory(category.slug)"
                        :class="[
                            'px-5 py-2 rounded-full text-sm font-medium whitespace-nowrap transition-all flex items-center gap-2',
                            selectedCategory === category.slug 
                                ? 'text-white' 
                                : 'bg-gray-100 text-gray-700 hover:bg-gray-200'
                        ]"
                        :style="selectedCategory === category.slug ? { backgroundColor: category.color } : {}"
                    >
                        <span v-if="category.icon" v-html="category.icon" class="w-4 h-4"></span>
                        {{ category.name }}
                    </button>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">
                <!-- Sidebar Filters -->
                <div class="lg:col-span-1">
                    <div class="bg-white rounded-lg shadow-sm p-6 sticky top-4">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Filters</h3>
                        
                        <!-- Country Filter -->
                        <div class="mb-6">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Country</label>
                            <select
                                v-model="form.country"
                                @change="applyFilters"
                                class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500"
                            >
                                <option value="">All Countries</option>
                                <option value="1">Bangladesh</option>
                                <!-- Add more countries as needed -->
                            </select>
                        </div>

                        <!-- City Filter -->
                        <div class="mb-6">
                            <label class="block text-sm font-medium text-gray-700 mb-2">City</label>
                            <input
                                v-model="form.city"
                                @input="debouncedFilter"
                                type="text"
                                placeholder="Enter city"
                                class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500"
                            />
                        </div>

                        <!-- Sort -->
                        <div class="mb-6">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Sort By</label>
                            <select
                                v-model="form.sort_by"
                                @change="applyFilters"
                                class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500"
                            >
                                <option value="name">Name</option>
                                <option value="views">Most Viewed</option>
                            </select>
                        </div>

                        <!-- Clear Filters -->
                        <button
                            @click="clearFilters"
                            class="w-full px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors"
                        >
                            Clear Filters
                        </button>
                    </div>
                </div>

                <!-- Directory Grid -->
                <div class="lg:col-span-3">
                    <!-- Results Count -->
                    <div class="mb-6 flex items-center justify-between">
                        <p class="text-sm text-gray-600">
                            Showing {{ directories.data.length }} of {{ directories.total }} directories
                        </p>
                    </div>

                    <!-- Directory Cards -->
                    <div v-if="directories.data.length > 0" class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
                        <Link
                            v-for="directory in directories.data"
                            :key="directory.id"
                            :href="route('directory.show', directory.slug)"
                            class="bg-white rounded-lg shadow-sm hover:shadow-lg transition-all overflow-hidden group"
                        >
                            <!-- Image -->
                            <div class="aspect-video bg-gray-200 overflow-hidden">
                                <img
                                    v-if="directory.featured_image"
                                    :src="directory.featured_image"
                                    :alt="directory.name"
                                    class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300"
                                />
                                <div v-else class="w-full h-full flex items-center justify-center bg-gradient-to-br from-blue-500 to-blue-600">
                                    <BuildingOffice2Icon class="h-16 w-16 text-white/50" />
                                </div>
                            </div>

                            <!-- Content -->
                            <div class="p-5">
                                <!-- Category Badge -->
                                <span
                                    v-if="directory.category"
                                    class="inline-block px-3 py-1 rounded-full text-xs font-medium mb-3"
                                    :style="{ backgroundColor: directory.category.color + '20', color: directory.category.color }"
                                >
                                    {{ directory.category.name }}
                                </span>

                                <!-- Name -->
                                <h3 class="text-lg font-semibold text-gray-900 mb-2 group-hover:text-blue-600 transition-colors">
                                    {{ directory.name }}
                                </h3>

                                <!-- Address -->
                                <div v-if="directory.address" class="flex items-start gap-2 text-sm text-gray-600 mb-2">
                                    <MapPinIcon class="h-5 w-5 flex-shrink-0 mt-0.5" />
                                    <span class="line-clamp-2">{{ directory.address }}, {{ directory.city }}</span>
                                </div>

                                <!-- Phone -->
                                <div v-if="directory.phone" class="flex items-center gap-2 text-sm text-gray-600 mb-3">
                                    <PhoneIcon class="h-5 w-5 flex-shrink-0" />
                                    <span>{{ directory.phone }}</span>
                                </div>

                                <!-- Stats -->
                                <div class="flex items-center justify-between pt-3 border-t border-gray-100">
                                    <div class="flex items-center gap-1 text-xs text-gray-500">
                                        <EyeIcon class="h-4 w-4" />
                                        <span>{{ directory.view_count || 0 }} views</span>
                                    </div>
                                    <span class="text-blue-600 text-sm font-medium group-hover:underline">
                                        View Details →
                                    </span>
                                </div>
                            </div>
                        </Link>
                    </div>

                    <!-- Empty State -->
                    <div v-else class="text-center py-12">
                        <BuildingOffice2Icon class="mx-auto h-16 w-16 text-gray-400 mb-4" />
                        <h3 class="text-lg font-medium text-gray-900 mb-2">No directories found</h3>
                        <p class="text-gray-600 mb-4">Try adjusting your search or filters</p>
                        <button
                            @click="clearFilters"
                            class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors"
                        >
                            Clear All Filters
                        </button>
                    </div>

                    <!-- Pagination -->
                    <div v-if="directories.data.length > 0" class="mt-8">
                        <Pagination :links="directories.links" />
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import { ref, computed } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import Pagination from '@/Components/Pagination.vue';
import {
    MagnifyingGlassIcon,
    BuildingOffice2Icon,
    MapPinIcon,
    PhoneIcon,
    EyeIcon,
} from '@heroicons/vue/24/outline';

const props = defineProps({
    directories: Object,
    categories: Array,
    filters: Object,
});

const searchQuery = ref(props.filters.search || '');
const selectedCategory = ref(props.filters.category || null);

const form = ref({
    search: props.filters.search || '',
    category: props.filters.category || '',
    country: props.filters.country || '',
    city: props.filters.city || '',
    sort_by: props.filters.sort_by || 'name',
    sort_order: props.filters.sort_order || 'asc',
});

let searchTimeout;
const debouncedSearch = () => {
    clearTimeout(searchTimeout);
    searchTimeout = setTimeout(() => {
        form.value.search = searchQuery.value;
        applyFilters();
    }, 500);
};

let filterTimeout;
const debouncedFilter = () => {
    clearTimeout(filterTimeout);
    filterTimeout = setTimeout(() => {
        applyFilters();
    }, 500);
};

const filterByCategory = (slug) => {
    selectedCategory.value = slug;
    form.value.category = slug || '';
    applyFilters();
};

const applyFilters = () => {
    router.get(route('directory.index'), form.value, {
        preserveState: true,
        preserveScroll: true,
    });
};

const clearFilters = () => {
    searchQuery.value = '';
    selectedCategory.value = null;
    form.value = {
        search: '',
        category: '',
        country: '',
        city: '',
        sort_by: 'name',
        sort_order: 'asc',
    };
    applyFilters();
};
</script>
