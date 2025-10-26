<script setup>
import PublicLayout from '@/Layouts/PublicLayout.vue';
import { Head, Link, useForm, usePage } from '@inertiajs/vue3';
import { computed, ref } from 'vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import InputError from '@/Components/InputError.vue';
import TextareaInput from '@/Components/TextareaInput.vue'; // Make sure this component exists

// Get props passed via Inertia router.get() from Index.vue
const props = defineProps({
    quote_reference: String,
    package_id: [String, Number],
    package_name: String,
    destination_country_id: [String, Number],
    start_date: String,
    end_date: String,
    num_travelers: Number,
    total_premium: [String, Number],
    currency: String,
    countries: Array,
    errors: Object, // Server-side validation errors if submission fails
});

// Find destination country name
const destinationCountry = computed(() => {
    return props.countries?.find(c => c.id == props.destination_country_id);
});

// State for API errors
const apiError = ref(null);

// Form structure for traveler details + policyholder info
const form = useForm({
    // Include quote details needed for submission
    quote_reference: props.quote_reference,
    package_id: props.package_id,
    package_name: props.package_name,
    destination_country_id: props.destination_country_id,
    start_date: props.start_date,
    end_date: props.end_date,
    num_travelers: props.num_travelers,
    total_premium: props.total_premium,
    currency: props.currency,

    // Array to hold traveler details
    travelers: Array.from({ length: props.num_travelers }, () => ({
        name: '',
        dob: '', // Date of Birth
        passport_no: '',
    })),

    // Policyholder details
    policyholder_name: usePage().props.auth.user?.name || '',
    policyholder_email: usePage().props.auth.user?.email || '',
    policyholder_phone: usePage().props.auth.user?.phone || '',
    policyholder_address: '',
});

// *** UPDATED: SUBMIT PURCHASE METHOD ***
const submitPurchase = async () => {
    apiError.value = null; // Clear old errors
    form.processing = true;

    try {
        // Call our backend API to initiate payment
        const response = await axios.post(route('api.payment.initiate.travel-insurance'), form.data());

        // The backend now returns a JSON response.
        // The redirect URL is in response.data.data
        if (response.data.redirectUrl) {
            // Redirect the user to the SSLCommerz payment page
            window.location.href = response.data.redirectUrl;
        } else {
            apiError.value = 'Failed to get payment URL. Please try again.';
        }

    } catch (error) {
        console.error("Payment initiation error:", error);
        if (error.response?.data?.errors) {
            form.setError(error.response.data.errors);
            apiError.value = error.response.data.message || 'Please check your details and try again.';
        } else if (error.response?.data?.message) {
            apiError.value = error.response.data.message;
        } else {
            apiError.value = 'An unexpected error occurred. Please try again later.';
        }
    } finally {
        form.processing = false;
    }
};

// Helper to format currency
const formatCurrency = (value, currency) => {
    if (value === null || value === undefined) return 'N/A';
    return `${currency || 'BDT'} ${Number(value).toLocaleString()}`; // Default to BDT
};
</script>

