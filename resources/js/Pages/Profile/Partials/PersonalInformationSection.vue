<script setup>
import { ref } from 'vue';
import { ChevronUpIcon, ChevronDownIcon } from '@heroicons/vue/24/solid';
import UpdateProfileInformationForm from '@/Pages/Profile/Partials/UpdateProfileInformationForm.vue';

// Define the props that the child component (UpdateProfileInformationForm) needs
defineProps({
    mustVerifyEmail: Boolean,
    status: String,
});

// State for the collapsible section
const isOpen = ref(true); // Start as open, since it's the main info

const toggle = () => {
    isOpen.value = !isOpen.value;
};
</script>

<template>
    <div class="p-4 sm:p-8 bg-white dark:bg-gray-800 shadow sm:rounded-lg">
        <section>
            <header @click="toggle" class="flex justify-between items-center cursor-pointer">
                <div>
                    <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                        Personal Information
                    </h2>
                    <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                        Update your account's profile information and email address.
                    </p>
                </div>
                <button>
                    <ChevronUpIcon v-if="isOpen" class="h-6 w-6 text-gray-500" />
                    <ChevronDownIcon v-else class="h-6 w-6 text-gray-500" />
                </button>
            </header>

            <div v-show="isOpen" class="mt-6 border-t border-gray-200 dark:border-gray-700 pt-6">
                <UpdateProfileInformationForm
                    :must-verify-email="mustVerifyEmail"
                    :status="status"
                    class="max-w-xl"
                />
            </div>
        </section>
    </div>
</template>