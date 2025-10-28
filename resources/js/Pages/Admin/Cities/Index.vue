<script setup>
import { ref, watch, computed } from 'vue';
import { Head, Link, router, useForm } from '@inertiajs/vue3';
import axios from 'axios';
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
    cities: Object,
    countries: Array,
    // states prop removed
    filters: Object,
    cityToEdit: Object,
});

// --- State Management ---
const showCreateModal = ref(false);
const showEditModal = ref(false);
const showDeleteModal = ref(false);
const cityToDelete = ref(null);
const statesForModal = ref([]);
const isLoadingStates = ref(false);
const selectedCountryHasStates = ref(false); // New flag

// Form state
const form = useForm({
    id: null,
    name: '',
    selected_country_id: '', // Separate property for the country dropdown in modal
    state_id: '',
    country_id: '', // Will be set based on state_id or selected_country_id
    is_active: true,
});

const isEditMode = computed(() => form.id !== null);

// --- Search Logic ---
const search = ref(props.filters.search || '');
watch(search, debounce((value) => {
    router.get(route('admin.cities.index'), { search: value }, {
        preserveState: true,
        replace: true,
    });
}, 300));

// --- Fetch States for Modal ---
const fetchStates = async (countryId) => {
    form.state_id = ''; // Reset state selection when country changes
    statesForModal.value = [];
    selectedCountryHasStates.value = false; // Reset flag

    if (!countryId) {
        return;
    }

    isLoadingStates.value = true;
    try {
        const response = await axios.get(route('admin.cities.getStates', { country_id: countryId }));
        statesForModal.value = response.data;
        selectedCountryHasStates.value = statesForModal.value.length > 0; // Set flag based on response
    } catch (error) {
        console.error("Error fetching states:", error);
    } finally {
        isLoadingStates.value = false;
    }
};

// Watch for country selection changes in the modal
watch(() => form.selected_country_id, (newCountryId) => {
    fetchStates(newCountryId);
});

// --- Modal Functions ---
const openCreateModal = () => {
    form.reset();
    statesForModal.value = [];
    selectedCountryHasStates.value = false;
    showCreateModal.value = true;
};

const openEditModal = (cityId) => {
    router.get(route('admin.cities.index'), { edit_id: cityId }, {
        preserveState: true,
        preserveScroll: true,
        replace: true,
    });
};

const openDeleteModal = (city) => {
    cityToDelete.value = city;
    showDeleteModal.value = true;
};

const closeModal = () => {
    showCreateModal.value = false;
    showEditModal.value = false;
    showDeleteModal.value = false;
    cityToDelete.value = null;
    statesForModal.value = [];
    selectedCountryHasStates.value = false;

    if (props.cityToEdit) {
         router.get(route('admin.cities.index'), { search: search.value }, {
            preserveState: true,
            preserveScroll: true,
            replace: true,
        });
    }
};

// --- Watcher for Edit Data ---
watch(() => props.cityToEdit, async (newCity) => {
    if (newCity) {
        form.id = newCity.id;
        form.name = newCity.name;
        form.is_active = newCity.is_active;

        if (newCity.state_id && newCity.state) { // City belongs to a State
            form.state_id = newCity.state_id;
            form.selected_country_id = newCity.state.country_id; // Set country from state
            form.country_id = null; // Belongs to state, not directly to country
            await fetchStates(form.selected_country_id); // Fetch states for dropdown consistency
        } else if (newCity.country_id) { // City belongs directly to a Country
            form.state_id = null;
            form.selected_country_id = newCity.country_id; // Set country directly
            form.country_id = newCity.country_id;
            await fetchStates(form.selected_country_id); // Fetch states (might be none)
        } else {
             console.error("City loaded without state or direct country link.");
             // Reset form fields maybe?
             form.state_id = '';
             form.selected_country_id = '';
             form.country_id = '';
        }
        showEditModal.value = true;
    }
});

// --- Form Submission ---
const submitForm = () => {
    // Determine the correct parent ID to send
    if (form.state_id) {
        form.country_id = null; // Ensure country_id is null if state_id is set
    } else {
        form.country_id = form.selected_country_id; // Use the selected country if no state
        form.state_id = null; // Ensure state_id is null
    }

    const dataToSend = {
        name: form.name,
        state_id: form.state_id,
        country_id: form.country_id,
        is_active: form.is_active,
    };

    if (isEditMode.value) {
        router.put(route('admin.cities.update', form.id), dataToSend, {
            preserveScroll: true,
            onSuccess: () => closeModal(),
            onError: (errors) => { form.errors = errors; }
        });
    } else {
        router.post(route('admin.cities.store'), dataToSend, {
            preserveScroll: true,
            onSuccess: () => closeModal(),
             onError: (errors) => { form.errors = errors; }
        });
    }
};

// --- Delete Logic ---
const deleteCity = () => {
    if (cityToDelete.value) {
        router.delete(route('admin.cities.destroy', cityToDelete.value.id), {
            preserveScroll: true,
            onSuccess: () => closeModal(),
        });
    }
};

// Helper to get display country name in table
const getCountryName = (city) => {
    if (city.state && city.state.country) {
        return city.state.country.name;
    }
    if (city.country) {
        return city.country.name;
    }
    return '—';
};

</script>

