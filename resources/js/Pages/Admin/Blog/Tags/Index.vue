<template>
    <Head title="Blog Tags" />

    <AdminLayout>
        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <!-- Header -->
                <div class="mb-6 flex items-center justify-between">
                    <div>
                        <h2 class="text-3xl font-bold text-gray-900">Blog Tags</h2>
                        <p class="mt-1 text-sm text-gray-600">Manage tags to help organize and categorize your blog posts</p>
                    </div>
                    <button
                        @click="showCreateModal = true"
                        class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700"
                    >
                        <PlusIcon class="h-5 w-5 mr-2" />
                        New Tag
                    </button>
                </div>

                <!-- Tags Grid -->
                <div class="bg-white shadow-sm rounded-lg p-6">
                    <div v-if="tags.length > 0" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                        <div
                            v-for="tag in tags"
                            :key="tag.id"
                            class="group relative flex items-center justify-between p-4 bg-gradient-to-br from-blue-50 to-indigo-50 rounded-lg border border-blue-100 hover:border-blue-300 hover:shadow-md transition-all duration-200"
                        >
                            <div class="flex items-center space-x-3 flex-1 min-w-0">
                                <div class="flex-shrink-0">
                                    <div class="h-10 w-10 rounded-full bg-blue-500 flex items-center justify-center">
                                        <TagIcon class="h-5 w-5 text-white" />
                                    </div>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-semibold text-gray-900 truncate">{{ tag.name }}</p>
                                    <p class="text-xs text-gray-500 truncate">{{ tag.slug }}</p>
                                    <p v-if="tag.posts_count !== undefined" class="text-xs text-blue-600 mt-1">
                                        {{ tag.posts_count }} {{ tag.posts_count === 1 ? 'post' : 'posts' }}
                                    </p>
                                </div>
                            </div>
                            <div class="flex items-center space-x-2 opacity-0 group-hover:opacity-100 transition-opacity">
                                <button
                                    @click="editTag(tag)"
                                    class="p-2 text-blue-600 hover:text-blue-800 hover:bg-blue-100 rounded-md transition-colors"
                                    title="Edit"
                                >
                                    <PencilIcon class="h-4 w-4" />
                                </button>
                                <button
                                    @click="deleteTag(tag)"
                                    class="p-2 text-red-600 hover:text-red-800 hover:bg-red-100 rounded-md transition-colors"
                                    title="Delete"
                                >
                                    <TrashIcon class="h-4 w-4" />
                                </button>
                            </div>
                        </div>
                    </div>

                    <div v-else class="text-center py-12">
                        <TagIcon class="mx-auto h-12 w-12 text-gray-400" />
                        <h3 class="mt-2 text-sm font-medium text-gray-900">No tags</h3>
                        <p class="mt-1 text-sm text-gray-500">Get started by creating a new tag.</p>
                        <div class="mt-6">
                            <button
                                @click="showCreateModal = true"
                                class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700"
                            >
                                <PlusIcon class="h-5 w-5 mr-2" />
                                Create Your First Tag
                            </button>
                        </div>
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
                                                <div class="flex items-center justify-center h-12 w-12 rounded-full bg-blue-100 mx-auto">
                                                    <TagIcon class="h-6 w-6 text-blue-600" />
                                                </div>
                                                <h3 class="text-lg font-medium leading-6 text-gray-900 text-center mt-3 mb-4">
                                                    {{ showEditModal ? 'Edit Tag' : 'Create New Tag' }}
                                                </h3>
                                                
                                                <div class="space-y-4">
                                                    <div>
                                                        <label for="name" class="block text-sm font-medium text-gray-700">
                                                            Tag Name <span class="text-red-500">*</span>
                                                        </label>
                                                        <input
                                                            id="name"
                                                            v-model="form.name"
                                                            type="text"
                                                            placeholder="e.g., Technology, Travel, Lifestyle"
                                                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                                            @input="generateSlug"
                                                            autofocus
                                                        />
                                                        <p v-if="form.errors.name" class="mt-1 text-sm text-red-600">{{ form.errors.name }}</p>
                                                    </div>

                                                    <div>
                                                        <label for="slug" class="block text-sm font-medium text-gray-700">
                                                            URL Slug
                                                        </label>
                                                        <div class="mt-1 flex rounded-md shadow-sm">
                                                            <span class="inline-flex items-center px-3 rounded-l-md border border-r-0 border-gray-300 bg-gray-50 text-gray-500 text-sm">
                                                                /tag/
                                                            </span>
                                                            <input
                                                                id="slug"
                                                                v-model="form.slug"
                                                                type="text"
                                                                placeholder="auto-generated"
                                                                class="flex-1 block w-full rounded-none rounded-r-md border-gray-300 focus:border-blue-500 focus:ring-blue-500"
                                                            />
                                                        </div>
                                                        <p class="mt-1 text-xs text-gray-500">
                                                            Auto-generated from tag name if left empty
                                                        </p>
                                                        <p v-if="form.errors.slug" class="mt-1 text-sm text-red-600">{{ form.errors.slug }}</p>
                                                    </div>

                                                    <div class="bg-blue-50 border border-blue-200 rounded-md p-3">
                                                        <div class="flex">
                                                            <div class="flex-shrink-0">
                                                                <svg class="h-5 w-5 text-blue-400" fill="currentColor" viewBox="0 0 20 20">
                                                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                                                                </svg>
                                                            </div>
                                                            <div class="ml-3">
                                                                <p class="text-sm text-blue-700">
                                                                    Tags help readers find related content and improve your blog's organization.
                                                                </p>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="mt-5 sm:mt-6 sm:grid sm:grid-flow-row-dense sm:grid-cols-2 sm:gap-3">
                                                <button
                                                    type="submit"
                                                    :disabled="form.processing"
                                                    class="inline-flex w-full justify-center items-center rounded-md bg-blue-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-blue-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-blue-600 sm:col-start-2 disabled:opacity-50"
                                                >
                                                    <CheckIcon v-if="!form.processing" class="h-5 w-5 mr-1" />
                                                    <svg v-else class="animate-spin h-5 w-5 mr-1" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                                    </svg>
                                                    {{ form.processing ? 'Saving...' : 'Save Tag' }}
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
import { PlusIcon, TagIcon, PencilIcon, TrashIcon, CheckIcon } from '@heroicons/vue/24/outline';

const props = defineProps({
    tags: Array,
});

const showCreateModal = ref(false);
const showEditModal = ref(false);
const editingTag = ref(null);

const form = useForm({
    name: '',
    slug: '',
});

const generateSlug = () => {
    if (!form.slug || (editingTag.value && form.slug === slugify(editingTag.value.name))) {
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

const editTag = (tag) => {
    editingTag.value = tag;
    form.name = tag.name;
    form.slug = tag.slug;
    showEditModal.value = true;
};

const closeModal = () => {
    showCreateModal.value = false;
    showEditModal.value = false;
    editingTag.value = null;
    form.reset();
    form.clearErrors();
};

const submitForm = () => {
    if (showEditModal.value && editingTag.value) {
        form.put(route('admin.blog.tags.update', editingTag.value.id), {
            preserveScroll: true,
            onSuccess: () => closeModal(),
        });
    } else {
        form.post(route('admin.blog.tags.store'), {
            preserveScroll: true,
            onSuccess: () => closeModal(),
        });
    }
};

const deleteTag = (tag) => {
    if (confirm(`Are you sure you want to delete "${tag.name}"?\n\nThis action cannot be undone.`)) {
        router.delete(route('admin.blog.tags.destroy', tag.id), {
            preserveScroll: true,
        });
    }
};
</script>
