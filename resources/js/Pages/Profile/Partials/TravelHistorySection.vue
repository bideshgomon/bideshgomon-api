<script setup>
import { ref, onMounted, watch } from 'vue';
import { useForm, router } from '@inertiajs/vue3';
import axios from 'axios';
import RhythmicCard from '@/Components/Rhythmic/RhythmicCard.vue';
import FlowButton from '@/Components/Rhythmic/FlowButton.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import DangerButton from '@/Components/DangerButton.vue';
import TextInput from '@/Components/TextInput.vue';
import Modal from '@/Components/Modal.vue';
import { GlobeAltIcon, TrashIcon, PlusIcon, CalendarIcon, MapPinIcon } from '@heroicons/vue/24/outline';

const props = defineProps({
  travelHistory: {
    type: Array,
    default: () => []
  },
});

const travelHistory = ref(props.travelHistory || []);
const showAddModal = ref(false);
const showEditModal = ref(false);
const editingTravel = ref(null);
const loading = ref(false);

const form = useForm({
    user_passport_id: '',
    user_visa_history_id: '',
    country: '',
    city: '',
    purpose: 'tourism',
    entry_date: '',
    exit_date: '',
    duration_days: '',
    visa_type_used: '',
    accommodation_type: 'hotel',
    accommodation_address: '',
    transportation_mode: 'air',
    entry_port: '',
    exit_port: '',
    sponsoring_organization: '',
    travel_companions: '',
    incidents_or_violations: '',
    notes: '',
});

const purposes = {
    tourism: 'Tourism',
    business: 'Business',
    education: 'Education',
    family: 'Family Visit',
    medical: 'Medical',
    transit: 'Transit',
};

const accommodationTypes = {
    hotel: 'Hotel',
    hostel: 'Hostel',
    airbnb: 'Airbnb/Rental',
    family: 'Family/Friends',
    company: 'Company Accommodation',
    university: 'University Housing',
    other: 'Other',
};

const transportationModes = {
    air: 'Air (Flight)',
    land: 'Land (Bus/Car/Train)',
    sea: 'Sea (Ship/Ferry)',
};

// Data is passed via props from ProfileController
onMounted(() => {
    travelHistory.value = props.travelHistory || [];
});

// Watch for prop changes
watch(() => props.travelHistory, (newVal) => {
    travelHistory.value = newVal || [];
});

const openAddModal = () => {
    form.reset();
    form.clearErrors();
    showAddModal.value = true;
};

const openEditModal = (travel) => {
    editingTravel.value = travel;
    Object.keys(form.data()).forEach(key => {
        form[key] = travel[key] || '';
    });
    form.clearErrors();
    showEditModal.value = true;
};

const closeModals = () => {
    showAddModal.value = false;
    showEditModal.value = false;
    editingTravel.value = null;
    form.reset();
    form.clearErrors();
};

const calculateDuration = () => {
    if (form.entry_date && form.exit_date) {
        const entry = new Date(form.entry_date);
        const exit = new Date(form.exit_date);
        const diffTime = Math.abs(exit - entry);
        form.duration_days = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
    }
};

const submitAdd = async () => {
    try {
        await axios.post(route('profile.travel-history.store'), form.data());
        closeModals();
        router.reload({ only: ['travelHistory'] });
    } catch (error) {
        if (error.response?.data?.errors) {
            Object.keys(error.response.data.errors).forEach(key => {
                form.setError(key, error.response.data.errors[key][0]);
            });
        }
    }
};

const submitEdit = async () => {
    try {
        await axios.put(route('profile.travel-history.update', editingTravel.value.id), form.data());
        closeModals();
        router.reload({ only: ['travelHistory'] });
    } catch (error) {
        if (error.response?.data?.errors) {
            Object.keys(error.response.data.errors).forEach(key => {
                form.setError(key, error.response.data.errors[key][0]);
            });
        }
    }
};

const deleteTravel = async (travelId) => {
    if (!confirm('Are you sure you want to delete this travel record?')) {
        return;
    }

    try {
        await axios.delete(route('profile.travel-history.destroy', travelId));
        router.reload({ only: ['travelHistory'] });
    } catch (error) {
        alert(error.response?.data?.message || 'Failed to delete travel record');
    }
};

