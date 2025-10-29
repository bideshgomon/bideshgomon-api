<script setup>
import { ref, onMounted } from 'vue';
import { useForm } from '@inertiajs/vue3';
import axios from 'axios';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import DangerButton from '@/Components/DangerButton.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import Checkbox from '@/Components/Checkbox.vue';
import InputError from '@/Components/InputError.vue';
import { ChevronUpIcon, ChevronDownIcon, PlusIcon, PencilIcon, TrashIcon } from '@heroicons/vue/24/solid';

// Collapsible state
const isOpen = ref(true);
const toggle = () => {
    isOpen.value = !isOpen.value;
};

// State
const membershipList = ref([]);
const editingMembershipId = ref(null);

const form = useForm({
    organization_name: '',
    role: '',
    start_date: '',
    end_date: '',
    is_current: false,
});

// Fetch data
const getMemberships = () => {
    axios.get(route('api.profile.memberships.index'))
        .then(response => {
            membershipList.value = response.data;
        })
        .catch(error => console.error("Error fetching memberships:", error));
};

// Submit form
const submitMembership = () => {
    if (form.is_current) {
        form.end_date = ''; // Clear end date
    }

    const url = editingMembershipId.value
        ? route('api.profile.memberships.update', editingMembershipId.value)
        : route('api.profile.memberships.store');
    
    const method = editingMembershipId.value ? 'put' : 'post';

    axios[method](url, form.data())
        .then(() => {
            form.reset();
            form.clearErrors();
            editingMembershipId.value = null;
            getMemberships(); // Refresh list
            form.recentlySuccessful = true;
            setTimeout(() => form.recentlySuccessful = false, 2000);
        })
        .catch(error => {
            if (error.response && error.response.status === 422) {
                form.setError(error.response.data.errors);
            } else {
                console.error("Error submitting form:", error);
            }
        });
};

// Start editing
const editMembership = (mem) => {
    editingMembershipId.value = mem.id;
    form.organization_name = mem.organization_name;
    form.role = mem.role;
    form.start_date = mem.start_date ? mem.start_date.substring(0, 10) : '';
    form.end_date = mem.end_date ? mem.end_date.substring(0, 10) : '';
    form.is_current = mem.is_current;
    form.clearErrors();
};

// Cancel editing
const cancelEdit = () => {
    editingMembershipId.value = null;
    form.reset();
    form.clearErrors();
};

// Delete item
const deleteMembership = (membershipId) => {
    if (!confirm("Are you sure you want to delete this membership?")) return;

    axios.delete(route('api.profile.memberships.destroy', membershipId))
        .then(() => {
            getMemberships();
            if (editingMembershipId.value === membershipId) {
                cancelEdit();
            }
        })
        .catch(error => {
            console.error("Error deleting membership:", error);
            alert("Failed to delete record.");
        });
};

onMounted(() => {
    getMemberships();
});

// Helper to format date
const formatDate = (dateString) => {
    if (!dateString) return 'Present';
    try {
        const date = new Date(dateString + 'T00:00:00Z'); // Assume UTC
        return date.toLocaleDateString(undefined, { timeZone: 'UTC', year: 'numeric', month: 'short' });
    } catch (e) {
        return 'Invalid Date';
    }
};

</script>

