<script setup>
import { ref, onMounted } from 'vue';
import { useForm } from '@inertiajs/vue3';
import axios from 'axios';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import DangerButton from '@/Components/DangerButton.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import InputError from '@/Components/InputError.vue';
import { ChevronUpIcon, ChevronDownIcon, PlusIcon, PencilIcon, TrashIcon } from '@heroicons/vue/24/solid';

// Collapsible state
const isOpen = ref(true);
const toggle = () => {
    isOpen.value = !isOpen.value;
};

// State
const languageList = ref([]);
const editingLanguageId = ref(null);

const form = useForm({
    language: '',
    proficiency: 'conversational', // Default value
});

// Proficiency options
const proficiencyLevels = [
    { value: 'basic', name: 'Basic' },
    { value: 'conversational', name: 'Conversational' },
    { value: 'fluent', name: 'Fluent' },
    { value: 'native', name: 'Native' },
];

// Fetch data
const getLanguages = () => {
    axios.get(route('api.profile.languages.index'))
        .then(response => {
            languageList.value = response.data;
        })
        .catch(error => console.error("Error fetching languages:", error));
};

// Submit form (Create or Update)
const submitLanguage = () => {
    const url = editingLanguageId.value
        ? route('api.profile.languages.update', editingLanguageId.value)
        : route('api.profile.languages.store');
    
    const method = editingLanguageId.value ? 'put' : 'post';

    axios[method](url, form.data())
        .then(() => {
            form.reset();
            form.clearErrors();
            editingLanguageId.value = null;
            getLanguages(); // Refresh list
            form.recentlySuccessful = true;
            setTimeout(() => form.recentlySuccessful = false, 2000);
        })
        .catch(error => {
            if (error.response && error.response.status === 422) {
                form.setError(error.response.data.errors);
            } else {
                console.error("Error submitting form:", error);
            }
        });
};

// Start editing
const editLanguage = (lang) => {
    editingLanguageId.value = lang.id;
    form.language = lang.language;
    form.proficiency = lang.proficiency;
    form.clearErrors();
};

// Cancel editing
const cancelEdit = () => {
    editingLanguageId.value = null;
    form.reset();
    form.clearErrors();
};

// Delete item
const deleteLanguage = (languageId) => {
    if (!confirm("Are you sure you want to delete this language?")) return;

    axios.delete(route('api.profile.languages.destroy', languageId))
        .then(() => {
            getLanguages(); // Refresh list
            if (editingLanguageId.value === languageId) {
                cancelEdit();
            }
        })
        .catch(error => {
            console.error("Error deleting language:", error);
            alert("Failed to delete record.");
        });
};

// Load data on mount
onMounted(() => {
    getLanguages();
});

// Helper to format proficiency
const formatProficiency = (value) => {
    const level = proficiencyLevels.find(l => l.value === value);
    return level ? level.name : value.charAt(0).toUpperCase() + value.slice(1);
};

</script>

<template>
    <div class="p-4 sm:p-8 bg-white dark:bg-gray-800 shadow sm:rounded-lg">
        <section>
            <header @click="toggle" class="flex justify-between items-center cursor-pointer">
                <div>
                    <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                        Languages
                    </h2>
                    <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                        Add languages you speak and your proficiency level.
                    </p>
                </div>
                <div>
                    <ChevronUpIcon v-if="isOpen" class="w-6 h-6 text-gray-500" />
                    <ChevronDownIcon v-else class="w-6 h-6 text-gray-500" />
                </div>
            </header>

            <div v-show="isOpen" class="mt-6 space-y-6">
                
                <form @submit.prevent="submitLanguage" class="p-4 border border-gray-200 dark:border-gray-700 rounded-lg space-y-4">
                    <h3 class="text-md font-medium text-gray-900 dark:text-gray-100">
                        {{ editingLanguageId ? 'Update Language' : 'Add New Language' }}
                    </h3>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <InputLabel for="lang_name" value="Language" />
                            <TextInput
                                id="lang_name"
                                v-model="form.language"
                                type="text"
                                class="mt-1 block w-full"
                                placeholder="e.g., English, Bengali, French"
                                required
                            />
                            <InputError :message="form.errors.language" class="mt-2" />
                        </div>

                        <div>
                            <InputLabel for="lang_proficiency" value="Proficiency" />
                            <select
                                id="lang_proficiency"
                                v-model="form.proficiency"
                                class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm"
                                required
                            >
                                <option v-for="level in proficiencyLevels" :key="level.value" :value="level.value">
                                    {{ level.name }}
                                </option>
                            </select>
                            <InputError :message="form.errors.proficiency" class="mt-2" />
                        </div>
                    </div>

                    <div class="flex items-center gap-4">
                        <PrimaryButton :disabled="form.processing">
                            <PlusIcon v-if="!editingLanguageId" class="w-4 h-4 mr-2" />
                            {{ editingLanguageId ? 'Update Language' : 'Save Language' }}
                        </PrimaryButton>
                        <SecondaryButton v-if="editingLanguageId" type="button" @click="cancelEdit">
                            Cancel
                        </SecondaryButton>
                        <Transition
                            enter-active-class="transition ease-in-out"
                            enter-from-class="opacity-0"
                            leave-active-class="transition ease-in-out"
                            leave-to-class="opacity-0"
                        >
                            <p v-if="form.recentlySuccessful" class="text-sm text-gray-600 dark:text-gray-400">Saved.</p>
                        </Transition>
                    </div>
                </form>

                <div class="mt-6 space-y-4">
                    <h3 class="text-md font-medium text-gray-900 dark:text-gray-100">
                        Your Languages
                    </h3>
                    <div v-if="languageList.length === 0" class="text-center text-gray-500 dark:text-gray-400 p-4 border-dashed border-2 border-gray-300 dark:border-gray-700 rounded-lg">
                        No languages added yet.
                    </div>
                    
                    <ul v-else class="divide-y divide-gray-200 dark:divide-gray-700">
                        <li v-for="lang in languageList" :key="lang.id" class="py-4 flex justify-between items-center">
                            <div class="flex-1">
                                <h4 class="text-lg font-semibold text-gray-800 dark:text-gray-200">
                                    {{ lang.language }}
                                </h4>
                                <p class="text-sm text-gray-600 dark:text-gray-400">
                                    {{ formatProficiency(lang.proficiency) }}
                                </p>
                            </div>
                            <div class="flex-shrink-0 flex gap-2">
                                <SecondaryButton @click="editLanguage(lang)" class="!px-3 !py-2">
                                    <PencilIcon class="w-4 h-4" />
                                </SecondaryButton>
                                <DangerButton @click="deleteLanguage(lang.id)" class="!px-3 !py-2">
                                    <TrashIcon class="w-4 h-4" />
                                </DangerButton>
                            </div>
                        </li>
                    </ul>
                </div>
            </div>
        </section>
    </div>
</template>