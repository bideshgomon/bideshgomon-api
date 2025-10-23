<script setup>
import { ref, onMounted } from 'vue';
import { useForm } from '@inertiajs/vue3';
import axios from 'axios';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import { ChevronUpIcon, ChevronDownIcon } from '@heroicons/vue/24/solid'; // <-- Icons for toggle

// --- Collapsible State ---
const isOpen = ref(false);
const toggle = () => {
    isOpen.value = !isOpen.value;
};

// --- Script Logic ---

// Create a reactive variable to hold the list of experience records
const experienceList = ref([]);

// Create a reactive form object
const form = useForm({
    company_name: '',
    designation: '',
    description: '',
    start_date: '',
    end_date: '',
    is_current: false,
});

// Function to fetch experience data from our API
const getExperience = () => {
    axios.get(route('api.profile.experience.index'))
        .then(response => {
            experienceList.value = response.data;
        })
        .catch(error => {
            console.error("Error fetching experience:", error);
        });
};

// Function to submit the new experience form
const submitExperience = () => {
    form.post(route('api.profile.experience.store'), {
        preserveScroll: true,
        onSuccess: () => {
            form.reset();
            getExperience(); // Re-fetch the list
        },
        onError: () => {
            console.error("Error submitting form:", form.errors);
        },
    });
};

// When the component is first loaded, call our getExperience function
onMounted(() => {
    getExperience();
});
</script>

<template>
    <div class="p-4 sm:p-8 bg-white dark:bg-gray-800 shadow sm:rounded-lg">
        <section>
            <!-- Collapsible Header -->
            <header @click="toggle" class="flex justify-between items-center cursor-pointer">
                <div>
                    <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">Work Experience</h2>
                    <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                        Add or update your professional work experience.
                    </p>
                </div>
                <button>
                    <ChevronUpIcon v-if="isOpen" class="h-6 w-6 text-gray-500" />
                    <ChevronDownIcon v-else class="h-6 w-6 text-gray-500" />
                </button>
            </header>

            <!-- Collapsible Content -->
            <div v-show="isOpen" class="mt-6 border-t border-gray-200 dark:border-gray-700 pt-6">
                <div class="space-y-4">
                    <div v-if="experienceList.length === 0" class="text-sm text-gray-500 dark:text-gray-400">
                        No work experience found. Add one using the form below.
                    </div>

                    <div 
                        v-for="exp in experienceList" 
                        :key="exp.id" 
                        class="p-4 border rounded-lg dark:border-gray-700"
                    >
                        <div class="flex justify-between items-center">
                            <div>
                                <h3 class="text-md font-semibold text-gray-900 dark:text-gray-100">
                                    {{ exp.designation }}
                                </h3>
                                <p class="text-sm text-gray-600 dark:text-gray-400">
                                    {{ exp.company_name }}
                                </p>
                                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                                    {{ exp.start_date }} – {{ exp.is_current ? 'Present' : exp.end_date }}
                                </p>
                            </div>
                            <div>
                                <button class="text-sm text-red-600 dark:text-red-400">Delete</button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Add New Experience Form -->
                <form @submit.prevent="submitExperience" class="mt-6 space-y-6 border-t border-gray-200 dark:border-gray-700 pt-6">
                    <h3 class="text-md font-medium text-gray-900 dark:text-gray-100">Add New Experience</h3>

                    <div>
                        <label for="designation" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                            Designation / Job Title
                        </label>
                        <input 
                            type="text" 
                            id="designation"
                            v-model="form.designation" 
                            class="mt-1 block w-full" 
                            placeholder="e.g., Software Engineer" 
                        />
                        <p v-if="form.errors.designation" class="text-sm text-red-600 mt-1">{{ form.errors.designation }}</p>
                    </div>

                    <div>
                        <label for="company_name" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                            Company Name
                        </label>
                        <input 
                            type="text" 
                            id="company_name" 
                            v-model="form.company_name"
                            class="mt-1 block w-full" 
                            placeholder="e.g., BideshGomon Ltd." 
                        />
                        <p v-if="form.errors.company_name" class="text-sm text-red-600 mt-1">{{ form.errors.company_name }}</p>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="exp_start_date" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                Start Date
                            </label>
                            <input 
                                type="date" 
                                id="exp_start_date" 
                                v-model="form.start_date"
                                class="mt-1 block w-full" 
                            />
                            <p v-if="form.errors.start_date" class="text-sm text-red-600 mt-1">{{ form.errors.start_date }}</p>
                        </div>
                        
                        <div>
                            <label for="exp_end_date" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                End Date
                            </label>
                            <input 
                                type="date" 
                                id="exp_end_date" 
                                v-model="form.end_date"
                                :disabled="form.is_current"
                                class="mt-1 block w-full"
                            />
                            <p v-if="form.errors.end_date" class="text-sm text-red-600 mt-1">{{ form.errors.end_date }}</p>
                        </div>
                    </div>

                    <div class="flex items-center">
                        <input 
                            type="checkbox" 
                            id="exp_is_current" 
                            v-model="form.is_current"
                            class="rounded border-gray-300 text-brand-primary focus:ring-brand-primary"
                        />
                        <label for="exp_is_current" class="ml-2 block text-sm text-gray-900 dark:text-gray-300">
                            I am currently working here
                        </label>
                    </div>

                    <div class="flex items-center gap-4">
                        <PrimaryButton :disabled="form.processing">
                            {{ form.processing ? 'Saving...' : 'Save Experience' }}
                        </PrimaryButton>
                    </div>
                </form>
            </div>
        </section>
    </div>
</template>
