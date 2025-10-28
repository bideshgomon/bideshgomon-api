<script setup>
import { ref, onMounted, computed } from 'vue';
import { useForm, usePage } from '@inertiajs/vue3';
import axios from 'axios';

// Import necessary components
import PrimaryButton from '@/Components/PrimaryButton.vue';
import InputLabel from '@/Components/InputLabel.vue';
import InputError from '@/Components/InputError.vue';
import Checkbox from '@/Components/Checkbox.vue'; // Using checkboxes for selection

// --- State Management ---
const allSkills = ref([]); // List of all available skills { id: 1, name: 'JavaScript' }
const userSkills = ref([]); // List of user's current skills { id: 1, name: 'JavaScript' }
const selectedSkillIds = ref(new Set()); // Use a Set for efficient add/remove/check
const isLoading = ref(true);
const isSaving = ref(false);
const recentlySuccessful = ref(false);
const successMessage = ref('');
const errorMessage = ref(''); // For general errors

// --- API Interaction ---
const fetchData = async () => {
    isLoading.value = true;
    errorMessage.value = '';
    try {
        // Fetch all available skills (adjust endpoint if needed)
        const allSkillsResponse = await axios.get(route('api.prebuilt-data')); // Assuming skills are in prebuilt-data
        allSkills.value = allSkillsResponse.data.skills || []; // Adjust based on actual API response structure

        // Fetch user's current skills
        const userSkillsResponse = await axios.get(route('profile.skills.index')); // API route for user's skills
        userSkills.value = userSkillsResponse.data.data || []; // Assuming API returns data wrapped in 'data'

        // Initialize selectedSkillIds based on user's current skills
        selectedSkillIds.value = new Set(userSkills.value.map(skill => skill.id));

    } catch (error) {
        console.error("Error fetching skills data:", error);
        errorMessage.value = "Failed to load skills data. Please try again later.";
    } finally {
        isLoading.value = false;
    }
};

// --- Form Submission (Saving Skills) ---
// We don't use Inertia's useForm here as we're just sending an array of IDs
const saveSkills = async () => {
    isSaving.value = true;
    recentlySuccessful.value = false;
    successMessage.value = '';
    errorMessage.value = ''; // Clear previous errors

    try {
        // The UserSkillsController likely expects an array of skill IDs in the request body
        const response = await axios.post(route('profile.skills.store'), {
            skills: Array.from(selectedSkillIds.value) // Convert Set to Array
        });

        // Update local state and show success
        userSkills.value = response.data.data || []; // Update userSkills with the saved list from response
        selectedSkillIds.value = new Set(userSkills.value.map(skill => skill.id)); // Resync Set
        successMessage.value = response.data.message || 'Skills updated successfully.';
        recentlySuccessful.value = true;
        setTimeout(() => recentlySuccessful.value = false, 2000);

    } catch (error) {
        console.error("Error saving skills:", error);
         if (error.response && error.response.data && error.response.data.message) {
             errorMessage.value = error.response.data.message;
         } else {
             errorMessage.value = 'An unexpected error occurred while saving skills.';
         }
    } finally {
        isSaving.value = false;
    }
};

// --- Helper for Checkbox ---
const handleCheckboxChange = (skillId, isChecked) => {
    if (isChecked) {
        selectedSkillIds.value.add(skillId);
    } else {
        selectedSkillIds.value.delete(skillId);
    }
};

// --- Computed property to check if skills have changed ---
const skillsChanged = computed(() => {
    const initialIds = new Set(userSkills.value.map(s => s.id));
    if (selectedSkillIds.value.size !== initialIds.size) {
        return true;
    }
    for (const id of selectedSkillIds.value) {
        if (!initialIds.has(id)) {
            return true;
        }
    }
    return false;
});


// --- Lifecycle Hook ---
onMounted(() => {
    fetchData();
});
</script>

<template>
    <section class="space-y-6">
        <header>
            <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">Skills</h2>
            <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                Select the skills relevant to your profession and experience.
            </p>
        </header>

        <div v-if="isLoading" class="text-sm text-gray-500 dark:text-gray-400">Loading skills...</div>

        <div v-else-if="errorMessage && !isLoading" class="text-sm text-red-600 dark:text-red-400">{{ errorMessage }}</div>

        <div v-else>
            <div v-if="allSkills.length === 0" class="text-sm text-gray-500 dark:text-gray-400">
                No skills available to select. Please contact support or check admin settings.
            </div>
            <div v-else class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-4 mt-4 border dark:border-gray-700 p-4 rounded-md max-h-60 overflow-y-auto">
                <div v-for="skill in allSkills" :key="skill.id" class="flex items-center">
                    <Checkbox
                        :id="'skill_' + skill.id"
                        :value="skill.id"
                        :checked="selectedSkillIds.has(skill.id)"
                        @update:checked="handleCheckboxChange(skill.id, $event)"
                    />
                    <InputLabel :for="'skill_' + skill.id" :value="skill.name" class="ml-2 text-sm text-gray-700 dark:text-gray-300"/>
                </div>
            </div>

             <div class="flex items-center gap-4 mt-6">
                <PrimaryButton @click="saveSkills" :disabled="isSaving || !skillsChanged">
                    {{ isSaving ? 'Saving...' : 'Save Skills' }}
                </PrimaryButton>
                <Transition
                    enter-active-class="transition ease-in-out"
                    enter-from-class="opacity-0"
                    leave-active-class="transition ease-in-out"
                    leave-to-class="opacity-0"
                >
                    <p v-if="recentlySuccessful" class="text-sm text-green-600 dark:text-green-400">
                        {{ successMessage }}
                    </p>
                </Transition>
                 <InputError :message="errorMessage" v-if="errorMessage && !isLoading" />
            </div>
        </div>

    </section>
</template>