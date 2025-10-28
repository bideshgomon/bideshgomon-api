<script setup>
// This component is very similar to DegreeLevels/Index.vue
// Added the 'code' field
import { ref, watch, computed } from 'vue';
import { Head, router, useForm } from '@inertiajs/vue3';
import debounce from 'lodash/debounce';

import AdminLayout from '@/Layouts/AdminLayout.vue';
import Pagination from '@/Components/Pagination.vue';
import TextInput from '@/Components/TextInput.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import DangerButton from '@/Components/DangerButton.vue';
import Checkbox from '@/Components/Checkbox.vue';
import Modal from '@/Components/Modal.vue';

const props = defineProps({
    languages: Object, // <-- Changed prop name
    filters: Object,
    languageToEdit: Object, // <-- Changed prop name
});

// --- State ---
const showCreateEditModal = ref(false);
const showDeleteModal = ref(false);
const itemToDelete = ref(null);

// Form
const form = useForm({
    id: null,
    name: '',
    code: '', // <-- Added code
    is_active: true,
});

const isEditMode = computed(() => form.id !== null);

// --- Search ---
const search = ref(props.filters.search || '');
watch(search, debounce((value) => {
    router.get(route('admin.languages.index'), { search: value }, { // <-- Changed route name
        preserveState: true,
        replace: true,
    });
}, 300));

// --- Modals ---
const openModal = (itemToEdit = null) => {
    if (itemToEdit) {
        router.get(route('admin.languages.index'), { edit_id: itemToEdit.id }, { // <-- Changed route name
            preserveState: true,
            preserveScroll: true,
            replace: true,
        });
    } else {
        form.reset();
        showCreateEditModal.value = true;
    }
};

const openDeleteModal = (item) => {
    itemToDelete.value = item;
    showDeleteModal.value = true;
};

const closeModal = () => {
    showCreateEditModal.value = false;
    showDeleteModal.value = false;
    itemToDelete.value = null;

    if (props.languageToEdit) { // <-- Changed prop name
         router.get(route('admin.languages.index'), { search: search.value }, { // <-- Changed route name
            preserveState: true,
            preserveScroll: true,
            replace: true,
        });
    }
};

// --- Watcher for Edit Data ---
watch(() => props.languageToEdit, (newItem) => { // <-- Changed prop name
    if (newItem) {
        form.id = newItem.id;
        form.name = newItem.name;
        form.code = newItem.code; // <-- Set code
        form.is_active = newItem.is_active;
        showCreateEditModal.value = true;
    }
});

// --- Actions ---
const submitForm = () => {
    if (isEditMode.value) {
        form.put(route('admin.languages.update', form.id), { // <-- Changed route name
            preserveScroll: true,
            onSuccess: () => closeModal(),
        });
    } else {
        form.post(route('admin.languages.store'), { // <-- Changed route name
            preserveScroll: true,
            onSuccess: () => closeModal(),
        });
    }
};

const deleteItem = () => {
    if (itemToDelete.value) {
        router.delete(route('admin.languages.destroy', itemToDelete.value.id), { // <-- Changed route name
            preserveScroll: true,
            onSuccess: () => closeModal(),
        });
    }
};

</script>

