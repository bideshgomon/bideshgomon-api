<script setup>
import { ref, onMounted, computed } from 'vue';
import { useForm } from '@inertiajs/vue3';
import axios from 'axios';

// Import components
import Modal from '@/Components/Modal.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import DangerButton from '@/Components/DangerButton.vue';
import InputLabel from '@/Components/InputLabel.vue';
import SelectInput from '@/Components/SelectInput.vue'; // Assuming you have or create this
import InputError from '@/Components/InputError.vue';

// --- State Management ---
const languageList = ref([]); // User's added languages [{ id: user_lang_id, language: { id: lang_id, name: 'English' }, proficiency: 'Fluent' }]
const allLanguages = ref([]); // All available languages [{ id: 1, name: 'English' }]
const proficiencyLevels = ref([ // Define proficiency levels
    { id: 'Beginner', name: 'Beginner' },
    { id: 'Conversational', name: 'Conversational' },
    { id: 'Fluent', name: 'Fluent' },
    { id: 'Native', name: 'Native / Bilingual' },
]);
const isLoading = ref(true);
const showModal = ref(false);
const isEditMode = ref(false);
const currentLanguageEntryId = ref(null); // ID of the user_languages entry being edited

// --- Form Definition ---
const form = useForm({
    language_id: null, // ID of the selected language from the `languages` table
    proficiency: null, // Selected proficiency level (e.g., 'Fluent')
});

// --- API Interaction ---
const fetchData = async () => {
    isLoading.value = true;
    try {
        // Fetch all available languages
        const prebuiltData = await axios.get(route('api.prebuilt-data'));
        allLanguages.value = prebuiltData.data.languages || [];

        // Fetch user's current languages
        const userLangResponse = await axios.get(route('profile.languages.index'));
        // The API returns UserLanguage models, often nested with the Language model
        languageList.value = userLangResponse.data.data || [];

    } catch (error) {
        console.error("Error fetching languages data:", error);
    } finally {
        isLoading.value = false;
    }
};

// --- Modal Controls ---
const openAddModal = () => {
    form.reset();
    isEditMode.value = false;
    currentLanguageEntryId.value = null;
    showModal.value = true;
};

const openEditModal = (langEntry) => {
    form.language_id = langEntry.language_id; // Set the ID of the language
    form.proficiency = langEntry.proficiency; // Set the proficiency
    isEditMode.value = true;
    currentLanguageEntryId.value = langEntry.id; // This is the ID of the row in user_languages table
    showModal.value = true;
};

const closeModal = () => {
    showModal.value = false;
    form.reset();
};

// --- Form Submission ---
const submit = () => {
    const options = {
        preserveScroll: true,
        onSuccess: () => {
            closeModal();
            fetchData(); // Re-fetch list
        },
        onError: () => {}
    };

    if (isEditMode.value) {
        form.put(route('profile.languages.update', currentLanguageEntryId.value), options);
    } else {
        form.post(route('profile.languages.store'), options);
    }
};

// --- Delete ---
const deleteForm = useForm({});
const confirmDelete = (langEntry) => {
    if (window.confirm(`Are you sure you want to remove ${langEntry.language?.name}?`)) {
        deleteForm.delete(route('profile.languages.destroy', langEntry.id), {
            preserveScroll: true,
            onSuccess: () => fetchData(), // Re-fetch list
            onError: () => {},
        });
    }
};

// --- Computed property for available languages in dropdown (excluding already added ones in Add mode) ---
const availableLanguagesToAdd = computed(() => {
    if (isEditMode.value) return allLanguages.value; // Show all when editing

    const addedLanguageIds = new Set(languageList.value.map(l => l.language_id));
    return allLanguages.value.filter(lang => !addedLanguageIds.has(lang.id));
});


// --- Lifecycle Hook ---
onMounted(() => {
    fetchData();
});
</script>

<template>
    <section class="space-y-6">
        <header>
            <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">Languages</h2>
            <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                Add languages you speak and your proficiency level.
            </p>
        </header>

        <div v-if="isLoading" class="text-sm text-gray-500 dark:text-gray-400">Loading languages...</div>

        <div v-else-if="languageList.length === 0" class="text-sm text-gray-500 dark:text-gray-400">
            No languages added yet.
        </div>

        <ul v-else class="space-y-4">
            <li v-for="lang in languageList" :key="lang.id" class="p-4 border border-gray-200 dark:border-gray-700 rounded-md flex justify-between items-start">
                <div>
                    <h3 class="font-semibold text-gray-900 dark:text-gray-100">{{ lang.language?.name }}</h3>
                    <p class="text-sm text-gray-600 dark:text-gray-400">{{ lang.proficiency }}</p>
                </div>
                <div class="flex-shrink-0 space-x-2">
                    <SecondaryButton @click="openEditModal(lang)">Edit</SecondaryButton>
                    <DangerButton @click="confirmDelete(lang)">Delete</DangerButton>
                </div>
            </li>
        </ul>

        <PrimaryButton @click="openAddModal" :disabled="isLoading">Add Language</PrimaryButton>

        <Modal :show="showModal" @close="closeModal">
            <div class="p-6 bg-white dark:bg-gray-800">
                <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                    {{ isEditMode ? 'Edit Language' : 'Add New Language' }}
                </h2>

                <form @submit.prevent="submit" class="mt-6 space-y-6">
                    <div>
                        <InputLabel for="language_id" value="Language" />
                        <SelectInput
                            id="language_id"
                            class="mt-1 block w-full"
                            v-model="form.language_id"
                            :options="availableLanguagesToAdd"
                            option-value="id"
                            option-label="name"
                            required
                            :disabled="isEditMode"
                        />
                         <InputError class="mt-2" :message="form.errors.language_id" />
                    </div>

                    <div>
                        <InputLabel for="proficiency" value="Proficiency Level" />
                        <SelectInput
                            id="proficiency"
                            class="mt-1 block w-full"
                            v-model="form.proficiency"
                            :options="proficiencyLevels"
                            option-value="id"
                            option-label="name"
                            required
                        />
                        <InputError class="mt-2" :message="form.errors.proficiency" />
                    </div>


                    <div class="flex justify-end gap-4">
                        <SecondaryButton @click="closeModal"> Cancel </SecondaryButton>
                        <PrimaryButton :disabled="form.processing">
                            {{ isEditMode ? 'Update Language' : 'Save Language' }}
                        </PrimaryButton>
                    </div>
                </form>
            </div>
        </Modal>
    </section>
</template>