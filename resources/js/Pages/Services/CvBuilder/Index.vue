<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import { PencilSquareIcon, SparklesIcon, CheckCircleIcon, StarIcon, DocumentTextIcon } from '@heroicons/vue/24/outline';

const props = defineProps({
    templates: Object,
    userCvs: Array,
});

const categoryNames = {
    'professional': 'Professional',
    'modern': 'Modern',
    'creative': 'Creative',
    'ats-friendly': 'ATS Friendly',
    'executive': 'Executive',
};

const categoryIcons = {
    'professional': '💼',
    'modern': '✨',
    'creative': '🎨',
    'ats-friendly': '🤖',
    'executive': '👔',
};
</script>

<template>
    <Head title="CV Builder" />

    <AuthenticatedLayout>
        <!-- Header with gradient -->
        <div class="bg-growth-600 text-white">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 sm:py-8">
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-xl sm:text-2xl font-bold">CV Builder</h1>
                        <p class="mt-1 text-sm sm:text-base text-emerald-100">Create professional CVs in minutes</p>
                    </div>
                    <PencilSquareIcon class="h-12 w-12 sm:h-16 sm:w-16 text-emerald-200 opacity-50" />
                </div>

                <!-- Quick Stats -->
                <div class="mt-6 grid grid-cols-3 gap-2 sm:gap-4">
                    <div class="bg-white/10 backdrop-blur-sm rounded-lg px-2 sm:px-4 py-3">
                        <div class="text-xl sm:text-2xl font-bold">{{ Object.keys(templates).reduce((sum, key) => sum + templates[key].length, 0) }}</div>
                        <div class="text-[10px] sm:text-xs text-emerald-100">Templates</div>
                    </div>
                    <div class="bg-white/10 backdrop-blur-sm rounded-lg px-2 sm:px-4 py-3">
                        <div class="text-xl sm:text-2xl font-bold">{{ userCvs.length }}</div>
                        <div class="text-[10px] sm:text-xs text-emerald-100">My CVs</div>
                    </div>
                    <div class="bg-white/10 backdrop-blur-sm rounded-lg px-2 sm:px-4 py-3">
                        <div class="text-xl sm:text-2xl font-bold">3</div>
                        <div class="text-[10px] sm:text-xs text-emerald-100">Free</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 space-y-6">
            <!-- My CVs Section (if any) -->
            <div v-if="userCvs.length > 0" class="bg-white rounded-lg shadow-sm border border-gray-200 p-4 sm:p-6">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-base sm:text-lg font-semibold text-gray-900">My CVs</h2>
                    <Link :href="route('cv-builder.my-cvs')" class="text-sm text-emerald-600 hover:text-emerald-700">
                        View All →
                    </Link>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3 sm:gap-4">
                    <Link 
                        v-for="cv in userCvs.slice(0, 3)" 
                        :key="cv.id"
                        :href="route('cv-builder.edit', cv.id)"
                        class="border border-gray-200 rounded-lg p-4 hover:border-emerald-500 hover:shadow-md transition-all"
                    >
                        <div class="flex items-start justify-between">
                            <div class="flex-1 min-w-0">
                                <h3 class="font-medium text-gray-900 truncate">{{ cv.title }}</h3>
                                <p class="text-sm text-gray-500 mt-1">{{ cv.cv_template.name }}</p>
                            </div>
                            <DocumentTextIcon class="h-5 w-5 text-gray-400 flex-shrink-0 ml-2" />
                        </div>
                        <div class="mt-3 text-xs text-gray-500">
                            Updated {{ new Date(cv.updated_at).toLocaleDateString() }}
                        </div>
                    </Link>
                </div>
            </div>

            <!-- Why Use CV Builder -->
            <div class="bg-growth-50 dark:bg-growth-900/20 rounded-lg p-4 sm:p-6 border border-growth-200 dark:border-growth-800">
                <h2 class="text-base sm:text-lg font-semibold text-gray-900 mb-3">Why Use Our CV Builder?</h2>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-3 sm:gap-4">
                    <div class="flex items-start space-x-3">
                        <SparklesIcon class="h-5 w-5 text-emerald-600 flex-shrink-0 mt-0.5" />
                        <div>
                            <div class="font-medium text-gray-900 text-sm">Professional Templates</div>
                            <div class="text-xs text-gray-600 mt-1">Industry-standard designs trusted by recruiters</div>
                        </div>
                    </div>
                    <div class="flex items-start space-x-3">
                        <CheckCircleIcon class="h-5 w-5 text-emerald-600 flex-shrink-0 mt-0.5" />
                        <div>
                            <div class="font-medium text-gray-900 text-sm">ATS Optimized</div>
                            <div class="text-xs text-gray-600 mt-1">Pass through automated screening systems</div>
                        </div>
                    </div>
                    <div class="flex items-start space-x-3">
                        <DocumentTextIcon class="h-5 w-5 text-emerald-600 flex-shrink-0 mt-0.5" />
                        <div>
                            <div class="font-medium text-gray-900 text-sm">Easy Export</div>
                            <div class="text-xs text-gray-600 mt-1">Download as PDF instantly</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Templates by Category -->
            <div v-for="(categoryTemplates, category) in templates" :key="category" class="space-y-4">
                <div class="flex items-center space-x-2 sm:space-x-3">
                    <span class="text-2xl sm:text-3xl">{{ categoryIcons[category] }}</span>
                    <h2 class="text-lg sm:text-xl font-bold text-gray-900">{{ categoryNames[category] }}</h2>
                    <span class="text-xs sm:text-sm text-gray-500">({{ categoryTemplates.length }} templates)</span>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 sm:gap-6">
                    <div 
                        v-for="template in categoryTemplates" 
                        :key="template.id"
                        class="bg-white rounded-lg shadow-sm border-2 border-gray-200 hover:border-emerald-500 hover:shadow-lg transition-all overflow-hidden group"
                    >
                        <!-- Template Preview (placeholder) -->
                        <div 
                            class="h-40 sm:h-48 relative overflow-hidden"
                            :style="{ background: `linear-gradient(135deg, ${template.color_scheme.primary} 0%, ${template.color_scheme.secondary} 100%)` }"
                        >
                            <div class="absolute inset-0 flex items-center justify-center">
                                <DocumentTextIcon class="h-24 w-24 text-white opacity-20" />
                            </div>
                            <!-- Premium Badge -->
                            <div v-if="template.is_premium" class="absolute top-3 right-3">
                                <div class="bg-yellow-400 text-yellow-900 px-3 py-1 rounded-full text-xs font-semibold flex items-center space-x-1">
                                    <StarIcon class="h-3 w-3" />
                                    <span>Premium</span>
                                </div>
                            </div>
                            <!-- Free Badge -->
                            <div v-else class="absolute top-3 right-3">
                                <div class="bg-green-500 text-white px-3 py-1 rounded-full text-xs font-semibold">
                                    FREE
                                </div>
                            </div>
                        </div>

                        <!-- Template Info -->
                        <div class="p-3 sm:p-4">
                            <h3 class="font-semibold text-gray-900 text-base sm:text-lg mb-2">{{ template.name }}</h3>
                            <p class="text-xs sm:text-sm text-gray-600 mb-4 line-clamp-2">{{ template.description }}</p>

                            <!-- Price -->
                            <div class="flex items-center justify-between mb-4">
                                <div>
                                    <span v-if="template.is_premium" class="text-xl sm:text-2xl font-bold text-gray-900">
                                        ৳{{ template.price }}
                                    </span>
                                    <span v-else class="text-xl sm:text-2xl font-bold text-emerald-600">
                                        FREE
                                    </span>
                                </div>
                                <div class="text-[10px] sm:text-xs text-gray-500">
                                    {{ template.download_count }} downloads
                                </div>
                            </div>

                            <!-- Actions -->
                            <div class="flex flex-col sm:flex-row space-y-2 sm:space-y-0 sm:space-x-2">
                                <Link
                                    :href="route('cv-builder.create', { template: template.id })"
                                    class="flex-1 bg-emerald-600 text-white px-4 py-2.5 rounded-lg font-medium hover:bg-emerald-700 transition-colors text-center text-sm"
                                >
                                    Use Template
                                </Link>
                                <Link
                                    :href="route('cv-builder.template', template.slug)"
                                    class="px-4 py-2.5 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors text-sm font-medium"
                                >
                                    Preview
                                </Link>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Empty State -->
            <div v-if="Object.keys(templates).length === 0" class="text-center py-12">
                <DocumentTextIcon class="mx-auto h-12 w-12 text-gray-400" />
                <h3 class="mt-2 text-sm font-medium text-gray-900">No templates available</h3>
                <p class="mt-1 text-sm text-gray-500">Check back soon for new templates!</p>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
