<script setup>
import { ref, onMounted } from 'vue';
import { useForm } from '@inertiajs/vue3';
import axios from 'axios';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import { ChevronUpIcon, ChevronDownIcon } from '@heroicons/vue/24/solid';

// --- Collapsible Section State ---
const isOpen = ref(false);
const toggle = () => {
    isOpen.value = !isOpen.value;
};

// --- Education Form Logic ---
const educationList = ref([]);

const form = useForm({
    custom_degree: '',
    custom_university: '',
    start_date: '',
    end_date: '',
    is_current: false,
});

const getEducation = () => {
    axios.get(route('api.profile.education.index'))
        .then(response => {
            educationList.value = response.data;
        })
        .catch(error => {
            console.error("Error fetching education:", error);
        });
};

const submitEducation = () => {
    form.post(route('api.profile.education.store'), {
        preserveScroll: true,
        onSuccess: () => {
            form.reset();
            getEducation();
        },
        onError: () => {
            console.error("Error submitting form:", form.errors);
        },
    });
};

onMounted(() => {
    getEducation();
});
</script>

<template>
    <div class="p-4 sm:p-8 bg-white dark:bg-gray-800 shadow sm:rounded-lg">
        <section>
            <!-- Header (collapsible) -->
            <header @click="toggle" class="flex justify-between items-center cursor-pointer">
                <div>
                    <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">Education History</h2>
                    <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                        Add or update your education history.
                    </p>
                </div>
                <button>
                    <ChevronUpIcon v-if="isOpen" class="h-6 w-6 text-gray-500" />
                    <ChevronDownIcon v-else class="h-6 w-6 text-gray-500" />
                </button>
            </header>

            <!-- Collapsible content -->
            <div v-show="isOpen" class="mt-6 border-t border-gray-200 dark:border-gray-700 pt-6">
                <div class="space-y-4">
                    <div v-if="educationList.length === 0" class="text-sm text-gray-500 dark:text-gray-400">
                        No education records found. Add one using the form below.
                    </div>

                    <div
                        v-for="edu in educationList"
                        :key="edu.id"
                        class="p-4 border rounded-lg dark:border-gray-700"
                    >
                        <div class="flex justify-between items-center">
                            <div>
                                <h3 class="text-md font-semibold text-gray-900 dark:text-gray-100">
                                    {{ edu.custom_degree }}
                                </h3>
                                <p class="text-sm text-gray-600 dark:text-gray-400">
                                    {{ edu.custom_university }}
                                </p>
                                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                                    {{ edu.start_date }} – {{ edu.is_current ? 'Present' : edu.end_date }}
                                </p>
                            </div>
                            <div>
                                <button class="text-sm text-red-600 dark:text-red-400">Delete</button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Add New Education Form -->
                <form 
                    @submit.prevent="submitEducation" 
                    class="mt-6 space-y-6 border-t border-gray-200 dark:border-gray-700 pt-6"
                >
                    <h3 class="text-md font-medium text-gray-900 dark:text-gray-100">Add New Education</h3>

                    <div>
                        <label for="custom_degree" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                            Degree
                        </label>
                        <input 
                            type="text" 
                            id="custom_degree"
                            v-model="form.custom_degree"
                            class="mt-1 block w-full"
                            placeholder="e.g., B.Sc. in Computer Science"
                        />
                        <p v-if="form.errors.custom_degree" class="text-sm text-red-600 mt-1">
                            {{ form.errors.custom_degree }}
                        </p>
                    </div>

                    <div>
                        <label for="custom_university" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                            University / Institution
                        </label>
                        <input 
                            type="text"
                            id="custom_university"
                            v-model="form.custom_university"
                            class="mt-1 block w-full"
                            placeholder="e.g., Dhaka University"
                        />
                        <p v-if="form.errors.custom_university" class="text-sm text-red-600 mt-1">
                            {{ form.errors.custom_university }}
                        </p>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="start_date" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                Start Date
                            </label>
                            <input 
                                type="date"
                                id="start_date"
                                v-model="form.start_date"
                                class="mt-1 block w-full"
                            />
                            <p v-if="form.errors.start_date" class="text-sm text-red-600 mt-1">
                                {{ form.errors.start_date }}
                            </p>
                        </div>

                        <div>
                            <label for="end_date" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                End Date
                            </label>
                            <input 
                                type="date"
                                id="end_date"
                                v-model="form.end_date"
                                :disabled="form.is_current"
                                class="mt-1 block w-full"
                            />
                            <p v-if="form.errors.end_date" class="text-sm text-red-600 mt-1">
                                {{ form.errors.end_date }}
                            </p>
                        </div>
                    </div>

                    <div class="flex items-center">
                        <input 
                            type="checkbox"
                            id="is_current"
                            v-model="form.is_current"
                            class="rounded border-gray-300 text-brand-primary focus:ring-brand-primary"
                        />
                        <label for="is_current" class="ml-2 block text-sm text-gray-900 dark:text-gray-300">
                            I am currently studying here
                        </label>
                    </div>

                    <div class="flex items-center gap-4">
                        <PrimaryButton :disabled="form.processing">
                            {{ form.processing ? 'Saving...' : 'Save Education' }}
                        </PrimaryButton>
                    </div>
                </form>
            </div>
        </section>
    </div>
</template>
