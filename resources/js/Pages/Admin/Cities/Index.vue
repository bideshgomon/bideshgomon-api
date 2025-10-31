<script setup>
import { ref, computed } from 'vue';
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

// Props received from CityPageController
const props = defineProps({
    cities: Object,
    countries: Array, 
    states: Array, 
});

// Modal state
const showingModal = ref(false);
const modalMode = ref('create'); // 'create' or 'edit'

// Form state
const form = useForm({
    id: null,
    name: '',
    country_id: null, 
    state_id: null, // Optional
});

// --- DYNAMIC STATE DROPDOWN LOGIC ---
const filteredStates = computed(() => {
    if (!form.country_id) {
        return []; // No country selected, show no states
    }
    // Return only states that match the selected country
    return props.states.filter(state => state.country_id == form.country_id);
});

// Watch for country changes to reset state_id if it's no longer valid
watch(() => form.country_id, (newCountryId) => {
    if (modalMode.value === 'create' || !props.states.find(s => s.id === form.state_id && s.country_id === newCountryId)) {
        form.state_id = null;
    }
});
// --- END DYNAMIC LOGIC ---

// Functions
const openCreateModal = () => {
    form.reset();
    modalMode.value = 'create';
    showingModal.value = true;
};

const openEditModal = (city) => {
    form.id = city.id;
    form.name = city.name;
    form.country_id = city.country_id; 
    form.state_id = city.state_id; // Will be null if no state
    
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
        form.post(route('api.admin.cities.store'), {
            preserveScroll: true,
            onSuccess: () => closeModal(),
        });
    } else {
        form.put(route('api.admin.cities.update', form.id), {
            preserveScroll: true,
            onSuccess: () => closeModal(),
        });
    }
};

const deleteCity = (id) => {
    if (confirm('Are you sure you want to delete this city?')) {
        useForm({}).delete(route('api.admin.cities.destroy', id), {
            preserveScroll: true,
        });
    }
};
</script>

<template>
    <Head title="Manage Cities" />

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="flex justify-between items-center mb-6">
                <h1 class="text-2xl font-semibold text-gray-800 dark:text-white">Manage Cities</h1>
                <PrimaryButton @click="openCreateModal">
                    <PlusIcon class="h-5 w-5 mr-2" />
                    Create City
                </PrimaryButton>
            </div>

            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-gray-700">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">City Name</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">State</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Country</th>
                                <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                            <tr v-if="cities.data.length === 0">
                                <td colspan="4" class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400 text-center">
                                    No cities found.
                                </td>
                            </tr>
                            <tr v-for="city in cities.data" :key="city.id">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900 dark:text-white">{{ city.name }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                    {{ city.state ? city.state.name : 'N/A' }}
                                </td>
                                 <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                    {{ city.country ? city.country.name : 'N/A' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium space-x-2">
                                    <button @click="openEditModal(city)" class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-300" title="Edit">
                                        <PencilIcon class="h-5 w-5" />
                                    </button>
                                    <button @click="deleteCity(city.id)" class="text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-300" title="Delete">
                                        <TrashIcon class="h-5 w-5" />
                                    </button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <Pagination :links="cities.links" class="mt-6" />
        </div>
    </div>

    <Modal :show="showingModal" @close="closeModal">
        <div class="p-6">
            <h2 class="text-lg font-medium text-gray-900 dark:text-white">
                {{ modalMode === 'create' ? 'Create New City' : 'Edit City' }}
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
                    <InputLabel for="state_id" value="State (Optional)" />
                    <select 
                        id="state_id" 
                        v-model="form.state_id" 
                        class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm"
                        :disabled="!form.country_id || filteredStates.length === 0"
                    >
                        <option :value="null">Select a state (if applicable)</option>
                        <option v-for="state in filteredStates" :key="state.id" :value="state.id">{{ state.name }}</option>
                    </select>
                    <InputError :message="form.errors.state_id" class="mt-2" />
                </div>

                <div>
                    <InputLabel for="name" value="City Name" />
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