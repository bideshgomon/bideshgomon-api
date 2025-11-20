<template>
    <AuthenticatedLayout>
        <Head title="My Translation Requests" />
        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="mb-8 flex items-center justify-between">
                    <h1 class="text-3xl font-bold text-gray-900">My Translation Requests</h1>
                    <Link :href="route('translation.create')" class="px-4 py-2 bg-indigo-600 text-white rounded-lg">New Request</Link>
                </div>

                <div v-if="requests.data.length > 0" class="space-y-4">
                    <div v-for="req in requests.data" :key="req.id" class="bg-white rounded-lg shadow-sm border p-6">
                        <div class="flex items-start justify-between">
                            <div>
                                <h3 class="text-lg font-semibold text-gray-900">{{ req.source_language }} → {{ req.target_language }}</h3>
                                <p class="text-sm text-gray-600 mt-1">{{ req.request_reference }}</p>
                                <div class="mt-2 flex items-center space-x-4">
                                    <span class="text-sm text-gray-600">{{ req.document_type }}</span>
                                    <span class="text-sm text-gray-600">{{ req.page_count }} pages</span>
                                    <span :class="`px-2 py-1 text-xs rounded-full ${getStatusClass(req.status)}`">{{ req.status }}</span>
                                </div>
                            </div>
                            <div class="text-right">
                                <div class="text-2xl font-bold text-indigo-600">৳{{ req.total_amount }}</div>
                                <Link :href="route('translation.show', req.id)" class="mt-2 inline-block px-3 py-1 bg-gray-100 text-gray-700 text-sm rounded-lg">View</Link>
                            </div>
                        </div>
                    </div>
                </div>

                <div v-else class="bg-white rounded-lg shadow-sm border p-12 text-center">
                    <p class="text-gray-600">No translation requests yet</p>
                    <Link :href="route('translation.create')" class="mt-4 inline-block px-4 py-2 bg-indigo-600 text-white rounded-lg">Create Request</Link>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import { Head, Link } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';

defineProps({ requests: Object });

const getStatusClass = (status) => {
    const map = {
        submitted: 'bg-blue-100 text-blue-800',
        in_progress: 'bg-orange-100 text-orange-800',
        completed: 'bg-green-100 text-green-800',
        cancelled: 'bg-red-100 text-red-800'
    };
    return map[status] || 'bg-gray-100 text-gray-800';
};
</script>
