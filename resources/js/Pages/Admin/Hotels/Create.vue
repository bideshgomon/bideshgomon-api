<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';

const form = useForm({
    name: '',
    description: '',
    city: '',
    area: '',
    address: '',
    country: 'Bangladesh',
    phone: '',
    email: '',
    website: '',
    star_rating: 3,
    property_type: 'hotel',
    check_in_time: '14:00',
    check_out_time: '11:00',
    is_active: true,
    is_featured: false,
});

const submit = () => {
    form.post(route('admin.hotels.store'));
};
</script>

<template>
    <Head title="Add Hotel - Admin" />

    <AdminLayout>
        <div class="py-12">
            <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 border-b border-gray-200">
                        <h2 class="text-2xl font-bold text-gray-800">Add New Hotel</h2>
                    </div>

                    <form @submit.prevent="submit" class="p-6 space-y-6">
                        <!-- Basic Information -->
                        <div>
                            <h3 class="text-lg font-semibold text-gray-700 mb-4">Basic Information</h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Hotel Name *</label>
                                    <input 
                                        v-model="form.name" 
                                        type="text" 
                                        required
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                    />
                                    <div v-if="form.errors.name" class="text-red-600 text-sm mt-1">{{ form.errors.name }}</div>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Star Rating *</label>
                                    <select 
                                        v-model="form.star_rating" 
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                    >
                                        <option :value="1">1 Star</option>
                                        <option :value="2">2 Stars</option>
                                        <option :value="3">3 Stars</option>
                                        <option :value="4">4 Stars</option>
                                        <option :value="5">5 Stars</option>
                                    </select>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Property Type *</label>
                                    <select 
                                        v-model="form.property_type" 
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                    >
                                        <option value="hotel">Hotel</option>
                                        <option value="resort">Resort</option>
                                        <option value="guesthouse">Guest House</option>
                                        <option value="hostel">Hostel</option>
                                        <option value="apartment">Apartment</option>
                                    </select>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700">City *</label>
                                    <input 
                                        v-model="form.city" 
                                        type="text" 
                                        required
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                    />
                                </div>

                                <div class="md:col-span-2">
                                    <label class="block text-sm font-medium text-gray-700">Description</label>
                                    <textarea 
                                        v-model="form.description" 
                                        rows="3"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                    ></textarea>
                                </div>
                            </div>
                        </div>

                        <!-- Location -->
                        <div>
                            <h3 class="text-lg font-semibold text-gray-700 mb-4">Location</h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div class="md:col-span-2">
                                    <label class="block text-sm font-medium text-gray-700">Address *</label>
                                    <input 
                                        v-model="form.address" 
                                        type="text" 
                                        required
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                    />
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Area</label>
                                    <input 
                                        v-model="form.area" 
                                        type="text" 
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                    />
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Country</label>
                                    <input 
                                        v-model="form.country" 
                                        type="text" 
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                    />
                                </div>
                            </div>
                        </div>

                        <!-- Contact Information -->
                        <div>
                            <h3 class="text-lg font-semibold text-gray-700 mb-4">Contact Information</h3>
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Phone</label>
                                    <input 
                                        v-model="form.phone" 
                                        type="text" 
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                    />
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Email</label>
                                    <input 
                                        v-model="form.email" 
                                        type="email" 
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                    />
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Website</label>
                                    <input 
                                        v-model="form.website" 
                                        type="url" 
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                    />
                                </div>
                            </div>
                        </div>

                        <!-- Check-in/Check-out -->
                        <div>
                            <h3 class="text-lg font-semibold text-gray-700 mb-4">Check-in/Check-out Times</h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Check-in Time</label>
                                    <input 
                                        v-model="form.check_in_time" 
                                        type="time" 
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                    />
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Check-out Time</label>
                                    <input 
                                        v-model="form.check_out_time" 
                                        type="time" 
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                    />
                                </div>
                            </div>
                        </div>

                        <!-- Status -->
                        <div>
                            <h3 class="text-lg font-semibold text-gray-700 mb-4">Status</h3>
                            <div class="space-y-2">
                                <label class="flex items-center">
                                    <input 
                                        v-model="form.is_active" 
                                        type="checkbox" 
                                        class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                    />
                                    <span class="ml-2 text-sm text-gray-700">Active (Visible to users)</span>
                                </label>

                                <label class="flex items-center">
                                    <input 
                                        v-model="form.is_featured" 
                                        type="checkbox" 
                                        class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                    />
                                    <span class="ml-2 text-sm text-gray-700">Featured (Promoted hotel)</span>
                                </label>
                            </div>
                        </div>

                        <!-- Actions -->
                        <div class="flex justify-end gap-4 pt-6 border-t">
                            <a 
                                :href="route('admin.hotels.index')" 
                                class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300"
                            >
                                Cancel
                            </a>
                            <button 
                                type="submit" 
                                :disabled="form.processing"
                                class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 disabled:opacity-50"
                            >
                                {{ form.processing ? 'Creating...' : 'Create Hotel' }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>
