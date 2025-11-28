<template>
    <AdminLayout>
        <Head title="Create Directory Category" />

        <div class="py-12">
            <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
                <!-- Header -->
                <div class="mb-6">
                    <div class="flex items-center mb-4">
                        <Link
                            :href="route('admin.directory-categories.index')"
                            class="text-gray-600 hover:text-gray-900 mr-4"
                        >
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                            </svg>
                        </Link>
                        <div>
                            <h2 class="text-2xl font-bold text-gray-900">Create Directory Category</h2>
                            <p class="mt-1 text-sm text-gray-600">Add a new category for organizing directories</p>
                        </div>
                    </div>
                </div>

                <!-- Form -->
                <div class="bg-white rounded-lg shadow-sm">
                    <form @submit.prevent="submit" class="p-6 space-y-6">
                        <!-- English Name -->
                        <div>
                            <label for="name" class="block text-sm font-medium text-gray-700 mb-1">
                                Name (English) <span class="text-red-500">*</span>
                            </label>
                            <input
                                id="name"
                                v-model="form.name"
                                type="text"
                                class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                :class="{ 'border-red-500': form.errors.name }"
                                placeholder="e.g., Embassy, Airline, Training Center"
                                required
                            />
                            <p v-if="form.errors.name" class="mt-1 text-sm text-red-600">{{ form.errors.name }}</p>
                        </div>

                        <!-- Bengali Name -->
                        <div>
                            <label for="name_bn" class="block text-sm font-medium text-gray-700 mb-1">
                                Name (Bengali) <span class="text-red-500">*</span>
                            </label>
                            <input
                                id="name_bn"
                                v-model="form.name_bn"
                                type="text"
                                class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                :class="{ 'border-red-500': form.errors.name_bn }"
                                placeholder="বাংলায় নাম লিখুন"
                                required
                            />
                            <p v-if="form.errors.name_bn" class="mt-1 text-sm text-red-600">{{ form.errors.name_bn }}</p>
                        </div>

                        <!-- Description -->
                        <div>
                            <label for="description" class="block text-sm font-medium text-gray-700 mb-1">
                                Description
                            </label>
                            <textarea
                                id="description"
                                v-model="form.description"
                                rows="3"
                                class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                :class="{ 'border-red-500': form.errors.description }"
                                placeholder="Brief description of this category..."
                            ></textarea>
                            <p v-if="form.errors.description" class="mt-1 text-sm text-red-600">{{ form.errors.description }}</p>
                        </div>

                        <!-- Icon & Color -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Icon -->
                            <div>
                                <label for="icon" class="block text-sm font-medium text-gray-700 mb-1">
                                    Icon Class
                                </label>
                                <input
                                    id="icon"
                                    v-model="form.icon"
                                    type="text"
                                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                    :class="{ 'border-red-500': form.errors.icon }"
                                    placeholder="e.g., fas fa-building, fas fa-plane"
                                />
                                <p class="mt-1 text-xs text-gray-500">Use FontAwesome class names</p>
                                <p v-if="form.errors.icon" class="mt-1 text-sm text-red-600">{{ form.errors.icon }}</p>
                                
                                <!-- Icon Preview -->
                                <div v-if="form.icon" class="mt-3">
                                    <div class="inline-flex items-center justify-center w-12 h-12 rounded-lg text-white text-xl"
                                         :style="{ backgroundColor: form.color || '#3B82F6' }">
                                        <i :class="form.icon"></i>
                                    </div>
                                    <span class="ml-2 text-sm text-gray-600">Preview</span>
                                </div>
                            </div>

                            <!-- Color -->
                            <div>
                                <label for="color" class="block text-sm font-medium text-gray-700 mb-1">
                                    Color
                                </label>
                                <div class="flex items-center space-x-3">
                                    <input
                                        id="color"
                                        v-model="form.color"
                                        type="color"
                                        class="h-10 w-20 rounded border-gray-300 cursor-pointer"
                                    />
                                    <input
                                        v-model="form.color"
                                        type="text"
                                        class="flex-1 rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                        :class="{ 'border-red-500': form.errors.color }"
                                        placeholder="#3B82F6"
                                    />
                                </div>
                                <p v-if="form.errors.color" class="mt-1 text-sm text-red-600">{{ form.errors.color }}</p>
                                
                                <!-- Common Colors -->
                                <div class="mt-3 flex flex-wrap gap-2">
                                    <button
                                        v-for="color in commonColors"
                                        :key="color.value"
                                        @click.prevent="form.color = color.value"
                                        type="button"
                                        class="w-8 h-8 rounded border-2 border-gray-300 hover:border-blue-500 transition-colors"
                                        :style="{ backgroundColor: color.value }"
                                        :title="color.name"
                                    ></button>
                                </div>
                            </div>
                        </div>

                        <!-- Sort Order -->
                        <div>
                            <label for="sort_order" class="block text-sm font-medium text-gray-700 mb-1">
                                Sort Order
                            </label>
                            <input
                                id="sort_order"
                                v-model.number="form.sort_order"
                                type="number"
                                min="0"
                                class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                :class="{ 'border-red-500': form.errors.sort_order }"
                            />
                            <p class="mt-1 text-xs text-gray-500">Lower numbers appear first</p>
                            <p v-if="form.errors.sort_order" class="mt-1 text-sm text-red-600">{{ form.errors.sort_order }}</p>
                        </div>

                        <!-- Active Status -->
                        <div>
                            <label class="flex items-center">
                                <input
                                    v-model="form.is_active"
                                    type="checkbox"
                                    class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                />
                                <span class="ml-2 text-sm text-gray-700">Active (visible to users)</span>
                            </label>
                            <p v-if="form.errors.is_active" class="mt-1 text-sm text-red-600">{{ form.errors.is_active }}</p>
                        </div>

                        <!-- Submit Buttons -->
                        <div class="flex items-center justify-end space-x-3 pt-6 border-t">
                            <Link
                                :href="route('admin.directory-categories.index')"
                                class="px-4 py-2 bg-white border border-gray-300 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-50"
                            >
                                Cancel
                            </Link>
                            <button
                                type="submit"
                                class="px-4 py-2 bg-blue-600 border border-transparent rounded-md text-sm font-medium text-white hover:bg-blue-700 disabled:opacity-50"
                                :disabled="form.processing"
                            >
                                <span v-if="form.processing">Creating...</span>
                                <span v-else>Create Category</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>

<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';

const form = useForm({
    name: '',
    name_bn: '',
    description: '',
    icon: '',
    color: '#3B82F6',
    sort_order: 0,
    is_active: true
});

const commonColors = [
    { name: 'Blue', value: '#3B82F6' },
    { name: 'Green', value: '#10B981' },
    { name: 'Red', value: '#EF4444' },
    { name: 'Yellow', value: '#F59E0B' },
    { name: 'Purple', value: '#8B5CF6' },
    { name: 'Pink', value: '#EC4899' },
    { name: 'Indigo', value: '#6366F1' },
    { name: 'Gray', value: '#6B7280' },
    { name: 'Teal', value: '#14B8A6' },
    { name: 'Orange', value: '#F97316' }
];

const submit = () => {
    form.post(route('admin.directory-categories.store'), {
        preserveScroll: true,
        onSuccess: () => {
            // Will redirect to index on success
        }
    });
};
</script>