<template>
    <Head :title="`Purchase ${package_name}`" />

    <PublicLayout :title="`Purchase ${package_name}`">
         <div class="py-12 bg-brand-light dark:bg-gray-900">
            <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 md:p-8 text-gray-900 dark:text-gray-100">

                        <h2 class="text-2xl font-bold text-gray-900 dark:text-gray-100 mb-2">
                            Review & Purchase Insurance
                        </h2>
                        <p class="text-gray-600 dark:text-gray-400 mb-6 border-b dark:border-gray-700 pb-4">
                            Please provide traveler details for your trip to {{ destinationCountry?.name }}.
                        </p>

                        <div class="mb-6 p-4 bg-gray-50 dark:bg-gray-700 rounded-md border dark:border-gray-600">
                            <h3 class="font-semibold mb-2">Trip Summary</h3>
                            <p><strong>Package:</strong> {{ package_name }}</p>
                            <p><strong>Destination:</strong> {{ destinationCountry?.name }}</p>
                            <p><strong>Dates:</strong> {{ start_date }} to {{ end_date }}</p>
                            <p><strong>Travelers:</strong> {{ num_travelers }}</p>
                            <p class="mt-2 text-xl font-bold"><strong>Total Premium:</strong> {{ formatCurrency(total_premium, currency) }}</p>
                            <p class="text-xs text-gray-500">(Quote Ref: {{ quote_reference }})</p>
                        </div>

                        <form @submit.prevent="submitPurchase">
                            <h3 class="text-lg font-semibold mt-8 mb-4 border-t dark:border-gray-700 pt-4">Policyholder Information</h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                                <div>
                                    <InputLabel for="ph_name" value="Full Name" required/>
                                    <TextInput id="ph_name" v-model="form.policyholder_name" class="mt-1 block w-full" required />
                                    <InputError :message="form.errors.policyholder_name" class="mt-2" />
                                </div>
                                 <div>
                                    <InputLabel for="ph_email" value="Email Address" required/>
                                    <TextInput id="ph_email" type="email" v-model="form.policyholder_email" class="mt-1 block w-full" required />
                                    <InputError :message="form.errors.policyholder_email" class="mt-2" />
                                </div>
                                 <div>
                                    <InputLabel for="ph_phone" value="Phone Number" required/>
                                    <TextInput id="ph_phone" v-model="form.policyholder_phone" class="mt-1 block w-full" required placeholder="e.g., 017..." />
                                     <InputError :message="form.errors.policyholder_phone" class="mt-2" />
                                </div>
                                <div>
                                    <InputLabel for="ph_address" value="Address" required/>
                                    <TextInput id="ph_address" v-model="form.policyholder_address" class="mt-1 block w-full" required placeholder="e.g., 123 Main St, Dhaka"/>
                                    <InputError :message="form.errors.policyholder_address" class="mt-2" />
                                </div>
                            </div>

                            <h3 class="text-lg font-semibold mt-8 mb-4 border-t dark:border-gray-700 pt-4">Traveler Information</h3>
                            <div v-for="(traveler, index) in form.travelers" :key="index" class="mb-6 p-4 border dark:border-gray-600 rounded-md">
                                <h4 class="font-medium mb-3">Traveler {{ index + 1 }}</h4>
                                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                     <div>
                                        <InputLabel :for="`t_name_${index}`" value="Full Name" required/>
                                        <TextInput :id="`t_name_${index}`" v-model="traveler.name" class="mt-1 block w-full" required />
                                        <InputError :message="form.errors[`travelers.${index}.name`]" class="mt-2" />
                                    </div>
                                     <div>
                                        <InputLabel :for="`t_dob_${index}`" value="Date of Birth" required/>
                                        <TextInput :id="`t_dob_${index}`" type="date" v-model="traveler.dob" class="mt-1 block w-full" required />
                                         <InputError :message="form.errors[`travelers.${index}.dob`]" class="mt-2" />
                                    </div>
                                     <div>
                                        <InputLabel :for="`t_passport_${index}`" value="Passport No." required/>
                                        <TextInput :id="`t_passport_${index}`" v-model="traveler.passport_no" class="mt-1 block w-full" required />
                                        <InputError :message="form.errors[`travelers.${index}.passport_no`]" class="mt-2" />
                                    </div>
                                </div>
                            </div>

                            <div v-if="apiError" class="my-4 p-3 bg-red-100 dark:bg-red-900 border border-red-300 dark:border-red-700 rounded-md text-red-700 dark:text-red-300">
                                {{ apiError }}
                            </div>

                            <div class="mt-8 flex justify-between items-center border-t dark:border-gray-700 pt-6">
                                 <Link :href="route('public.travel-insurance.index')" class="text-sm text-gray-600 hover:text-gray-900">
                                    &larr; Back to Packages
                                </Link>
                                <PrimaryButton :disabled="form.processing">
                                    {{ form.processing ? 'Processing...' : 'Proceed to Payment' }}
                                </PrimaryButton>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </PublicLayout>
</template>