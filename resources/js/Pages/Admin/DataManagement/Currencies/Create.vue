<template>
    <AdminLayout>
        <Head title="Add Currency" />

        <div class="py-6">
            <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
                <!-- Header -->
                <div class="mb-6">
                    <Link :href="route('admin.data.currencies.index')" class="text-sm text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 mb-2 inline-block">
                        ← Back to Currencies
                    </Link>
                    <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Add New Currency</h1>
                </div>

                <!-- Form -->
                <form @submit.prevent="submit" class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Currency Code -->
                        <div>
                            <label for="code" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Currency Code <span class="text-red-500">*</span>
                            </label>
                            <input
                                id="code"
                                v-model="form.code"
                                type="text"
                                maxlength="3"
                                required
                                placeholder="USD"
                                class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-indigo-500 focus:border-indigo-500 uppercase"
                                :class="{ 'border-red-500': form.errors.code }"
                            />
                            <p v-if="form.errors.code" class="mt-1 text-sm text-red-600 dark:text-red-400">{{ form.errors.code }}</p>
                            <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">3-character ISO currency code</p>
                        </div>

                        <!-- Currency Name -->
                        <div>
                            <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Currency Name <span class="text-red-500">*</span>
                            </label>
                            <input
                                id="name"
                                v-model="form.name"
                                type="text"
                                required
                                placeholder="US Dollar"
                                class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-indigo-500 focus:border-indigo-500"
                                :class="{ 'border-red-500': form.errors.name }"
                            />
                            <p v-if="form.errors.name" class="mt-1 text-sm text-red-600 dark:text-red-400">{{ form.errors.name }}</p>
                        </div>

                        <!-- Symbol -->
                        <div>
                            <label for="symbol" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Symbol <span class="text-red-500">*</span>
                            </label>
                            <input
                                id="symbol"
                                v-model="form.symbol"
                                type="text"
                                required
                                placeholder="$"
                                class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-indigo-500 focus:border-indigo-500"
                                :class="{ 'border-red-500': form.errors.symbol }"
                            />
                            <p v-if="form.errors.symbol" class="mt-1 text-sm text-red-600 dark:text-red-400">{{ form.errors.symbol }}</p>
                        </div>

                        <!-- Exchange Rate -->
                        <div>
                            <label for="exchange_rate_to_bdt" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Exchange Rate to BDT <span class="text-red-500">*</span>
                            </label>
                            <input
                                id="exchange_rate_to_bdt"
                                v-model="form.exchange_rate_to_bdt"
                                type="number"
                                step="0.01"
                                min="0"
                                required
                                placeholder="110.50"
                                class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-indigo-500 focus:border-indigo-500"
                                :class="{ 'border-red-500': form.errors.exchange_rate_to_bdt }"
                            />
                            <p v-if="form.errors.exchange_rate_to_bdt" class="mt-1 text-sm text-red-600 dark:text-red-400">{{ form.errors.exchange_rate_to_bdt }}</p>
                            <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">1 {{ form.code || 'XXX' }} = X BDT</p>
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
                            :href="route('admin.data.currencies.index')"
                            class="px-4 py-2 bg-gray-200 hover:bg-gray-300 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-300 rounded-lg transition-colors"
                        >
                            Cancel
                        </Link>
                        <button
                            type="submit"
                            :disabled="form.processing"
                            class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg transition-colors disabled:opacity-50"
                        >
                            {{ form.processing ? 'Saving...' : 'Save Currency' }}
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

const form = useForm({
    code: '',
    name: '',
    symbol: '',
    exchange_rate_to_bdt: '',
    is_active: true,
});

const submit = () => {
    form.post(route('admin.data.currencies.store'));
};
</script>
