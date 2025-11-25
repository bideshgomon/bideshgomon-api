<template>
    <AdminLayout>
        <Head title="Edit Language" />

        <div class="py-6">
            <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
                <!-- Header -->
                <div class="mb-6">
                    <Link :href="route('admin.data.languages.index')" class="text-sm text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 mb-2 inline-block">
                        ← Back to Languages
                    </Link>
                    <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Edit Language</h1>
                </div>

                <!-- Form -->
                <form @submit.prevent="submit" class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Name (English) -->
                        <div>
                            <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Name (English) <span class="text-red-500">*</span>
                            </label>
                            <input
                                id="name"
                                v-model="form.name"
                                type="text"
                                required
                                placeholder="English"
                                class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-indigo-500 focus:border-indigo-500"
                                :class="{ 'border-red-500': form.errors.name }"
                            />
                            <p v-if="form.errors.name" class="mt-1 text-sm text-red-600 dark:text-red-400">{{ form.errors.name }}</p>
                        </div>

                        <!-- Name (Bengali) -->
                        <div>
                            <label for="name_bn" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Name (Bengali)</label>
                            <input
                                id="name_bn"
                                v-model="form.name_bn"
                                type="text"
                                placeholder="ইংরেজি"
                                class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-indigo-500 focus:border-indigo-500"
                            />
                        </div>

                        <!-- Language Code -->
                        <div>
                            <label for="code" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Language Code <span class="text-red-500">*</span>
                            </label>
                            <input
                                id="code"
                                v-model="form.code"
                                type="text"
                                maxlength="10"
                                required
                                placeholder="en"
                                class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-indigo-500 focus:border-indigo-500"
                                :class="{ 'border-red-500': form.errors.code }"
                            />
                            <p v-if="form.errors.code" class="mt-1 text-sm text-red-600 dark:text-red-400">{{ form.errors.code }}</p>
                            <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">ISO 639 code (e.g., en, bn, ar)</p>
                        </div>

                        <!-- Native Name -->
                        <div>
                            <label for="native_name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Native Name</label>
                            <input
                                id="native_name"
                                v-model="form.native_name"
                                type="text"
                                placeholder="English (in native script)"
                                class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-indigo-500 focus:border-indigo-500"
                            />
                            <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Name in the language's own script</p>
                        </div>

                        <!-- Status -->
                        <div class="md:col-span-2">
                            <label class="flex items-center">
                                <input
                                    v-model="form.is_active"
                                    type="checkbox"
                                    class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500"
                                />
                                <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">Active</span>
                            </label>
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="flex items-center justify-end space-x-3 mt-6 pt-6 border-t border-gray-200 dark:border-gray-700">
                        <Link
                            :href="route('admin.data.languages.index')"
                            class="px-4 py-2 bg-gray-200 hover:bg-gray-300 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-300 rounded-lg transition-colors"
                        >
                            Cancel
                        </Link>
                        <button
                            type="submit"
                            :disabled="form.processing"
                            class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg transition-colors disabled:opacity-50"
                        >
                            {{ form.processing ? 'Updating...' : 'Update Language' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </AdminLayout>
</template>

<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';

const props = defineProps({
    language: Object,
});

const form = useForm({
    name: props.language.name,
    name_bn: props.language.name_bn,
    code: props.language.code,
    native_name: props.language.native_name,
    is_active: props.language.is_active,
});

const submit = () => {
    form.put(route('admin.data.languages.update', props.language.id));
};
</script>
