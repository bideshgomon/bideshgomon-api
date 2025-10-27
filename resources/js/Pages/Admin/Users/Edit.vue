<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import Checkbox from '@/Components/Checkbox.vue';
import DangerButton from '@/Components/DangerButton.vue';
import { Head, Link, useForm, router } from '@inertiajs/vue3';
import { ref } from 'vue';

const props = defineProps({
    user: Object, // Passed from UserPageController's edit method
    roles: Array, // Passed from UserPageController's edit method
});

// To show a success message
const successMessage = ref('');

const form = useForm({
    _method: 'PUT', // <-- Important for Laravel update
    name: props.user.name,
    email: props.user.email,
    phone: props.user.phone || '', // Handle null phone
    role_id: props.user.role_id,
    is_active: props.user.is_active,
    password: '', // Leave empty unless changing
    password_confirmation: '',
});

const submitUpdate = () => {
    successMessage.value = ''; // Clear previous message

    // --- UPDATED ---
    // Point to the correct API update route
    form.post(route('api.admin.users.update', props.user.id), {
        onSuccess: () => {
             form.reset('password', 'password_confirmation'); // Clear password fields
             successMessage.value = 'User updated successfully!';
        },
        onError: () => {
             // Errors are automatically handled by form.errors
        },
        onFinish: () => {
            form.reset('password', 'password_confirmation');
        },
        preserveScroll: true,
    });
};

const deleteUser = () => {
    successMessage.value = ''; // Clear message

    if (confirm(`Are you sure you want to delete this user: ${props.user.name}? This action cannot be undone.`)) {
        
        // --- UPDATED ---
        // Point to the correct API delete route
        router.delete(route('api.admin.users.destroy', props.user.id), {
            preserveScroll: true,
            onSuccess: () => {
                // On successful delete, redirect to the user list
                router.visit(route('admin.users.index'), {
                    // You could add a flash message here if your HandleInertiaRequests middleware supports it
                });
            },
            onError: (errors) => {
                 // Handle error (e.g., if API returns 500)
                 alert(errors.message || 'Failed to delete user.');
            }
        });
    }
};

</script>

<template>
    <Head :title="`Edit User: ${user.name}`" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                Edit User: <span class="text-brand-primary">{{ user.name }}</span>
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-2xl mx-auto sm:px-6 lg:px-8 space-y-6">
                
                <div
                    v-if="successMessage"
                    class="mb-4 p-4 text-sm text-green-700 bg-green-100 rounded-lg dark:bg-green-900 dark:text-green-300"
                    role="alert"
                >
                    {{ successMessage }}
                </div>

                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <form @submit.prevent="submitUpdate" class="p-6 space-y-6">
                        <header>
                            <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">User Details</h3>
                            <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">Update the user's information and role.</p>
                        </header>

                        <div>
                            <InputLabel for="name" value="Name *" />
                            <TextInput
                                id="name"
                                type="text"
                                class="mt-1 block w-full"
                                v-model="form.name"
                                required
                                autofocus
                                autocomplete="name"
                            />
                            <InputError class="mt-2" :message="form.errors.name" />
                        </div>

                        <div>
                            <InputLabel for="email" value="Email *" />
                            <TextInput
                                id="email"
                                type="email"
                                class="mt-1 block w-full"
                                v-model="form.email"
                                required
                                autocomplete="email"
                            />
                            <InputError class="mt-2" :message="form.errors.email" />
                        </div>

                         <div>
                            <InputLabel for="phone" value="Phone (Optional)" />
                            <TextInput
                                id="phone"
                                type="tel"
                                class="mt-1 block w-full"
                                v-model="form.phone"
                                autocomplete="tel"
                            />
                            <InputError class="mt-2" :message="form.errors.phone" />
                        </div>

                        <div>
                            <InputLabel for="role_id" value="Role *" />
                            <select
                                id="role_id"
                                v-model="form.role_id"
                                required
                                class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm"
                            >
                                <option v-for="role in roles" :key="role.id" :value="role.id">
                                    {{ role.name }}
                                </option>
                            </select>
                            <InputError class="mt-2" :message="form.errors.role_id" />
                        </div>

                         <div class="block">
                            <label class="flex items-center">
                                <Checkbox name="is_active" v-model:checked="form.is_active" />
                                <span class="ms-2 text-sm text-gray-600 dark:text-gray-400">Active Account</span>
                            </label>
                             <InputError class="mt-2" :message="form.errors.is_active" />
                        </div>

                        <header class="pt-6 border-t border-gray-200 dark:border-gray-700">
                             <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">Update Password</h3>
                            <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">Leave blank if you do not want to change the password.</p>
                        </header>

                         <div>
                            <InputLabel for="password" value="New Password" />
                            <TextInput
                                id="password"
                                type="password"
                                class="mt-1 block w-full"
                                v-model="form.password"
                                autocomplete="new-password"
                            />
                            <InputError class="mt-2" :message="form.errors.password" />
                        </div>

                        <div>
                            <InputLabel for="password_confirmation" value="Confirm New Password" />
                            <TextInput
                                id="password_confirmation"
                                type="password"
                                class="mt-1 block w-full"
                                v-model="form.password_confirmation"
                                autocomplete="new-password"
                            />
                            <InputError class="mt-2" :message="form.errors.password_confirmation" />
                        </div>

                        <div class="flex items-center justify-end gap-4 border-t border-gray-200 dark:border-gray-700 pt-6">
                            <Link
                                :href="route('admin.users.index')"
                                class="inline-flex items-center px-4 py-2 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-500 rounded-md font-semibold text-xs text-gray-700 dark:text-gray-300 uppercase tracking-widest shadow-sm hover:bg-gray-50 dark:hover:bg-gray-700 disabled:opacity-25 transition ease-in-out duration-150"
                            >
                                Cancel
                            </Link>
                            <PrimaryButton :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                                {{ form.processing ? 'Updating...' : 'Update User' }}
                            </PrimaryButton>
                        </div>
                    </form>
                </div>

                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900 dark:text-gray-100">
                        <header>
                            <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">Delete User</h3>
                            <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                                Once this user is deleted, all of its resources and data will be permanently lost.
                            </p>
                        </header>
                         <DangerButton @click="deleteUser" class="mt-4">
                            Delete This User
                        </DangerButton>
                    </div>
                </div>

            </div>
        </div>
    </AuthenticatedLayout>
</template>