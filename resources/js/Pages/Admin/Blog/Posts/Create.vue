<template>
    <Head title="Create Blog Post" />

    <AdminLayout>
        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <!-- Header -->
                <div class="mb-6 flex items-center justify-between">
                    <div>
                        <h2 class="text-3xl font-bold text-gray-900">Create New Blog Post</h2>
                        <p class="mt-1 text-sm text-gray-600">Write and publish engaging content for your audience</p>
                    </div>
                    <Link
                        :href="route('admin.blog.posts.index')"
                        class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-50"
                    >
                        <ArrowLeftIcon class="h-4 w-4 mr-2" />
                        Back to Posts
                    </Link>
                </div>

                <form @submit.prevent="submitForm" class="space-y-6">
                    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                        <!-- Main Content Column -->
                        <div class="lg:col-span-2 space-y-6">
                            <!-- Title -->
                            <div class="bg-white rounded-lg shadow-sm p-6">
                                <label for="title" class="block text-sm font-medium text-gray-700 mb-2">
                                    Title <span class="text-red-500">*</span>
                                </label>
                                <input
                                    id="title"
                                    v-model="form.title"
                                    type="text"
                                    placeholder="Enter post title..."
                                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                    @input="generateSlug"
                                />
                                <p v-if="form.errors.title" class="mt-2 text-sm text-red-600">{{ form.errors.title }}</p>
                            </div>

                            <!-- Slug -->
                            <div class="bg-white rounded-lg shadow-sm p-6">
                                <label for="slug" class="block text-sm font-medium text-gray-700 mb-2">
                                    URL Slug
                                </label>
                                <div class="flex items-center text-sm text-gray-500 mb-2">
                                    <span>{{ appUrl }}/blog/</span>
                                    <input
                                        id="slug"
                                        v-model="form.slug"
                                        type="text"
                                        placeholder="auto-generated"
                                        class="flex-1 ml-1 rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                    />
                                </div>
                                <p v-if="form.errors.slug" class="mt-2 text-sm text-red-600">{{ form.errors.slug }}</p>
                            </div>

                            <!-- Excerpt -->
                            <div class="bg-white rounded-lg shadow-sm p-6">
                                <label for="excerpt" class="block text-sm font-medium text-gray-700 mb-2">
                                    Excerpt
                                </label>
                                <textarea
                                    id="excerpt"
                                    v-model="form.excerpt"
                                    rows="3"
                                    placeholder="Brief summary of the post..."
                                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                ></textarea>
                                <p class="mt-1 text-xs text-gray-500">Short description shown in post listings</p>
                                <p v-if="form.errors.excerpt" class="mt-2 text-sm text-red-600">{{ form.errors.excerpt }}</p>
                            </div>

                            <!-- Content -->
                            <div class="bg-white rounded-lg shadow-sm p-6">
                                <label for="content" class="block text-sm font-medium text-gray-700 mb-2">
                                    Content <span class="text-red-500">*</span>
                                </label>
                                <textarea
                                    id="content"
                                    v-model="form.content"
                                    rows="20"
                                    placeholder="Write your post content here..."
                                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 font-mono text-sm"
                                ></textarea>
                                <p class="mt-1 text-xs text-gray-500">Supports HTML and Markdown</p>
                                <p v-if="form.errors.content" class="mt-2 text-sm text-red-600">{{ form.errors.content }}</p>
                            </div>

                            <!-- SEO Section -->
                            <div class="bg-white rounded-lg shadow-sm p-6">
                                <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                                    <MagnifyingGlassIcon class="h-5 w-5 mr-2 text-gray-400" />
                                    SEO Settings
                                </h3>
                                
                                <div class="space-y-4">
                                    <div>
                                        <label for="meta_title" class="block text-sm font-medium text-gray-700 mb-2">
                                            Meta Title
                                        </label>
                                        <input
                                            id="meta_title"
                                            v-model="form.meta_title"
                                            type="text"
                                            placeholder="SEO title (defaults to post title)"
                                            class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                        />
                                        <p class="mt-1 text-xs text-gray-500">{{ (form.meta_title || form.title).length }}/60 characters</p>
                                    </div>

                                    <div>
                                        <label for="meta_description" class="block text-sm font-medium text-gray-700 mb-2">
                                            Meta Description
                                        </label>
                                        <textarea
                                            id="meta_description"
                                            v-model="form.meta_description"
                                            rows="3"
                                            placeholder="SEO description for search engines"
                                            class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                        ></textarea>
                                        <p class="mt-1 text-xs text-gray-500">{{ (form.meta_description || '').length }}/160 characters</p>
                                    </div>

                                    <div>
                                        <label for="meta_keywords" class="block text-sm font-medium text-gray-700 mb-2">
                                            Meta Keywords
                                        </label>
                                        <input
                                            id="meta_keywords"
                                            v-model="form.meta_keywords"
                                            type="text"
                                            placeholder="keyword1, keyword2, keyword3"
                                            class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                        />
                                        <p class="mt-1 text-xs text-gray-500">Separate keywords with commas</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Sidebar Column -->
                        <div class="lg:col-span-1 space-y-6">
                            <!-- Publish Settings -->
                            <div class="bg-white rounded-lg shadow-sm p-6">
                                <h3 class="text-lg font-semibold text-gray-900 mb-4">Publish Settings</h3>
                                
                                <div class="space-y-4">
                                    <div>
                                        <label for="status" class="block text-sm font-medium text-gray-700 mb-2">
                                            Status <span class="text-red-500">*</span>
                                        </label>
                                        <select
                                            id="status"
                                            v-model="form.status"
                                            class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                        >
                                            <option value="draft">Draft</option>
                                            <option value="published">Published</option>
                                            <option value="scheduled">Scheduled</option>
                                        </select>
                                        <p v-if="form.errors.status" class="mt-2 text-sm text-red-600">{{ form.errors.status }}</p>
                                    </div>

                                    <div v-if="form.status === 'published' || form.status === 'scheduled'">
                                        <label for="published_at" class="block text-sm font-medium text-gray-700 mb-2">
                                            Publish Date
                                        </label>
                                        <input
                                            id="published_at"
                                            v-model="form.published_at"
                                            type="datetime-local"
                                            class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                        />
                                        <p v-if="form.errors.published_at" class="mt-2 text-sm text-red-600">{{ form.errors.published_at }}</p>
                                    </div>

                                    <div class="flex items-center">
                                        <input
                                            id="is_featured"
                                            v-model="form.is_featured"
                                            type="checkbox"
                                            class="h-4 w-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500"
                                        />
                                        <label for="is_featured" class="ml-2 block text-sm text-gray-700">
                                            Featured Post
                                        </label>
                                    </div>
                                </div>
                            </div>

                            <!-- Category -->
                            <div class="bg-white rounded-lg shadow-sm p-6">
                                <label for="category_id" class="block text-sm font-medium text-gray-700 mb-2">
                                    Category <span class="text-red-500">*</span>
                                </label>
                                <select
                                    id="category_id"
                                    v-model="form.category_id"
                                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                >
                                    <option value="">Select a category</option>
                                    <option v-for="category in categories" :key="category.id" :value="category.id">
                                        {{ category.name }}
                                    </option>
                                </select>
                                <p v-if="form.errors.category_id" class="mt-2 text-sm text-red-600">{{ form.errors.category_id }}</p>
                            </div>

                            <!-- Tags -->
                            <div class="bg-white rounded-lg shadow-sm p-6">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Tags</label>
                                <div class="space-y-2 max-h-48 overflow-y-auto">
                                    <div v-for="tag in tags" :key="tag.id" class="flex items-center">
                                        <input
                                            :id="'tag-' + tag.id"
                                            v-model="form.tags"
                                            type="checkbox"
                                            :value="tag.id"
                                            class="h-4 w-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500"
                                        />
                                        <label :for="'tag-' + tag.id" class="ml-2 block text-sm text-gray-700">
                                            {{ tag.name }}
                                        </label>
                                    </div>
                                </div>
                                <p v-if="tags.length === 0" class="text-sm text-gray-500 italic">No tags available</p>
                            </div>

                            <!-- Featured Image -->
                            <div class="bg-white rounded-lg shadow-sm p-6">
                                <label for="featured_image" class="block text-sm font-medium text-gray-700 mb-2">
                                    Featured Image URL
                                </label>
                                <input
                                    id="featured_image"
                                    v-model="form.featured_image"
                                    type="text"
                                    placeholder="https://example.com/image.jpg"
                                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                />
                                <p class="mt-1 text-xs text-gray-500">Enter image URL or upload to your CDN</p>
                                
                                <div v-if="form.featured_image" class="mt-3">
                                    <img :src="form.featured_image" alt="Preview" class="w-full rounded-md" @error="imageError = true" />
                                    <p v-if="imageError" class="mt-2 text-sm text-red-600">Failed to load image</p>
                                </div>

                                <div class="mt-3">
                                    <label for="image_credit" class="block text-sm font-medium text-gray-700 mb-2">
                                        Image Credit
                                    </label>
                                    <input
                                        id="image_credit"
                                        v-model="form.image_credit"
                                        type="text"
                                        placeholder="Photo by John Doe"
                                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                    />
                                </div>
                            </div>

                            <!-- Submit Buttons -->
                            <div class="bg-white rounded-lg shadow-sm p-6">
                                <div class="space-y-3">
                                    <button
                                        type="submit"
                                        :disabled="form.processing"
                                        class="w-full inline-flex justify-center items-center px-4 py-3 bg-blue-600 border border-transparent rounded-md font-semibold text-sm text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 disabled:opacity-50 transition ease-in-out duration-150"
                                    >
                                        <CheckIcon v-if="!form.processing" class="h-5 w-5 mr-2" />
                                        <svg v-else class="animate-spin h-5 w-5 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                        </svg>
                                        {{ form.processing ? 'Creating...' : 'Create Post' }}
                                    </button>

                                    <Link
                                        :href="route('admin.blog.posts.index')"
                                        class="w-full inline-flex justify-center items-center px-4 py-3 bg-white border border-gray-300 rounded-md font-semibold text-sm text-gray-700 uppercase tracking-widest hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2"
                                    >
                                        Cancel
                                    </Link>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </AdminLayout>
</template>

<script setup>
import { ref, computed } from 'vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { ArrowLeftIcon, CheckIcon, MagnifyingGlassIcon } from '@heroicons/vue/24/outline';

const props = defineProps({
    categories: Array,
    tags: Array,
});

const appUrl = window.location.origin;
const imageError = ref(false);

const form = useForm({
    category_id: '',
    title: '',
    slug: '',
    excerpt: '',
    content: '',
    featured_image: '',
    image_credit: '',
    status: 'draft',
    published_at: '',
    is_featured: false,
    meta_title: '',
    meta_description: '',
    meta_keywords: '',
    tags: [],
});

const generateSlug = () => {
    if (!form.slug || form.slug === slugify(form.title)) {
        form.slug = slugify(form.title);
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

const submitForm = () => {
    form.post(route('admin.blog.posts.store'), {
        preserveScroll: true,
    });
};
</script>
