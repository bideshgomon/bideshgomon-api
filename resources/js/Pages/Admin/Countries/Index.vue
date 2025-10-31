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

// Props received from CountryPageController
const props = defineProps({
    countries: Object,
});

// Modal state
const showingModal = ref(false);
const modalMode = ref('create'); // 'create' or 'edit'

// Form state
const form = useForm({
    id: null,
    name: '',
    iso2: '',
    iso3: '',
    phone_code: '',
    capital: '',
    currency: '',
});

// Functions
const openCreateModal = () => {
    form.reset();
    modalMode.value = 'create';
    showingModal.value = true;
};

const openEditModal = (country) => {
    form.id = country.id;
    form.name = country.name;
    form.iso2 = country.iso2;
    form.iso3 = country.iso3;
    form.phone_code = country.phone_code;
    form.capital = country.capital;
    form.currency = country.currency;
    
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
        form.post(route('api.admin.countries.store'), {
            preserveScroll: true,
            onSuccess: () => closeModal(),
            onError: () => console.log('Error creating country'),
        });
    } else {
        // Use API route to update
        form.put(route('api.admin.countries.update', form.id), {
            preserveScroll: true,
            onSuccess: () => closeModal(),
            onError: () => console.log('Error updating country'),
        });
    }
};

const deleteCountry = (id) => {
    if (confirm('Are you sure you want to delete this country?')) {
        useForm({}).delete(route('api.admin.countries.destroy', id), {
            preserveScroll: true,
            onSuccess: () => console.log('Country deleted'),
        });
    }
};
</script>

<template>
    <Head title="Manage Countries" />

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="flex justify-between items-center mb-6">
                <h1 class="text-2xl font-semibold text-gray-800 dark:text-white">Manage Countries</h1>
                <PrimaryButton @click="openCreateModal">
                    <PlusIcon class="h-5 w-5 mr-2" />
                    Create Country
                </PrimaryButton>
            </div>

            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-gray-700">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Name</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">ISO2</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Phone Code</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Capital</th>
                                <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                            <tr v-if="countries.data.length === 0">
                                <td colspan="5" class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400 text-center">
                                    No countries found.
                                </td>
                            </tr>
                            <tr v-for="country in countries.data" :key="country.id">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900 dark:text-white">{{ country.name }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                    {{ country.iso2 }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                    {{ country.phone_code }}
                                </td>
                                 <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                    {{ country.capital }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium space-x-2">
                                    <button @click="openEditModal(country)" class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-300" title="Edit">
                                        <PencilIcon class="h-5 w-5" />
                                    </button>
                                    <button @click="deleteCountry(country.id)" class="text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-300" title="Delete">
                                        <TrashIcon class="h-5 w-5" />
                                    </button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <Pagination :links="countries.links" class="mt-6" />
        </div>
    </div>

    <Modal :show="showingModal" @close="closeModal">
        <div class="p-6">
            <h2 class="text-lg font-medium text-gray-900 dark:text-white">
                {{ modalMode === 'create' ? 'Create New Country' : 'Edit Country' }}
            </h2>

            <form @submit.prevent="submitForm" class="mt-6 space-y-4">
                <div>
                    <InputLabel for="name" value="Country Name" />
                    <TextInput id="name" v-model="form.name" type="text" class="mt-1 block w-full" required />
                    <InputError :message="form.errors.name" class="mt-2" />
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <InputLabel for="iso2" value="ISO2 Code" />
                        <TextInput id="iso2" v-model="form.iso2" type="text" class="mt-1 block w-full" />
                        <InputError :message="form.errors.iso2" class="mt-2" />
                    </div>
                    <div>
                        <InputLabel for="iso3" value="ISO3 Code" />
                        <TextInput id="iso3" v-model="form.iso3" type="text" class="mt-1 block w-full" />
                        <InputError :message="form.errors.iso3" class="mt-2" />
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <InputLabel for="phone_code" value="Phone Code" />
                        <TextInput id="phone_code" v-model="form.phone_code" type="text" class="mt-1 block w-full" />
                        <InputError :message="form.errors.phone_code" class="mt-2" />
                    </div>
                     <div>
                        <InputLabel for="capital" value="Capital City" />
                        <TextInput id="capital" v-model="form.capital" type="text" class="mt-1 block w-full" />
                        <InputError :message="form.errors.capital" class="mt-2" />
                    </div>
                     <div>
                        <InputLabel for="currency" value="Currency" />
                        <TextInput id="currency" v-model="form.currency" type="text" class="mt-1 block w-full" />
                        <InputError :message="form.errors.currency" class="mt-2" />
                    </div>
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