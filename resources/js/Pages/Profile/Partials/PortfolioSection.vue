<script setup>
import { ref, onMounted } from 'vue';
import { useForm } from '@inertiajs/vue3';
import axios from 'axios';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import { ChevronUpIcon, ChevronDownIcon } from '@heroicons/vue/24/solid'; // <-- For toggle icons

// --- Collapsible Section State ---
const isOpen = ref(false);
const toggle = () => {
    isOpen.value = !isOpen.value;
};

// --- Portfolio Logic ---

// Create a reactive variable to hold the list of portfolio items
const portfolioList = ref([]);

// Create a reactive form object
const form = useForm({
    project_title: '',
    project_url: '',
    description: '',
});

// Fetch portfolio data
const getPortfolio = () => {
    axios.get(route('api.profile.portfolio.index'))
        .then(response => {
            portfolioList.value = response.data;
        })
        .catch(error => {
            console.error("Error fetching portfolio:", error);
        });
};

// Submit new portfolio project
const submitPortfolio = () => {
    form.post(route('api.profile.portfolio.store'), {
        preserveScroll: true,
        onSuccess: () => {
            form.reset();
            getPortfolio();
        },
        onError: () => {
            console.error("Error submitting form:", form.errors);
        },
    });
};

// On mount, load portfolio
onMounted(() => {
    getPortfolio();
});
</script>

<template>
    <div class="p-4 sm:p-8 bg-white dark:bg-gray-800 shadow sm:rounded-lg">
        <section>
            <!-- Collapsible Header -->
            <header @click="toggle" class="flex justify-between items-center cursor-pointer">
                <div>
                    <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">Portfolio & Projects</h2>
                    <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                        Showcase your work by adding links to your projects (e.g., GitHub, Behance, personal site).
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
                    <div v-if="portfolioList.length === 0" class="text-sm text-gray-500 dark:text-gray-400">
                        No portfolio items found. Add one using the form below.
                    </div>

                    <div 
                        v-for="item in portfolioList" 
                        :key="item.id" 
                        class="p-4 border rounded-lg dark:border-gray-700"
                    >
                        <div class="flex justify-between items-center">
                            <div>
                                <h3 class="text-md font-semibold text-gray-900 dark:text-gray-100">
                                    {{ item.project_title }}
                                </h3>
                                <a 
                                    :href="item.project_url" 
                                    target="_blank" 
                                    rel="noopener noreferrer" 
                                    class="text-sm text-blue-600 dark:text-blue-400 hover:underline"
                                >
                                    {{ item.project_url }}
                                </a>
                                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                                    {{ item.description }}
                                </p>
                            </div>
                            <div>
                                <button class="text-sm text-red-600 dark:text-red-400">Delete</button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Add New Portfolio Item -->
                <form @submit.prevent="submitPortfolio" class="mt-6 space-y-6 border-t border-gray-200 dark:border-gray-700 pt-6">
                    <h3 class="text-md font-medium text-gray-900 dark:text-gray-100">Add New Project</h3>

                    <div>
                        <label for="project_title" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Project Title</label>
                        <input 
                            type="text" 
                            id="project_title"
                            v-model="form.project_title" 
                            class="mt-1 block w-full" 
                            placeholder="e.g., Personal Portfolio Website" 
                        />
                        <p v-if="form.errors.project_title" class="text-sm text-red-600 mt-1">{{ form.errors.project_title }}</p>
                    </div>

                    <div>
                        <label for="project_url" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Project URL</label>
                        <input 
                            type="url" 
                            id="project_url" 
                            v-model="form.project_url"
                            class="mt-1 block w-full" 
                            placeholder="https://my-github-project.com" 
                        />
                        <p v-if="form.errors.project_url" class="text-sm text-red-600 mt-1">{{ form.errors.project_url }}</p>
                    </div>

                    <div>
                        <label for="project_description" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Description (Optional)</label>
                        <textarea 
                            id="project_description" 
                            v-model="form.description"
                            rows="3"
                            class="mt-1 block w-full" 
                            placeholder="A brief description of the project."
                        ></textarea>
                        <p v-if="form.errors.description" class="text-sm text-red-600 mt-1">{{ form.errors.description }}</p>
                    </div>

                    <div class="flex items-center gap-4">
                        <PrimaryButton :disabled="form.processing">
                            {{ form.processing ? 'Saving...' : 'Save Project' }}
                        </PrimaryButton>
                    </div>
                </form>
            </div>
        </section>
    </div>
</template>