const formatDate = (date) => {
    if (!date) return 'N/A';
    return new Date(date).toLocaleDateString('en-US', { year: 'numeric', month: 'short', day: 'numeric' });
};

onMounted(() => {
    loadTravelHistory();
});
</script>

<template>
    <section>
        <header class="mb-rhythm-lg">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-indigo-600 flex items-center justify-center shadow-sm">
                        <GlobeAltIcon class="w-6 h-6 text-white" />
                    </div>
                    <div>
                        <h2 class="font-display font-bold text-xl text-gray-800">Travel History</h2>
                        <p class="text-xs text-gray-500">
                            International travel history for visa applications
                        </p>
                    </div>
                </div>
                <FlowButton @click="openAddModal" variant="primary">
                    <template #icon-left><PlusIcon class="w-4 h-4" /></template>
                    <span class="hidden sm:inline">Add Travel</span>
                    <span class="sm:hidden">Add</span>
                </FlowButton>
            </div>
        </header>

        <!-- Travel History List -->
        <div class="space-y-4">
            <div v-if="loading" class="text-center py-8">
                <div class="inline-block animate-spin rounded-full h-8 w-8 border-b-2 border-indigo-600"></div>
                <p class="mt-2 text-sm text-gray-500">Loading travel history...</p>
            </div>

            <div v-else-if="travelHistory.length === 0" class="text-center py-12 bg-gray-50 rounded-xl border-2 border-dashed border-gray-300">
                <GlobeAltIcon class="mx-auto h-16 w-16 md:h-20 md:w-20 text-gray-400" />
                <h3 class="mt-2 text-sm font-medium text-gray-900">No travel history</h3>
                <p class="mt-1 text-sm text-gray-500">Get started by adding your first travel record</p>
                <div class="mt-6">
                    <SecondaryButton @click="openAddModal">
                        <PlusIcon class="w-5 h-5 md:w-6 md:h-6 mr-2" />
                        Add Your First Travel
                    </SecondaryButton>
                </div>
            </div>

            <div v-else class="space-y-4">
                <div
                    v-for="travel in travelHistory"
                    :key="travel.id"
                    class="bg-white border border-gray-200 rounded-xl overflow-hidden shadow-sm hover:shadow-md transition-shadow"
                >
                    <div class="h-1 bg-green-500"></div>
                    <div class="p-4 sm:p-6">
                        <div class="flex items-start justify-between gap-3 mb-3">
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center gap-2 mb-1">
                                    <GlobeAltIcon class="w-6 h-6 md:w-7 md:h-7 text-green-600 flex-shrink-0" />
                                    <h3 class="text-base sm:text-lg font-bold text-gray-900 truncate">
                                        {{ travel.country }}
                                    </h3>
                                </div>
                                <p class="text-sm sm:text-base text-emerald-600 font-semibold">
                                    {{ travel.city }}
                                </p>
                            </div>
                            <span class="flex-shrink-0 inline-flex items-center px-2.5 py-1 bg-emerald-100 text-emerald-800 text-xs font-medium rounded-full">
                                {{ purposes[travel.purpose] || travel.purpose }}
                            </span>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 mb-3 text-sm text-gray-600">
                            <div class="flex items-center gap-1.5">
                                <CalendarIcon class="w-4 h-4 flex-shrink-0" />
                                <span>{{ formatDate(travel.entry_date) }}</span>
                            </div>
                            <div class="flex items-center gap-1.5">
                                <CalendarIcon class="w-4 h-4 flex-shrink-0" />
                                <span>{{ formatDate(travel.exit_date) }}</span>
                            </div>
                            <div class="flex items-center gap-1.5 font-medium text-emerald-600 sm:col-span-2">
                                <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <span>{{ travel.duration_days }} days</span>
                            </div>
                        </div>

                        <div v-if="travel.visa_type_used" class="flex flex-wrap gap-2 mb-3">
                            <span class="px-2.5 py-1 bg-blue-50 text-blue-700 text-xs font-medium rounded-full">
                                {{ travel.visa_type_used }}
                            </span>
                        </div>

                        <p v-if="travel.notes" class="text-sm text-gray-600 leading-relaxed">
                            {{ travel.notes }}
                        </p>
                    </div>

                    <div class="px-4 py-3 bg-gray-50 border-t border-gray-100">
                        <div class="flex gap-2">
                            <button
                                @click="openEditModal(travel)"
                                class="flex-1 inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-white border border-gray-300 text-gray-700 hover:bg-gray-50 rounded-lg font-medium text-sm transition-colors"
                            >
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                </svg>
                                <span>Edit</span>
                            </button>
                            <button
                                @click="deleteTravel(travel.id)"
                                class="flex-1 inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-white border border-red-300 text-red-600 hover:bg-red-50 rounded-lg font-medium text-sm transition-colors"
                            >
                                <TrashIcon class="h-5 w-5" />
                                <span>Delete</span>
                            </button>
                        </div>
                    </div>
                </div>

                <button
                    @click="openAddModal"
                    class="w-full inline-flex items-center justify-center gap-2 px-6 py-4 bg-green-600 hover:bg-green-700 text-white font-semibold rounded-xl shadow-md hover:shadow-lg transition-all duration-200 text-base"
                >
                    <PlusIcon class="h-5 w-5" />
                    <span>ADD MORE TRAVEL</span>
                </button>
            </div>
        </div>

        <!-- Add/Edit Travel Modal -->
        <Modal :show="showAddModal || showEditModal" @close="closeModals" max-width="2xl">
            <div class="p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">
                    {{ showAddModal ? 'Add Travel Record' : 'Edit Travel Record' }}
                </h2>

                <form @submit.prevent="showAddModal ? submitAdd() : submitEdit()" class="space-y-4">
                    <!-- Country & City -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <InputLabel for="country" value="Country Visited *" />
                            <TextInput
                                id="country"
                                type="text"
                                class="mt-1 block w-full"
                                v-model="form.country"
                                placeholder="e.g., United Arab Emirates"
                                required
                            />
                            <InputError class="mt-2" :message="form.errors.country" />
                        </div>

                        <div>
                            <InputLabel for="city" value="City" />
                            <TextInput
                                id="city"
                                type="text"
                                class="mt-1 block w-full"
                                v-model="form.city"
                                placeholder="e.g., Dubai"
                            />
                            <InputError class="mt-2" :message="form.errors.city" />
                        </div>
                    </div>

                    <!-- Purpose & Dates -->
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                        <div>
                            <InputLabel for="purpose" value="Purpose *" />
                            <select
                                id="purpose"
                                v-model="form.purpose"
                                class="mt-1 block w-full border-gray-300 focus:border-emerald-500 focus:ring-emerald-500 rounded-md shadow-sm"
                            >
                                <option v-for="(label, value) in purposes" :key="value" :value="value">
                                    {{ label }}
                                </option>
                            </select>
                            <InputError class="mt-2" :message="form.errors.purpose" />
                        </div>

                        <div>
                            <InputLabel for="entry_date" value="Entry Date *" />
                            <TextInput
                                id="entry_date"
                                type="date"
                                class="mt-1 block w-full"
                                v-model="form.entry_date"
                                @change="calculateDuration"
                                required
                            />
                            <InputError class="mt-2" :message="form.errors.entry_date" />
                        </div>

                        <div>
                            <InputLabel for="exit_date" value="Exit Date *" />
                            <TextInput
                                id="exit_date"
                                type="date"
                                class="mt-1 block w-full"
                                v-model="form.exit_date"
                                @change="calculateDuration"
                                required
                            />
                            <InputError class="mt-2" :message="form.errors.exit_date" />
                        </div>
                    </div>

                    <!-- Duration & Accommodation -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <InputLabel for="duration_days" value="Duration (Days) *" />
                            <TextInput
                                id="duration_days"
                                type="number"
                                class="mt-1 block w-full bg-gray-50"
                                v-model="form.duration_days"
                                readonly
                            />
                            <p class="mt-1 text-xs text-gray-500">Auto-calculated from entry and exit dates</p>
                            <InputError class="mt-2" :message="form.errors.duration_days" />
                        </div>

                        <div>
                            <InputLabel for="accommodation_type" value="Accommodation Type" />
                            <select
                                id="accommodation_type"
                                v-model="form.accommodation_type"
                                class="mt-1 block w-full border-gray-300 focus:border-emerald-500 focus:ring-emerald-500 rounded-md shadow-sm"
                            >
                                <option v-for="(label, value) in accommodationTypes" :key="value" :value="value">
                                    {{ label }}
                                </option>
                            </select>
                            <InputError class="mt-2" :message="form.errors.accommodation_type" />
                        </div>
                    </div>

                    <!-- Accommodation Address -->
                    <div>
                        <InputLabel for="accommodation_address" value="Accommodation Address" />
                        <TextInput
                            id="accommodation_address"
                            type="text"
                            class="mt-1 block w-full"
                            v-model="form.accommodation_address"
                            placeholder="Full address where you stayed"
                        />
                        <InputError class="mt-2" :message="form.errors.accommodation_address" />
                    </div>

                    <!-- Transportation & Ports -->
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                        <div>
                            <InputLabel for="transportation_mode" value="Transportation" />
                            <select
                                id="transportation_mode"
                                v-model="form.transportation_mode"
                                class="mt-1 block w-full border-gray-300 focus:border-emerald-500 focus:ring-emerald-500 rounded-md shadow-sm"
                            >
                                <option v-for="(label, value) in transportationModes" :key="value" :value="value">
                                    {{ label }}
                                </option>
                            </select>
                            <InputError class="mt-2" :message="form.errors.transportation_mode" />
                        </div>

                        <div>
                            <InputLabel for="entry_port" value="Entry Port" />
                            <TextInput
                                id="entry_port"
                                type="text"
                                class="mt-1 block w-full"
                                v-model="form.entry_port"
                                placeholder="e.g., Dubai International Airport"
                            />
                            <InputError class="mt-2" :message="form.errors.entry_port" />
                        </div>

                        <div>
                            <InputLabel for="exit_port" value="Exit Port" />
                            <TextInput
                                id="exit_port"
                                type="text"
                                class="mt-1 block w-full"
                                v-model="form.exit_port"
                                placeholder="e.g., Dubai International Airport"
                            />
                            <InputError class="mt-2" :message="form.errors.exit_port" />
                        </div>
                    </div>

                    <!-- Optional Fields -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <InputLabel for="visa_type_used" value="Visa Type Used" />
                            <TextInput
                                id="visa_type_used"
                                type="text"
                                class="mt-1 block w-full"
                                v-model="form.visa_type_used"
                                placeholder="e.g., Tourist Visa, Work Visa"
                            />
                            <InputError class="mt-2" :message="form.errors.visa_type_used" />
                        </div>

                        <div>
                            <InputLabel for="sponsoring_organization" value="Sponsoring Organization" />
                            <TextInput
                                id="sponsoring_organization"
                                type="text"
                                class="mt-1 block w-full"
                                v-model="form.sponsoring_organization"
                                placeholder="Company or organization that sponsored"
                            />
                            <InputError class="mt-2" :message="form.errors.sponsoring_organization" />
                        </div>
                    </div>

                    <!-- Travel Companions -->
                    <div>
                        <InputLabel for="travel_companions" value="Travel Companions" />
                        <TextInput
                            id="travel_companions"
                            type="text"
                            class="mt-1 block w-full"
                            v-model="form.travel_companions"
                            placeholder="Names of people you traveled with"
                        />
                        <InputError class="mt-2" :message="form.errors.travel_companions" />
                    </div>

                    <!-- Notes -->
                    <div>
                        <InputLabel for="notes" value="Additional Notes" />
                        <textarea
                            id="notes"
                            v-model="form.notes"
                            rows="3"
                            class="mt-1 block w-full border-gray-300 focus:border-emerald-500 focus:ring-emerald-500 rounded-md shadow-sm"
                            placeholder="Any additional information about this trip..."
                        ></textarea>
                        <InputError class="mt-2" :message="form.errors.notes" />
                    </div>

                    <div class="flex items-center justify-end space-x-3 mt-6 pt-4 border-t">
                        <SecondaryButton @click="closeModals" type="button">
                            Cancel
                        </SecondaryButton>
                        <PrimaryButton :disabled="form.processing">
                            {{ showAddModal ? 'Add Travel Record' : 'Update Travel Record' }}
                        </PrimaryButton>
                    </div>
                </form>
            </div>
        </Modal>
    </section>
</template>
