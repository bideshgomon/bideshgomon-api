<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { PencilSquareIcon, ArrowLeftIcon } from '@heroicons/vue/24/outline';

const props = defineProps({
    user: Object,
    roles: Array,
    countries: Array,
});

const form = useForm({
    name: props.user.name || '',
    email: props.user.email || '',
    password: '',
    password_confirmation: '',
    phone: props.user.phone || '',
    role_id: props.user.role_id || '',
    country_id: props.user.country_id || '',
    email_verified: !!props.user.email_verified_at,
});

const submit = () => {
    form.put(route('admin.users.update', props.user.id));
};
</script>

<template>
    <Head title="Edit User - Admin" />

    <AdminLayout>
        <div class="py-12">
            <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
                <!-- Header -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 mb-6">
                    <div class="p-6">
                        <div class="flex justify-between items-center">
                            <div class="flex items-center gap-3">
                                <div class="w-12 h-12 bg-indigo-100 rounded-lg flex items-center justify-center">
                                    <PencilSquareIcon class="w-6 h-6 text-indigo-600" />
                                </div>
                                <div>
                                    <h2 class="text-2xl font-bold text-gray-900">Edit User</h2>
                                    <p class="text-sm text-gray-600 mt-0.5">Update user information</p>
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

                <!-- Edit Form -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                    <form @submit.prevent="submit" class="p-6">
                        <div class="space-y-6">
                            <!-- User Info Badge -->
                            <div class="bg-indigo-50 border border-indigo-200 rounded-lg p-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-12 h-12 bg-indigo-600 rounded-lg flex items-center justify-center text-white font-bold text-lg">
                                        {{ user.name.charAt(0).toUpperCase() }}
                                    </div>
                                    <div>
                                        <div class="font-semibold text-gray-900">{{ user.name }}</div>
                                        <div class="text-sm text-gray-600">{{ user.email }}</div>
                                        <div class="flex gap-2 mt-1">
                                            <span class="text-xs px-2 py-1 rounded bg-indigo-100 text-indigo-700">
                                                {{ user.role?.name || 'User' }}
                                            </span>
                                            <span v-if="user.suspended_at" class="text-xs px-2 py-1 rounded bg-red-100 text-red-800">
                                                Suspended
                                            </span>
                                            <span v-else class="text-xs px-2 py-1 rounded bg-green-100 text-green-800">
                                                Active
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>

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

                            <!-- Password (Optional) -->
                            <div>
                                <h3 class="text-lg font-semibold text-gray-900 mb-4">Change Password (Optional)</h3>
                                <p class="text-sm text-gray-600 mb-4">Leave blank to keep current password</p>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">
                                            New Password
                                        </label>
                                        <input
                                            v-model="form.password"
                                            type="password"
                                            class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500"
                                            placeholder="Enter new password"
                                        />
                                        <div v-if="form.errors.password" class="text-red-600 text-sm mt-1">
                                            {{ form.errors.password }}
                                        </div>
                                    </div>

                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">
                                            Confirm New Password
                                        </label>
                                        <input
                                            v-model="form.password_confirmation"
                                            type="password"
                                            class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500"
                                            placeholder="Confirm new password"
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
                                            <span class="text-sm text-gray-700">Email verified</span>
                                        </label>
                                    </div>
                                </div>
                            </div>

                            <!-- Additional Info -->
                            <div class="bg-gray-50 border border-gray-200 rounded-lg p-4">
                                <h4 class="text-sm font-semibold text-gray-700 mb-2">Account Information</h4>
                                <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-sm">
                                    <div>
                                        <span class="text-gray-600">Created:</span>
                                        <div class="font-medium">{{ new Date(user.created_at).toLocaleDateString() }}</div>
                                    </div>
                                    <div>
                                        <span class="text-gray-600">Last Updated:</span>
                                        <div class="font-medium">{{ new Date(user.updated_at).toLocaleDateString() }}</div>
                                    </div>
                                    <div>
                                        <span class="text-gray-600">Status:</span>
                                        <div class="font-medium" :class="user.suspended_at ? 'text-red-600' : 'text-green-600'">
                                            {{ user.suspended_at ? 'Suspended' : 'Active' }}
                                        </div>
                                    </div>
                                    <div>
                                        <span class="text-gray-600">User ID:</span>
                                        <div class="font-medium">#{{ user.id }}</div>
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
                                <span v-if="form.processing">Updating...</span>
                                <span v-else>Update User</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>
