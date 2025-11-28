<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { ArrowLeftIcon } from '@heroicons/vue/24/outline';

const props = defineProps({
    categories: Array,
});

const form = useForm({
    faq_category_id: '',
    question: '',
    answer: '',
    order: '',
    is_published: true,
});

const submit = () => {
    form.post(route('admin.faqs.store'), {
        onSuccess: () => {
            form.reset();
        }
    });
};
</script>

<template>
    <Head title="Create FAQ" />

    <AdminLayout>
        <div class="min-h-screen bg-gray-50 py-8">
            <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
                <Link
                    :href="route('admin.faqs.index')"
                    class="inline-flex items-center text-gray-600 hover:text-gray-900 mb-6"
                >
                    <ArrowLeftIcon class="h-5 w-5 mr-2" />
                    Back to FAQs
                </Link>

                <div class="bg-white rounded-2xl shadow-sm p-8">
                    <h1 class="text-2xl font-bold text-gray-900 mb-6">Create New FAQ</h1>

                    <form @submit.prevent="submit" class="space-y-6">
                        <!-- Category Selection -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Category *
                            </label>
                            <select
                                v-model="form.faq_category_id"
                                required
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500"
                            >
                                <option value="">Select Category</option>
                                <option v-for="category in categories" :key="category.id" :value="category.id">
                                    {{ category.name }}
                                </option>
                            </select>
                            <div v-if="form.errors.faq_category_id" class="text-red-600 text-sm mt-1">
                                {{ form.errors.faq_category_id }}
                            </div>
                        </div>

                        <!-- Question -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Question *
                            </label>
                            <input
                                v-model="form.question"
                                type="text"
                                required
                                maxlength="500"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500"
                                placeholder="e.g., What documents do I need for a student visa?"
                            />
                            <p class="text-xs text-gray-500 mt-1">
                                {{ form.question.length }}/500 characters
                            </p>
                            <div v-if="form.errors.question" class="text-red-600 text-sm mt-1">
                                {{ form.errors.question }}
                            </div>
                        </div>

                        <!-- Answer -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Answer *
                            </label>
                            <textarea
                                v-model="form.answer"
                                required
                                rows="8"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500"
                                placeholder="Provide a detailed answer to the question..."
                            ></textarea>
                            <p class="text-xs text-gray-500 mt-1">
                                You can use HTML for formatting if needed
                            </p>
                            <div v-if="form.errors.answer" class="text-red-600 text-sm mt-1">
                                {{ form.errors.answer }}
                            </div>
                        </div>

                        <!-- Order -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Display Order
                            </label>
                            <input
                                v-model="form.order"
                                type="number"
                                min="0"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500"
                                placeholder="Leave empty to add at the end"
                            />
                            <p class="text-xs text-gray-500 mt-1">
                                Lower numbers appear first. Leave empty to auto-assign.
                            </p>
                            <div v-if="form.errors.order" class="text-red-600 text-sm mt-1">
                                {{ form.errors.order }}
                            </div>
                        </div>

                        <!-- Publishing Status -->
                        <div class="border-t border-gray-200 pt-6">
                            <div class="flex items-center">
                                <input
                                    v-model="form.is_published"
                                    type="checkbox"
                                    id="is_published"
                                    class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded"
                                />
                                <label for="is_published" class="ml-2 text-sm font-medium text-gray-700">
                                    Publish immediately
                                </label>
                            </div>
                            <p class="text-xs text-gray-500 mt-1 ml-6">
                                Uncheck to save as draft
                            </p>
                        </div>

                        <!-- Form Actions -->
                        <div class="flex items-center justify-between pt-6 border-t border-gray-200">
                            <Link
                                :href="route('admin.faqs.index')"
                                class="px-6 py-3 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors font-medium"
                            >
                                Cancel
                            </Link>
                            <button
                                type="submit"
                                :disabled="form.processing"
                                class="px-6 py-3 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors font-medium disabled:opacity-50 disabled:cursor-not-allowed"
                            >
                                <span v-if="form.processing">Creating...</span>
                                <span v-else>Create FAQ</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>
