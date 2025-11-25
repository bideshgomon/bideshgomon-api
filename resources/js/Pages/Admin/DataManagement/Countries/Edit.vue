<template>
    <AdminLayout>
        <Head title="Edit Country" />

        <div class="py-6">
            <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
                <!-- Header -->
                <div class="mb-6">
                    <Link :href="route('admin.data.countries.index')" class="text-sm text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 mb-2 inline-block">
                        ← Back to Countries
                    </Link>
                    <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Edit Country</h1>
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
                                class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-indigo-500 focus:border-indigo-500"
                            />
                        </div>

                        <!-- ISO Code 2 -->
                        <div>
                            <label for="iso_code_2" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                ISO Code (2-char) <span class="text-red-500">*</span>
                            </label>
                            <input
                                id="iso_code_2"
                                v-model="form.iso_code_2"
                                type="text"
                                maxlength="2"
                                required
                                placeholder="BD"
                                class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-indigo-500 focus:border-indigo-500 uppercase"
                                :class="{ 'border-red-500': form.errors.iso_code_2 }"
                            />
                            <p v-if="form.errors.iso_code_2" class="mt-1 text-sm text-red-600 dark:text-red-400">{{ form.errors.iso_code_2 }}</p>
                        </div>

                        <!-- ISO Code 3 -->
                        <div>
                            <label for="iso_code_3" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                ISO Code (3-char) <span class="text-red-500">*</span>
                            </label>
                            <input
                                id="iso_code_3"
                                v-model="form.iso_code_3"
                                type="text"
                                maxlength="3"
                                required
                                placeholder="BGD"
                                class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-indigo-500 focus:border-indigo-500 uppercase"
                                :class="{ 'border-red-500': form.errors.iso_code_3 }"
                            />
                            <p v-if="form.errors.iso_code_3" class="mt-1 text-sm text-red-600 dark:text-red-400">{{ form.errors.iso_code_3 }}</p>
                        </div>

                        <!-- Phone Code -->
                        <div>
                            <label for="phone_code" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Phone Code <span class="text-red-500">*</span>
                            </label>
                            <input
                                id="phone_code"
                                v-model="form.phone_code"
                                type="text"
                                required
                                placeholder="+880"
                                class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-indigo-500 focus:border-indigo-500"
                                :class="{ 'border-red-500': form.errors.phone_code }"
                            />
                            <p v-if="form.errors.phone_code" class="mt-1 text-sm text-red-600 dark:text-red-400">{{ form.errors.phone_code }}</p>
                        </div>

                        <!-- Currency Code -->
                        <div>
                            <label for="currency_code" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Currency Code <span class="text-red-500">*</span>
                            </label>
                            <input
                                id="currency_code"
                                v-model="form.currency_code"
                                type="text"
                                maxlength="3"
                                required
                                placeholder="BDT"
                                class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-indigo-500 focus:border-indigo-500 uppercase"
                                :class="{ 'border-red-500': form.errors.currency_code }"
                            />
                            <p v-if="form.errors.currency_code" class="mt-1 text-sm text-red-600 dark:text-red-400">{{ form.errors.currency_code }}</p>
                        </div>

                        <!-- Flag Emoji -->
                        <div>
                            <label for="flag_emoji" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Flag Emoji</label>
                            <input
                                id="flag_emoji"
                                v-model="form.flag_emoji"
                                type="text"
                                placeholder="🇧🇩"
                                class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-indigo-500 focus:border-indigo-500"
                            />
                        </div>

                        <!-- Region -->
                        <div>
                            <label for="region" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Region</label>
                            <input
                                id="region"
                                v-model="form.region"
                                type="text"
                                placeholder="South Asia"
                                class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-indigo-500 focus:border-indigo-500"
                            />
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
                            :href="route('admin.data.countries.index')"
                            class="px-4 py-2 bg-gray-200 hover:bg-gray-300 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-300 rounded-lg transition-colors"
                        >
                            Cancel
                        </Link>
                        <button
                            type="submit"
                            :disabled="form.processing"
                            class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg transition-colors disabled:opacity-50"
                        >
                            {{ form.processing ? 'Updating...' : 'Update Country' }}
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
    country: Object,
});

const form = useForm({
    name: props.country.name,
    name_bn: props.country.name_bn,
    iso_code_2: props.country.iso_code_2,
    iso_code_3: props.country.iso_code_3,
    phone_code: props.country.phone_code,
    currency_code: props.country.currency_code,
    flag_emoji: props.country.flag_emoji,
    region: props.country.region,
    is_active: props.country.is_active,
});

const submit = () => {
    form.put(route('admin.data.countries.update', props.country.id));
};
</script>
