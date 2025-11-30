<template>
    <AdminLayout>
        <Head title="Admin Sitemap" />

        <div class="py-6">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <!-- Header -->
                <div class="mb-8">
                    <div class="flex items-center justify-between">
                        <div>
                            <h1 class="text-3xl font-bold text-gray-900">Admin Panel Sitemap</h1>
                            <p class="mt-2 text-sm text-gray-600">
                                Complete list of all admin routes - Test and verify all links work properly
                            </p>
                        </div>
                        <div class="flex gap-2">
                            <button
                                @click="exportToJson"
                                class="inline-flex items-center px-4 py-2 bg-gray-600 text-white rounded-md hover:bg-gray-700 transition"
                            >
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                                Export JSON
                            </button>
                            <button
                                @click="collapseAll"
                                class="inline-flex items-center px-4 py-2 bg-gray-600 text-white rounded-md hover:bg-gray-700 transition"
                            >
                                Collapse All
                            </button>
                            <button
                                @click="expandAll"
                                class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 transition"
                            >
                                Expand All
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Statistics -->
                <div class="grid grid-cols-1 md:grid-cols-5 gap-6 mb-8">
                    <div class="bg-white rounded-lg shadow p-6 hover:shadow-lg transition">
                        <div class="flex items-center">
                            <div class="p-3 bg-indigo-100 rounded-full">
                                <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                                </svg>
                            </div>
                            <div class="ml-4">
                                <div class="text-sm font-medium text-gray-500">Total Routes</div>
                                <div class="mt-1 text-3xl font-bold text-indigo-600">{{ stats.total_routes }}</div>
                            </div>
                        </div>
                    </div>
                    <div class="bg-white rounded-lg shadow p-6 hover:shadow-lg transition">
                        <div class="flex items-center">
                            <div class="p-3 bg-green-100 rounded-full">
                                <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                                </svg>
                            </div>
                            <div class="ml-4">
                                <div class="text-sm font-medium text-gray-500">Categories</div>
                                <div class="mt-1 text-3xl font-bold text-green-600">{{ stats.categories }}</div>
                            </div>
                        </div>
                    </div>
                    <div class="bg-white rounded-lg shadow p-6 hover:shadow-lg transition">
                        <div class="flex items-center">
                            <div class="p-3 bg-blue-100 rounded-full">
                                <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                                </svg>
                            </div>
                            <div class="ml-4">
                                <div class="text-sm font-medium text-gray-500">Static Routes</div>
                                <div class="mt-1 text-3xl font-bold text-blue-600">{{ stats.without_params }}</div>
                            </div>
                        </div>
                    </div>
                    <div class="bg-white rounded-lg shadow p-6 hover:shadow-lg transition">
                        <div class="flex items-center">
                            <div class="p-3 bg-purple-100 rounded-full">
                                <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17h.01" />
                                </svg>
                            </div>
                            <div class="ml-4">
                                <div class="text-sm font-medium text-gray-500">Dynamic Routes</div>
                                <div class="mt-1 text-3xl font-bold text-purple-600">{{ stats.with_params }}</div>
                            </div>
                        </div>
                    </div>
                    <div class="bg-white rounded-lg shadow p-6 hover:shadow-lg transition">
                        <div class="flex items-center">
                            <div class="p-3 bg-yellow-100 rounded-full">
                                <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                            <div class="ml-4">
                                <div class="text-sm font-medium text-gray-500">Tested</div>
                                <div class="mt-1 text-3xl font-bold text-yellow-600">{{ testedRoutes.size }}</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Search and Filters -->
                <div class="bg-white rounded-lg shadow p-6 mb-8">
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Search Routes</label>
                            <div class="relative">
                                <input
                                    v-model="searchQuery"
                                    type="text"
                                    placeholder="Search by URI, name, action, or category..."
                                    class="w-full pl-10 rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                />
                                <svg class="absolute left-3 top-3 w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                </svg>
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">HTTP Method</label>
                            <select
                                v-model="methodFilter"
                                class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                            >
                                <option value="">All Methods</option>
                                <option value="GET">GET</option>
                                <option value="POST">POST</option>
                                <option value="PUT">PUT</option>
                                <option value="DELETE">DELETE</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Route Type</label>
                            <select
                                v-model="routeTypeFilter"
                                class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                            >
                                <option value="">All Types</option>
                                <option value="static">Static Only</option>
                                <option value="dynamic">Dynamic Only</option>
                                <option value="testable">Testable Only</option>
                                <option value="tested">Tested Only</option>
                                <option value="untested">Untested Only</option>
                            </select>
                        </div>
                    </div>
                    <div class="mt-4 flex items-center justify-between">
                        <div class="text-sm text-gray-600">
                            Showing <span class="font-semibold">{{ totalFilteredRoutes }}</span> of <span class="font-semibold">{{ stats.total_routes }}</span> routes
                        </div>
                        <div class="flex gap-2">
                            <button
                                @click="clearFilters"
                                class="px-3 py-1 text-sm bg-gray-100 text-gray-700 rounded-md hover:bg-gray-200 transition"
                            >
                                Clear Filters
                            </button>
                            <button
                                @click="testFilteredLinks"
                                :disabled="testableFilteredCount === 0"
                                :class="[
                                    'px-4 py-2 rounded-md transition font-medium',
                                    testableFilteredCount > 0
                                        ? 'bg-green-600 text-white hover:bg-green-700'
                                        : 'bg-gray-300 text-gray-500 cursor-not-allowed'
                                ]"
                            >
                                <span v-if="isTesting" class="inline-flex items-center">
                                    <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                    </svg>
                                    Testing...
                                </span>
                                <span v-else>
                                    Test {{ testableFilteredCount }} Links
                                </span>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Routes by Category -->
                <div class="space-y-6">
                    <div
                        v-for="(categoryRoutes, category) in filteredRoutes"
                        :key="category"
                        class="bg-white rounded-lg shadow overflow-hidden"
                    >
                        <!-- Category Header -->
                        <div 
                            @click="toggleCategory(category)"
                            class="bg-gray-50 px-6 py-4 border-b border-gray-200 cursor-pointer hover:bg-gray-100 transition"
                        >
                            <div class="flex items-center justify-between">
                                <div class="flex items-center flex-1">
                                    <span class="text-xl mr-3 text-indigo-600">
                                        {{ expandedCategories.includes(category) ? '▼' : '▶' }}
                                    </span>
                                    <div class="flex-1">
                                        <div class="flex items-center gap-3">
                                            <h2 class="text-lg font-semibold text-gray-900">{{ category }}</h2>
                                            <span class="px-2 py-1 bg-gray-200 text-gray-700 rounded-full text-xs font-medium">
                                                {{ categoryRoutes.length }}
                                            </span>
                                        </div>
                                        <div class="mt-1 flex items-center gap-4 text-sm text-gray-500">
                                            <span class="flex items-center">
                                                <svg class="w-4 h-4 mr-1 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                                </svg>
                                                {{ (categoryRoutes || []).filter(r => !r?.has_params && r.methods.includes('GET')).length }} testable
                                            </span>
                                            <span class="flex items-center">
                                                <svg class="w-4 h-4 mr-1 text-yellow-500" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd" />
                                                </svg>
                                                {{ categoryRoutes.filter(r => testedRoutes.has(r.uri)).length }} tested
                                            </span>
                                            <span class="flex items-center">
                                                <svg class="w-4 h-4 mr-1 text-purple-500" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M11.3 1.046A1 1 0 0112 2v5h4a1 1 0 01.82 1.573l-7 10A1 1 0 018 18v-5H4a1 1 0 01-.82-1.573l7-10a1 1 0 011.12-.38z" clip-rule="evenodd" />
                                                </svg>
                                                {{ (categoryRoutes || []).filter(r => r?.has_params).length }} dynamic
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                <button
                                    @click.stop="testCategoryLinks(categoryRoutes)"
                                    v-if="(categoryRoutes || []).filter(r => !r?.has_params && r.methods.includes('GET')).length > 0"
                                    class="ml-4 px-3 py-1 bg-indigo-600 text-white text-sm rounded-md hover:bg-indigo-700 transition"
                                >
                                    Test Category
                                </button>
                            </div>
                        </div>

                        <!-- Routes List -->
                        <div v-if="expandedCategories.includes(category)" class="divide-y divide-gray-200">
                            <div
                                v-for="route in categoryRoutes"
                                :key="route.uri"
                                :class="[
                                    'px-6 py-4 transition',
                                    testedRoutes.has(route.uri) ? 'bg-green-50 hover:bg-green-100' : 'hover:bg-gray-50'
                                ]"
                            >
                                <div class="flex items-start justify-between">
                                    <div class="flex-1">
                                        <div class="flex items-center gap-3 mb-2 flex-wrap">
                                            <!-- HTTP Methods -->
                                            <span :class="[
                                                'px-2 py-1 text-xs font-medium rounded',
                                                route.methods.includes('GET') ? 'bg-blue-100 text-blue-800' :
                                                route.methods.includes('POST') ? 'bg-green-100 text-green-800' :
                                                route.methods.includes('PUT') ? 'bg-yellow-100 text-yellow-800' :
                                                'bg-red-100 text-red-800'
                                            ]">
                                                {{ route.methods }}
                                            </span>
                                            
                                            <!-- Route Name -->
                                            <span v-if="route.name" class="text-sm text-gray-600 font-mono">
                                                {{ route.name }}
                                            </span>
                                            
                                            <!-- Dynamic Route Badge -->
                                            <span v-if="route.has_params" class="px-2 py-1 bg-purple-100 text-purple-800 text-xs font-medium rounded flex items-center">
                                                <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M11.3 1.046A1 1 0 0112 2v5h4a1 1 0 01.82 1.573l-7 10A1 1 0 018 18v-5H4a1 1 0 01-.82-1.573l7-10a1 1 0 011.12-.38z" clip-rule="evenodd" />
                                                </svg>
                                                Dynamic
                                            </span>

                                            <!-- Tested Badge -->
                                            <span v-if="testedRoutes.has(route.uri)" class="px-2 py-1 bg-green-100 text-green-800 text-xs font-medium rounded flex items-center">
                                                <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                                </svg>
                                                Tested
                                            </span>
                                        </div>

                                        <!-- URI -->
                                        <div class="flex items-center gap-2 mb-2">
                                            <code class="text-sm font-mono text-gray-900 bg-gray-100 px-2 py-1 rounded">
                                                {{ route.uri }}
                                            </code>
                                            <button
                                                @click="copyToClipboard(route.uri)"
                                                class="p-1 text-gray-400 hover:text-gray-600 transition"
                                                title="Copy URI"
                                            >
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z" />
                                                </svg>
                                            </button>
                                        </div>

                                        <!-- Action -->
                                        <div class="flex items-center gap-2 text-xs text-gray-500">
                                            <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M12.316 3.051a1 1 0 01.633 1.265l-4 12a1 1 0 11-1.898-.632l4-12a1 1 0 011.265-.633zM5.707 6.293a1 1 0 010 1.414L3.414 10l2.293 2.293a1 1 0 11-1.414 1.414l-3-3a1 1 0 010-1.414l3-3a1 1 0 011.414 0zm8.586 0a1 1 0 011.414 0l3 3a1 1 0 010 1.414l-3 3a1 1 0 11-1.414-1.414L16.586 10l-2.293-2.293a1 1 0 010-1.414z" clip-rule="evenodd" />
                                            </svg>
                                            {{ route.action }}
                                        </div>
                                    </div>

                                    <!-- Test Button -->
                                    <div class="ml-4 flex gap-2">
                                        <a
                                            v-if="!route.has_params && route.methods.includes('GET')"
                                            :href="route.url"
                                            target="_blank"
                                            @click="markAsTested(route.uri)"
                                            class="inline-flex items-center px-3 py-2 border border-indigo-300 shadow-sm text-sm leading-4 font-medium rounded-md text-indigo-700 bg-white hover:bg-indigo-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition"
                                        >
                                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                                            </svg>
                                            Test Link
                                        </a>
                                        <button
                                            v-if="testedRoutes.has(route.uri)"
                                            @click="unmarkAsTested(route.uri)"
                                            class="inline-flex items-center px-2 py-2 text-sm text-gray-500 hover:text-gray-700 transition"
                                            title="Unmark as tested"
                                        >
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                            </svg>
                                        </button>
                                        <span v-if="route.has_params || !route.methods.includes('GET')" class="inline-flex items-center px-3 py-2 text-sm text-gray-400 italic">
                                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                                            </svg>
                                            {{ route.has_params ? 'Needs params' : 'Not GET' }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Empty State -->
                <div v-if="Object.keys(filteredRoutes).length === 0" class="bg-white rounded-lg shadow p-12 text-center">
                    <div class="text-gray-400 text-6xl mb-4">🔍</div>
                    <h3 class="text-lg font-medium text-gray-900 mb-2">No routes found</h3>
                    <p class="text-gray-500">Try adjusting your search or filters</p>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>

<script setup>
import { ref, computed } from 'vue';
import { Head } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';

const props = defineProps({
    routes: Object,
    stats: Object,
});

const searchQuery = ref('');
const methodFilter = ref('');
const routeTypeFilter = ref('');
const expandedCategories = ref(Object.keys(props.routes)); // All expanded by default
const testedRoutes = ref(new Set(JSON.parse(localStorage.getItem('testedRoutes') || '[]')));
const isTesting = ref(false);

// Filtered routes based on search and filters
const filteredRoutes = computed(() => {
    let filtered = { ...props.routes };

    // Apply search filter
    if (searchQuery.value) {
        const query = searchQuery.value.toLowerCase();
        filtered = Object.fromEntries(
            Object.entries(filtered).map(([category, routes]) => [
                category,
                routes.filter(route =>
                    route.uri.toLowerCase().includes(query) ||
                    route.name?.toLowerCase().includes(query) ||
                    route.action.toLowerCase().includes(query) ||
                    category.toLowerCase().includes(query)
                )
            ]).filter(([_, routes]) => routes.length > 0)
        );
    }

    // Apply method filter
    if (methodFilter.value) {
        filtered = Object.fromEntries(
            Object.entries(filtered).map(([category, routes]) => [
                category,
                routes.filter(route => route.methods.includes(methodFilter.value))
            ]).filter(([_, routes]) => routes.length > 0)
        );
    }

    // Apply route type filter
    if (routeTypeFilter.value) {
        filtered = Object.fromEntries(
            Object.entries(filtered).map(([category, routes]) => [
                category,
                routes.filter(route => {
                    switch (routeTypeFilter.value) {
                        case 'static':
                            return !route.has_params;
                        case 'dynamic':
                            return route.has_params;
                        case 'testable':
                            return !route.has_params && route.methods.includes('GET');
                        case 'tested':
                            return testedRoutes.value.has(route.uri);
                        case 'untested':
                            return !testedRoutes.value.has(route.uri) && !route.has_params && route.methods.includes('GET');
                        default:
                            return true;
                    }
                })
            ]).filter(([_, routes]) => routes.length > 0)
        );
    }

    return filtered;
});

// Total filtered routes count
const totalFilteredRoutes = computed(() => {
    return Object.values(filteredRoutes.value).flat().length;
});

// Testable filtered routes count
const testableFilteredCount = computed(() => {
    return Object.values(filteredRoutes.value)
        .flat()
        .filter(route => !route.has_params && route.methods.includes('GET'))
        .length;
});

// Toggle category expansion
const toggleCategory = (category) => {
    const index = expandedCategories.value.indexOf(category);
    if (index > -1) {
        expandedCategories.value.splice(index, 1);
    } else {
        expandedCategories.value.push(category);
    }
};

// Collapse all categories
const collapseAll = () => {
    expandedCategories.value = [];
};

// Expand all categories
const expandAll = () => {
    expandedCategories.value = Object.keys(filteredRoutes.value);
};

// Mark route as tested
const markAsTested = (uri) => {
    testedRoutes.value.add(uri);
    localStorage.setItem('testedRoutes', JSON.stringify([...testedRoutes.value]));
};

// Unmark route as tested
const unmarkAsTested = (uri) => {
    testedRoutes.value.delete(uri);
    localStorage.setItem('testedRoutes', JSON.stringify([...testedRoutes.value]));
};

// Copy to clipboard
const copyToClipboard = (text) => {
    navigator.clipboard.writeText(text).then(() => {
        alert('Copied to clipboard: ' + text);
    });
};

// Export routes to JSON
const exportToJson = () => {
    const data = {
        exported_at: new Date().toISOString(),
        total_routes: stats.value?.total_routes || props.stats.total_routes,
        categories: Object.keys(props.routes).length,
        routes: props.routes
    };
    
    const blob = new Blob([JSON.stringify(data, null, 2)], { type: 'application/json' });
    const url = URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.href = url;
    a.download = `admin-sitemap-${new Date().toISOString().split('T')[0]}.json`;
    document.body.appendChild(a);
    a.click();
    document.body.removeChild(a);
    URL.revokeObjectURL(url);
};

// Clear all filters
const clearFilters = () => {
    searchQuery.value = '';
    methodFilter.value = '';
    routeTypeFilter.value = '';
};

// Test category links
const testCategoryLinks = (categoryRoutes) => {
    const testableRoutes = categoryRoutes.filter(
        route => !route.has_params && route.methods.includes('GET')
    );

    if (testableRoutes.length === 0) {
        alert('No testable routes in this category');
        return;
    }

    if (confirm(`Test ${testableRoutes.length} links from this category?`)) {
        testableRoutes.slice(0, 5).forEach((route, index) => {
            setTimeout(() => {
                window.open(route.url, '_blank');
                markAsTested(route.uri);
            }, index * 300);
        });

        if (testableRoutes.length > 5) {
            alert(`Opened first 5 links. ${testableRoutes.length - 5} remaining. Test manually to avoid browser blocking.`);
        }
    }
};

// Test filtered links
const testFilteredLinks = () => {
    if (isTesting.value) return;

    const testableRoutes = Object.values(filteredRoutes.value)
        .flat()
        .filter(route => !route.has_params && route.methods.includes('GET'));

    if (testableRoutes.length === 0) {
        alert('No testable routes matching current filters');
        return;
    }

    const maxToOpen = Math.min(testableRoutes.length, 10);
    if (confirm(`Test ${maxToOpen} of ${testableRoutes.length} filtered links?`)) {
        isTesting.value = true;
        
        testableRoutes.slice(0, maxToOpen).forEach((route, index) => {
            setTimeout(() => {
                window.open(route.url, '_blank');
                markAsTested(route.uri);
                
                if (index === maxToOpen - 1) {
                    setTimeout(() => {
                        isTesting.value = false;
                        if (testableRoutes.length > maxToOpen) {
                            alert(`Tested first ${maxToOpen} links. ${testableRoutes.length - maxToOpen} remaining.`);
                        }
                    }, 500);
                }
            }, index * 400);
        });
    }
};
</script>
