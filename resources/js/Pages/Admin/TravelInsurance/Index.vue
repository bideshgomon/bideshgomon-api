<script setup>
import PublicLayout from '@/Layouts/PublicLayout.vue';
// --- MERGED: Added 'router' import ---
import { Head, Link, router } from '@inertiajs/vue3';
import { ref, reactive, computed, onMounted } from 'vue';
import axios from 'axios';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import InputError from '@/Components/InputError.vue';

const props = defineProps({
    countries: Array,
});

// --- State ---
const form = reactive({
    destination_country_id: '',
    start_date: '',
    end_date: '',
    num_travelers: 1,
});

const packages = ref([]);
const selectedPackage = ref(null);
const premiumQuote = ref(null);
const isLoadingPackages = ref(true); // Start true for initial load
const isLoadingQuote = ref(false);
const errors = ref({});

// --- Computed ---
const today = computed(() => {
    const d = new Date();
    return d.toISOString().split('T')[0];
});

// --- Methods ---
const fetchPackages = async () => {
    isLoadingPackages.value = true;
    packages.value = [];
    errors.value = {}; // Clear errors on fetch
    try {
        const response = await axios.get(route('api.travel-insurance.packages'));
        // **IMPORTANT**: Adapt this based on the actual structure of Bimafy's package response
        packages.value = response.data; // Assuming response is an array of package objects
    } catch (error) {
        console.error("Error fetching packages:", error);
        errors.value.packages = 'Failed to load insurance packages. Please try again later.';
    } finally {
        isLoadingPackages.value = false;
    }
};

// --- MERGED: Replaced getQuote with new version (Snippet 1) ---
const getQuote = async (pkg) => {
    selectedPackage.value = pkg;
    isLoadingQuote.value = true;
    premiumQuote.value = null; // Reset previous quote
    errors.value = {}; // Clear previous errors

    // VALIDATION (Client-side)
    if (!form.destination_country_id || !form.start_date || !form.end_date || form.num_travelers < 1) { // Added num_travelers check
        errors.value.form = 'Please select destination, dates, and number of travelers (at least 1).';
        isLoadingQuote.value = false;
        return;
    }
    if (new Date(form.end_date) < new Date(form.start_date)) {
         errors.value.form = 'End date cannot be before start date.';
         isLoadingQuote.value = false;
         return;
    }

    // --- UPDATED AGE HANDLING (Still simplified) ---
    // In a real scenario, you'd collect actual ages here or before calling getQuote.
    // For now, generate an array based on the count, assuming age 30 for all.
    const travelerAges = Array(form.num_travelers).fill(30);
    // --- End Updated Age Handling ---


    try {
        // Find country code - ensure props.countries exists and has 'code'
        const countryCode = props.countries?.find(c => c.id == form.destination_country_id)?.code || null;
        if (!countryCode) {
             throw new Error('Could not find country code for selected destination.');
        }

        const response = await axios.post(route('api.travel-insurance.calculate-premium'), {
            package_id: pkg.id, // Assuming package has an 'id' from Bimafy
            destination_country_code: countryCode, // Use the found code
            start_date: form.start_date,
            end_date: form.end_date,
            traveler_ages: travelerAges, // Use the generated ages array
        });
        premiumQuote.value = response.data;

    } catch (error) {
        console.error("Error calculating premium:", error);
        if (error.response?.data?.errors) {
            errors.value.quote = error.response.data.message || 'Validation failed.';
             // Map specific errors if needed
             console.error("Validation Errors:", error.response.data.errors);
        } else {
             errors.value.quote = error.message || 'Failed to calculate premium. Please check details or try again later.';
        }
    } finally {
        isLoadingQuote.value = false;
    }
};

// --- MERGED: Replaced proceedToPurchase with new version (Snippet 2) ---
const proceedToPurchase = () => {
    if (!premiumQuote.value || !selectedPackage.value) return;

    // Data to pass to the purchase page
    const purchaseData = {
        destination_country_id: form.destination_country_id,
        start_date: form.start_date,
        end_date: form.end_date,
        num_travelers: form.num_travelers,
        package_id: selectedPackage.value.id, // Or relevant package identifier
        package_name: selectedPackage.value.name, // Pass name for display
        quote_reference: premiumQuote.value.quote_reference, // Crucial reference from Bimafy
        total_premium: premiumQuote.value.total_premium,
        currency: premiumQuote.value.currency,
        // Pass any other necessary details from the quote or package
    };

    // Navigate to the purchase form route (we'll create this route next)
    router.get(route('public.travel-insurance.purchase'), purchaseData);
};


// Fetch packages on mount
onMounted(fetchPackages);

// Helper to format currency
const formatCurrency = (value, currency = 'USD') => {
    if (value === null || value === undefined) return 'N/A';
    return new Intl.NumberFormat('en-US', { style: 'currency', currency: currency, minimumFractionDigits: 0, maximumFractionDigits: 2 }).format(value);
};

</script>

