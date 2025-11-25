<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import { 
    DocumentTextIcon, PencilIcon, EyeIcon, 
    ArrowDownTrayIcon, TrashIcon, PlusIcon 
} from '@heroicons/vue/24/outline';

const props = defineProps({
    cvs: Object,
});

const deleteCv = (id) => {
    if (confirm('Are you sure you want to delete this CV? This action cannot be undone.')) {
        // Delete CV logic
    }
};
</script>

<template>
    <Head title="My CVs" />

    <AuthenticatedLayout>
        <!-- Header with gradient -->
        <div class="bg-gradient-to-br from-blue-500 via-blue-600 to-indigo-600 text-white">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 sm:py-8">
                <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
                    <div>
                        <h1 class="text-xl sm:text-2xl font-bold">My CVs</h1>
                        <p class="mt-1 text-sm sm:text-base text-blue-100">Manage and download your CVs</p>
                    </div>
                    <Link 
                        :href="route('cv-builder.index')"
                        class="flex items-center justify-center space-x-2 bg-white text-blue-600 px-4 py-2.5 rounded-lg hover:bg-blue-50 transition-colors font-medium text-sm w-full sm:w-auto"
                    >
                        <PlusIcon class="h-5 w-5" />
                        <span>Create New CV</span>
                    </Link>
                </div>

                <!-- Quick Stats -->
                <div class="mt-6 grid grid-cols-3 gap-2 sm:gap-4">
                    <div class="bg-white/10 backdrop-blur-sm rounded-lg px-2 sm:px-4 py-3">
                        <div class="text-xl sm:text-2xl font-bold">{{ cvs.total }}</div>
                        <div class="text-[10px] sm:text-xs text-blue-100">Total CVs</div>
                    </div>
                    <div class="bg-white/10 backdrop-blur-sm rounded-lg px-2 sm:px-4 py-3">
                        <div class="text-xl sm:text-2xl font-bold">{{ cvs.data.reduce((sum, cv) => sum + cv.view_count, 0) }}</div>
                        <div class="text-[10px] sm:text-xs text-blue-100">Total Views</div>
                    </div>
                    <div class="bg-white/10 backdrop-blur-sm rounded-lg px-2 sm:px-4 py-3">
                        <div class="text-xl sm:text-2xl font-bold">{{ cvs.data.reduce((sum, cv) => sum + cv.download_count, 0) }}</div>
                        <div class="text-[10px] sm:text-xs text-blue-100">Downloads</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
            <!-- CV List -->
            <div v-if="cvs.data.length > 0" class="space-y-4">
                <div 
                    v-for="cv in cvs.data" 
                    :key="cv.id"
                    class="bg-white rounded-lg shadow-sm border border-gray-200 p-4 sm:p-6 hover:shadow-md transition-all"
                >
                    <div class="flex flex-col lg:flex-row items-start gap-4">
                        <div class="flex-1 min-w-0 w-full">
                            <div class="flex items-center space-x-3 mb-2">
                                <div 
                                    class="w-12 h-12 rounded-lg flex items-center justify-center flex-shrink-0"
                                    :style="{ 
                                        background: `linear-gradient(135deg, ${cv.cv_template.color_scheme.primary} 0%, ${cv.cv_template.color_scheme.secondary} 100%)` 
                                    }"
                                >
                                    <DocumentTextIcon class="h-6 w-6 text-white" />
                                </div>
                                <div class="flex-1 min-w-0">
                                    <h3 class="text-base sm:text-lg font-semibold text-gray-900 truncate">{{ cv.title }}</h3>
                                    <p class="text-xs sm:text-sm text-gray-500">{{ cv.cv_template.name }}</p>
                                </div>
                            </div>

                            <!-- Stats -->
                            <div class="flex flex-wrap items-center gap-x-4 gap-y-2 text-xs sm:text-sm text-gray-600 mt-3">
                                <div class="flex items-center space-x-1">
                                    <EyeIcon class="h-4 w-4" />
                                    <span>{{ cv.view_count }} views</span>
                                </div>
                                <div class="flex items-center space-x-1">
                                    <ArrowDownTrayIcon class="h-4 w-4" />
                                    <span>{{ cv.download_count }} downloads</span>
                                </div>
                                <div class="text-gray-400">
                                    Updated {{ new Date(cv.updated_at).toLocaleDateString() }}
                                </div>
                            </div>

                            <!-- Data Summary -->
                            <div class="flex flex-wrap items-center gap-x-3 gap-y-1 mt-3 text-xs text-gray-500">
                                <span v-if="cv.education && cv.education.length > 0">
                                    {{ cv.education.length }} Education {{ cv.education.length === 1 ? 'entry' : 'entries' }}
                                </span>
                                <span v-if="cv.experience && cv.experience.length > 0">
                                    {{ cv.experience.length }} Work {{ cv.experience.length === 1 ? 'experience' : 'experiences' }}
                                </span>
                                <span v-if="cv.skills && cv.skills.length > 0">
                                    {{ cv.skills.length }} Skills
                                </span>
                                <span v-if="cv.languages && cv.languages.length > 0">
                                    {{ cv.languages.length }} Languages
                                </span>
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="grid grid-cols-2 lg:grid-cols-1 gap-2 w-full lg:w-auto lg:min-w-[140px]">
                            <Link 
                                :href="route('cv-builder.preview', cv.id)"
                                class="flex items-center justify-center space-x-2 px-4 py-2.5 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors text-sm font-medium"
                            >
                                <EyeIcon class="h-4 w-4" />
                                <span>Preview</span>
                            </Link>
                            <Link 
                                :href="route('cv-builder.edit', cv.id)"
                                class="flex items-center justify-center space-x-2 px-4 py-2.5 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors text-sm font-medium"
                            >
                                <PencilIcon class="h-4 w-4" />
                                <span>Edit</span>
                            </Link>
                            <a 
                                :href="route('cv-builder.download', cv.id)"
                                class="flex items-center justify-center space-x-2 px-4 py-2.5 border border-emerald-300 text-emerald-700 bg-emerald-50 rounded-lg hover:bg-emerald-100 transition-colors text-sm font-medium"
                            >
                                <ArrowDownTrayIcon class="h-4 w-4" />
                                <span>Download</span>
                            </a>
                            <Link 
                                :href="route('cv-builder.destroy', cv.id)"
                                method="delete"
                                as="button"
                                class="flex items-center justify-center space-x-2 px-4 py-2.5 border border-red-300 text-red-700 bg-red-50 rounded-lg hover:bg-red-100 transition-colors text-sm font-medium"
                                @click.prevent="deleteCv(cv.id)"
                            >
                                <TrashIcon class="h-4 w-4" />
                                <span>Delete</span>
                            </Link>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Empty State -->
            <div v-else class="text-center py-16">
                <DocumentTextIcon class="mx-auto h-16 w-16 text-gray-400" />
                <h3 class="mt-4 text-lg font-medium text-gray-900">No CVs yet</h3>
                <p class="mt-2 text-sm text-gray-500">Get started by creating your first professional CV.</p>
                <Link 
                    :href="route('cv-builder.index')"
                    class="inline-flex items-center space-x-2 mt-6 bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700 transition-colors font-medium"
                >
                    <PlusIcon class="h-5 w-5" />
                    <span>Create Your First CV</span>
                </Link>
            </div>

            <!-- Pagination -->
            <div v-if="cvs.data.length > 0 && (cvs.prev_page_url || cvs.next_page_url)" class="mt-6 flex items-center justify-center space-x-2">
                <Link 
                    v-if="cvs.prev_page_url"
                    :href="cvs.prev_page_url"
                    class="px-3 sm:px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors text-sm"
                >
                    Previous
                </Link>
                <div class="px-2 sm:px-4 py-2 text-gray-700 text-sm">
                    Page {{ cvs.current_page }} of {{ cvs.last_page }}
                </div>
                <Link 
                    v-if="cvs.next_page_url"
                    :href="cvs.next_page_url"
                    class="px-3 sm:px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors text-sm"
                >
                    Next
                </Link>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
