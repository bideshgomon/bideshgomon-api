<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'; // <-- Use AuthenticatedLayout
import Pagination from '@/Components/Pagination.vue';
import TextInput from '@/Components/TextInput.vue'; // <-- For search input
import { Head, Link, router } from '@inertiajs/vue3';
import { ref, watch } from 'vue';
import debounce from 'lodash/debounce';

// Props passed from UserPageController
const props = defineProps({
    users: Object, // Paginated user data
    roles: Array,  // List of roles for filter dropdown
    filters: Object, // Current filter values (search, role)
});

// Reactive state for filters, initialized with props
const search = ref(props.filters.search || '');
const roleFilter = ref(props.filters.role || '');

// Watch for filter changes and reload the page via Inertia visit
// Debounce prevents rapid reloads while typing
watch([search, roleFilter], debounce(() => {
    router.get(route('admin.users.index'), {
        search: search.value,
        role: roleFilter.value,
    }, {
        preserveState: true, // Keep component state (like scroll position)
        replace: true,       // Replace history state instead of pushing
    });
}, 300)); // 300ms debounce delay

// Function to format date (optional)
const formatDate = (datetimeString) => {
    if (!datetimeString) return 'N/A';
    try {
        const options = { year: 'numeric', month: 'short', day: 'numeric' };
        return new Date(datetimeString).toLocaleDateString(undefined, options);
    } catch (e) {
        return 'Invalid Date';
    }
};

</script>

<template>
    <Head title="Manage Users" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                Manage Users
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900 dark:text-gray-100">

                        <div class="mb-6 flex flex-col md:flex-row justify-between items-center gap-4">
                            <div class="flex flex-col md:flex-row gap-4 w-full md:w-auto">
                                <TextInput
                                    type="text"
                                    class="block w-full md:w-64"
                                    v-model="search"
                                    placeholder="Search name or email..."
                                />
                                <select
                                    v-model="roleFilter"
                                    class="block w-full md:w-48 border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm"
                                >
                                    <option value="">All Roles</option>
                                    <option v-for="role in roles" :key="role.slug" :value="role.slug">
                                        {{ role.name }}
                                    </option>
                                </select>
                            </div>
                            <Link
                                :href="route('admin.users.create')"
                                class="btn btn-primary whitespace-nowrap w-full md:w-auto mt-2 md:mt-0"
                            >
                                Add New User
                            </Link>
                        </div>

                        <div class="overflow-x-auto border rounded-lg dark:border-gray-700">
                            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                <thead class="bg-gray-50 dark:bg-gray-700">
                                    <tr>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Name</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Email</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Role</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Status</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Joined</th>
                                        <th scope="col" class="relative px-6 py-3"><span class="sr-only">Actions</span></th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                    <tr v-if="users.data.length === 0">
                                         <td colspan="6" class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400 text-center">No users found matching your criteria.</td>
                                    </tr>
                                    <tr v-else v-for="user in users.data" :key="user.id" class="hover:bg-gray-50 dark:hover:bg-gray-700 transition duration-150">
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-gray-100">{{ user.name }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">{{ user.email }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">{{ user.role?.name || 'N/A' }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                                            <span :class="user.is_active ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300' : 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300'" class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full">
                                                {{ user.is_active ? 'Active' : 'Inactive' }}
                                            </span>
                                        </td>
                                         <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">{{ formatDate(user.created_at) }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                            <Link :href="route('admin.users.edit', user.id)" class="text-brand-primary hover:text-opacity-80 dark:text-blue-400 dark:hover:text-blue-300">
                                                Edit
                                            </Link>
                                            </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <Pagination class="mt-6" :links="users.links" />

                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>