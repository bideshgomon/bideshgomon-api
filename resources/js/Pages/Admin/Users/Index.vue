<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { ref, onMounted, computed } from 'vue';
import axios from 'axios';
import Pagination from '@/Components/Pagination.vue'; // Assuming you have a Pagination component

const props = defineProps({
    roles: Array, // Passed from UserPageController
});

const users = ref({ data: [], links: [], total: 0, current_page: 1 });
const loading = ref(false);
const searchTerm = ref(''); // For search functionality later
const selectedRole = ref(''); // For filtering later

const fetchUsers = async (page = 1) => {
    loading.value = true;
    try {
        // Adjust params for search/filter later
        const response = await axios.get('/api/admin/users', {
            params: {
                page: page,
                // search: searchTerm.value,
                // role: selectedRole.value
            }
        });
        users.value = response.data;
    } catch (error) {
        console.error("Error fetching users:", error);
        // Add user notification feedback
    } finally {
        loading.value = false;
    }
};

const deleteUser = (userId, userName) => {
    if (confirm(`Are you sure you want to delete user: ${userName}?`)) {
        router.delete(`/api/admin/users/${userId}`, {
            preserveScroll: true,
            onSuccess: () => {
                // Refresh the user list after delete
                fetchUsers(users.value.current_page);
                // Add success notification
            },
            onError: (errors) => {
                console.error("Error deleting user:", errors);
                // Add error notification
            }
        });
    }
};

// Fetch users when the component mounts
onMounted(() => {
    fetchUsers();
});

// Computed property for easy access to user data array
const userList = computed(() => users.value.data);

</script>

<template>
    <Head title="Admin - Manage Users" />

    <AdminLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Manage Users</h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900">

                        <div class="mb-4 flex justify-between items-center">
                            <Link :href="route('admin.users.create')" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:ring ring-gray-300 disabled:opacity-25 transition ease-in-out duration-150">
                                Add New User
                            </Link>
                        </div>

                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Role</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                        <th scope="col" class="relative px-6 py-3">
                                            <span class="sr-only">Actions</span>
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    <tr v-if="loading">
                                        <td colspan="5" class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">Loading...</td>
                                    </tr>
                                    <tr v-else-if="userList.length === 0">
                                         <td colspan="5" class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">No users found.</td>
                                    </tr>
                                    <tr v-else v-for="user in userList" :key="user.id">
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ user.name }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ user.email }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ user.role?.name }}</td>
                                         <td class="px-6 py-4 whitespace-nowrap text-sm">
                                            <span :class="user.is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'" class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full">
                                                {{ user.is_active ? 'Active' : 'Inactive' }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                            <Link :href="route('admin.users.edit', user.id)" class="text-indigo-600 hover:text-indigo-900 mr-3">Edit</Link>
                                            <button @click="deleteUser(user.id, user.name)" class="text-red-600 hover:text-red-900">Delete</button>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                         <Pagination class="mt-6" :links="users.links" @page-click="fetchUsers"/>

                    </div>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>