<template>
    <AdminLayout>
        <Head title="Add Airport" />

        <div class="py-6">
            <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
                <!-- Header -->
                <div class="mb-6">
                    <Link :href="route('admin.data.airports.index')" class="text-sm text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 mb-2 inline-block">
                        ← Back to Airports
                    </Link>
                    <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Add New Airport</h1>
                </div>

                <!-- Form -->
                <form @submit.prevent="submit" class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- City -->
                        <div class="md:col-span-2">
                            <label for="city_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                City <span class="text-red-500">*</span>
                            </label>
                            <select
                                id="city_id"
                                v-model="form.city_id"
                                required
                                class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-indigo-500 focus:border-indigo-500"
                                :class="{ 'border-red-500': form.errors.city_id }"
                            >
                                <option value="">Select a city</option>
                                <option v-for="city in cities" :key="city.id" :value="city.id">{{ city.name }}</option>
                            </select>
                            <p v-if="form.errors.city_id" class="mt-1 text-sm text-red-600 dark:text-red-400">{{ form.errors.city_id }}</p>
                        </div>

                        <!-- Name (English) -->
                        <div>
                            <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Airport Name (English) <span class="text-red-500">*</span>
                            </label>
                            <input
                                id="name"
                                v-model="form.name"
                                type="text"
                                required
                                placeholder="Hazrat Shahjalal International Airport"
                                class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-indigo-500 focus:border-indigo-500"
                                :class="{ 'border-red-500': form.errors.name }"
                            />
                            <p v-if="form.errors.name" class="mt-1 text-sm text-red-600 dark:text-red-400">{{ form.errors.name }}</p>
                        </div>

                        <!-- Name (Bengali) -->
                        <div>
                            <label for="name_bn" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Airport Name (Bengali)</label>
                            <input
                                id="name_bn"
                                v-model="form.name_bn"
                                type="text"
                                placeholder="হযরত শাহজালাল আন্তর্জাতিক বিমানবন্দর"
                                class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-indigo-500 focus:border-indigo-500"
                            />
                        </div>

                        <!-- IATA Code -->
                        <div>
                            <label for="iata_code" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                IATA Code (3 letters) <span class="text-red-500">*</span>
                            </label>
                            <input
                                id="iata_code"
                                v-model="form.iata_code"
                                type="text"
                                maxlength="3"
                                required
                                placeholder="DAC"
                                class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-indigo-500 focus:border-indigo-500 uppercase"
                                :class="{ 'border-red-500': form.errors.iata_code }"
                                @input="form.iata_code = form.iata_code.toUpperCase()"
                            />
                            <p v-if="form.errors.iata_code" class="mt-1 text-sm text-red-600 dark:text-red-400">{{ form.errors.iata_code }}</p>
                            <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">3-letter IATA code (e.g., DAC, DXB, LHR)</p>
                        </div>

                        <!-- ICAO Code -->
                        <div>
                            <label for="icao_code" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">ICAO Code (4 letters)</label>
                            <input
                                id="icao_code"
                                v-model="form.icao_code"
                                type="text"
                                maxlength="4"
                                placeholder="VGHS"
                                class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-indigo-500 focus:border-indigo-500 uppercase"
                                @input="form.icao_code = form.icao_code.toUpperCase()"
                            />
                            <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">4-letter ICAO code (e.g., VGHS, OMDB, EGLL)</p>
                        </div>

                        <!-- Latitude -->
                        <div>
                            <label for="latitude" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Latitude</label>
                            <input
                                id="latitude"
                                v-model="form.latitude"
                                type="number"
                                step="0.00000001"
                                min="-90"
                                max="90"
                                placeholder="23.8433"
                                class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-indigo-500 focus:border-indigo-500"
                            />
                        </div>

                        <!-- Longitude -->
                        <div>
                            <label for="longitude" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Longitude</label>
                            <input
                                id="longitude"
                                v-model="form.longitude"
                                type="number"
                                step="0.00000001"
                                min="-180"
                                max="180"
                                placeholder="90.3978"
                                class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-indigo-500 focus:border-indigo-500"
                            />
                        </div>

                        <!-- Flags -->
                        <div class="md:col-span-2 space-y-3">
                            <label class="flex items-center">
                                <input
                                    v-model="form.is_international"
                                    type="checkbox"
                                    class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500"
                                />
                                <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">International Airport</span>
                            </label>
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
                            :href="route('admin.data.airports.index')"
                            class="px-4 py-2 bg-gray-200 hover:bg-gray-300 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-300 rounded-lg transition-colors"
                        >
                            Cancel
                        </Link>
                        <button
                            type="submit"
                            :disabled="form.processing"
                            class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg transition-colors disabled:opacity-50"
                        >
                            {{ form.processing ? 'Saving...' : 'Save Airport' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </AdminLayout>
</template>

<script setup>
import { reactive, onMounted } from 'vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import axios from 'axios';

const cities = reactive([]);

const form = useForm({
    city_id: '',
    name: '',
    name_bn: '',
    iata_code: '',
    icao_code: '',
    latitude: '',
    longitude: '',
    is_international: false,
    is_active: true,
});

onMounted(async () => {
    const response = await axios.get('/api/cities');
    cities.push(...response.data);
});

const submit = () => {
    form.post(route('admin.data.airports.store'));
};
</script>
