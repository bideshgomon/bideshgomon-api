<script setup>
import { ref, computed, onMounted } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import axios from 'axios';

const applications = ref({ data: [], links: [], total: 0, current_page: 1 });
const loading = ref(false);

const fetchApplications = async (page = 1) => {
    loading.value = true;
    try {
        const response = await axios.get(route('api.translation-applications.index'), {
            params: { page },
        });
        applications.value = response.data;
    } catch (error) {
        console.error('Error fetching applications:', error);
    } finally {
        loading.value = false;
    }
};

onMounted(() => {
    fetchApplications();
});

const applicationList = computed(() => applications.value.data);

const statusColors = {
    pending: 'bg-gray-100 text-gray-800',
    submitted: 'bg-blue-100 text-blue-800',
    processing: 'bg-yellow-100 text-yellow-800',
    approved: 'bg-green-100 text-green-800',
    rejected: 'bg-red-100 text-red-800',
    cancelled: 'bg-gray-100 text-gray-600',
};

const statusLabels = {
    pending: 'Pending',
    submitted: 'Submitted',
    processing: 'Processing',
    approved: 'Approved',
    rejected: 'Rejected',
    cancelled: 'Cancelled',
};

const deleteApplication = (id) => {
    if (confirm('Are you sure you want to delete this application?')) {
        axios.delete(route('api.translation-applications.destroy', id))
            .then(() => {
                fetchApplications(applications.value.current_page);
            })
            .catch(error => {
                console.error('Error deleting application:', error);
                alert('Failed to delete application');
            });
    }
};

const formatDate = (date) => {
    if (!date) return 'Not specified';
    return new Date(date).toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'short',
        day: 'numeric',
    });
};
</script>

