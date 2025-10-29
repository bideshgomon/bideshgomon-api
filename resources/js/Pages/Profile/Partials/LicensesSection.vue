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
const licenseList = ref([]);
const isLoading = ref(true);
const showModal = ref(false);
const isEditMode = ref(false);
const currentLicenseId = ref(null);

// --- Form Definition ---
const form = useForm({
    name: '',
    issuing_organization: '',
    issue_date: '',
    expiration_date: '', // Make sure this is nullable in DB/Model if not required
    credential_id: '', // Optional
    credential_url: '', // Optional
});

// --- API Interaction ---
const fetchLicenses = async () => {
    isLoading.value = true;
    try {
        const response = await axios.get(route('profile.licenses.index')); // API route
        licenseList.value = response.data.data || [];
    } catch (error) {
        console.error("Error fetching licenses:", error);
    } finally {
        isLoading.value = false;
    }
};

// --- Modal Controls ---
const openAddModal = () => {
    form.reset();
    isEditMode.value = false;
    currentLicenseId.value = null;
    showModal.value = true;
};

const openEditModal = (license) => {
    form.name = license.name;
    form.issuing_organization = license.issuing_organization;
    form.issue_date = license.issue_date; // Assuming YYYY-MM-DD
    form.expiration_date = license.expiration_date;
    form.credential_id = license.credential_id;
    form.credential_url = license.credential_url;

    isEditMode.value = true;
    currentLicenseId.value = license.id;
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
            fetchLicenses(); // Re-fetch list
        },
        onError: () => {}
    };

    if (isEditMode.value) {
        form.put(route('profile.licenses.update', currentLicenseId.value), options);
    } else {
        form.post(route('profile.licenses.store'), options);
    }
};

// --- Delete ---
const deleteForm = useForm({});
const confirmDelete = (license) => {
    if (window.confirm(`Are you sure you want to delete the license "${license.name}"?`)) {
        deleteForm.delete(route('profile.licenses.destroy', license.id), {
            preserveScroll: true,
            onSuccess: () => fetchLicenses(), // Re-fetch list
            onError: () => {},
        });
    }
};

// --- Lifecycle Hook ---
onMounted(() => {
    fetchLicenses();
});
</script>

<template>
    <section class="space-y-6">
        <header>
            <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">Licenses & Certifications</h2>
            <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                Add relevant licenses or certifications you hold.
            </p>
        </header>

        <div v-if="isLoading" class="text-sm text-gray-500 dark:text-gray-400">Loading licenses...</div>

        <div v-else-if="licenseList.length === 0" class="text-sm text-gray-500 dark:text-gray-400">
            No licenses or certifications added yet.
        </div>

        <ul v-else class="space-y-4">
            <li v-for="lic in licenseList" :key="lic.id" class="p-4 border border-gray-200 dark:border-gray-700 rounded-md flex justify-between items-start">
                <div>
                    <h3 class="font-semibold text-gray-900 dark:text-gray-100">{{ lic.name }}</h3>
                    <p class="text-sm text-gray-700 dark:text-gray-300">Issued by: {{ lic.issuing_organization }}</p>
                    <p class="text-xs text-gray-500 dark:text-gray-400">
                        Issued: {{ lic.issue_date }}
                        <span v-if="lic.expiration_date"> | Expires: {{ lic.expiration_date }}</span>
                    </p>
                    <p v-if="lic.credential_id" class="text-xs text-gray-500 dark:text-gray-400">ID: {{ lic.credential_id }}</p>
                    <a v-if="lic.credential_url" :href="lic.credential_url" target="_blank" rel="noopener noreferrer" class="text-xs text-brand-primary hover:underline dark:text-blue-400">
                        View Credential
                    </a>
                </div>
                <div class="flex-shrink-0 space-x-2">
                    <SecondaryButton @click="openEditModal(lic)">Edit</SecondaryButton>
                    <DangerButton @click="confirmDelete(lic)">Delete</DangerButton>
                </div>
            </li>
        </ul>

        <PrimaryButton @click="openAddModal" :disabled="isLoading">Add License/Certification</PrimaryButton>

        <Modal :show="showModal" @close="closeModal">
            <div class="p-6 bg-white dark:bg-gray-800">
                <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                    {{ isEditMode ? 'Edit License/Certification' : 'Add New License/Certification' }}
                </h2>

                <form @submit.prevent="submit" class="mt-6 space-y-6">
                    <div>
                        <InputLabel for="lic_name" value="Name of License/Certification" />
                        <TextInput id="lic_name" type="text" class="mt-1 block w-full" v-model="form.name" required />
                        <InputError class="mt-2" :message="form.errors.name" />
                    </div>

                    <div>
                        <InputLabel for="issuing_organization" value="Issuing Organization" />
                        <TextInput id="issuing_organization" type="text" class="mt-1 block w-full" v-model="form.issuing_organization" required />
                        <InputError class="mt-2" :message="form.errors.issuing_organization" />
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                        <div>
                            <InputLabel for="issue_date" value="Issue Date" />
                            <TextInput id="issue_date" type="date" class="mt-1 block w-full" v-model="form.issue_date" required />
                            <InputError class="mt-2" :message="form.errors.issue_date" />
                        </div>
                        <div>
                            <InputLabel for="expiration_date" value="Expiration Date (Optional)" />
                            <TextInput id="expiration_date" type="date" class="mt-1 block w-full" v-model="form.expiration_date" />
                            <InputError class="mt-2" :message="form.errors.expiration_date" />
                        </div>
                    </div>

                    <div>
                        <InputLabel for="credential_id" value="Credential ID (Optional)" />
                        <TextInput id="credential_id" type="text" class="mt-1 block w-full" v-model="form.credential_id" />
                        <InputError class="mt-2" :message="form.errors.credential_id" />
                    </div>

                     <div>
                        <InputLabel for="credential_url" value="Credential URL (Optional)" />
                        <TextInput id="credential_url" type="url" class="mt-1 block w-full" v-model="form.credential_url" placeholder="https://..." />
                        <InputError class="mt-2" :message="form.errors.credential_url" />
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