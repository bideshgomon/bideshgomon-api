<template>
    <Head :title="seo.title">
        <meta name="description" :content="seo.description" />
        <meta name="keywords" :content="seo.keywords" />
    </Head>

    <AuthenticatedLayout>
        <!-- Category Hero -->
        <div class="text-white" :style="{ backgroundColor: category.color || '#3B82F6' }">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
                <div class="flex items-center gap-4 mb-4">
                    <div
                        v-if="category.icon"
                        class="w-16 h-16 bg-white/20 rounded-lg flex items-center justify-center"
                        v-html="category.icon"
                    ></div>
                    <div>
                        <h1 class="text-4xl font-bold mb-2">{{ category.name }}</h1>
                        <p v-if="category.description" class="text-lg opacity-90">{{ category.description }}</p>
                    </div>
                </div>
                
                <div class="flex items-center gap-6 text-sm">
                    <div class="flex items-center gap-2">
                        <BuildingOffice2Icon class="h-5 w-5" />
                        <span>{{ directories.total }} Directories</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <!-- Breadcrumb -->
            <nav class="flex items-center gap-2 text-sm text-gray-600 mb-8">
                <Link :href="route('directory.index')" class="hover:text-blue-600">Directory</Link>
                <ChevronRightIcon class="h-4 w-4" />
                <span class="text-gray-900 font-medium">{{ category.name }}</span>
            </nav>

            <!-- Directory Grid -->
            <div v-if="directories.data.length > 0" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
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
                        <div v-else class="w-full h-full flex items-center justify-center" :style="{ backgroundColor: category.color || '#3B82F6' }">
                            <BuildingOffice2Icon class="h-16 w-16 text-white/50" />
                        </div>
                    </div>

                    <!-- Content -->
                    <div class="p-5">
                        <!-- Name -->
                        <h3 class="text-lg font-semibold text-gray-900 mb-2 group-hover:text-blue-600 transition-colors line-clamp-2">
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
                <p class="text-gray-600 mb-4">There are currently no directories in this category.</p>
                <Link
                    :href="route('directory.index')"
                    class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors"
                >
                    Browse All Directories
                </Link>
            </div>

            <!-- Pagination -->
            <div v-if="directories.data.length > 0" class="mt-8">
                <Pagination :links="directories.links" />
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import { Head, Link } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import Pagination from '@/Components/Pagination.vue';
import {
    ChevronRightIcon,
    BuildingOffice2Icon,
    MapPinIcon,
    PhoneIcon,
    EyeIcon,
} from '@heroicons/vue/24/outline';

defineProps({
    category: Object,
    directories: Object,
    seo: Object,
});
</script>