<template>
    <Head title="Translation Applications" />

    <AuthenticatedLayout>
        <div class="py-6 sm:py-12">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <!-- Header -->
                <div class="mb-6 sm:mb-8 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                    <div>
                        <h1 class="text-2xl sm:text-3xl font-bold text-gray-900">Translation Applications</h1>
                        <p class="mt-2 text-sm text-gray-600">Manage your student visa applications for studying abroad</p>
                    </div>
                    <Link
                        :href="route('profile.translation.create')"
                        class="inline-flex items-center justify-center px-4 py-2 bg-indigo-600 border border-transparent rounded-lg font-semibold text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors w-full sm:w-auto"
                    >
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        New Application
                    </Link>
                </div>

                <!-- Applications List -->
                <div v-if="loading" class="flex flex-col items-center justify-center py-12">
                    <div class="animate-spin rounded-full h-10 w-10 border-b-2 border-indigo-600 mb-3"></div>
                    <p class="text-sm text-gray-500">Loading applications...</p>
                </div>

                <div v-else-if="applicationList.length > 0" class="space-y-4">
                    <div
                        v-for="application in applicationList"
                        :key="application.id"
                        class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden hover:shadow-md transition-shadow"
                    >
                        <div class="p-4 sm:p-6">
                            <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-4">
                                <div class="flex-1 min-w-0">
                                    <div class="flex flex-col sm:flex-row sm:items-center gap-2 sm:gap-3 mb-3">
                                        <h3 class="text-lg font-semibold text-gray-900 truncate">
                                            {{ application.destination_country?.name || 'Unknown Destination' }}
                                        </h3>
                                        <span
                                            :class="[
                                                'px-3 py-1 rounded-full text-xs font-medium inline-flex items-center w-fit',
                                                statusColors[application.status] || 'bg-gray-100 text-gray-800',
                                            ]"
                                        >
                                            {{ statusLabels[application.status] || application.status }}
                                        </span>
                                    </div>
                                    
                                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 text-sm">
                                        <div class="flex items-start gap-2">
                                            <svg class="w-5 h-5 text-gray-400 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                                            </svg>
                                            <div>
                                                <p class="text-gray-500">Reference</p>
                                                <p class="font-medium text-gray-900">{{ application.application_reference }}</p>
                                            </div>
                                        </div>
                                        
                                        <div class="flex items-start gap-2">
                                            <svg class="w-5 h-5 text-gray-400 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                                            </svg>
                                            <div>
                                                <p class="text-gray-500">Education Level</p>
                                                <p class="font-medium text-gray-900">{{ application.education_level || 'Not specified' }}</p>
                                            </div>
                                        </div>
                                        
                                        <div class="flex items-start gap-2">
                                            <svg class="w-5 h-5 text-gray-400 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                            </svg>
                                            <div>
                                                <p class="text-gray-500">Institution</p>
                                                <p class="font-medium text-gray-900">{{ application.institution_name || 'Not specified' }}</p>
                                            </div>
                                        </div>
                                        
                                        <div class="flex items-start gap-2">
                                            <svg class="w-5 h-5 text-gray-400 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                            </svg>
                                            <div>
                                                <p class="text-gray-500">Intended Start</p>
                                                <p class="font-medium text-gray-900">{{ formatDate(application.intended_start_date) }}</p>
                                            </div>
                                        </div>

                                        <div class="flex items-start gap-2">
                                            <svg class="w-5 h-5 text-gray-400 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                            <div>
                                                <p class="text-gray-500">English Test</p>
                                                <p class="font-medium text-gray-900">
                                                    {{ application.english_test_type || 'Not taken' }}
                                                    <span v-if="application.english_test_score" class="text-gray-600">
                                                        ({{ application.english_test_score }})
                                                    </span>
                                                </p>
                                            </div>
                                        </div>
                                        
                                        <div class="flex items-start gap-2">
                                            <svg class="w-5 h-5 text-gray-400 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                            <div>
                                                <p class="text-gray-500">Applied On</p>
                                                <p class="font-medium text-gray-900">{{ formatDate(application.created_at) }}</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="flex sm:flex-col gap-2 sm:ml-4">
                                    <Link
                                        :href="route('profile.translation.show', application.id)"
                                        class="inline-flex items-center justify-center px-4 py-2 bg-white border border-gray-300 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors"
                                    >
                                        View Details
                                    </Link>
                                    <button
                                        v-if="application.status === 'pending'"
                                        @click="deleteApplication(application.id)"
                                        class="inline-flex items-center justify-center px-4 py-2 bg-white border border-red-300 rounded-lg text-sm font-medium text-red-700 hover:bg-red-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition-colors"
                                    >
                                        Delete
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Pagination -->
                    <div v-if="applications.links.length > 3" class="flex flex-wrap justify-center gap-2 mt-6">
                        <button
                            v-for="(link, index) in applications.links"
                            :key="index"
                            @click="link.url ? fetchApplications(new URL(link.url).searchParams.get('page')) : null"
                            :disabled="!link.url || link.active"
                            :class="[
                                'px-4 py-2 text-sm font-medium rounded-lg transition-colors',
                                link.active
                                    ? 'bg-indigo-600 text-white'
                                    : link.url
                                    ? 'bg-white text-gray-700 border border-gray-300 hover:bg-gray-50'
                                    : 'bg-gray-100 text-gray-400 cursor-not-allowed',
                            ]"
                            v-html="link.label"
                        />
                    </div>
                </div>

                <!-- Empty State -->
                <div v-else class="bg-white rounded-lg shadow-sm border border-gray-200 p-12 text-center">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                    </svg>
                    <h3 class="mt-4 text-lg font-medium text-gray-900">No student visa applications yet</h3>
                    <p class="mt-2 text-sm text-gray-500">Get started by creating your first student visa application.</p>
                    <div class="mt-6">
                        <Link
                            :href="route('profile.translation.create')"
                            class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-lg font-semibold text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors"
                        >
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                            </svg>
                            Create Translation Application
                        </Link>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
