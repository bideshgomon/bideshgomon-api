<template>
    <AdminLayout>
        <div class="px-4 sm:px-6 lg:px-8 py-8">
            <!-- Header -->
            <div class="mb-6">
                <Link :href="route('admin.data.cv-templates.index')" class="text-sm font-medium text-blue-600 hover:text-blue-500 dark:text-blue-400 flex items-center gap-1 mb-4">
                    <ChevronLeftIcon class="h-4 w-4" />
                    Back to CV Templates
                </Link>
                <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Create CV Template</h1>
                <p class="mt-2 text-sm text-gray-700 dark:text-gray-300">Add a new CV template with customization options.</p>
            </div>

            <!-- Form -->
            <form @submit.prevent="submit" class="space-y-6">
                <div class="bg-white dark:bg-gray-800 shadow-sm ring-1 ring-gray-900/5 rounded-lg p-6 space-y-6">
                    <!-- Name -->
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-900 dark:text-white">Template Name <span class="text-red-500">*</span></label>
                        <input
                            type="text"
                            id="name"
                            v-model="form.name"
                            class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-blue-500 focus:ring-blue-500"
                            required
                        />
                        <p v-if="form.errors.name" class="mt-1 text-sm text-red-600 dark:text-red-400">{{ form.errors.name }}</p>
                    </div>

                    <!-- Slug -->
                    <div>
                        <label for="slug" class="block text-sm font-medium text-gray-900 dark:text-white">Slug</label>
                        <input
                            type="text"
                            id="slug"
                            v-model="form.slug"
                            class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-blue-500 focus:ring-blue-500"
                            placeholder="Leave empty to auto-generate"
                        />
                        <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">URL-friendly identifier. Auto-generated from name if left empty.</p>
                        <p v-if="form.errors.slug" class="mt-1 text-sm text-red-600 dark:text-red-400">{{ form.errors.slug }}</p>
                    </div>

                    <!-- Description -->
                    <div>
                        <label for="description" class="block text-sm font-medium text-gray-900 dark:text-white">Description</label>
                        <textarea
                            id="description"
                            v-model="form.description"
                            rows="3"
                            class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-blue-500 focus:ring-blue-500"
                        ></textarea>
                        <p v-if="form.errors.description" class="mt-1 text-sm text-red-600 dark:text-red-400">{{ form.errors.description }}</p>
                    </div>

                    <!-- Thumbnail URL -->
                    <div>
                        <label for="thumbnail" class="block text-sm font-medium text-gray-900 dark:text-white">Thumbnail URL</label>
                        <input
                            type="text"
                            id="thumbnail"
                            v-model="form.thumbnail"
                            class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-blue-500 focus:ring-blue-500"
                            placeholder="/images/templates/my-template.png"
                        />
                        <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Path or URL to template preview image.</p>
                        <p v-if="form.errors.thumbnail" class="mt-1 text-sm text-red-600 dark:text-red-400">{{ form.errors.thumbnail }}</p>
                    </div>

                    <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                        <!-- Category -->
                        <div>
                            <label for="category" class="block text-sm font-medium text-gray-900 dark:text-white">Category <span class="text-red-500">*</span></label>
                            <select
                                id="category"
                                v-model="form.category"
                                class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                required
                            >
                                <option value="">Select Category</option>
                                <option value="professional">Professional</option>
                                <option value="creative">Creative</option>
                                <option value="modern">Modern</option>
                                <option value="traditional">Traditional</option>
                                <option value="academic">Academic</option>
                            </select>
                            <p v-if="form.errors.category" class="mt-1 text-sm text-red-600 dark:text-red-400">{{ form.errors.category }}</p>
                        </div>

                        <!-- Sort Order -->
                        <div>
                            <label for="sort_order" class="block text-sm font-medium text-gray-900 dark:text-white">Sort Order</label>
                            <input
                                type="number"
                                id="sort_order"
                                v-model.number="form.sort_order"
                                min="0"
                                class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-blue-500 focus:ring-blue-500"
                            />
                            <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Lower numbers appear first.</p>
                            <p v-if="form.errors.sort_order" class="mt-1 text-sm text-red-600 dark:text-red-400">{{ form.errors.sort_order }}</p>
                        </div>
                    </div>

                    <!-- Premium & Price -->
                    <div class="border-t border-gray-200 dark:border-gray-700 pt-6">
                        <div class="flex items-center gap-2 mb-4">
                            <input
                                type="checkbox"
                                id="is_premium"
                                v-model="form.is_premium"
                                class="h-4 w-4 rounded border-gray-300 dark:border-gray-600 text-blue-600 focus:ring-blue-500"
                            />
                            <label for="is_premium" class="text-sm font-medium text-gray-900 dark:text-white">Premium Template</label>
                        </div>

                        <div v-if="form.is_premium" class="ml-6">
                            <label for="price" class="block text-sm font-medium text-gray-900 dark:text-white">Price (BDT) <span class="text-red-500">*</span></label>
                            <div class="mt-1 relative rounded-md shadow-sm">
                                <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                                    <span class="text-gray-500 dark:text-gray-400 sm:text-sm">৳</span>
                                </div>
                                <input
                                    type="number"
                                    id="price"
                                    v-model.number="form.price"
                                    min="0"
                                    step="0.01"
                                    class="block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white pl-7 focus:border-blue-500 focus:ring-blue-500"
                                    :required="form.is_premium"
                                />
                            </div>
                            <p v-if="form.errors.price" class="mt-1 text-sm text-red-600 dark:text-red-400">{{ form.errors.price }}</p>
                        </div>
                    </div>

                    <!-- Color Scheme (JSON) -->
                    <div>
                        <label for="color_scheme" class="block text-sm font-medium text-gray-900 dark:text-white">Color Scheme (JSON)</label>
                        <textarea
                            id="color_scheme"
                            v-model="form.color_scheme"
                            rows="3"
                            class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-blue-500 focus:ring-blue-500 font-mono text-sm"
                            placeholder='{"primary":"#2563EB","secondary":"#64748B"}'
                        ></textarea>
                        <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">JSON object defining template colors. Example: {"primary":"#2563EB","secondary":"#64748B"}</p>
                        <p v-if="form.errors.color_scheme" class="mt-1 text-sm text-red-600 dark:text-red-400">{{ form.errors.color_scheme }}</p>
                    </div>

                    <!-- Sections (JSON) -->
                    <div>
                        <label for="sections" class="block text-sm font-medium text-gray-900 dark:text-white">Template Sections (JSON)</label>
                        <textarea
                            id="sections"
                            v-model="form.sections"
                            rows="4"
                            class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-blue-500 focus:ring-blue-500 font-mono text-sm"
                            placeholder='["personal_info","education","experience","skills"]'
                        ></textarea>
                        <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">JSON array of section identifiers. Example: ["personal_info","education","experience","skills","certifications"]</p>
                        <p v-if="form.errors.sections" class="mt-1 text-sm text-red-600 dark:text-red-400">{{ form.errors.sections }}</p>
                    </div>

                    <!-- Active Status -->
                    <div class="flex items-center gap-2">
                        <input
                            type="checkbox"
                            id="is_active"
                            v-model="form.is_active"
                            class="h-4 w-4 rounded border-gray-300 dark:border-gray-600 text-blue-600 focus:ring-blue-500"
                        />
                        <label for="is_active" class="text-sm font-medium text-gray-900 dark:text-white">Active</label>
                    </div>
                </div>

                <!-- Actions -->
                <div class="flex items-center justify-end gap-3">
                    <Link :href="route('admin.data.cv-templates.index')" class="px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm text-sm font-medium text-gray-700 dark:text-gray-200 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700">
                        Cancel
                    </Link>
                    <button
                        type="submit"
                        :disabled="form.processing"
                        class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 disabled:opacity-50"
                    >
                        <span v-if="form.processing">Creating...</span>
                        <span v-else>Create Template</span>
                    </button>
                </div>
            </form>
        </div>
    </AdminLayout>
</template>

<script setup>
import { useForm, Link } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'
import { ChevronLeftIcon } from '@heroicons/vue/24/outline'

const form = useForm({
    name: '',
    slug: '',
    description: '',
    thumbnail: '',
    category: 'professional',
    is_premium: false,
    price: 0,
    color_scheme: '',
    sections: '',
    sort_order: 0,
    is_active: true,
})

const submit = () => {
    form.post(route('admin.data.cv-templates.store'))
}
</script>
