<script setup>
import { ref } from 'vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import InputError from '@/Components/InputError.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import DangerButton from '@/Components/DangerButton.vue';
import Modal from '@/Components/Modal.vue';
import { useForm, usePage } from '@inertiajs/vue3';

// Props received from Edit.vue
const props = defineProps({
    userLanguages: Array, // Array of user's current languages
    languages: Array,    // Array of all available languages (id, name)
});

const page = usePage();
const confirmingDeletion = ref(false);
const languageToDelete = ref(null);
const editingLanguage = ref(null);

const form = useForm({
    id: null, // For editing
    language_id: '',
    proficiency: '',
    test_score: '', // Optional
});

// Function to open modal for adding/editing
const openEditModal = (language = null) => {
    if (language) {
        // Editing existing
        editingLanguage.value = language;
        form.id = language.id;
        form.language_id = language.language_id;
        form.proficiency = language.proficiency;
        form.test_score = language.test_score;
    } else {
        // Adding new
        editingLanguage.value = {}; // Indicate adding new
        form.reset(); // Clear form fields
        form.id = null;
    }
};

// Function to close modal
const closeModal = () => {
    editingLanguage.value = null;
    form.reset();
    form.clearErrors();
};

// Function to save (add or update)
const saveLanguage = () => {
    const url = form.id 
        ? route('api.profile.languages.update', form.id) // Assuming API route exists
        : route('api.profile.languages.store');           // Assuming API route exists
    
    const method = form.id ? 'patch' : 'post';

    form.submit(method, url, {
        preserveScroll: true,
        onSuccess: () => closeModal(),
        onError: () => { /* Handle error, maybe keep modal open */ },
    });
};

// --- Deletion ---
const confirmDeletion = (language) => {
    languageToDelete.value = language;
    confirmingDeletion.value = true;
};

const closeDeleteModal = () => {
    confirmingDeletion.value = false;
    languageToDelete.value = null;
};

const deleteLanguage = () => {
    if (!languageToDelete.value) return;

    // Assuming API route exists: route('api.profile.languages.destroy', languageToDelete.value.id)
    useForm({}).delete(route('api.profile.languages.destroy', languageToDelete.value.id), {
        preserveScroll: true,
        onSuccess: () => closeDeleteModal(),
        onError: () => closeDeleteModal(), // Close modal even on error for simplicity
    });
};

// Helper to get language name from ID
const getLanguageName = (id) => {
    const lang = props.languages.find(l => l.id === id);
    return lang ? lang.name : 'Unknown';
};

</script>

<template>
    <section>
        <header>
            <h2 class="text-lg font-medium text-gray-900">Language Proficiency</h2>
            <p class="mt-1 text-sm text-gray-600">
                Add or update languages you speak and your proficiency level.
            </p>
        </header>

        <div class="mt-6 space-y-6">
            <div v-if="userLanguages && userLanguages.length > 0" class="space-y-3">
                <div v-for="lang in userLanguages" :key="lang.id" class="p-4 border rounded-md flex justify-between items-center">
                    <div>
                        <p class="font-semibold">{{ getLanguageName(lang.language_id) }}</p>
                        <p class="text-sm text-gray-600">Proficiency: {{ lang.proficiency }}</p>
                        <p v-if="lang.test_score" class="text-sm text-gray-600">Test Score: {{ lang.test_score }}</p>
                    </div>
                    <div>
                        <SecondaryButton @click="openEditModal(lang)" class="mr-2">Edit</SecondaryButton>
                        <DangerButton @click="confirmDeletion(lang)">Delete</DangerButton>
                    </div>
                </div>
            </div>
             <p v-else class="text-sm text-gray-500">No languages added yet.</p>

            <PrimaryButton @click="openEditModal(null)">Add Language</PrimaryButton>

            <Modal :show="editingLanguage !== null" @close="closeModal">
                <div class="p-6">
                    <h2 class="text-lg font-medium text-gray-900">
                        {{ form.id ? 'Edit Language' : 'Add New Language' }}
                    </h2>

                    <form @submit.prevent="saveLanguage" class="mt-6 space-y-4">
                        <div>
                            <InputLabel for="language_id" value="Language" />
                            <select
                                id="language_id"
                                v-model="form.language_id"
                                class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                                required
                            >
                                <option value="" disabled>Select a language</option>
                                <option v-for="langOption in languages" :key="langOption.id" :value="langOption.id">
                                    {{ langOption.name }}
                                </option>
                            </select>
                            <InputError class="mt-2" :message="form.errors.language_id" />
                        </div>

                         <div>
                            <InputLabel for="proficiency" value="Proficiency Level" />
                             <select
                                id="proficiency"
                                v-model="form.proficiency"
                                class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                                required
                            >
                                <option value="" disabled>Select proficiency</option>
                                <option value="Beginner">Beginner</option>
                                <option value="Intermediate">Intermediate</option>
                                <option value="Advanced">Advanced</option>
                                <option value="Fluent">Fluent</option>
                                <option value="Native">Native</option>
                            </select>
                            <InputError class="mt-2" :message="form.errors.proficiency" />
                        </div>

                        <div>
                            <InputLabel for="test_score" value="Test Score (Optional)" />
                            <TextInput
                                id="test_score"
                                type="text"
                                class="mt-1 block w-full"
                                v-model="form.test_score"
                                placeholder="e.g., IELTS 7.5, TOEFL 100"
                            />
                            <InputError class="mt-2" :message="form.errors.test_score" />
                        </div>


                        <div class="mt-6 flex justify-end">
                            <SecondaryButton @click="closeModal"> Cancel </SecondaryButton>
                            <PrimaryButton
                                class="ms-3"
                                :class="{ 'opacity-25': form.processing }"
                                :disabled="form.processing"
                                type="submit"
                            >
                                {{ form.id ? 'Update' : 'Save' }}
                            </PrimaryButton>
                        </div>
                    </form>
                </div>
            </Modal>

            <Modal :show="confirmingDeletion" @close="closeDeleteModal">
                 <div class="p-6">
                    <h2 class="text-lg font-medium text-gray-900">
                        Are you sure you want to delete this language entry?
                    </h2>
                     <p class="mt-1 text-sm text-gray-600" v-if="languageToDelete">
                       Language: {{ getLanguageName(languageToDelete.language_id) }} - Proficiency: {{ languageToDelete.proficiency }}
                    </p>

                    <div class="mt-6 flex justify-end">
                        <SecondaryButton @click="closeDeleteModal"> Cancel </SecondaryButton>
                        <DangerButton
                            class="ms-3"
                            @click="deleteLanguage"
                        >
                            Delete Entry
                        </DangerButton>
                    </div>
                </div>
            </Modal>

        </div>
    </section>
</template>