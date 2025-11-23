<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { UserPlusIcon, ArrowLeftIcon } from '@heroicons/vue/24/outline';

const props = defineProps({
    roles: Array,
    countries: Array,
});

const form = useForm({
    name: '',
    email: '',
    password: '',
    password_confirmation: '',
    phone: '',
    role_id: '',
    country_id: '',
    email_verified: false,
});

const submit = () => {
    form.post(route('admin.users.store'));
};
</script>

<template>
    <Head title="Create User - Admin" />

    <AdminLayout>
        <div class="py-12">
            <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
                <!-- Header -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                    <div class="p-6 border-b border-gray-200">
                        <div class="flex justify-between items-center">
                            <div class="flex items-center gap-3">
                                <UserPlusIcon class="w-8 h-8 text-indigo-600" />
                                <div>
                                    <h2 class="text-2xl font-bold text-gray-800">Create New User</h2>
                                    <p class="text-gray-600 mt-1">Add a new user to the system</p>
                                </div>
                            </div>
                            <Link
                                :href="route('admin.users.index')"
                                class="flex items-center gap-2 text-gray-600 hover:text-gray-800 px-4 py-2 rounded-lg border border-gray-300"
                            >
                                <ArrowLeftIcon class="w-4 h-4" />
                                Back to Users
                            </Link>
                        </div>
                    </div>
                </div>

                <!-- Create Form -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <form @submit.prevent="submit" class="p-6">
                        <div class="space-y-6">
                            <!-- Basic Information -->
                            <div>
                                <h3 class="text-lg font-semibold text-gray-900 mb-4">Basic Information</h3>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">
                                            Full Name <span class="text-red-500">*</span>
                                        </label>
                                        <input
                                            v-model="form.name"
                                            type="text"
                                            required
                                            class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500"
                                            placeholder="Enter full name"
                                        />
                                        <div v-if="form.errors.name" class="text-red-600 text-sm mt-1">
                                            {{ form.errors.name }}
                                        </div>
                                    </div>

                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">
                                            Email Address <span class="text-red-500">*</span>
                                        </label>
                                        <input
                                            v-model="form.email"
                                            type="email"
                                            required
                                            class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500"
                                            placeholder="user@example.com"
                                        />
                                        <div v-if="form.errors.email" class="text-red-600 text-sm mt-1">
                                            {{ form.errors.email }}
                                        </div>
                                    </div>

                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">
                                            Phone Number
                                        </label>
                                        <input
                                            v-model="form.phone"
                                            type="text"
                                            class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500"
                                            placeholder="+880 1XXX-XXXXXX"
                                        />
                                        <div v-if="form.errors.phone" class="text-red-600 text-sm mt-1">
                                            {{ form.errors.phone }}
                                        </div>
                                    </div>

                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">
                                            Country
                                        </label>
                                        <select
                                            v-model="form.country_id"
                                            class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500"
                                        >
                                            <option value="">Select Country</option>
                                            <option v-for="country in countries" :key="country.id" :value="country.id">
                                                {{ country.name }}
                                            </option>
                                        </select>
                                        <div v-if="form.errors.country_id" class="text-red-600 text-sm mt-1">
                                            {{ form.errors.country_id }}
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Password -->
                            <div>
                                <h3 class="text-lg font-semibold text-gray-900 mb-4">Password</h3>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">
                                            Password <span class="text-red-500">*</span>
                                        </label>
                                        <input
                                            v-model="form.password"
                                            type="password"
                                            required
                                            class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500"
                                            placeholder="Enter password"
                                        />
                                        <div v-if="form.errors.password" class="text-red-600 text-sm mt-1">
                                            {{ form.errors.password }}
                                        </div>
                                    </div>

                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">
                                            Confirm Password <span class="text-red-500">*</span>
                                        </label>
                                        <input
                                            v-model="form.password_confirmation"
                                            type="password"
                                            required
                                            class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500"
                                            placeholder="Confirm password"
                                        />
                                        <div v-if="form.errors.password_confirmation" class="text-red-600 text-sm mt-1">
                                            {{ form.errors.password_confirmation }}
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Role & Permissions -->
                            <div>
                                <h3 class="text-lg font-semibold text-gray-900 mb-4">Role & Permissions</h3>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">
                                            User Role <span class="text-red-500">*</span>
                                        </label>
                                        <select
                                            v-model="form.role_id"
                                            required
                                            class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500"
                                        >
                                            <option value="">Select Role</option>
                                            <option v-for="role in roles" :key="role.id" :value="role.id">
                                                {{ role.name }}
                                            </option>
                                        </select>
                                        <div v-if="form.errors.role_id" class="text-red-600 text-sm mt-1">
                                            {{ form.errors.role_id }}
                                        </div>
                                    </div>

                                    <div class="flex items-end">
                                        <label class="flex items-center gap-2">
                                            <input
                                                v-model="form.email_verified"
                                                type="checkbox"
                                                class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500"
                                            />
                                            <span class="text-sm text-gray-700">Mark email as verified</span>
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Actions -->
                        <div class="flex justify-end gap-3 mt-8 pt-6 border-t border-gray-200">
                            <Link
                                :href="route('admin.users.index')"
                                class="px-6 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50"
                            >
                                Cancel
                            </Link>
                            <button
                                type="submit"
                                :disabled="form.processing"
                                class="px-6 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 disabled:opacity-50"
                            >
                                <span v-if="form.processing">Creating...</span>
                                <span v-else>Create User</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>
