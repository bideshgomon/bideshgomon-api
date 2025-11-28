<script setup>
import { ref } from 'vue';
import { Head, useForm } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { ArrowLeftIcon } from '@heroicons/vue/24/outline';

const props = defineProps({
    pageTypes: Object,
    templates: Object,
});

const form = useForm({
    title: '',
    slug: '',
    type: 'custom',
    template: 'default',
    content: '',
    meta_title: '',
    meta_description: '',
    meta_keywords: '',
    is_published: false,
    show_in_menu: false,
    menu_order: 0,
});

const generateSlug = () => {
    if (form.title) {
        form.slug = form.title
            .toLowerCase()
            .replace(/[^a-z0-9]+/g, '-')
            .replace(/^-+|-+$/g, '');
    }
};

const submit = () => {
    form.post(route('admin.pages.store'), {
        preserveScroll: true,
    });
};
</script>

<template>
    <Head title="Create New Page" />

    <AdminLayout>
        <div class="py-6">
            <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
                <!-- Header -->
                <div class="mb-6">
                    <div class="flex items-center gap-3 mb-2">
                        <a
                            :href="route('admin.pages.index')"
                            class="text-gray-400 hover:text-gray-600 transition-colors"
                        >
                            <ArrowLeftIcon class="h-5 w-5" />
                        </a>
                        <h1 class="text-3xl font-bold text-gray-900">Create New Page</h1>
                    </div>
                    <p class="text-sm text-gray-600">Create a new CMS page for your website</p>
                </div>

                <!-- Form -->
                <form @submit.prevent="submit" class="space-y-6">
                    <!-- Basic Information -->
                    <div class="bg-white shadow-sm rounded-lg p-6">
                        <h2 class="text-lg font-semibold text-gray-900 mb-4">Basic Information</h2>
                        
                        <div class="space-y-4">
                            <!-- Title -->
                            <div>
                                <label for="title" class="block text-sm font-medium text-gray-700 mb-1">
                                    Page Title *
                                </label>
                                <input
                                    id="title"
                                    v-model="form.title"
                                    type="text"
                                    required
                                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                    @blur="generateSlug"
                                />
                                <p v-if="form.errors.title" class="mt-1 text-sm text-red-600">{{ form.errors.title }}</p>
                            </div>

                            <!-- Slug -->
                            <div>
                                <label for="slug" class="block text-sm font-medium text-gray-700 mb-1">
                                    Slug *
                                    <span class="text-gray-500 font-normal">(URL-friendly version)</span>
                                </label>
                                <div class="flex items-center gap-2">
                                    <span class="text-sm text-gray-500">/pages/</span>
                                    <input
                                        id="slug"
                                        v-model="form.slug"
                                        type="text"
                                        required
                                        class="flex-1 rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                    />
                                </div>
                                <p v-if="form.errors.slug" class="mt-1 text-sm text-red-600">{{ form.errors.slug }}</p>
                            </div>

                            <!-- Type and Template -->
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label for="type" class="block text-sm font-medium text-gray-700 mb-1">
                                        Page Type *
                                    </label>
                                    <select
                                        id="type"
                                        v-model="form.type"
                                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                    >
                                        <option v-for="(label, value) in pageTypes" :key="value" :value="value">
                                            {{ label }}
                                        </option>
                                    </select>
                                    <p v-if="form.errors.type" class="mt-1 text-sm text-red-600">{{ form.errors.type }}</p>
                                </div>

                                <div>
                                    <label for="template" class="block text-sm font-medium text-gray-700 mb-1">
                                        Template
                                    </label>
                                    <select
                                        id="template"
                                        v-model="form.template"
                                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                    >
                                        <option v-for="(label, value) in templates" :key="value" :value="value">
                                            {{ label }}
                                        </option>
                                    </select>
                                </div>
                            </div>

                            <!-- Content -->
                            <div>
                                <label for="content" class="block text-sm font-medium text-gray-700 mb-1">
                                    Page Content *
                                </label>
                                <textarea
                                    id="content"
                                    v-model="form.content"
                                    rows="10"
                                    required
                                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 font-mono text-sm"
                                    placeholder="Enter your page content here..."
                                ></textarea>
                                <p v-if="form.errors.content" class="mt-1 text-sm text-red-600">{{ form.errors.content }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- SEO Settings -->
                    <div class="bg-white shadow-sm rounded-lg p-6">
                        <h2 class="text-lg font-semibold text-gray-900 mb-4">SEO Settings</h2>
                        
                        <div class="space-y-4">
                            <div>
                                <label for="meta_title" class="block text-sm font-medium text-gray-700 mb-1">
                                    Meta Title
                                </label>
                                <input
                                    id="meta_title"
                                    v-model="form.meta_title"
                                    type="text"
                                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                    placeholder="Leave empty to use page title"
                                />
                            </div>

                            <div>
                                <label for="meta_description" class="block text-sm font-medium text-gray-700 mb-1">
                                    Meta Description
                                </label>
                                <textarea
                                    id="meta_description"
                                    v-model="form.meta_description"
                                    rows="3"
                                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                    placeholder="Brief description for search engines"
                                ></textarea>
                            </div>

                            <div>
                                <label for="meta_keywords" class="block text-sm font-medium text-gray-700 mb-1">
                                    Meta Keywords
                                </label>
                                <input
                                    id="meta_keywords"
                                    v-model="form.meta_keywords"
                                    type="text"
                                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                    placeholder="keyword1, keyword2, keyword3"
                                />
                            </div>
                        </div>
                    </div>

                    <!-- Publishing Options -->
                    <div class="bg-white shadow-sm rounded-lg p-6">
                        <h2 class="text-lg font-semibold text-gray-900 mb-4">Publishing Options</h2>
                        
                        <div class="space-y-4">
                            <div class="flex items-center gap-3">
                                <input
                                    id="is_published"
                                    v-model="form.is_published"
                                    type="checkbox"
                                    class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500"
                                />
                                <label for="is_published" class="text-sm font-medium text-gray-700">
                                    Publish this page immediately
                                </label>
                            </div>

                            <div class="flex items-center gap-3">
                                <input
                                    id="show_in_menu"
                                    v-model="form.show_in_menu"
                                    type="checkbox"
                                    class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500"
                                />
                                <label for="show_in_menu" class="text-sm font-medium text-gray-700">
                                    Show in navigation menu
                                </label>
                            </div>

                            <div v-if="form.show_in_menu">
                                <label for="menu_order" class="block text-sm font-medium text-gray-700 mb-1">
                                    Menu Order
                                </label>
                                <input
                                    id="menu_order"
                                    v-model.number="form.menu_order"
                                    type="number"
                                    min="0"
                                    class="w-32 rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                />
                                <p class="mt-1 text-sm text-gray-500">Lower numbers appear first</p>
                            </div>
                        </div>
                    </div>

                    <!-- Submit Buttons -->
                    <div class="flex items-center justify-end gap-3">
                        <a
                            :href="route('admin.pages.index')"
                            class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50 transition-colors"
                        >
                            Cancel
                        </a>
                        <button
                            type="submit"
                            :disabled="form.processing"
                            class="px-6 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 disabled:opacity-50 disabled:cursor-not-allowed transition-colors"
                        >
                            {{ form.processing ? 'Creating...' : 'Create Page' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </AdminLayout>
</template>