<template>
    <Head title="Manage Languages" />

    <AdminLayout>
        <div class="p-4 sm:p-8 bg-white dark:bg-gray-800 shadow sm:rounded-lg">
            <section>
                <header class="flex flex-col sm:flex-row justify-between sm:items-center mb-6 gap-4">
                    <div>
                        <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                            Languages
                        </h2>
                        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                            Manage languages spoken by users (e.g., English, Spanish).
                        </p>
                    </div>
                    <div class="flex flex-col sm:flex-row sm:items-center gap-3 w-full sm:w-auto">
                         <TextInput
                            id="search"
                            type="text"
                            class="block w-full sm:w-56"
                            v-model="search"
                            placeholder="Search by name or code..."
                         />
                         <PrimaryButton @click="openModal()" class="w-full sm:w-auto justify-center">
                            Add Language
                         </PrimaryButton>
                    </div>
                </header>

                <div class="flow-root">
                     <div class="-mx-4 -my-2 overflow-x-auto sm:-mx-6 lg:-mx-8">
                        <div class="inline-block min-w-full py-2 align-middle sm:px-6 lg:px-8">
                            <table class="min-w-full divide-y divide-gray-300 dark:divide-gray-700">
                                <thead>
                                    <tr>
                                        <th scope="col" class="py-3.5 pl-4 pr-3 text-left text-sm font-semibold text-gray-900 dark:text-gray-100 sm:pl-0">Name</th>
                                        <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900 dark:text-gray-100">Code</th> <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900 dark:text-gray-100">Status</th>
                                        <th scope="col" class="relative py-3.5 pl-3 pr-4 sm:pr-0"><span class="sr-only">Actions</span></th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200 dark:divide-gray-800">
                                    <tr v-if="languages.data.length === 0"> <td colspan="4" class="py-4 text-center text-sm text-gray-500 dark:text-gray-400">No languages found.</td> </tr>
                                    <tr v-for="lang in languages.data" :key="lang.id"> <td class="py-4 pl-4 pr-3 text-sm font-medium text-gray-900 dark:text-gray-100 sm:pl-0">{{ lang.name }}</td>
                                        <td class="px-3 py-4 text-sm text-gray-500 dark:text-gray-400">{{ lang.code }}</td> <td class="px-3 py-4 text-sm">
                                             <span :class="lang.is_active ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300' : 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300'" class="inline-flex items-center rounded-md px-2 py-1 text-xs font-medium ring-1 ring-inset ring-opacity-20">
                                                {{ lang.is_active ? 'Active' : 'Inactive' }}
                                            </span>
                                        </td>
                                        <td class="relative py-4 pl-3 pr-4 text-right text-sm font-medium sm:pr-0 space-x-3 whitespace-nowrap">
                                            <button @click="openModal(lang)" class="text-brand-primary hover:text-opacity-80">Edit</button>
                                            <button @click="openDeleteModal(lang)" class="text-red-600 hover:text-red-800 dark:text-red-400 dark:hover:text-red-300">Delete</button>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <Pagination class="mt-6" :links="languages.links" /> </section>
        </div>

        <Modal :show="showCreateEditModal" @close="closeModal">
             <div class="p-6 bg-white dark:bg-gray-800">
                <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                    {{ isEditMode ? 'Edit Language' : 'Add New Language' }}
                </h2>
                 <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                     Enter the name and code (e.g., 'en' for English) for the language.
                </p>

                <form @submit.prevent="submitForm" class="mt-6 space-y-6">
                    <div>
                        <InputLabel for="name" value="Name *" />
                        <TextInput id="name" type="text" class="mt-1 block w-full" v-model="form.name" required autofocus />
                        <InputError class="mt-2" :message="form.errors.name" />
                    </div>

                    <div> <InputLabel for="code" value="Code *" />
                        <TextInput id="code" type="text" class="mt-1 block w-full" v-model="form.code" required placeholder="e.g., en, es, fr" />
                        <InputError class="mt-2" :message="form.errors.code" />
                    </div>

                    <div class="block">
                        <label class="flex items-center">
                            <Checkbox name="is_active" v-model:checked="form.is_active" />
                            <span class="ms-2 text-sm text-gray-600 dark:text-gray-400">Active</span>
                        </label>
                         <InputError class="mt-2" :message="form.errors.is_active" />
                    </div>

                    <div class="flex items-center justify-end gap-4">
                        <SecondaryButton @click="closeModal">Cancel</SecondaryButton>
                        <PrimaryButton :disabled="form.processing">{{ form.processing ? 'Saving...' : (isEditMode ? 'Update' : 'Save') }}</PrimaryButton>
                    </div>
                </form>
            </div>
        </Modal>

        <Modal :show="showDeleteModal" @close="closeModal">
             <div class="p-6 bg-white dark:bg-gray-800">
                <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                    Delete Language
                </h2>
                <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                    Are you sure you want to delete <strong>{{ itemToDelete?.name }}</strong>? This action cannot be undone.
                </p>

                <div class="mt-6 flex justify-end space-x-4">
                     <SecondaryButton @click="closeModal">Cancel</SecondaryButton>
                     <DangerButton @click="deleteItem" :disabled="form.processing">Delete</DangerButton>
                </div>
            </div>
        </Modal>

    </AdminLayout>
</template>