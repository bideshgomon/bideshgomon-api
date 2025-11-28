<script setup>
import { ref } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { 
    MagnifyingGlassIcon,
    GlobeAltIcon,
    DocumentTextIcon,
    ArrowRightIcon
} from '@heroicons/vue/24/outline';

const props = defineProps({
    countries: Object,
    filters: Object,
});

const search = ref(props.filters?.search || '');

const searchCountries = () => {
    router.get(route('admin.document-assignments.index'), {
        search: search.value,
    }, {
        preserveState: true,
        replace: true,
    });
};
</script>

<template>
    <Head title="Document Assignments" />

    <AdminLayout>
        <div class="py-6">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <!-- Header -->
                <div class="mb-6">
                    <h1 class="text-2xl font-bold text-gray-900">Country Document Assignments</h1>
                    <p class="mt-1 text-sm text-gray-600">
                        Assign visa documents from the master library to countries
                    </p>
                </div>

                <!-- Search -->
                <div class="bg-white rounded-lg shadow p-4 mb-6">
                    <div class="relative">
                        <MagnifyingGlassIcon class="absolute left-3 top-1/2 transform -translate-y-1/2 w-5 h-5 text-gray-400" />
                        <input
                            v-model="search"
                            @keyup.enter="searchCountries"
                            type="text"
                            placeholder="Search countries..."
                            class="pl-10 w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                        />
                    </div>
                </div>

                <!-- Countries Grid -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <Link
                        v-for="country in countries.data"
                        :key="country.id"
                        :href="route('admin.document-assignments.show', country.id)"
                        class="bg-white rounded-lg shadow hover:shadow-lg transition-shadow p-6 cursor-pointer group"
                    >
                        <div class="flex items-start justify-between">
                            <div class="flex items-center">
                                <span class="text-4xl mr-4">{{ country.flag_emoji }}</span>
                                <div>
                                    <h3 class="text-lg font-semibold text-gray-900 group-hover:text-indigo-600">
                                        {{ country.name }}
                                    </h3>
                                    <p class="text-sm text-gray-500">{{ country.iso_code_2 }}</p>
                                </div>
                            </div>
                            <ArrowRightIcon class="w-5 h-5 text-gray-400 group-hover:text-indigo-600 transition-colors" />
                        </div>

                        <div class="mt-4 pt-4 border-t border-gray-200">
                            <div class="flex items-center justify-between text-sm">
                                <div class="flex items-center text-gray-600">
                                    <DocumentTextIcon class="w-4 h-4 mr-1" />
                                    <span>{{ country.document_requirements ? country.document_requirements.length : 0 }} documents</span>
                                </div>
                                <div class="text-indigo-600 font-medium group-hover:underline">
                                    Manage →
                                </div>
                            </div>
                        </div>
                    </Link>
                </div>

                <!-- Pagination -->
                <div v-if="countries.links && countries.links.length > 3" class="mt-6 flex justify-center">
                    <nav class="flex space-x-2">
                        <component
                            :is="link.url ? Link : 'span'"
                            v-for="(link, index) in countries.links"
                            :key="index"
                            :href="link.url"
                            :class="[
                                'px-3 py-2 text-sm rounded-md',
                                link.active
                                    ? 'bg-indigo-600 text-white'
                                    : link.url
                                        ? 'bg-white text-gray-700 hover:bg-gray-50 border border-gray-300 cursor-pointer'
                                        : 'bg-gray-100 text-gray-400 border border-gray-200 cursor-not-allowed'
                            ]"
                            v-html="link.label"
                        />
                    </nav>
                </div>

                <!-- Quick Links -->
                <div class="mt-8 bg-indigo-50 rounded-lg p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Quick Actions</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <Link
                            :href="route('admin.master-documents.index')"
                            class="flex items-center p-4 bg-white rounded-lg hover:shadow-md transition-shadow"
                        >
                            <DocumentTextIcon class="w-6 h-6 text-indigo-600 mr-3" />
                            <div>
                                <div class="font-medium text-gray-900">Master Documents</div>
                                <div class="text-sm text-gray-600">Manage document library</div>
                            </div>
                        </Link>
                        <Link
                            :href="route('admin.document-categories.index')"
                            class="flex items-center p-4 bg-white rounded-lg hover:shadow-md transition-shadow"
                        >
                            <GlobeAltIcon class="w-6 h-6 text-indigo-600 mr-3" />
                            <div>
                                <div class="font-medium text-gray-900">Document Categories</div>
                                <div class="text-sm text-gray-600">Manage categories</div>
                            </div>
                        </Link>
                    </div>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>
