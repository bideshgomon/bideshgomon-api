<script setup>
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
    degreeLevels: Object,
    filters: Object,
    degreeLevelToEdit: Object,
});

// --- State ---
const showCreateEditModal = ref(false);
const showDeleteModal = ref(false);
const itemToDelete = ref(null);

// Form
const form = useForm({
    id: null,
    name: '',
    is_active: true, // Default to active for new entries
});

const isEditMode = computed(() => form.id !== null);

// --- Search ---
const search = ref(props.filters.search || '');
watch(search, debounce((value) => {
    router.get(route('admin.degree-levels.index'), { search: value }, {
        preserveState: true,
        replace: true,
    });
}, 300));

// --- Modals ---
const openModal = (itemToEdit = null) => {
    if (itemToEdit) {
        // Fetch full data via Inertia partial reload
        router.get(route('admin.degree-levels.index'), { edit_id: itemToEdit.id }, {
            preserveState: true,
            preserveScroll: true,
            replace: true,
        });
    } else {
        // Explicitly reset the form to its default create state
        form.reset({
            id: null,
            name: '',
            is_active: true // Ensure reset goes back to active
        });
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

    // Clear edit prop from URL if needed
    if (props.degreeLevelToEdit) {
         router.get(route('admin.degree-levels.index'), { search: search.value }, {
            preserveState: true,
            preserveScroll: true,
            replace: true,
        });
    }
};

// --- Watcher for Edit Data ---
watch(() => props.degreeLevelToEdit, (newItem) => {
    if (newItem) {
        form.id = newItem.id;
        form.name = newItem.name;
        form.is_active = newItem.is_active;
        showCreateEditModal.value = true;
    }
});

// --- Actions ---
const submitForm = () => {
    if (isEditMode.value) {
        form.put(route('admin.degree-levels.update', form.id), {
            preserveScroll: true,
            onSuccess: () => closeModal(),
        });
    } else {
        form.post(route('admin.degree-levels.store'), {
            preserveScroll: true,
            onSuccess: () => closeModal(),
        });
    }
};

const deleteItem = () => {
    if (itemToDelete.value) {
        router.delete(route('admin.degree-levels.destroy', itemToDelete.value.id), {
            preserveScroll: true,
            onSuccess: () => closeModal(),
        });
    }
};

</script>

<template>
    <Head title="Manage Degree Levels" />

    <AdminLayout>
        <div class="p-4 sm:p-8 bg-white dark:bg-gray-800 shadow sm:rounded-lg">
            <section>
                <header class="flex flex-col sm:flex-row justify-between sm:items-center mb-6 gap-4">
                    <div>
                        <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                            Degree Levels
                        </h2>
                        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                            Manage academic degree levels (e.g., Bachelor's, Master's).
                        </p>
                    </div>
                    <div class="flex flex-col sm:flex-row sm:items-center gap-3 w-full sm:w-auto">
                         <TextInput
                            id="search"
                            type="text"
                            class="block w-full sm:w-56"
                            v-model="search"
                            placeholder="Search by name..."
                         />
                         <PrimaryButton @click="openModal()" class="w-full sm:w-auto justify-center">
                            Add Degree Level
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
                                        <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900 dark:text-gray-100">Status</th>
                                        <th scope="col" class="relative py-3.5 pl-3 pr-4 sm:pr-0"><span class="sr-only">Actions</span></th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200 dark:divide-gray-800">
                                    <tr v-if="degreeLevels.data.length === 0">
                                        <td colspan="3" class="py-4 text-center text-sm text-gray-500 dark:text-gray-400">No degree levels found.</td>
                                    </tr>
                                    <tr v-for="level in degreeLevels.data" :key="level.id">
                                        <td class="py-4 pl-4 pr-3 text-sm font-medium text-gray-900 dark:text-gray-100 sm:pl-0">{{ level.name }}</td>
                                        <td class="px-3 py-4 text-sm">
                                             <span :class="level.is_active ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300' : 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300'" class="inline-flex items-center rounded-md px-2 py-1 text-xs font-medium ring-1 ring-inset ring-opacity-20">
                                                {{ level.is_active ? 'Active' : 'Inactive' }}
                                            </span>
                                        </td>
                                        <td class="relative py-4 pl-3 pr-4 text-right text-sm font-medium sm:pr-0 space-x-3 whitespace-nowrap">
                                            <button @click="openModal(level)" class="text-brand-primary hover:text-opacity-80">Edit</button>
                                            <button @click="openDeleteModal(level)" class="text-red-600 hover:text-red-800 dark:text-red-400 dark:hover:text-red-300">Delete</button>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <Pagination class="mt-6" :links="degreeLevels.links" />
            </section>
        </div>

        <Modal :show="showCreateEditModal" @close="closeModal">
             <div class="p-6 bg-white dark:bg-gray-800">
                <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                    {{ isEditMode ? 'Edit Degree Level' : 'Add New Degree Level' }}
                </h2>
                 <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                     Enter the name for the degree level.
                </p>

                <form @submit.prevent="submitForm" class="mt-6 space-y-6">
                    <div>
                        <InputLabel for="name" value="Name *" />
                        <TextInput id="name" type="text" class="mt-1 block w-full" v-model="form.name" required autofocus />
                        <InputError class="mt-2" :message="form.errors.name" />
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
                    Delete Degree Level
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