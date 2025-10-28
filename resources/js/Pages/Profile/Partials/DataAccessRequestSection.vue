<script setup>
import { ref, onMounted } from 'vue';
import axios from 'axios';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import { CheckIcon, XMarkIcon, ClockIcon } from '@heroicons/vue/24/solid';

const pendingRequests = ref([]);
const isLoading = ref(true);
const errorMessage = ref('');

onMounted(async () => {
    fetchRequests();
});

async function fetchRequests() {
    isLoading.value = true;
    errorMessage.value = '';
    try {
        // Use the correct API route name defined in api.php
        const response = await axios.get(route('data-access.list'));
        pendingRequests.value = response.data;
    } catch (error) {
        console.error("Error fetching data access requests:", error);
        errorMessage.value = 'Failed to load requests. Please try again later.';
    } finally {
        isLoading.value = false;
    }
}

async function handleRequest(requestId, action) {
    const request = pendingRequests.value.find(r => r.id === requestId);
    if (!request || request.processing) return; // Add check if request exists
    request.processing = true;

    const url = action === 'approve'
        ? route('data-access.approve', { dataAccessRequest: requestId })
        : route('data-access.deny', { dataAccessRequest: requestId });

    try {
        await axios.post(url);
        pendingRequests.value = pendingRequests.value.filter(r => r.id !== requestId);
        console.log(`Request ${requestId} ${action}d successfully.`);
        // Optional: Add flash message logic here
    } catch (error) {
        console.error(`Error ${action}ing request ${requestId}:`, error);
        alert(`Failed to ${action} the request. Please try again.`);
        delete request.processing; // Ensure flag is removed on error
    }
}

function formatDate(dateString) {
    if (!dateString) return 'N/A';
    try {
        return new Date(dateString).toLocaleDateString(undefined, {
            year: 'numeric', month: 'short', day: 'numeric', hour: 'numeric', minute: '2-digit'
        });
    } catch (e) {
        return dateString;
    }
}
</script>

<template>
    <section class="space-y-6 bg-white dark:bg-gray-800 shadow sm:rounded-lg p-6">
        <header>
            <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                Data Access Requests
            </h2>
            <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                Consultants require your approval to view your detailed profile information. Manage pending requests below.
            </p>
        </header>

        <div v-if="isLoading" class="text-center py-4 text-gray-500 dark:text-gray-400">
            Loading requests...
        </div>

        <div v-else-if="errorMessage" class="text-center py-4 text-red-600 dark:text-red-400">
            {{ errorMessage }}
        </div>

        <div v-else-if="pendingRequests.length === 0" class="text-center py-4 text-gray-500 dark:text-gray-400">
            You have no pending data access requests.
        </div>

        <div v-else class="space-y-4">
            <div
                v-for="request in pendingRequests"
                :key="request.id"
                class="flex flex-col sm:flex-row items-center justify-between p-4 border rounded-md dark:border-gray-700 bg-gray-50 dark:bg-gray-700"
            >
                <div class="mb-3 sm:mb-0 sm:mr-4 flex-grow">
                    <p class="text-sm font-medium text-gray-900 dark:text-gray-100">
                        <span class="font-semibold">{{ request.consultant?.name || 'Unknown Consultant' }}</span>
                        ({{ request.consultant?.email || 'No Email' }})
                    </p>
                    <p class="text-xs text-gray-500 dark:text-gray-400 flex items-center mt-1">
                        <ClockIcon class="h-3 w-3 mr-1 inline-block" />
                        Requested on: {{ formatDate(request.requested_at) }}
                    </p>
                </div>
                <div class="flex space-x-2 flex-shrink-0">
                    <button
                        @click="handleRequest(request.id, 'approve')"
                        :disabled="request.processing"
                        class="inline-flex items-center px-3 py-1.5 border border-transparent text-xs font-medium rounded-md shadow-sm text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 disabled:opacity-50"
                    >
                        <CheckIcon class="-ml-0.5 mr-1.5 h-4 w-4" aria-hidden="true" />
                        Approve
                    </button>
                    <button
                        @click="handleRequest(request.id, 'deny')"
                        :disabled="request.processing"
                        class="inline-flex items-center px-3 py-1.5 border border-gray-300 dark:border-gray-600 text-xs font-medium rounded-md shadow-sm text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 disabled:opacity-50"
                    >
                         <XMarkIcon class="-ml-0.5 mr-1.5 h-4 w-4" aria-hidden="true" />
                        Deny
                    </button>
                </div>
            </div>
        </div>
    </section>
</template>