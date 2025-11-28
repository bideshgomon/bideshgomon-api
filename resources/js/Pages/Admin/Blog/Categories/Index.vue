<template>
    <Head title="Blog Categories" />

    <AdminLayout>
        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <!-- Header -->
                <div class="mb-6 flex items-center justify-between">
                    <div>
                        <h2 class="text-3xl font-bold text-gray-900">Blog Categories</h2>
                        <p class="mt-1 text-sm text-gray-600">Organize your blog posts into categories</p>
                    </div>
                    <button
                        @click="showCreateModal = true"
                        class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700"
                    >
                        <PlusIcon class="h-5 w-5 mr-2" />
                        New Category
                    </button>
                </div>

                <!-- Categories Table -->
                <div class="bg-white shadow-sm rounded-lg overflow-hidden">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Category
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Description
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Order
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
                            <tr v-for="category in categories" :key="category.id" class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div
                                            class="flex-shrink-0 h-10 w-10 rounded-lg flex items-center justify-center text-white text-sm font-bold"
                                            :style="{ backgroundColor: category.color }"
                                        >
                                            {{ category.icon || category.name.charAt(0).toUpperCase() }}
                                        </div>
                                        <div class="ml-4">
                                            <div class="text-sm font-medium text-gray-900">{{ category.name }}</div>
                                            <div class="text-sm text-gray-500">{{ category.slug }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-sm text-gray-900">{{ category.description || '-' }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ category.order }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <button
                                        @click="toggleStatus(category)"
                                        class="px-2 py-1 text-xs font-semibold rounded-full"
                                        :class="category.is_active ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800'"
                                    >
                                        {{ category.is_active ? 'Active' : 'Inactive' }}
                                    </button>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <button
                                        @click="editCategory(category)"
                                        class="text-blue-600 hover:text-blue-900 mr-4"
                                    >
                                        Edit
                                    </button>
                                    <button
                                        @click="deleteCategory(category)"
                                        class="text-red-600 hover:text-red-900"
                                    >
                                        Delete
                                    </button>
                                </td>
                            </tr>
                        </tbody>
                    </table>

                    <div v-if="categories.length === 0" class="text-center py-12">
                        <FolderIcon class="mx-auto h-12 w-12 text-gray-400" />
                        <h3 class="mt-2 text-sm font-medium text-gray-900">No categories</h3>
                        <p class="mt-1 text-sm text-gray-500">Get started by creating a new category.</p>
                    </div>
                </div>

                <!-- Create/Edit Modal -->
                <TransitionRoot :show="showCreateModal || showEditModal" as="template">
                    <Dialog as="div" class="relative z-50" @close="closeModal">
                        <TransitionChild
                            as="template"
                            enter="ease-out duration-300"
                            enter-from="opacity-0"
                            enter-to="opacity-100"
                            leave="ease-in duration-200"
                            leave-from="opacity-100"
                            leave-to="opacity-0"
                        >
                            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" />
                        </TransitionChild>

                        <div class="fixed inset-0 z-10 overflow-y-auto">
                            <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
                                <TransitionChild
                                    as="template"
                                    enter="ease-out duration-300"
                                    enter-from="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                                    enter-to="opacity-100 translate-y-0 sm:scale-100"
                                    leave="ease-in duration-200"
                                    leave-from="opacity-100 translate-y-0 sm:scale-100"
                                    leave-to="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                                >
                                    <DialogPanel class="relative transform overflow-hidden rounded-lg bg-white px-4 pb-4 pt-5 text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-lg sm:p-6">
                                        <form @submit.prevent="submitForm">
                                            <div>
                                                <h3 class="text-lg font-medium leading-6 text-gray-900 mb-4">
                                                    {{ showEditModal ? 'Edit Category' : 'Create Category' }}
                                                </h3>
                                                
                                                <div class="space-y-4">
                                                    <div>
                                                        <label for="name" class="block text-sm font-medium text-gray-700">
                                                            Name <span class="text-red-500">*</span>
                                                        </label>
                                                        <input
                                                            id="name"
                                                            v-model="form.name"
                                                            type="text"
                                                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                                            @input="generateSlug"
                                                        />
                                                        <p v-if="form.errors.name" class="mt-1 text-sm text-red-600">{{ form.errors.name }}</p>
                                                    </div>

                                                    <div>
                                                        <label for="slug" class="block text-sm font-medium text-gray-700">
                                                            Slug
                                                        </label>
                                                        <input
                                                            id="slug"
                                                            v-model="form.slug"
                                                            type="text"
                                                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                                        />
                                                        <p v-if="form.errors.slug" class="mt-1 text-sm text-red-600">{{ form.errors.slug }}</p>
                                                    </div>

                                                    <div>
                                                        <label for="description" class="block text-sm font-medium text-gray-700">
                                                            Description
                                                        </label>
                                                        <textarea
                                                            id="description"
                                                            v-model="form.description"
                                                            rows="3"
                                                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                                        ></textarea>
                                                    </div>

                                                    <div class="grid grid-cols-2 gap-4">
                                                        <div>
                                                            <label for="icon" class="block text-sm font-medium text-gray-700">
                                                                Icon (emoji)
                                                            </label>
                                                            <input
                                                                id="icon"
                                                                v-model="form.icon"
                                                                type="text"
                                                                placeholder="📝"
                                                                maxlength="2"
                                                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                                            />
                                                        </div>

                                                        <div>
                                                            <label for="color" class="block text-sm font-medium text-gray-700">
                                                                Color
                                                            </label>
                                                            <input
                                                                id="color"
                                                                v-model="form.color"
                                                                type="color"
                                                                class="mt-1 block w-full h-10 rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                                            />
                                                        </div>
                                                    </div>

                                                    <div>
                                                        <label for="order" class="block text-sm font-medium text-gray-700">
                                                            Order
                                                        </label>
                                                        <input
                                                            id="order"
                                                            v-model="form.order"
                                                            type="number"
                                                            min="0"
                                                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                                        />
                                                    </div>

                                                    <div class="flex items-center">
                                                        <input
                                                            id="is_active"
                                                            v-model="form.is_active"
                                                            type="checkbox"
                                                            class="h-4 w-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500"
                                                        />
                                                        <label for="is_active" class="ml-2 block text-sm text-gray-700">
                                                            Active
                                                        </label>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="mt-5 sm:mt-6 sm:grid sm:grid-flow-row-dense sm:grid-cols-2 sm:gap-3">
                                                <button
                                                    type="submit"
                                                    :disabled="form.processing"
                                                    class="inline-flex w-full justify-center rounded-md bg-blue-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-blue-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-blue-600 sm:col-start-2 disabled:opacity-50"
                                                >
                                                    {{ form.processing ? 'Saving...' : 'Save' }}
                                                </button>
                                                <button
                                                    type="button"
                                                    @click="closeModal"
                                                    class="mt-3 inline-flex w-full justify-center rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50 sm:col-start-1 sm:mt-0"
                                                >
                                                    Cancel
                                                </button>
                                            </div>
                                        </form>
                                    </DialogPanel>
                                </TransitionChild>
                            </div>
                        </div>
                    </Dialog>
                </TransitionRoot>
            </div>
        </div>
    </AdminLayout>
</template>

<script setup>
import { ref } from 'vue';
import { Head, router, useForm } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Dialog, DialogPanel, TransitionChild, TransitionRoot } from '@headlessui/vue';
import { PlusIcon, FolderIcon } from '@heroicons/vue/24/outline';

