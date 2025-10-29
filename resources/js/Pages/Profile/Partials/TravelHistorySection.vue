<script setup>
import { ref, onMounted } from 'vue';
import { useForm } from '@inertiajs/vue3';
import axios from 'axios';

// Import components
import Modal from '@/Components/Modal.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import DangerButton from '@/Components/DangerButton.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import InputError from '@/Components/InputError.vue';

// --- State Management ---
const travelList = ref([]);
const isLoading = ref(true);
const showModal = ref(false);
const isEditMode = ref(false);
const currentTravelId = ref(null);

// --- Form Definition ---
const form = useForm({
    country: '',
    city: '', // Nullable
    start_date: '',
    end_date: '', // Nullable
    purpose_of_visit: '', // e.g., 'Tourism', 'Work', 'Study'
});

// --- API Interaction ---
// We need API endpoints for travel history CRUD. Let's assume they follow the pattern:
// profile.travel-history.index, .store, .update, .destroy

const fetchTravelHistory = async () => {
    isLoading.value = true;
    try {
        // **IMPORTANT:** Replace with your actual API route if different
        const response = await axios.get(route('profile.travel-history.index'));
        travelList.value = response.data.data || [];
    } catch (error) {
        // Handle cases where the route might not exist yet
        if (error.response && error.response.status === 404) {
             console.warn("Travel History API endpoint not found. Please create it.");
             // Optionally set an error message for the user
        } else {
            console.error("Error fetching travel history:", error);
        }
        travelList.value = []; // Ensure list is empty on error
    } finally {
        isLoading.value = false;
    }
};

// --- Modal Controls ---
const openAddModal = () => {
    form.reset();
    isEditMode.value = false;
    currentTravelId.value = null;
    showModal.value = true;
};

const openEditModal = (travel) => {
    form.country = travel.country;
    form.city = travel.city;
    form.start_date = travel.start_date; // Assuming YYYY-MM-DD
    form.end_date = travel.end_date;
    form.purpose_of_visit = travel.purpose_of_visit;

    isEditMode.value = true;
    currentTravelId.value = travel.id;
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
            fetchTravelHistory(); // Re-fetch list
        },
        onError: (errors) => {
            // Log errors for debugging if the API isn't ready
             console.error("Error saving travel history:", errors);
        }
    };

    try {
        if (isEditMode.value) {
            // **IMPORTANT:** Replace with your actual API route if different
            form.put(route('profile.travel-history.update', currentTravelId.value), options);
        } else {
             // **IMPORTANT:** Replace with your actual API route if different
            form.post(route('profile.travel-history.store'), options);
        }
    } catch (e) {
         // Catch errors if routes don't exist
         console.error("Route likely missing for travel history:", e);
         // Display a user-friendly message in the modal if desired
         form.setError('general', 'Could not save travel history. Feature might be unavailable.');
    }
};

// --- Delete ---
const deleteForm = useForm({});
const confirmDelete = (travel) => {
    if (window.confirm(`Are you sure you want to delete the travel record for "${travel.country}"?`)) {
         try {
             // **IMPORTANT:** Replace with your actual API route if different
            deleteForm.delete(route('profile.travel-history.destroy', travel.id), {
                preserveScroll: true,
                onSuccess: () => fetchTravelHistory(), // Re-fetch list
                onError: (errors) => {
                     console.error("Error deleting travel history:", errors);
                     alert('Failed to delete travel history. Please try again.');
                },
            });
         } catch(e) {
             console.error("Route likely missing for travel history delete:", e);
             alert('Could not delete travel history. Feature might be unavailable.');
         }
    }
};

// --- Lifecycle Hook ---
onMounted(() => {
    fetchTravelHistory();
});
</script>

<template>
    <section class="space-y-6">
        <header>
            <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">Travel History</h2>
            <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                List countries you have previously visited (optional but helpful).
            </p>
        </header>

        <div v-if="isLoading" class="text-sm text-gray-500 dark:text-gray-400">Loading travel history...</div>

        <div v-else-if="travelList.length === 0" class="text-sm text-gray-500 dark:text-gray-400">
            No travel history added yet.
        </div>

        <ul v-else class="space-y-4">
            <li v-for="tvl in travelList" :key="tvl.id" class="p-4 border border-gray-200 dark:border-gray-700 rounded-md flex justify-between items-start">
                <div>
                    <h3 class="font-semibold text-gray-900 dark:text-gray-100">{{ tvl.country }} <span v-if="tvl.city"> ({{ tvl.city }})</span></h3>
                    <p class="text-sm text-gray-700 dark:text-gray-300">Purpose: {{ tvl.purpose_of_visit }}</p>
                    <p class="text-xs text-gray-500 dark:text-gray-400">
                        {{ tvl.start_date }} - {{ tvl.end_date ? tvl.end_date : 'N/A' }}
                    </p>
                </div>
                <div class="flex-shrink-0 space-x-2">
                    <SecondaryButton @click="openEditModal(tvl)">Edit</SecondaryButton>
                    <DangerButton @click="confirmDelete(tvl)">Delete</DangerButton>
                </div>
            </li>
        </ul>

        <PrimaryButton @click="openAddModal" :disabled="isLoading">Add Travel Record</PrimaryButton>

        <Modal :show="showModal" @close="closeModal">
            <div class="p-6 bg-white dark:bg-gray-800">
                <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                    {{ isEditMode ? 'Edit Travel Record' : 'Add New Travel Record' }}
                </h2>

                <form @submit.prevent="submit" class="mt-6 space-y-6">
                    <div>
                        <InputLabel for="travel_country" value="Country Visited" />
                        <TextInput id="travel_country" type="text" class="mt-1 block w-full" v-model="form.country" required />
                        <InputError class="mt-2" :message="form.errors.country" />
                    </div>

                    <div>
                        <InputLabel for="travel_city" value="City (Optional)" />
                        <TextInput id="travel_city" type="text" class="mt-1 block w-full" v-model="form.city" />
                        <InputError class="mt-2" :message="form.errors.city" />
                    </div>

                     <div>
                        <InputLabel for="purpose_of_visit" value="Purpose of Visit" />
                        <TextInput id="purpose_of_visit" type="text" class="mt-1 block w-full" v-model="form.purpose_of_visit" placeholder="e.g., Tourism, Work, Study" required />
                        <InputError class="mt-2" :message="form.errors.purpose_of_visit" />
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                        <div>
                            <InputLabel for="travel_start_date" value="Start Date of Visit" />
                            <TextInput id="travel_start_date" type="date" class="mt-1 block w-full" v-model="form.start_date" required />
                            <InputError class="mt-2" :message="form.errors.start_date" />
                        </div>
                        <div>
                            <InputLabel for="travel_end_date" value="End Date of Visit (Optional)" />
                            <TextInput id="travel_end_date" type="date" class="mt-1 block w-full" v-model="form.end_date" />
                            <InputError class="mt-2" :message="form.errors.end_date" />
                        </div>
                    </div>

                     <InputError class="mt-2" :message="form.errors.general" />

                    <div class="flex justify-end gap-4">
                        <SecondaryButton @click="closeModal"> Cancel </SecondaryButton>
                        <PrimaryButton :disabled="form.processing">
                            {{ isEditMode ? 'Update Record' : 'Save Record' }}
                        </PrimaryButton>
                    </div>
                </form>
            </div>
        </Modal>
    </section>
</template>