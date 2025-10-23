<script setup>
import { ref, onMounted } from 'vue';
import { useForm } from '@inertiajs/vue3';
import axios from 'axios';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import { ChevronUpIcon, ChevronDownIcon } from '@heroicons/vue/24/solid'; // <-- Collapsible icons

// --- Collapsible Toggle Logic ---
const isOpen = ref(false);
const toggle = () => {
    isOpen.value = !isOpen.value;
};

// --- Script Logic ---

// 'skills' will hold the list of skills saved in the database
const skills = ref([]);

// 'form' will manage the state of our skills list for submission
const form = useForm({
    skills: [],
});

// 'newSkill' is a temporary variable for the input field
const newSkill = ref('');

// Function to fetch the user's current skills
const getSkills = () => {
    axios.get(route('api.user'))
        .then(response => {
            if (response.data.skills) {
                skills.value = response.data.skills;
                form.skills = [...response.data.skills];
            }
        })
        .catch(error => console.error("Error fetching user skills:", error));
};

// Run getSkills when the component loads
onMounted(() => {
    getSkills();
});

// Function to add a new skill
const addSkill = () => {
    if (newSkill.value.trim() !== '' && !form.skills.includes(newSkill.value.trim())) {
        form.skills.push(newSkill.value.trim());
        newSkill.value = ''; // Clear input
    }
};

// Function to remove a skill
const removeSkill = (skillToRemove) => {
    form.skills = form.skills.filter(skill => skill !== skillToRemove);
};

// Function to submit the skills list
const submitSkills = () => {
    form.post(route('api.profile.skills.store'), {
        preserveScroll: true,
        onSuccess: () => {
            skills.value = [...form.skills];
        },
        onError: () => {
            console.error("Error submitting skills:", form.errors);
        },
    });
};
</script>

<template>
    <div class="p-4 sm:p-8 bg-white dark:bg-gray-800 shadow sm:rounded-lg">
        <section>
            <!-- Collapsible Header -->
            <header @click="toggle" class="flex justify-between items-center cursor-pointer">
                <div>
                    <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">Your Skills</h2>
                    <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                        Add skills that you are proficient in. This will help match you with the right opportunities.
                    </p>
                </div>
                <button>
                    <ChevronUpIcon v-if="isOpen" class="h-6 w-6 text-gray-500" />
                    <ChevronDownIcon v-else class="h-6 w-6 text-gray-500" />
                </button>
            </header>

            <!-- Collapsible Content -->
            <div v-show="isOpen" class="mt-6 border-t border-gray-200 dark:border-gray-700 pt-6">
                <!-- Add Skill Input -->
                <div class="flex gap-2">
                    <input 
                        type="text" 
                        v-model="newSkill" 
                        @keydown.enter.prevent="addSkill"
                        class="flex-grow rounded-md shadow-sm border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300"
                        placeholder="e.g., PHP, Laravel, Vue.js..."
                    />
                    <button 
                        @click="addSkill" 
                        type="button" 
                        class="btn btn-secondary"
                    >
                        Add
                    </button>
                </div>

                <!-- Display Added Skills -->
                <div class="mt-4 flex flex-wrap gap-2 min-h-[40px]">
                    <div v-if="form.skills.length === 0" class="text-sm text-gray-500 dark:text-gray-400 py-2">
                        No skills added yet.
                    </div>
                    <span 
                        v-for="skill in form.skills" 
                        :key="skill"
                        class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200"
                    >
                        {{ skill }}
                        <button 
                            @click="removeSkill(skill)" 
                            type="button" 
                            class="ml-1.5 flex-shrink-0 inline-flex items-center justify-center h-4 w-4 rounded-full text-blue-600 dark:text-blue-300 hover:bg-blue-200 dark:hover:bg-blue-800"
                        >
                            <span class="sr-only">Remove skill</span>
                            <svg class="h-2 w-2" stroke="currentColor" fill="none" viewBox="0 0 8 8">
                                <path stroke-linecap="round" stroke-width="1.5" d="M1 1l6 6m0-6L1 7" />
                            </svg>
                        </button>
                    </span>
                </div>

                <!-- Save Skills Button -->
                <form @submit.prevent="submitSkills" class="mt-6 border-t border-gray-200 dark:border-gray-700 pt-6">
                    <div class="flex items-center gap-4">
                        <PrimaryButton :disabled="form.processing">
                            {{ form.processing ? 'Saving...' : 'Save Skills' }}
                        </PrimaryButton>
                    </div>
                </form>
            </div>
        </section>
    </div>
</template>
