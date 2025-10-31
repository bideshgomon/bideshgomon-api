<script setup>
import { ref } from 'vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import Modal from '@/Components/Modal.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import DangerButton from '@/Components/DangerButton.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import InputError from '@/Components/InputError.vue';
import Pagination from '@/Components/Pagination.vue';
import { PencilIcon, TrashIcon, PlusIcon } from '@heroicons/vue/24/outline';

// Set layout
defineOptions({
    layout: AdminLayout
});

// Props received from StatePageController
const props = defineProps({
    states: Object,
    countries: Array, // <-- Array of countries for the dropdown
});

// Modal state
const showingModal = ref(false);
const modalMode = ref('create'); // 'create' or 'edit'

// Form state
const form = useForm({
    id: null,
    name: '',
    country_id: null, // <-- Added country_id
});

// Functions
const openCreateModal = () => {
    form.reset();
    modalMode.value = 'create';
    showingModal.value = true;
};

const openEditModal = (state) => {
    form.id = state.id;
    form.name = state.name;
    form.country_id = state.country_id; // <-- Set country_id for editing
    
    modalMode.value = 'edit';
    showingModal.value = true;
};

const closeModal = () => {
    showingModal.value = false;
    form.reset();
    form.clearErrors();
};

const submitForm = () => {
    if (modalMode.value === 'create') {
        // Use API route to store
        form.post(route('api.admin.states.store'), {
            preserveScroll: true,
            onSuccess: () => closeModal(),
            onError: () => console.log('Error creating state'),
        });
    } else {
        // Use API route to update
        form.put(route('api.admin.states.update', form.id), {
            preserveScroll: true,
            onSuccess: () => closeModal(),
            onError: () => console.log('Error updating state'),
        });
    }
};

const deleteState = (id) => {
    if (confirm('Are you sure you want to delete this state?')) {
        useForm({}).delete(route('api.admin.states.destroy', id), {
            preserveScroll: true,
            onSuccess: () => console.log('State deleted'),
        });
    }
};
</script>

<template>
    <Head title="Manage States" />

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="flex justify-between items-center mb-6">
                <h1 class="text-2xl font-semibold text-gray-800 dark:text-white">Manage States</h1>
                <PrimaryButton @click="openCreateModal">
                    <PlusIcon class="h-5 w-5 mr-2" />
                    Create State
                </PrimaryButton>
            </div>

            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-gray-700">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">State Name</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Country</th>
                                <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                            <tr v-if="states.data.length === 0">
                                <td colspan="3" class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400 text-center">
                                    No states found.
                                </td>
                            </tr>
                            <tr v-for="state in states.data" :key="state.id">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900 dark:text-white">{{ state.name }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                    {{ state.country ? state.country.name : 'N/A' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium space-x-2">
                                    <button @click="openEditModal(state)" class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-300" title="Edit">
                                        <PencilIcon class="h-5 w-5" />
                                    </button>
                                    <button @click="deleteState(state.id)" class="text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-300" title="Delete">
                                        <TrashIcon class="h-5 w-5" />
                                    </button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <Pagination :links="states.links" class="mt-6" />
        </div>
    </div>

    <Modal :show="showingModal" @close="closeModal">
        <div class="p-6">
            <h2 class="text-lg font-medium text-gray-900 dark:text-white">
                {{ modalMode === 'create' ? 'Create New State' : 'Edit State' }}
            </h2>

            <form @submit.prevent="submitForm" class="mt-6 space-y-4">
                <div>
                    <InputLabel for="country_id" value="Country" />
                    <select 
                        id="country_id" 
                        v-model="form.country_id" 
                        class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm"
                        required
                    >
                        <option :value="null" disabled>Select a country</option>
                        <option v-for="country in countries" :key="country.id" :value="country.id">{{ country.name }}</option>
                    </select>
                    <InputError :message="form.errors.country_id" class="mt-2" />
                </div>

                <div>
                    <InputLabel for="name" value="State Name" />
                    <TextInput id="name" v-model="form.name" type="text" class="mt-1 block w-full" required />
                    <InputError :message="form.errors.name" class="mt-2" />
                </div>

                <div class="flex justify-end pt-4 space-x-3">
                    <SecondaryButton @click="closeModal">Cancel</SecondaryButton>
                    <PrimaryButton :disabled="form.processing">
                        {{ form.processing ? 'Saving...' : (modalMode === 'create' ? 'Create' : 'Save Changes') }}
                    </PrimaryButton>
                </div>
            </form>
        </div>
    </Modal>
</template>