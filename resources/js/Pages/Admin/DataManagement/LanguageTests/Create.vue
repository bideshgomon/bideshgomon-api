<template>
    <AdminLayout>
        <Head title="Add Language Test" />

        <div class="py-6">
            <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
                <!-- Header -->
                <div class="mb-6">
                    <Link :href="route('admin.data.language-tests.index')" class="text-sm text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 mb-2 inline-block">
                        ← Back to Language Tests
                    </Link>
                    <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Add New Language Test</h1>
                </div>

                <!-- Form -->
                <form @submit.prevent="submit" class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Language -->
                        <div class="md:col-span-2">
                            <label for="language_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Language</label>
                            <select
                                id="language_id"
                                v-model="form.language_id"
                                class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-indigo-500 focus:border-indigo-500"
                            >
                                <option value="">-- Select Language (Optional) --</option>
                                <option v-for="language in languages" :key="language.id" :value="language.id">{{ language.name }}</option>
                            </select>
                            <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Select the language this test is for (optional for general tests)</p>
                        </div>

                        <!-- Name (English) -->
                        <div>
                            <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Test Name (English) <span class="text-red-500">*</span>
                            </label>
                            <input
                                id="name"
                                v-model="form.name"
                                type="text"
                                required
                                placeholder="IELTS"
                                class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-indigo-500 focus:border-indigo-500"
                                :class="{ 'border-red-500': form.errors.name }"
                            />
                            <p v-if="form.errors.name" class="mt-1 text-sm text-red-600 dark:text-red-400">{{ form.errors.name }}</p>
                        </div>

                        <!-- Name (Bengali) -->
                        <div>
                            <label for="name_bn" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Test Name (Bengali)</label>
                            <input
                                id="name_bn"
                                v-model="form.name_bn"
                                type="text"
                                placeholder="আইইএলটিএস"
                                class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-indigo-500 focus:border-indigo-500"
                            />
                        </div>

                        <!-- Code -->
                        <div>
                            <label for="code" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Test Code <span class="text-red-500">*</span>
                            </label>
                            <input
                                id="code"
                                v-model="form.code"
                                type="text"
                                maxlength="50"
                                required
                                placeholder="ielts"
                                class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-indigo-500 focus:border-indigo-500"
                                :class="{ 'border-red-500': form.errors.code }"
                            />
                            <p v-if="form.errors.code" class="mt-1 text-sm text-red-600 dark:text-red-400">{{ form.errors.code }}</p>
                            <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Unique identifier (lowercase, no spaces)</p>
                        </div>

                        <!-- Score Type -->
                        <div>
                            <label for="score_type" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Score Type <span class="text-red-500">*</span>
                            </label>
                            <select
                                id="score_type"
                                v-model="form.score_type"
                                required
                                class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-indigo-500 focus:border-indigo-500"
                                :class="{ 'border-red-500': form.errors.score_type }"
                            >
                                <option value="">-- Select Score Type --</option>
                                <option value="band">Band (e.g., IELTS 1-9)</option>
                                <option value="percentage">Percentage (0-100%)</option>
                                <option value="points">Points (custom range)</option>
                                <option value="grade">Grade (A, B, C, etc.)</option>
                            </select>
                            <p v-if="form.errors.score_type" class="mt-1 text-sm text-red-600 dark:text-red-400">{{ form.errors.score_type }}</p>
                        </div>

                        <!-- Min Score -->
                        <div>
                            <label for="min_score" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Minimum Score</label>
                            <input
                                id="min_score"
                                v-model="form.min_score"
                                type="number"
                                step="0.01"
                                placeholder="0"
                                class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-indigo-500 focus:border-indigo-500"
                                :class="{ 'border-red-500': form.errors.min_score }"
                            />
                            <p v-if="form.errors.min_score" class="mt-1 text-sm text-red-600 dark:text-red-400">{{ form.errors.min_score }}</p>
                        </div>

                        <!-- Max Score -->
                        <div>
                            <label for="max_score" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Maximum Score</label>
                            <input
                                id="max_score"
                                v-model="form.max_score"
                                type="number"
                                step="0.01"
                                placeholder="9"
                                class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-indigo-500 focus:border-indigo-500"
                                :class="{ 'border-red-500': form.errors.max_score }"
                            />
                            <p v-if="form.errors.max_score" class="mt-1 text-sm text-red-600 dark:text-red-400">{{ form.errors.max_score }}</p>
                        </div>

                        <!-- Description -->
                        <div class="md:col-span-2">
                            <label for="description" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Description</label>
                            <textarea
                                id="description"
                                v-model="form.description"
                                rows="3"
                                placeholder="International English Language Testing System"
                                class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-indigo-500 focus:border-indigo-500"
                            ></textarea>
                            <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Brief description of the test (max 500 characters)</p>
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
                            :href="route('admin.data.language-tests.index')"
                            class="px-4 py-2 bg-gray-200 hover:bg-gray-300 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-300 rounded-lg transition-colors"
                        >
                            Cancel
                        </Link>
                        <button
                            type="submit"
                            :disabled="form.processing"
                            class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg transition-colors disabled:opacity-50"
                        >
                            {{ form.processing ? 'Saving...' : 'Save Language Test' }}
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
    languages: Array,
});

const form = useForm({
    language_id: '',
    name: '',
    name_bn: '',
    code: '',
    min_score: '',
    max_score: '',
    score_type: '',
    description: '',
    is_active: true,
});

const submit = () => {
    form.post(route('admin.data.language-tests.store'));
};
</script>