<template>
    <div class="p-4 sm:p-8 bg-white dark:bg-gray-800 shadow sm:rounded-lg">
        <section>
            <header @click="toggle" class="flex justify-between items-center cursor-pointer">
                <div>
                    <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                        Professional Memberships
                    </h2>
                    <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                        List your memberships in professional organizations.
                    </p>
                </div>
                <div>
                    <ChevronUpIcon v-if="isOpen" class="w-6 h-6 text-gray-500" />
                    <ChevronDownIcon v-else class="w-6 h-6 text-gray-500" />
                </div>
            </header>

            <div v-show="isOpen" class="mt-6 space-y-6">
                
                <form @submit.prevent="submitMembership" class="p-4 border border-gray-200 dark:border-gray-700 rounded-lg space-y-4">
                    <h3 class="text-md font-medium text-gray-900 dark:text-gray-100">
                        {{ editingMembershipId ? 'Update Membership' : 'Add New Membership' }}
                    </h3>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <InputLabel for="mem_org" value="Organization Name" />
                            <TextInput
                                id="mem_org"
                                v-model="form.organization_name"
                                type="text"
                                class="mt-1 block w-full"
                                placeholder="e.g., IEEE, BASIS"
                                required
                            />
                            <InputError :message="form.errors.organization_name" class="mt-2" />
                        </div>
                        <div>
                            <InputLabel for="mem_role" value="Your Role/Title" />
                            <TextInput
                                id="mem_role"
                                v-model="form.role"
                                type="text"
                                class="mt-1 block w-full"
                                placeholder="e.g., Member, Fellow"
                                required
                            />
                            <InputError :message="form.errors.role" class="mt-2" />
                        </div>
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                         <div>
                            <InputLabel for="mem_start_date" value="Start Date" />
                            <TextInput
                                id="mem_start_date"
                                v-model="form.start_date"
                                type="date"
                                class="mt-1 block w-full"
                                required
                            />
                            <InputError :message="form.errors.start_date" class="mt-2" />
                        </div>
                        <div>
                            <InputLabel for="mem_end_date" value="End Date" />
                            <TextInput
                                id="mem_end_date"
                                v-model="form.end_date"
                                type="date"
                                class="mt-1 block w-full"
                                :disabled="form.is_current"
                                :class="{ 'bg-gray-100 dark:bg-gray-800': form.is_current }"
                            />
                            <InputError :message="form.errors.end_date" class="mt-2" />
                        </div>
                    </div>
                    
                    <div class="block mt-4">
                        <label class="flex items-center">
                            <Checkbox v-model:checked="form.is_current" name="mem_is_current" />
                            <span class="ml-2 text-sm text-gray-600 dark:text-gray-400">I am currently a member</span>
                        </label>
                    </div>

                    <div class="flex items-center gap-4">
                        <PrimaryButton :disabled="form.processing">
                            <PlusIcon v-if="!editingMembershipId" class="w-4 h-4 mr-2" />
                            {{ editingMembershipId ? 'Update Membership' : 'Save Membership' }}
                        </PrimaryButton>
                        <SecondaryButton v-if="editingMembershipId" type="button" @click="cancelEdit">
                            Cancel
                        </SecondaryButton>
                        <Transition
                            enter-active-class="transition ease-in-out"
                            enter-from-class="opacity-0"
                            leave-active-class="transition ease-in-out"
                            leave-to-class="opacity-0"
                        >
                            <p v-if="form.recentlySuccessful" class="text-sm text-gray-600 dark:text-gray-400">Saved.</p>
                        </Transition>
                    </div>
                </form>

                <div class="mt-6 space-y-4">
                    <h3 class="text-md font-medium text-gray-900 dark:text-gray-100">
                        Your Memberships
                    </h3>
                    <div v-if="membershipList.length === 0" class="text-center text-gray-500 dark:text-gray-400 p-4 border-dashed border-2 border-gray-300 dark:border-gray-700 rounded-lg">
                        No memberships added yet.
                    </div>
                    
                    <ul v-else class="divide-y divide-gray-200 dark:divide-gray-700">
                        <li v-for="mem in membershipList" :key="mem.id" class="py-4 flex justify-between items-center">
                            <div class="flex-1">
                                <h4 class="text-lg font-semibold text-gray-800 dark:text-gray-200">
                                    {{ mem.organization_name }}
                                </h4>
                                <p class="text-sm text-gray-600 dark:text-gray-400">
                                    {{ mem.role }}
                                </p>
                                <p class="text-sm text-gray-500 dark:text-gray-500">
                                    {{ formatDate(mem.start_date) }} - {{ formatDate(mem.end_date) }}
                                </p>
                            </div>
                            <div class="flex-shrink-0 flex gap-2">
                                <SecondaryButton @click="editMembership(mem)" class="!px-3 !py-2">
                                    <PencilIcon class="w-4 h-4" />
                                </SecondaryButton>
                                <DangerButton @click="deleteMembership(mem.id)" class="!px-3 !py-2">
                                    <TrashIcon class="w-4 h-4" />
                                </DangerButton>
                            </div>
                        </li>
                    </ul>
                </div>
            </div>
        </section>
    </div>
</template>