const props = defineProps({
    categories: Array,
});

const showCreateModal = ref(false);
const showEditModal = ref(false);
const editingCategory = ref(null);

const form = useForm({
    name: '',
    slug: '',
    description: '',
    icon: '',
    color: '#3B82F6',
    order: 0,
    is_active: true,
});

const generateSlug = () => {
    if (!form.slug || (editingCategory.value && form.slug === slugify(editingCategory.value.name))) {
        form.slug = slugify(form.name);
    }
};

const slugify = (text) => {
    return text
        .toString()
        .toLowerCase()
        .trim()
        .replace(/\s+/g, '-')
        .replace(/[^\w\-]+/g, '')
        .replace(/\-\-+/g, '-')
        .replace(/^-+/, '')
        .replace(/-+$/, '');
};

const editCategory = (category) => {
    editingCategory.value = category;
    form.name = category.name;
    form.slug = category.slug;
    form.description = category.description || '';
    form.icon = category.icon || '';
    form.color = category.color;
    form.order = category.order;
    form.is_active = category.is_active;
    showEditModal.value = true;
};

const closeModal = () => {
    showCreateModal.value = false;
    showEditModal.value = false;
    editingCategory.value = null;
    form.reset();
    form.clearErrors();
};

const submitForm = () => {
    if (showEditModal.value && editingCategory.value) {
        form.put(route('admin.blog.categories.update', editingCategory.value.id), {
            preserveScroll: true,
            onSuccess: () => closeModal(),
        });
    } else {
        form.post(route('admin.blog.categories.store'), {
            preserveScroll: true,
            onSuccess: () => closeModal(),
        });
    }
};

const toggleStatus = (category) => {
    router.post(route('admin.data.blog-categories.toggle-status', category.id), {}, {
        preserveScroll: true,
    });
};

const deleteCategory = (category) => {
    if (confirm(`Are you sure you want to delete "${category.name}"?`)) {
        router.delete(route('admin.blog.categories.destroy', category.id), {
            preserveScroll: true,
        });
    }
};
</script>