<template>
    <Head title="Travel Insurance" />

    <PublicLayout>

        <div class="hero" style="padding-top: 4rem; padding-bottom: 4rem; background-color: #fff; border-bottom: 1px solid var(--brand-border);">
            <div class="container">
                <h1 style="font-size: 2.5rem;">Travel Insurance</h1>
                <p class="mt-2" style="font-size: 1.1rem; color: #555;">Get a quote and secure your trip with comprehensive coverage.</p>
            </div>
        </div>

        <div class="container py-8 md:py-12">
            <div class="p-4 sm:p-8 bg-white dark:bg-gray-800 shadow sm:rounded-lg">
                <section>
                    <header class="text-center mb-8">
                        <h2 class="text-2xl font-semibold text-gray-900 dark:text-gray-100">
                            Get Your Travel Insurance Quote
                         </h2>
                         <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                             Select your destination and travel dates to see available plans.
                         </p>
                    </header>

                    <div class="max-w-4xl mx-auto grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-10 pb-8 border-b dark:border-gray-700">
                        <div class="form-group">
                            <InputLabel for="destination" value="Destination Country" class="font-semibold"/>
                            <select
                                id="destination"
                                class="mt-1 block w-full text-sm"
                                v-model="form.destination_country_id"
                                required
                            >
                                <option value="" disabled>Select Country</option>
                                <option v-for="country in countries" :key="country.id" :value="country.id">
                                    {{ country.name }}
                                </option>
                            </select>
                        </div>
                        <div class="form-group">
                            <InputLabel for="start_date" value="Start Date" class="font-semibold"/>
                            <TextInput
                                id="start_date" type="date"
                                class="mt-1 block w-full text-sm"
                                v-model="form.start_date"
                                :min="today" required
                            />
                        </div>
                        <div class="form-group">
                            <InputLabel for="end_date" value="End Date" class="font-semibold"/>
                            <TextInput
                                id="end_date" type="date"
                                class="mt-1 block w-full text-sm"
                                v-model="form.end_date"
                                :min="form.start_date || today" required
                            />
                        </div>
                        <div class="form-group">
                             <InputLabel for="num_travelers" value="Travelers" class="font-semibold"/>
                            <TextInput
                                id="num_travelers" type="number"
                                class="mt-1 block w-full text-sm"
                                v-model.number="form.num_travelers"
                                min="1" required
                            />
                        </div>
                         <InputError class="mt-2 sm:col-span-2 lg:col-span-4 text-center" :message="errors.form" />
                    </div>

                    {/* Loading Packages Skeleton */}
                    <div v-if="isLoadingPackages" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        <div v-for="n in 3" :key="n" class="card animate-pulse">
                            <div class="h-6 bg-gray-200 rounded w-3/4 mb-3"></div>
                            <div class="h-4 bg-gray-200 rounded w-full mb-2"></div>
                            <div class="h-4 bg-gray-200 rounded w-5/6 mb-4"></div>
                            <div class="space-y-1 mb-4">
                                <div class="h-3 bg-gray-200 rounded w-1/2"></div>
                                <div class="h-3 bg-gray-200 rounded w-1/3"></div>
                            </div>
                            <div class="h-9 bg-gray-200 rounded w-full mt-auto"></div>
                        </div>
                    </div>

                    <div v-if="errors.packages" class="text-center py-6 text-red-600 dark:text-red-400">
                         {{ errors.packages }}
                     </div>

                    {/* Display Packages */}
                    <div v-if="!isLoadingPackages && packages.length > 0">
                        <h3 class="text-xl font-semibold mb-4 text-center text-gray-900 dark:text-gray-100">Available Packages</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                            <div v-for="pkg in packages" :key="pkg.id" class="card flex flex-col justify-between">
                                <div>
                                    <h4 class="font-semibold text-lg mb-2" style="color: var(--brand-primary);">{{ pkg.name }}</h4>
                                    <p class="text-sm text-gray-600 dark:text-gray-400 mb-3">{{ pkg.description || 'Details unavailable.' }}</p>
                                    <ul v-if="pkg.coverageHighlights" class="text-xs list-disc list-inside space-y-1 text-gray-500 dark:text-gray-400 mb-4">
                                        <li v-for="(highlight, index) in pkg.coverageHighlights" :key="index">{{ highlight }}</li>
                                    </ul>
                                </div>
                                <SecondaryButton
                                    @click="getQuote(pkg)"
                                    class="w-full mt-4 justify-center !text-sm !py-1.5"
                                    :disabled="isLoadingQuote || !form.destination_country_id || !form.start_date || !form.end_date || form.num_travelers < 1"
                                    :class="{ 'opacity-50 cursor-not-allowed': !form.destination_country_id || !form.start_date || !form.end_date || form.num_travelers < 1 }"
                                >
                                    {{ (isLoadingQuote && selectedPackage?.id === pkg.id) ? 'Calculating...' : 'Get Quote' }}
                                </SecondaryButton>
                            </div>
                        </div>
                    </div>

                    {/* Quote Display */}
                    <div v-if="errors.quote" class="mt-8 text-center py-4 text-red-600 dark:text-red-400 border-t dark:border-gray-700">
                         {{ errors.quote }}
                     </div>
                    <div v-if="premiumQuote && selectedPackage" class="mt-8 pt-6 border-t dark:border-gray-700 text-center bg-green-50 dark:bg-gray-700 p-6 rounded-lg">
                        <h3 class="text-lg font-semibold mb-2 text-gray-900 dark:text-gray-100">Your Quote for: <span class="font-bold" style="color: var(--brand-primary);">{{ selectedPackage.name }}</span></h3>
                        <p class="text-3xl font-bold" style="color: var(--brand-secondary);">
                            {{ formatCurrency(premiumQuote.total_premium, premiumQuote.currency) }}
                        </p>
                        <p class="text-sm text-gray-600 dark:text-gray-400">
                            For {{ form.num_travelers }} traveler(s) for {{ premiumQuote.duration_days || 'N/A' }} days.
                        </p>
                        <p v-if="premiumQuote.quote_reference" class="text-xs text-gray-400 mt-1">(Ref: {{ premiumQuote.quote_reference }})</p>
                        <PrimaryButton @click="proceedToPurchase" class="mt-6">
                            Proceed to Purchase
                        </PrimaryButton>
                    </div>

                </section>
            </div>
        </div>
    </PublicLayout>
</template>