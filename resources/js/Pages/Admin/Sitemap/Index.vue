<template>
    <AdminLayout>
        <Head title="Admin Sitemap" />

        <div class="py-6">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <!-- Header -->
                <div class="mb-8">
                    <h1 class="text-3xl font-bold text-gray-900">Admin Panel Sitemap</h1>
                    <p class="mt-2 text-sm text-gray-600">
                        Complete list of all admin routes - Test and verify all links work properly
                    </p>
                </div>

                <!-- Statistics -->
                <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
                    <div class="bg-white rounded-lg shadow p-6">
                        <div class="text-sm font-medium text-gray-500">Total Routes</div>
                        <div class="mt-2 text-3xl font-bold text-indigo-600">{{ stats.total_routes }}</div>
                    </div>
                    <div class="bg-white rounded-lg shadow p-6">
                        <div class="text-sm font-medium text-gray-500">Categories</div>
                        <div class="mt-2 text-3xl font-bold text-green-600">{{ stats.categories }}</div>
                    </div>
                    <div class="bg-white rounded-lg shadow p-6">
                        <div class="text-sm font-medium text-gray-500">Static Routes</div>
                        <div class="mt-2 text-3xl font-bold text-blue-600">{{ stats.without_params }}</div>
                    </div>
                    <div class="bg-white rounded-lg shadow p-6">
                        <div class="text-sm font-medium text-gray-500">Dynamic Routes</div>
                        <div class="mt-2 text-3xl font-bold text-purple-600">{{ stats.with_params }}</div>
                    </div>
                </div>

                <!-- Search and Filters -->
                <div class="bg-white rounded-lg shadow p-6 mb-8">
                    <div class="flex flex-col md:flex-row gap-4">
                        <div class="flex-1">
                            <input
                                v-model="searchQuery"
                                type="text"
                                placeholder="Search routes by URI, name, or action..."
                                class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                            />
                        </div>
                        <div class="flex gap-2">
                            <button
                                @click="showOnlyTestable = !showOnlyTestable"
                                :class="[
                                    'px-4 py-2 rounded-md border transition',
                                    showOnlyTestable
                                        ? 'bg-indigo-600 text-white border-indigo-600'
                                        : 'bg-white text-gray-700 border-gray-300 hover:bg-gray-50'
                                ]"
                            >
                                {{ showOnlyTestable ? '✓ ' : '' }}Testable Only
                            </button>
                            <button
                                @click="testAllLinks"
                                class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 transition"
                            >
                                Test All Links
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
                                <div class="flex items-center">
                                    <span class="text-2xl mr-3">
                                        {{ expandedCategories.includes(category) ? '▼' : '▶' }}
                                    </span>
                                    <div>
                                        <h2 class="text-lg font-semibold text-gray-900">{{ category }}</h2>
                                        <p class="text-sm text-gray-500">{{ categoryRoutes.length }} routes</p>
                                    </div>
                                </div>
                                <span class="px-3 py-1 bg-indigo-100 text-indigo-800 rounded-full text-sm font-medium">
                                    {{ (categoryRoutes || []).filter(r => !r?.has_params).length }} testable
                                </span>
                            </div>
                        </div>

                        <!-- Routes List -->
                        <div v-if="expandedCategories.includes(category)" class="divide-y divide-gray-200">
                            <div
                                v-for="route in categoryRoutes"
                                :key="route.uri"
                                class="px-6 py-4 hover:bg-gray-50 transition"
                            >
                                <div class="flex items-start justify-between">
                                    <div class="flex-1">
                                        <div class="flex items-center gap-3 mb-2">
                                            <!-- HTTP Methods -->
                                            <span class="px-2 py-1 bg-blue-100 text-blue-800 text-xs font-medium rounded">
                                                {{ route.methods }}
                                            </span>
                                            
                                            <!-- Route Name -->
                                            <span v-if="route.name" class="text-sm text-gray-600">
                                                {{ route.name }}
                                            </span>
                                            
                                            <!-- Dynamic Route Badge -->
                                            <span v-if="route.has_params" class="px-2 py-1 bg-yellow-100 text-yellow-800 text-xs font-medium rounded">
                                                Dynamic
                                            </span>
                                        </div>

                                        <!-- URI -->
                                        <code class="text-sm font-mono text-gray-900 bg-gray-100 px-2 py-1 rounded">
                                            {{ route.uri }}
                                        </code>

                                        <!-- Action -->
                                        <div class="mt-2 text-xs text-gray-500">
                                            {{ route.action }}
                                        </div>
                                    </div>

                                    <!-- Test Button -->
                                    <div class="ml-4">
                                        <a
                                            v-if="!route.has_params && route.methods.includes('GET')"
                                            :href="route.url"
                                            target="_blank"
                                            class="inline-flex items-center px-3 py-2 border border-gray-300 shadow-sm text-sm leading-4 font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                                        >
                                            🔗 Test
                                        </a>
                                        <span v-else class="text-sm text-gray-400 italic">
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
const showOnlyTestable = ref(false);
const expandedCategories = ref(Object.keys(props.routes)); // All expanded by default

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
                    route.action.toLowerCase().includes(query)
                )
            ]).filter(([_, routes]) => routes.length > 0)
        );
    }

    // Apply testable filter
    if (showOnlyTestable.value) {
        filtered = Object.fromEntries(
            Object.entries(filtered).map(([category, routes]) => [
                category,
                routes.filter(route => !route.has_params && route.methods.includes('GET'))
            ]).filter(([_, routes]) => routes.length > 0)
        );
    }

    return filtered;
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

// Test all testable links
const testAllLinks = () => {
    const testableRoutes = Object.values(props.routes)
        .flat()
        .filter(route => !route.has_params && route.methods.includes('GET'));

    if (confirm(`This will open ${testableRoutes.length} links in new tabs. Continue?`)) {
        testableRoutes.slice(0, 10).forEach((route, index) => {
            setTimeout(() => {
                window.open(route.url, '_blank');
            }, index * 500); // Stagger opening to avoid browser blocking
        });

        if (testableRoutes.length > 10) {
            alert(`Opened first 10 links. ${testableRoutes.length - 10} remaining. Please test manually.`);
        }
    }
};
</script>
