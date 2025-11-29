<template>
    <Head title="Find Agencies" />

    <AuthenticatedLayout>
        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <!-- Header -->
                <div class="mb-8">
                    <h1 class="text-3xl font-bold text-gray-900">Find Agencies</h1>
                    <p class="mt-2 text-gray-600">Browse and connect with verified agencies</p>
                </div>

                <!-- Filters -->
                <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Search</label>
                            <input 
                                v-model="filters.search"
                                @input="applyFilters"
                                type="text" 
                                placeholder="Agency name or location..."
                                class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                            />
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Country</label>
                            <select 
                                v-model="filters.country"
                                @change="applyFilters"
                                class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                            >
                                <option value="">All Countries</option>
                                <option v-for="country in countries" :key="country" :value="country">{{ country }}</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Business Type</label>
                            <select 
                                v-model="filters.business_type"
                                @change="applyFilters"
                                class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                            >
                                <option value="">All Types</option>
                                <option value="recruitment">Recruitment Agency</option>
                                <option value="education">Education Consultancy</option>
                                <option value="immigration">Immigration Services</option>
                                <option value="travel">Travel Agency</option>
                                <option value="consulting">General Consulting</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Minimum Rating</label>
                            <select 
                                v-model="filters.min_rating"
                                @change="applyFilters"
                                class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                            >
                                <option value="">Any Rating</option>
                                <option value="4">4+ Stars</option>
                                <option value="4.5">4.5+ Stars</option>
                            </select>
                        </div>
                    </div>

                    <div class="mt-4 flex items-center justify-between">
                        <div class="text-sm text-gray-600">
                            Showing {{ agencies.data.length }} of {{ agencies.total }} agencies
                        </div>
                        <div>
                            <label class="text-sm font-medium text-gray-700 mr-2">Sort by:</label>
                            <select 
                                v-model="filters.sort"
                                @change="applyFilters"
                                class="rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                            >
                                <option value="featured">Featured</option>
                                <option value="rating">Highest Rated</option>
                                <option value="reviews">Most Reviews</option>
                                <option value="clients">Most Clients</option>
                                <option value="newest">Newest</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Agencies Grid -->
                <div v-if="agencies.data.length > 0" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
                    <Link 
                        v-for="agency in agencies.data" 
                        :key="agency.id"
                        :href="route('agencies.show', agency.slug)"
                        class="bg-white rounded-lg shadow-sm hover:shadow-lg transition-shadow overflow-hidden border border-gray-200"
                    >
                        <!-- Featured Badge -->
                        <div v-if="agency.is_featured" class="bg-yellow-500 px-4 py-1 text-center">
                            <span class="text-xs font-semibold text-white">FEATURED</span>
                        </div>

                        <div class="p-6">
                            <!-- Logo and Basic Info -->
                            <div class="flex items-start space-x-4 mb-4">
                                <div class="flex-shrink-0">
                                    <img 
                                        v-if="agency.logo" 
                                        :src="`/storage/${agency.logo}`" 
                                        :alt="agency.name"
                                        class="h-16 w-16 object-cover rounded-lg"
                                    />
                                    <div v-else class="h-16 w-16 bg-indigo-100 rounded-lg flex items-center justify-center">
                                        <BuildingOfficeIcon class="h-8 w-8 text-indigo-600" />
                                    </div>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <h3 class="text-lg font-semibold text-gray-900 truncate flex items-center">
                                        {{ agency.name }}
                                        <CheckBadgeIcon v-if="agency.is_verified" class="h-5 w-5 text-blue-500 ml-1" />
                                    </h3>
                                    <p v-if="agency.company_name" class="text-sm text-gray-600 truncate">{{ agency.company_name }}</p>
                                    <div v-if="agency.agency_type" class="mt-1">
                                        <span :class="[
                                            'inline-flex items-center px-2 py-0.5 rounded text-xs font-medium',
                                            `bg-${agency.agency_type.color}-100 text-${agency.agency_type.color}-800`
                                        ]">
                                            {{ agency.agency_type.name }}
                                        </span>
                                    </div>
                                    <div v-else-if="agency.business_type" class="mt-1">
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-indigo-100 text-indigo-800">
                                            {{ formatBusinessType(agency.business_type) }}
                                        </span>
                                    </div>
                                </div>
                            </div>

                            <!-- Description -->
                            <p v-if="agency.description" class="text-sm text-gray-600 line-clamp-2 mb-4">
                                {{ agency.description }}
                            </p>

                            <!-- Stats -->
                            <div class="grid grid-cols-3 gap-3 mb-4 py-3 border-y border-gray-200">
                                <div class="text-center">
                                    <div class="text-lg font-semibold text-gray-900">{{ agency.total_clients || 0 }}</div>
                                    <div class="text-xs text-gray-500">Clients</div>
                                </div>
                                <div class="text-center">
                                    <div class="text-lg font-semibold text-gray-900">{{ agency.success_rate || 0 }}%</div>
                                    <div class="text-xs text-gray-500">Success</div>
                                </div>
                                <div class="text-center">
                                    <div class="flex items-center justify-center">
                                        <StarIcon class="h-5 w-5 text-yellow-400 fill-current" />
                                        <span class="ml-1 text-lg font-semibold text-gray-900">{{ agency.average_rating || 0 }}</span>
                                    </div>
                                    <div class="text-xs text-gray-500">{{ agency.total_reviews || 0 }} reviews</div>
                                </div>
                            </div>

                            <!-- Countries -->
                            <div v-if="agency.country_assignments && agency.country_assignments.length > 0" class="flex flex-wrap gap-1">
                                <span 
                                    v-for="assignment in agency.country_assignments.slice(0, 3)" 
                                    :key="assignment.id"
                                    class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-800"
                                >
                                    {{ assignment.country?.name }}
                                </span>
                                <span 
                                    v-if="agency.country_assignments.length > 3"
                                    class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-800"
                                >
                                    +{{ agency.country_assignments.length - 3 }}
                                </span>
                            </div>
                        </div>
                    </Link>
                </div>

                <div v-else class="text-center py-12 bg-white rounded-lg shadow-sm">
                    <BuildingOfficeIcon class="mx-auto h-12 w-12 text-gray-400" />
                    <h3 class="mt-2 text-sm font-medium text-gray-900">No agencies found</h3>
                    <p class="mt-1 text-sm text-gray-500">Try adjusting your filters</p>
                </div>

                <!-- Pagination -->
                <div v-if="agencies.data.length > 0" class="mt-6">
                    <div class="flex items-center justify-between">
                        <div class="text-sm text-gray-700">
                            Showing {{ agencies.from }} to {{ agencies.to }} of {{ agencies.total }} results
                        </div>
                        <div class="flex space-x-2">
                            <Link
                                v-for="link in agencies.links"
                                :key="link.label"
                                :href="link.url"
                                :class="[
                                    'px-3 py-2 text-sm font-medium rounded-md',
                                    link.active 
                                        ? 'bg-indigo-600 text-white' 
                                        : link.url 
                                            ? 'bg-white text-gray-700 hover:bg-gray-50 border border-gray-300' 
                                            : 'bg-gray-100 text-gray-400 cursor-not-allowed'
                                ]"
                                v-html="link.label"
                            />
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import { ref, reactive } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { BuildingOfficeIcon, CheckBadgeIcon, StarIcon } from '@heroicons/vue/24/solid';

const props = defineProps({
    agencies: Object,
    countries: Array,
    filters: Object,
});

const filters = reactive({
    search: props.filters.search || '',
    country: props.filters.country || '',
    business_type: props.filters.business_type || '',
    min_rating: props.filters.min_rating || '',
    sort: props.filters.sort || 'featured',
});

const applyFilters = () => {
    router.get(route('agencies.index'), filters, {
        preserveScroll: true,
        preserveState: true,
    });
};

const formatBusinessType = (type) => {
    const types = {
        recruitment: 'Recruitment',
        education: 'Education',
        immigration: 'Immigration',
        travel: 'Travel',
        consulting: 'Consulting'
    };
    return types[type] || type;
};
</script>
