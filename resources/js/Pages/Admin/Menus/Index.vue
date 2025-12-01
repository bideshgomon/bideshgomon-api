<script setup>
import { ref } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { 
    PlusIcon, 
    PencilIcon, 
    TrashIcon,
    ArrowPathIcon,
    Bars3Icon,
    CheckCircleIcon,
    XCircleIcon
} from '@heroicons/vue/24/outline';

const props = defineProps({
    menus: Object,
    locations: Object,
});

const deleteMenu = (id) => {
    if (confirm('Are you sure you want to delete this menu item? All child items will also be deleted.')) {
        router.delete(route('menus.destroy', id));
    }
};

const clearCache = () => {
    router.post(route('menus.clear-cache'));
};

const getLocationBadgeColor = (location) => {
    const colors = {
        'header_main': 'bg-blue-100 text-blue-800',
        'footer_column_1': 'bg-green-100 text-green-800',
        'footer_column_2': 'bg-purple-100 text-purple-800',
        'footer_column_3': 'bg-pink-100 text-pink-800',
        'mobile_menu': 'bg-orange-100 text-orange-800',
    };
    return colors[location] || 'bg-gray-100 text-gray-800';
};
</script>

<template>
    <Head title="Menu Management" />

    <AdminLayout>
        <div class="py-6">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <!-- Header -->
                <div class="mb-8">
                    <div class="flex justify-between items-center">
                        <div>
                            <h1 class="text-3xl font-bold text-gray-900">Menu Management</h1>
                            <p class="mt-2 text-sm text-gray-600">
                                Manage navigation menus for header, footer, and mobile
                            </p>
                        </div>
                        <div class="flex gap-3">
                            <button
                                @click="clearCache"
                                class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-lg shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 transition-colors"
                            >
                                <ArrowPathIcon class="h-5 w-5 mr-2" />
                                Clear Cache
                            </button>
                            <Link
                                :href="route('menus.create')"
                                class="inline-flex items-center px-4 py-2 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 transition-colors"
                            >
                                <PlusIcon class="h-5 w-5 mr-2" />
                                Add Menu Item
                            </Link>
                        </div>
                    </div>
                </div>

                <!-- Menu Table -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Order
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Label
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Location
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Type
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Link
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Status
                                    </th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Actions
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <tr v-for="menu in menus.data" :key="menu.id" class="hover:bg-gray-50">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center gap-2">
                                            <Bars3Icon class="h-4 w-4 text-gray-400" />
                                            <span class="text-sm font-medium text-gray-900">{{ menu.order }}</span>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center gap-2">
                                            <span v-if="menu.parent_id" class="text-gray-400">↳</span>
                                            <span class="text-sm font-medium text-gray-900">{{ menu.label }}</span>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span :class="[
                                            'inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium',
                                            getLocationBadgeColor(menu.location)
                                        ]">
                                            {{ locations[menu.location] || menu.location }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span v-if="menu.is_external" class="text-xs text-gray-500">External</span>
                                        <span v-else-if="menu.route_name" class="text-xs text-gray-500">Route</span>
                                        <span v-else class="text-xs text-gray-500">Custom URL</span>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="text-sm text-gray-500 max-w-xs truncate">
                                            {{ menu.route_name || menu.url || '-' }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span v-if="menu.is_active" class="inline-flex items-center gap-1 text-xs font-medium text-green-700">
                                            <CheckCircleIcon class="h-4 w-4" />
                                            Active
                                        </span>
                                        <span v-else class="inline-flex items-center gap-1 text-xs font-medium text-gray-500">
                                            <XCircleIcon class="h-4 w-4" />
                                            Inactive
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <div class="flex items-center justify-end gap-2">
                                            <Link
                                                :href="route('menus.edit', menu.id)"
                                                class="text-indigo-600 hover:text-indigo-900"
                                            >
                                                <PencilIcon class="h-5 w-5" />
                                            </Link>
                                            <button
                                                @click="deleteMenu(menu.id)"
                                                class="text-red-600 hover:text-red-900"
                                            >
                                                <TrashIcon class="h-5 w-5" />
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                <tr v-if="menus.data.length === 0">
                                    <td colspan="7" class="px-6 py-12 text-center">
                                        <div class="text-gray-500">
                                            <Bars3Icon class="mx-auto h-12 w-12 text-gray-400" />
                                            <h3 class="mt-2 text-sm font-medium text-gray-900">No menu items</h3>
                                            <p class="mt-1 text-sm text-gray-500">Get started by creating a new menu item.</p>
                                            <div class="mt-6">
                                                <Link
                                                    :href="route('menus.create')"
                                                    class="inline-flex items-center px-4 py-2 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700"
                                                >
                                                    <PlusIcon class="h-5 w-5 mr-2" />
                                                    Add Menu Item
                                                </Link>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div v-if="menus.links && menus.links.length > 3" class="bg-white px-4 py-3 border-t border-gray-200 sm:px-6">
                        <div class="flex items-center justify-between">
                            <div class="flex-1 flex justify-between sm:hidden">
                                <Link
                                    v-if="menus.prev_page_url"
                                    :href="menus.prev_page_url"
                                    class="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50"
                                >
                                    Previous
                                </Link>
                                <Link
                                    v-if="menus.next_page_url"
                                    :href="menus.next_page_url"
                                    class="ml-3 relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50"
                                >
                                    Next
                                </Link>
                            </div>
                            <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
                                <div>
                                    <p class="text-sm text-gray-700">
                                        Showing
                                        <span class="font-medium">{{ menus.from }}</span>
                                        to
                                        <span class="font-medium">{{ menus.to }}</span>
                                        of
                                        <span class="font-medium">{{ menus.total }}</span>
                                        results
                                    </p>
                                </div>
                                <div>
                                    <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px">
                                        <Link
                                            v-for="link in menus.links"
                                            :key="link.label"
                                            :href="link.url"
                                            :class="[
                                                link.active
                                                    ? 'z-10 bg-indigo-50 border-indigo-500 text-indigo-600'
                                                    : 'bg-white border-gray-300 text-gray-500 hover:bg-gray-50',
                                                'relative inline-flex items-center px-4 py-2 border text-sm font-medium'
                                            ]"
                                            v-html="link.label"
                                        />
                                    </nav>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Info Panel -->
                <div class="mt-6 bg-blue-50 border border-blue-200 rounded-lg p-4">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <Bars3Icon class="h-5 w-5 text-blue-400" />
                        </div>
                        <div class="ml-3">
                            <h3 class="text-sm font-medium text-blue-800">Menu Locations</h3>
                            <div class="mt-2 text-sm text-blue-700">
                                <ul class="list-disc list-inside space-y-1">
                                    <li><strong>Header Main:</strong> Primary navigation at top of website</li>
                                    <li><strong>Footer Columns:</strong> Three columns in footer section</li>
                                    <li><strong>Mobile Menu:</strong> Navigation for mobile devices</li>
                                    <li><strong>Cache:</strong> Menus are cached for 1 hour. Click "Clear Cache" after changes.</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>

