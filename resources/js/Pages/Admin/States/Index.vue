<script setup>
import { ref, watch, computed } from 'vue';
import { Head, Link, router, useForm } from '@inertiajs/vue3';
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
    states: Object,      // Paginated states
    countries: Array,    // Full list of countries for dropdown
    filters: Object,     // Search filters
    stateToEdit: Object, // State object for editing
});

// --- State Management ---
const showCreateModal = ref(false);
const showEditModal = ref(false);
const showDeleteModal = ref(false);
const stateToDelete = ref(null);

// Form state using useForm
const form = useForm({
    id: null,
    name: '',
    country_id: '', // <-- Add country_id
    is_active: true,
});

// Computed property to determine if we are in "edit" mode
const isEditMode = computed(() => form.id !== null);

// --- Search Logic ---
const search = ref(props.filters.search || '');
watch(search, debounce((value) => {
    router.get(route('admin.states.index'), { search: value }, {
        preserveState: true,
        replace: true,
    });
}, 300));

// --- Modal Functions ---
const openCreateModal = () => {
    form.reset();
    showCreateModal.value = true;
};

const openEditModal = (stateId) => {
    router.get(route('admin.states.index'), { edit_id: stateId }, {
        preserveState: true,
        preserveScroll: true,
        replace: true,
    });
};

const openDeleteModal = (state) => {
    stateToDelete.value = state;
    showDeleteModal.value = true;
};

const closeModal = () => {
    showCreateModal.value = false;
    showEditModal.value = false;
    showDeleteModal.value = false;
    stateToDelete.value = null;

    if (props.stateToEdit) {
         router.get(route('admin.states.index'), { search: search.value }, {
            preserveState: true,
            preserveScroll: true,
            replace: true,
        });
    }
};

// --- Watcher for Edit Data ---
watch(() => props.stateToEdit, (newState) => {
    if (newState) {
        form.id = newState.id;
        form.name = newState.name;
        form.country_id = newState.country_id; // <-- Set country_id
        form.is_active = newState.is_active;
        showEditModal.value = true;
    }
});

// --- Form Submission ---
const submitForm = () => {
    if (isEditMode.value) {
        // UPDATE
        form.put(route('admin.states.update', form.id), {
            preserveScroll: true,
            onSuccess: () => {
                closeModal();
                form.reset();
            },
        });
    } else {
        // CREATE
        form.post(route('admin.states.store'), {
            preserveScroll: true,
            onSuccess: () => {
                closeModal();
                form.reset();
            },
        });
    }
};

// --- Delete Logic ---
const deleteState = () => {
    if (stateToDelete.value) {
        router.delete(route('admin.states.destroy', stateToDelete.value.id), {
            preserveScroll: true,
            onSuccess: () => closeModal(),
        });
    }
};

</script>

<template>
    <Head title="Manage States" />

    <AdminLayout>
        <div class="p-4 sm:p-8 bg-white dark:bg-gray-800 shadow sm:rounded-lg">
            <section>
                <header class="flex flex-col sm:flex-row justify-between sm:items-center mb-6 gap-4">
                    <div>
                        <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                            States
                        </h2>
                        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                            Manage states within countries.
                        </p>
                    </div>

                    <div class="flex flex-col sm:flex-row sm:items-center gap-3 w-full sm:w-auto">
                         <TextInput
                            id="search"
                            type="text"
                            class="block w-full sm:w-56"
                            v-model="search"
                            placeholder="Search by state or country..."
                         />
                         <div class="flex items-center gap-3 w-full sm:w-auto">
                            <PrimaryButton @click="openCreateModal" class="w-full sm:w-auto justify-center">
                                Add State
                            </PrimaryButton>
                         </div>
                    </div>
                </header>

                <div class="flow-root">
                    <div class="-mx-4 -my-2 overflow-x-auto sm:-mx-6 lg:-mx-8">
                        <div class="inline-block min-w-full py-2 align-middle sm:px-6 lg:px-8">
                            <table class="min-w-full divide-y divide-gray-300 dark:divide-gray-700">
                                <thead>
                                    <tr>
                                        <th scope="col" class="py-3.5 pl-4 pr-3 text-left text-sm font-semibold text-gray-900 dark:text-gray-100 sm:pl-0">State Name</th>
                                        <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900 dark:text-gray-100">Country</th>
                                        <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900 dark:text-gray-100">Status</th>
                                        <th scope="col" class="relative py-3.5 pl-3 pr-4 sm:pr-0"><span class="sr-only">Actions</span></th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200 dark:divide-gray-800">
                                    <tr v-if="states.data.length === 0">
                                        <td colspan="4" class="py-4 text-center text-sm text-gray-500 dark:text-gray-400">No states found.</td>
                                    </tr>
                                    <tr v-for="state in states.data" :key="state.id">
                                        <td class="py-4 pl-4 pr-3 text-sm font-medium text-gray-900 dark:text-gray-100 sm:pl-0">{{ state.name }}</td>
                                        <td class="px-3 py-4 text-sm text-gray-500 dark:text-gray-400">{{ state.country ? state.country.name : '—' }}</td>
                                        <td class="px-3 py-4 text-sm">
                                            <span :class="state.is_active ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300' : 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300'" class="inline-flex items-center rounded-md px-2 py-1 text-xs font-medium ring-1 ring-inset ring-opacity-20">
                                                {{ state.is_active ? 'Active' : 'Inactive' }}
                                            </span>
                                        </td>
                                        <td class="relative py-4 pl-3 pr-4 text-right text-sm font-medium sm:pr-0 space-x-3 whitespace-nowrap">
                                            <button @click="openEditModal(state.id)" class="text-brand-primary hover:text-opacity-80">Edit</button>
                                            <button @click="openDeleteModal(state)" class="text-red-600 hover:text-red-800 dark:text-red-400 dark:hover:text-red-300">Delete</button>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <Pagination class="mt-6" :links="states.links" />
            </section>
        </div>

        <Modal :show="showCreateModal || showEditModal" @close="closeModal">
             <div class="p-6 bg-white dark:bg-gray-800">
                <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                    {{ isEditMode ? 'Edit State' : 'Add New State' }}
                </h2>
                <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                     {{ isEditMode ? 'Update the details for this state.' : 'Enter the details for the new state.' }}
                </p>

                <form @submit.prevent="submitForm" class="mt-6 space-y-6">
                    <div>
                        <InputLabel for="country" value="Country" />
                        <select
                            id="country"
                            v-model="form.country_id"
                            class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm"
                            required
                        >
                            <option value="" disabled>Select a country</option>
                            <option v-for="country in countries" :key="country.id" :value="country.id">
                                {{ country.name }}
                            </option>
                        </select>
                        <InputError class="mt-2" :message="form.errors.country_id" />
                    </div>

                    <div>
                        <InputLabel for="name" value="State Name" />
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
                    Delete State
                </h2>
                <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                    Are you sure you want to delete <strong>{{ stateToDelete?.name }}</strong>? This action cannot be undone and may affect related cities.
                </p>

                <div class="mt-6 flex justify-end space-x-4">
                     <SecondaryButton @click="closeModal">Cancel</SecondaryButton>
                     <DangerButton @click="deleteState" :disabled="form.processing">Delete</DangerButton>
                </div>
            </div>
        </Modal>

    </AdminLayout>
</template>