<template>
    <Head title="Manage Cities" />

    <AdminLayout>
        <div class="p-4 sm:p-8 bg-white dark:bg-gray-800 shadow sm:rounded-lg">
            <section>
                <header class="flex flex-col sm:flex-row justify-between sm:items-center mb-6 gap-4">
                    <div>
                        <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                            Cities
                        </h2>
                        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                            Manage cities within states or countries.
                        </p>
                    </div>

                    <div class="flex flex-col sm:flex-row sm:items-center gap-3 w-full sm:w-auto">
                         <TextInput
                            id="search"
                            type="text"
                            class="block w-full sm:w-56"
                            v-model="search"
                            placeholder="Search by city, state, country..."
                         />
                         <div class="flex items-center gap-3 w-full sm:w-auto">
                            <PrimaryButton @click="openCreateModal" class="w-full sm:w-auto justify-center">
                                Add City
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
                                        <th scope="col" class="py-3.5 pl-4 pr-3 text-left text-sm font-semibold text-gray-900 dark:text-gray-100 sm:pl-0">City Name</th>
                                        <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900 dark:text-gray-100">State</th>
                                        <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900 dark:text-gray-100">Country</th>
                                        <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900 dark:text-gray-100">Status</th>
                                        <th scope="col" class="relative py-3.5 pl-3 pr-4 sm:pr-0"><span class="sr-only">Actions</span></th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200 dark:divide-gray-800">
                                    <tr v-if="cities.data.length === 0">
                                        <td colspan="5" class="py-4 text-center text-sm text-gray-500 dark:text-gray-400">No cities found.</td>
                                    </tr>
                                    <tr v-for="city in cities.data" :key="city.id">
                                        <td class="py-4 pl-4 pr-3 text-sm font-medium text-gray-900 dark:text-gray-100 sm:pl-0">{{ city.name }}</td>
                                        <td class="px-3 py-4 text-sm text-gray-500 dark:text-gray-400">{{ city.state ? city.state.name : 'N/A' }}</td>
                                        <td class="px-3 py-4 text-sm text-gray-500 dark:text-gray-400">{{ getCountryName(city) }}</td>
                                        <td class="px-3 py-4 text-sm">
                                            <span :class="city.is_active ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300' : 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300'" class="inline-flex items-center rounded-md px-2 py-1 text-xs font-medium ring-1 ring-inset ring-opacity-20">
                                                {{ city.is_active ? 'Active' : 'Inactive' }}
                                            </span>
                                        </td>
                                        <td class="relative py-4 pl-3 pr-4 text-right text-sm font-medium sm:pr-0 space-x-3 whitespace-nowrap">
                                            <button @click="openEditModal(city.id)" class="text-brand-primary hover:text-opacity-80">Edit</button>
                                            <button @click="openDeleteModal(city)" class="text-red-600 hover:text-red-800 dark:text-red-400 dark:hover:text-red-300">Delete</button>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <Pagination class="mt-6" :links="cities.links" />
            </section>
        </div>

        <Modal :show="showCreateModal || showEditModal" @close="closeModal">
             <div class="p-6 bg-white dark:bg-gray-800">
                <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                    {{ isEditMode ? 'Edit City' : 'Add New City' }}
                </h2>
                <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                     Select the country and state (if applicable), then enter the city name.
                </p>

                <form @submit.prevent="submitForm" class="mt-6 space-y-6">
                    <div>
                        <InputLabel for="country_modal" value="Country *" />
                        <select
                            id="country_modal"
                            v-model="form.selected_country_id"
                            class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm"
                            required
                        >
                            <option value="" disabled>Select a country</option>
                            <option v-for="country in countries" :key="country.id" :value="country.id">
                                {{ country.name }}
                            </option>
                        </select>
                         <InputError class="mt-2" :message="form.errors.country_id || form.errors.selected_country_id" />
                    </div>

                    <div v-if="form.selected_country_id"> <InputLabel for="state_modal" :value="'State' + (selectedCountryHasStates ? ' *' : ' (Optional/N/A)')" />
                        <select
                            id="state_modal"
                            v-model="form.state_id"
                            class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm"
                            :required="selectedCountryHasStates" :disabled="isLoadingStates || !selectedCountryHasStates"
                        >
                            <option value="" :disabled="selectedCountryHasStates">{{ isLoadingStates ? 'Loading...' : (selectedCountryHasStates ? 'Select a state' : 'No states available') }}</option>
                            <option v-for="state in statesForModal" :key="state.id" :value="state.id">
                                {{ state.name }}
                            </option>
                        </select>
                        <InputError class="mt-2" :message="form.errors.state_id" />
                    </div>

                    <div>
                        <InputLabel for="name" value="City Name *" />
                        <TextInput
                            id="name"
                            type="text"
                            class="mt-1 block w-full"
                            v-model="form.name"
                            required
                            autofocus
                            :disabled="!form.selected_country_id"
                            placeholder="Select country first"
                         />
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
                        <PrimaryButton
                            :disabled="form.processing || isLoadingStates || !form.selected_country_id || (selectedCountryHasStates && !form.state_id)">
                                {{ form.processing ? 'Saving...' : (isEditMode ? 'Update' : 'Save') }}
                        </PrimaryButton>
                    </div>
                </form>
            </div>
        </Modal>

        <Modal :show="showDeleteModal" @close="closeModal">
             <div class="p-6 bg-white dark:bg-gray-800">
                <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                    Delete City
                </h2>
                <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                    Are you sure you want to delete <strong>{{ cityToDelete?.name }}</strong>? This action cannot be undone.
                </p>

                <div class="mt-6 flex justify-end space-x-4">
                     <SecondaryButton @click="closeModal">Cancel</SecondaryButton>
                     <DangerButton @click="deleteCity" :disabled="form.processing">Delete</DangerButton>
                </div>
            </div>
        </Modal>

    </AdminLayout>
</template>