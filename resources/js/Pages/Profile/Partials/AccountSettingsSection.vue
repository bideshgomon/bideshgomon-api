<script setup>
import { ref } from 'vue';
import { ChevronUpIcon, ChevronDownIcon } from '@heroicons/vue/24/solid';
import UpdatePasswordForm from '@/Pages/Profile/Partials/UpdatePasswordForm.vue';
import DeleteUserForm from '@/Pages/Profile/Partials/DeleteUserForm.vue';

// State for the collapsible section
const isOpen = ref(false); // Start as closed

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
                        Account Settings
                    </h2>
                    <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                        Update your password or permanently delete your account.
                    </p>
                </div>
                <button>
                    <ChevronUpIcon v-if="isOpen" class="h-6 w-6 text-gray-500" />
                    <ChevronDownIcon v-else class="h-6 w-6 text-gray-500" />
                </button>
            </header>

            <div v-show="isOpen" class="mt-6 border-t border-gray-200 dark:border-gray-700 pt-6 space-y-6">
                
                <div>
                    <UpdatePasswordForm class="max-w-xl" />
                </div>

                <div class="border-t border-gray-200 dark:border-gray-700"></div>

                <div>
                    <DeleteUserForm class="max-w-xl" />
                </div>
                
            </div>
        </section>
    </div>
</template>