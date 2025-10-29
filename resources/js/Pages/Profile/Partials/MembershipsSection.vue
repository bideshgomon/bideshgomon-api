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
import TextareaInput from '@/Components/TextareaInput.vue';
import InputError from '@/Components/InputError.vue';

// --- State Management ---
const membershipList = ref([]);
const isLoading = ref(true);
const showModal = ref(false);
const isEditMode = ref(false);
const currentMembershipId = ref(null);

// --- Form Definition ---
const form = useForm({
    organization_name: '',
    position_held: '', // e.g., 'Member', 'Committee Chair'
    start_date: '',
    end_date: '', // Nullable
    description: '', // Optional details
});

// --- API Interaction ---
const fetchMemberships = async () => {
    isLoading.value = true;
    try {
        const response = await axios.get(route('profile.memberships.index')); // API route
        membershipList.value = response.data.data || [];
    } catch (error) {
        console.error("Error fetching memberships:", error);
    } finally {
        isLoading.value = false;
    }
};

// --- Modal Controls ---
const openAddModal = () => {
    form.reset();
    isEditMode.value = false;
    currentMembershipId.value = null;
    showModal.value = true;
};

const openEditModal = (membership) => {
    form.organization_name = membership.organization_name;
    form.position_held = membership.position_held;
    form.start_date = membership.start_date; // Assuming YYYY-MM-DD
    form.end_date = membership.end_date;
    form.description = membership.description;

    isEditMode.value = true;
    currentMembershipId.value = membership.id;
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
            fetchMemberships(); // Re-fetch list
        },
        onError: () => {}
    };

    if (isEditMode.value) {
        form.put(route('profile.memberships.update', currentMembershipId.value), options);
    } else {
        form.post(route('profile.memberships.store'), options);
    }
};

// --- Delete ---
const deleteForm = useForm({});
const confirmDelete = (membership) => {
    if (window.confirm(`Are you sure you want to delete the membership for "${membership.organization_name}"?`)) {
        deleteForm.delete(route('profile.memberships.destroy', membership.id), {
            preserveScroll: true,
            onSuccess: () => fetchMemberships(), // Re-fetch list
            onError: () => {},
        });
    }
};

// --- Lifecycle Hook ---
onMounted(() => {
    fetchMemberships();
});
</script>

<template>
    <section class="space-y-6">
        <header>
            <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">Professional Memberships</h2>
            <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                List any professional organizations or associations you are a part of.
            </p>
        </header>

        <div v-if="isLoading" class="text-sm text-gray-500 dark:text-gray-400">Loading memberships...</div>

        <div v-else-if="membershipList.length === 0" class="text-sm text-gray-500 dark:text-gray-400">
            No memberships added yet.
        </div>

        <ul v-else class="space-y-4">
            <li v-for="mem in membershipList" :key="mem.id" class="p-4 border border-gray-200 dark:border-gray-700 rounded-md flex justify-between items-start">
                <div>
                    <h3 class="font-semibold text-gray-900 dark:text-gray-100">{{ mem.organization_name }}</h3>
                    <p class="text-sm text-gray-700 dark:text-gray-300">{{ mem.position_held }}</p>
                    <p class="text-xs text-gray-500 dark:text-gray-400">
                        {{ mem.start_date }} - {{ mem.end_date ? mem.end_date : 'Present' }}
                    </p>
                    <p v-if="mem.description" class="mt-2 text-sm text-gray-600 dark:text-gray-400 whitespace-pre-line">{{ mem.description }}</p>
                </div>
                <div class="flex-shrink-0 space-x-2">
                    <SecondaryButton @click="openEditModal(mem)">Edit</SecondaryButton>
                    <DangerButton @click="confirmDelete(mem)">Delete</DangerButton>
                </div>
            </li>
        </ul>

        <PrimaryButton @click="openAddModal" :disabled="isLoading">Add Membership</PrimaryButton>

        <Modal :show="showModal" @close="closeModal">
            <div class="p-6 bg-white dark:bg-gray-800">
                <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                    {{ isEditMode ? 'Edit Membership' : 'Add New Membership' }}
                </h2>

                <form @submit.prevent="submit" class="mt-6 space-y-6">
                    <div>
                        <InputLabel for="organization_name" value="Organization Name" />
                        <TextInput id="organization_name" type="text" class="mt-1 block w-full" v-model="form.organization_name" required />
                        <InputError class="mt-2" :message="form.errors.organization_name" />
                    </div>

                    <div>
                        <InputLabel for="position_held" value="Position Held (e.g., Member)" />
                        <TextInput id="position_held" type="text" class="mt-1 block w-full" v-model="form.position_held" />
                        <InputError class="mt-2" :message="form.errors.position_held" />
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                        <div>
                            <InputLabel for="mem_start_date" value="Start Date" />
                            <TextInput id="mem_start_date" type="date" class="mt-1 block w-full" v-model="form.start_date" required />
                            <InputError class="mt-2" :message="form.errors.start_date" />
                        </div>
                        <div>
                            <InputLabel for="mem_end_date" value="End Date (Leave blank if current)" />
                            <TextInput id="mem_end_date" type="date" class="mt-1 block w-full" v-model="form.end_date" />
                            <InputError class="mt-2" :message="form.errors.end_date" />
                        </div>
                    </div>

                    <div>
                        <InputLabel for="mem_description" value="Description (Optional)" />
                        <TextareaInput id="mem_description" class="mt-1 block w-full" v-model="form.description" rows="3" />
                        <InputError class="mt-2" :message="form.errors.description" />
                    </div>


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