<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import InputError from '@/Components/InputError.vue';
import SelectInput from '@/Components/SelectInput.vue';
import Checkbox from '@/Components/Checkbox.vue';

const props = defineProps({
    user: Object, // User data passed from controller
    roles: Array, // List of all roles
});

const form = useForm({
    name: props.user.name,
    email: props.user.email,
    role_id: props.user.role_id,
    is_active: props.user.is_active,
    // password: '', // Uncomment if adding password change
    // password_confirmation: '', // Uncomment if adding password change
});

const submit = () => {
    form.put(route('admin.users.update', props.user.id));
};

</script>

<template>
    <Head :title="`Edit User: ${user.name}`" />

    <AdminLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Edit User: {{ user.name }}
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <form @submit.prevent="submit">
                        <div class="p-6 bg-white border-b border-gray-200 space-y-6">
                            <div>
                                <InputLabel for="name" value="Name" />
                                <TextInput
                                    id="name"
                                    type="text"
                                    class="mt-1 block w-full"
                                    v-model="form.name"
                                    required
                                    autofocus
                                />
                                <InputError class="mt-2" :message="form.errors.name" />
                            </div>

                            <div>
                                <InputLabel for="email" value="Email" />
                                <TextInput
                                    id="email"
                                    type="email"
                                    class="mt-1 block w-full"
                                    v-model="form.email"
                                    required
                                />
                                <InputError class="mt-2" :message="form.errors.email" />
                            </div>

                            <div>
                                <InputLabel for="role_id" value="Role" />
                                <SelectInput
                                    id="role_id"
                                    class="mt-1 block w-full"
                                    v-model="form.role_id"
                                    required
                                >
                                    <option value="" disabled>Select a role</option>
                                    <option v-for="role in roles" :key="role.id" :value="role.id">
                                        {{ role.name }}
                                    </option>
                                </SelectInput>
                                <InputError class="mt-2" :message="form.errors.role_id" />
                            </div>

                            <div class="block">
                                <label class="flex items-center">
                                    <Checkbox name="is_active" v-model:checked="form.is_active" />
                                    <span class="ms-2 text-sm text-gray-600">User is Active</span>
                                </label>
                                <InputError class="mt-2" :message="form.errors.is_active" />
                            </div>

                            </div>
                        <div class="px-6 py-4 bg-gray-50 flex justify-end items-center">
                            <Link :href="route('admin.users.index')" class="text-sm text-gray-600 hover:text-gray-900 underline mr-4">
                                Cancel
                            </Link>
                            <PrimaryButton :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                                Update User
                            </PrimaryButton>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>