<template>
    <AdminLayout>
        <Head title="Service Modules Management" />

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <!-- Header -->
                <div class="mb-8 flex items-center justify-between">
                    <div>
                        <h1 class="text-3xl font-bold text-gray-900">Service Modules Management</h1>
                        <p class="mt-2 text-gray-600">Manage all 39 services across 6 categories</p>
                    </div>
                </div>

                <!-- Statistics Cards -->
                <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
                    <div class="bg-white rounded-lg shadow p-6">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 bg-blue-500 rounded-md p-3">
                                <svg class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                                </svg>
                            </div>
                            <div class="ml-5 w-0 flex-1">
                                <dl>
                                    <dt class="text-sm font-medium text-gray-500 truncate">Total Services</dt>
                                    <dd class="text-2xl font-semibold text-gray-900">{{ stats.total_services }}</dd>
                                </dl>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white rounded-lg shadow p-6">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 bg-green-500 rounded-md p-3">
                                <svg class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                            <div class="ml-5 w-0 flex-1">
                                <dl>
                                    <dt class="text-sm font-medium text-gray-500 truncate">Active Services</dt>
                                    <dd class="text-2xl font-semibold text-gray-900">{{ stats.active_services }}</dd>
                                </dl>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white rounded-lg shadow p-6">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 bg-yellow-500 rounded-md p-3">
                                <svg class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                            <div class="ml-5 w-0 flex-1">
                                <dl>
                                    <dt class="text-sm font-medium text-gray-500 truncate">Coming Soon</dt>
                                    <dd class="text-2xl font-semibold text-gray-900">{{ stats.coming_soon }}</dd>
                                </dl>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white rounded-lg shadow p-6">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 bg-purple-500 rounded-md p-3">
                                <svg class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                                </svg>
                            </div>
                            <div class="ml-5 w-0 flex-1">
                                <dl>
                                    <dt class="text-sm font-medium text-gray-500 truncate">Total Applications</dt>
                                    <dd class="text-2xl font-semibold text-gray-900">{{ stats.total_applications }}</dd>
                                </dl>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Service Categories -->
                <div class="space-y-6">
                    <div 
                        v-for="category in categories" 
                        :key="category.id"
                        class="bg-white rounded-lg shadow overflow-hidden"
                    >
                        <!-- Category Header -->
                        <div 
                            class="px-6 py-4 flex items-center justify-between cursor-pointer"
                            :style="{ borderLeft: `4px solid ${category.color}` }"
                            @click="toggleCategory(category.id)"
                        >
                            <div class="flex items-center space-x-4">
                                <div 
                                    class="w-12 h-12 rounded-lg flex items-center justify-center text-white text-xl"
                                    :style="{ backgroundColor: category.color }"
                                >
                                    <i :class="`fa ${category.icon}`"></i>
                                </div>
                                <div>
                                    <h2 class="text-xl font-semibold text-gray-900">{{ category.name }}</h2>
                                    <p class="text-sm text-gray-600">{{ category.description }}</p>
                                </div>
                            </div>
                            <div class="flex items-center space-x-6">
                                <div class="text-center">
                                    <div class="text-2xl font-bold text-gray-900">{{ category.modules_count }}</div>
                                    <div class="text-xs text-gray-500">Total</div>
                                </div>
                                <div class="text-center">
                                    <div class="text-2xl font-bold text-green-600">{{ category.active_modules_count }}</div>
                                    <div class="text-xs text-gray-500">Active</div>
                                </div>
                                <svg 
                                    class="w-6 h-6 text-gray-400 transition-transform"
                                    :class="{ 'rotate-180': expandedCategories.includes(category.id) }"
                                    fill="none" 
                                    viewBox="0 0 24 24" 
                                    stroke="currentColor"
                                >
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                </svg>
                            </div>
                        </div>

                        <!-- Services List -->
                        <div v-show="expandedCategories.includes(category.id)" class="px-6 pb-6">
                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 mt-4">
                                <div 
                                    v-for="service in category.modules" 
                                    :key="service.id"
                                    class="border rounded-lg p-4 hover:shadow-md transition-shadow relative"
                                    :class="{ 
                                        'border-green-300 bg-green-50': service.is_active && !service.coming_soon,
                                        'border-yellow-300 bg-yellow-50': service.coming_soon,
                                        'border-gray-300 bg-gray-50': !service.is_active && !service.coming_soon
                                    }"
                                >
                                    <!-- Service Status Badge -->
                                    <div class="absolute top-2 right-2 flex gap-1">
                                        <span 
                                            v-if="service.is_featured"
                                            class="px-2 py-1 text-xs font-semibold rounded-full bg-purple-100 text-purple-800"
                                        >
                                            ⭐ Featured
                                        </span>
                                        <span 
                                            v-if="service.is_active && !service.coming_soon"
                                            class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800"
                                        >
                                            ✓ Active
                                        </span>
                                        <span 
                                            v-else-if="service.coming_soon"
                                            class="px-2 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800"
                                        >
                                            🕐 Coming Soon
                                        </span>
                                        <span 
                                            v-else
                                            class="px-2 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-800"
                                        >
                                            ⊗ Inactive
                                        </span>
                                    </div>

                                    <!-- Service Info -->
                                    <div class="mt-6">
                                        <h3 class="text-lg font-semibold text-gray-900 mb-1">{{ service.name }}</h3>
                                        <p class="text-sm text-gray-600 mb-3">{{ service.short_description }}</p>

                                        <!-- Price & Stats -->
                                        <div class="flex items-center justify-between text-sm mb-3">
                                            <span class="font-semibold text-lg" :style="{ color: category.color }">
                                                {{ service.formatted_price }}
                                            </span>
                                            <span class="text-gray-500">
                                                {{ service.applications_count }} applications
                                            </span>
                                        </div>

                                        <!-- Progress Bar -->
                                        <div v-if="service.applications_count > 0" class="mb-3">
                                            <div class="flex justify-between text-xs text-gray-600 mb-1">
                                                <span>Completion Rate</span>
                                                <span>{{ service.completion_rate }}%</span>
                                            </div>
                                            <div class="w-full bg-gray-200 rounded-full h-2">
                                                <div 
                                                    class="h-2 rounded-full transition-all"
                                                    :style="{ 
                                                        width: service.completion_rate + '%',
                                                        backgroundColor: category.color
                                                    }"
                                                ></div>
                                            </div>
                                        </div>

                                        <!-- Action Buttons -->
                                        <div class="flex gap-2">
                                            <Link
                                                :href="route('admin.service-modules.show', service.id)"
                                                class="flex-1 px-3 py-2 text-sm font-medium text-white rounded-md text-center"
                                                :style="{ backgroundColor: category.color }"
                                            >
                                                View Details
                                            </Link>
                                            <button
                                                @click="toggleServiceActive(service)"
                                                class="px-3 py-2 text-sm font-medium rounded-md border"
                                                :class="service.is_active ? 'border-red-300 text-red-700 hover:bg-red-50' : 'border-green-300 text-green-700 hover:bg-green-50'"
                                            >
                                                {{ service.is_active ? 'Deactivate' : 'Activate' }}
                                            </button>
                                        </div>

                                        <!-- Implementation Info -->
                                        <div v-if="service.controller" class="mt-3 pt-3 border-t border-gray-200">
                                            <p class="text-xs text-gray-500">
                                                <span class="font-medium">Route:</span> {{ service.route_prefix }}
                                            </p>
                                            <p class="text-xs text-gray-500">
                                                <span class="font-medium">Controller:</span> {{ service.controller }}
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>

<script setup>
import { ref } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';

const props = defineProps({
    categories: Array,
    stats: Object,
});

const expandedCategories = ref(props.categories.map(c => c.id)); // Expand all by default

const toggleCategory = (categoryId) => {
    const index = expandedCategories.value.indexOf(categoryId);
    if (index > -1) {
        expandedCategories.value.splice(index, 1);
    } else {
        expandedCategories.value.push(categoryId);
    }
};

const toggleServiceActive = (service) => {
    if (confirm(`Are you sure you want to ${service.is_active ? 'deactivate' : 'activate'} ${service.name}?`)) {
        router.post(route('admin.service-modules.toggle-active', service.id), {}, {
            preserveScroll: true,
            onSuccess: () => {
                // Success feedback is handled by flash message
            }
        });
    }
};
</script>

<style scoped>
.rotate-180 {
    transform: rotate(180deg);
}
</style>
